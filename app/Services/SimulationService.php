<?php

namespace App\Services;

use App\Mail\SimulationReportMail;
use App\Models\Simulation;
use App\Models\User;
use App\Repositories\SimulationRepository;
use App\Repositories\AdRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SimulationService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected AdRepository $adRepository,
        protected SimulationRepository $simulationRepository,
    ) {}

    /**
     * Persist a full simulation payload and trigger emails.
     *
     * @return array{simulation: Simulation, user: User, user_created: bool}
     */
    public function submit(array $validatedPayload, array $originalPayload = []): array
    {
        $result = DB::transaction(function () use ($validatedPayload) {
            $annonce = (array) Arr::get($validatedPayload, 'annonce', []);
            $utilisateur = (array) Arr::get($validatedPayload, 'utilisateur', []);
            $simulationPayload = (array) Arr::get($validatedPayload, 'simulation', []);

            ['user' => $user, 'created' => $userCreated] = $this->userRepository->upsertFromPayload($utilisateur);

            $ad = $this->adRepository->upsertFromPayload($annonce);
            $simulation = $this->simulationRepository->createSimulation($user, $ad, $simulationPayload);

            $this->simulationRepository->syncWorks($simulation, (array) Arr::get($simulationPayload, 'travaux', []));
            $this->simulationRepository->syncAids($simulation, (array) Arr::get($simulationPayload, 'aides_details', []));

            return [
                'simulation' => $simulation,
                'user' => $user,
                'user_created' => $userCreated,
            ];
        });

        $this->archivePayloadSafely(
            $result['simulation'],
            $originalPayload !== [] ? $originalPayload : $validatedPayload,
        );

        $this->queueSimulationMailSafely(
            $result['simulation'],
            $result['user'],
            $originalPayload !== [] ? $originalPayload : $validatedPayload,
        );

        if ($result['user_created']) {
            $this->sendVerificationMailSafely($result['user']);
        }

        return $result;
    }

    public function getManagementSimulations(
        User $viewer,
        bool $isAdmin,
        int $perPage = 12,
        ?string $firstName = null,
        ?string $lastName = null,
    ): LengthAwarePaginator {
        return $this->simulationRepository->paginateForManagement(
            viewer: $viewer,
            isAdmin: $isAdmin,
            perPage: $perPage,
            firstName: $firstName,
            lastName: $lastName,
        );
    }

    public function findVisibleSimulation(User $viewer, bool $isAdmin, int $simulationId): ?Simulation
    {
        return $this->simulationRepository->findForManagement($viewer, $isAdmin, $simulationId);
    }

    private function queueSimulationMailSafely(Simulation $simulation, User $user, array $payload): void
    {
        try {
            Mail::to($user->email)->queue(new SimulationReportMail($simulation, $payload));
        } catch (Throwable $exception) {
            Log::warning('Failed to queue simulation report mail.', [
                'simulation_id' => $simulation->id,
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function sendVerificationMailSafely(User $user): void
    {
        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $exception) {
            Log::warning('Failed to send user email verification after simulation submission.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function archivePayloadSafely(Simulation $simulation, array $payload): void
    {
        try {
            $timestamp = now()->format('Ymd_His_u');
            $directory = 'simulation-api-payloads/'.now()->format('Y/m/d');
            $path = sprintf('%s/simulation_%d_%s.json', $directory, $simulation->id, $timestamp);

            Storage::disk('local')->put(
                $path,
                json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
            );
        } catch (Throwable $exception) {
            Log::warning('Failed to archive simulation API payload.', [
                'simulation_id' => $simulation->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
