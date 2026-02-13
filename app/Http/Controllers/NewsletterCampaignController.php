<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsletterCampaignRequest;
use App\Http\Requests\ScheduleNewsletterCampaignRequest;
use App\Http\Requests\ManageNewsletterCampaignRequest;
use App\Models\NewsletterCampaign;
use App\Repositories\NewsletterCampaignRepository;
use App\Services\NewsletterCampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsletterCampaignController extends Controller
{
    public function __construct(
        protected NewsletterCampaignService $campaignService,
        protected NewsletterCampaignRepository $campaignRepository
    ) {
    }

    /**
     * Display newsletters dashboard (datatable index).
     */
    public function index(ManageNewsletterCampaignRequest $request): View
    {
        $totalCount = $this->campaignRepository->count();

        return view('admin.newsletters.campaigns.index', [
            'totalCount' => $totalCount,
        ]);
    }

    /**
     * Get campaigns list for datatable (API endpoint).
     */
    public function list(ManageNewsletterCampaignRequest $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 20);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $campaigns = $this->campaignRepository->search($search, $sort, $direction, $perPage);

        $data = collect($campaigns->items())->map(function ($campaign) {
            $actions = [];

            // All campaigns have a view action
            $actions[] = [
                'type' => 'show',
                'label' => 'Voir',
                'icon' => 'fa-eye',
                'route' => route('admin.newsletters.show', $campaign),
                'class' => '',
            ];

            // Conditional actions based on campaign status
            if ($campaign->status === 'draft') {
                $actions[] = [
                    'type' => 'edit',
                    'label' => 'Éditer',
                    'icon' => 'fa-pencil',
                    'route' => route('admin.newsletters.edit', $campaign),
                    'class' => '',
                ];
                $actions[] = [
                    'type' => 'schedule',
                    'label' => 'Programmer',
                    'icon' => 'fa-clock',
                    'id' => $campaign->id,
                    'route' => route('admin.newsletters.schedule-form', $campaign),
                    'class' => '',
                ];
                $actions[] = [
                    'type' => 'send',
                    'label' => 'Envoyer',
                    'icon' => 'fa-paper-plane',
                    'route' => route('admin.newsletters.send-now', $campaign),
                    'needs_confirm' => true,
                    'confirm_message' => "Êtes-vous sûr de vouloir envoyer la campagne '{$campaign->title}' ?",
                    'class' => 'text-success',
                ];
                $actions[] = [
                    'type' => 'delete',
                    'label' => 'Supprimer',
                    'icon' => 'fa-trash',
                    'route' => route('admin.newsletters.destroy', $campaign),
                    'needs_confirm' => true,
                    'confirm_message' => "Êtes-vous sûr de vouloir supprimer la campagne '{$campaign->title}' ?",
                    'class' => 'text-danger',
                ];
            } elseif ($campaign->status === 'scheduled') {
                $actions[] = [
                    'type' => 'edit',
                    'label' => 'Éditer la programmation',
                    'icon' => 'fa-pencil',
                    'route' => route('admin.newsletters.schedule-form', $campaign),
                    'class' => '',
                ];
                $actions[] = [
                    'type' => 'cancel',
                    'label' => 'Annuler la programmation',
                    'icon' => 'fa-x-circle',
                    'route' => route('admin.newsletters.cancel-schedule', $campaign),
                    'needs_confirm' => true,
                    'confirm_message' => "Êtes-vous sûr de vouloir annuler la programmation de '{$campaign->title}' ?",
                    'class' => 'text-warning',
                ];
            }

            return [
                'id' => $campaign->id,
                'label' => $campaign->title,
                'title' => $campaign->title,
                'status' => $campaign->status,
                'created_at' => $campaign->created_at,
                'sent_count' => $campaign->sent_count ?? 0,
                'scheduled_at' => $campaign->scheduled_at,
                'actions' => $actions,
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $campaigns->total(),
            'per_page' => $perPage,
            'current_page' => $campaigns->currentPage(),
            'last_page' => $campaigns->lastPage(),
        ]);
    }

    /**
     * Show create form.
     */
    public function create(ManageNewsletterCampaignRequest $request): View
    {
        $breadcrumbs = [
            ['label' => 'Newsletters', 'url' => route('admin.newsletters.index')],
            ['label' => 'Créer'],
        ];

        return view('admin.newsletters.campaigns.create', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Store a new draft campaign.
     */
    public function store(StoreNewsletterCampaignRequest $request): RedirectResponse
    {
        $result = $this->campaignService->createDraft(
            $request->validated('title'),
            $request->validated('content')
        );

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        // Check which button was clicked
        if ($request->has('action_schedule')) {
            // Schedule button clicked - redirect to schedule page
            return redirect()
                ->route('admin.newsletters.schedule-form', $result['data']->id)
                ->with('success', $result['message']);
        }

        if ($request->has('action_publish_now')) {
            // Publish now button clicked - send immediately
            $publishResult = $this->campaignService->sendNow($result['data']->id);
            if ($publishResult['success']) {
                return redirect()
                    ->route('admin.newsletters.index')
                    ->with('success', $publishResult['message']);
            }
            return back()->withErrors(['error' => $publishResult['message']]);
        }

        // Default: redirect to edit page (save draft or create button)
        return redirect()
            ->route('admin.newsletters.edit', $result['data']->id)
            ->with('success', $result['message']);
    }

    /**
     * Show edit form for draft campaigns.
     */
    public function edit(ManageNewsletterCampaignRequest $request, NewsletterCampaign $campaign): View
    {
        if (!$campaign->isDraft()) {
            abort(403, 'Seules les campagnes brouillon peuvent être modifiées.');
        }

        $breadcrumbs = [
            ['label' => 'Newsletters', 'url' => route('admin.newsletters.index')],
            ['label' => 'Modifier'],
        ];

        return view('admin.newsletters.campaigns.edit', [
            'campaign' => $campaign,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Show campaign details (read-only).
     */
    public function show(ManageNewsletterCampaignRequest $request, NewsletterCampaign $campaign): View
    {
        $breadcrumbs = [
            ['label' => 'Newsletters', 'url' => route('admin.newsletters.index')],
            ['label' => $campaign->title],
        ];

        return view('admin.newsletters.campaigns.show', [
            'campaign' => $campaign,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Update a draft campaign.
     */
    public function update(StoreNewsletterCampaignRequest $request, NewsletterCampaign $campaign): RedirectResponse
    {
        if (!$campaign->isDraft()) {
            abort(403, 'Seules les campagnes brouillon peuvent être mises à jour.');
        }

        $result = $this->campaignService->updateDraft(
            $campaign->id,
            $request->validated('title'),
            $request->validated('content')
        );

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        // Check which button was clicked
        if ($request->has('action_schedule')) {
            // Schedule button clicked - redirect to schedule page
            return redirect()
                ->route('admin.newsletters.schedule-form', $campaign->id)
                ->with('success', $result['message']);
        }

        if ($request->has('action_publish_now')) {
            // Publish now button clicked - send immediately
            $publishResult = $this->campaignService->sendNow($campaign->id);
            if ($publishResult['success']) {
                return redirect()
                    ->route('admin.newsletters.index')
                    ->with('success', $publishResult['message']);
            }
            return back()->withErrors(['error' => $publishResult['message']]);
        }

        // Default: redirect to edit page (save draft button)
        return redirect()
            ->route('admin.newsletters.edit', $campaign->id)
            ->with('success', $result['message']);
    }

    /**
     * Delete a campaign (only drafts).
     */
    public function destroy(ManageNewsletterCampaignRequest $request, NewsletterCampaign $campaign): RedirectResponse
    {
        $result = $this->campaignService->deleteCampaign($campaign->id);

        if ($result['success']) {
            return redirect()
                ->route('admin.newsletters.index')
                ->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * Send campaign now.
     */
    public function sendNow(ManageNewsletterCampaignRequest $request, NewsletterCampaign $campaign): RedirectResponse
    {
        if (!$campaign->isDraft()) {
            abort(403, 'Seules les campagnes brouillon peuvent être envoyées.');
        }

        $result = $this->campaignService->sendNow($campaign->id);

        if ($result['success']) {
            return redirect()
                ->route('admin.newsletters.index')
                ->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * Show schedule form for draft or scheduled campaigns.
     */
    public function scheduleForm(ManageNewsletterCampaignRequest $request, NewsletterCampaign $campaign): View
    {
        if (!$campaign->isDraft() && !$campaign->isScheduled()) {
            abort(403, 'Seules les campagnes brouillon ou programmées peuvent être programmées.');
        }

        $subscribersCount = $this->campaignService->getSubscribersCount($campaign);

        $breadcrumbs = [
            ['label' => 'Newsletters', 'url' => route('admin.newsletters.index')],
            ['label' => $campaign->title],
            ['label' => $campaign->isScheduled() ? 'Modifier la programmation' : 'Programmer'],
        ];

        return view('admin.newsletters.campaigns.schedule', [
            'campaign' => $campaign,
            'subscribersCount' => $subscribersCount,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Schedule campaign for later or update existing schedule.
     */
    public function schedule(ScheduleNewsletterCampaignRequest $request, NewsletterCampaign $campaign): RedirectResponse
    {
        // Handle cancel schedule button
        if ($request->has('action_cancel_schedule')) {
            $result = $this->campaignService->cancelSchedule($campaign->id);

            if ($result['success']) {
                return redirect()
                    ->route('admin.newsletters.index')
                    ->with('success', $result['message']);
            }

            return back()->withErrors(['error' => $result['message']]);
        }

        if (!$campaign->isDraft() && !$campaign->isScheduled()) {
            abort(403, 'Seules les campagnes brouillon ou programmées peuvent être programmées.');
        }

        $scheduledAt = new \DateTime($request->validated('scheduled_at'));
        $result = $this->campaignService->schedule($campaign->id, $scheduledAt);
        if ($result['success']) {
            return redirect()
                ->route('admin.newsletters.index')
                ->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }

}

