<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\ResetUserPasswordRequest;
use App\Http\Requests\ShowUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\ToggleUserActiveRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Services\BlogService;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected BlogService $blogService,
        protected RoleService $roleService
    ) {}

    public function index(IndexUserRequest $request): View
    {
        $totalCount = $this->userService->getUserCount();

        return view('admin.users.index', [
            'title' => 'Gestion des utilisateurs',
            'totalCount' => $totalCount,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Utilisateurs'],
            ],
        ]);
    }

    public function list(IndexUserRequest $request): JsonResponse
    {
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'last_name');
        $direction = $request->get('direction', 'asc');
        $perPage = (int) $request->get('per_page', 50);

        $users = $this->userService->getAllUsers($perPage, $search, $sort, $direction);
        $isAdmin = auth()->check() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin();

        $data = collect($users->items())->map(
            fn ($user) => $this->userService->formatUserForList($user, $isAdmin)
        );

        return response()->json([
            'data' => $data,
            'total' => $users->total(),
            'per_page' => $perPage,
            'current_page' => $users->currentPage(),
        ]);
    }

    public function create(CreateUserRequest $request): View
    {
        $rolesOptions = $this->roleService->getRolesForSelect();
        $civilStatuses = config('users.civil_statuses');
        $familyStatuses = config('users.family_statuses');

        return view('admin.users.create', [
            'title' => 'Créer un utilisateur',
            'rolesOptions' => $rolesOptions,
            'civilStatuses' => $civilStatuses,
            'familyStatuses' => $familyStatuses,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Utilisateurs', 'url' => route('admin.users.index')],
                ['label' => 'Créer'],
            ],
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $this->userService->createUser($data);

            // Send verification email with password setup link
            $this->userService->sendEmailVerification($user);

            return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès! Un email de verification a ete envoye.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création: '.$e->getMessage());
        }
    }

    public function edit(EditUserRequest $request, User $user): RedirectResponse
    {
        return redirect()->route('admin.users.show', $user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $data = $request->validated();
            $this->userService->updateUser($user, $data);

            return redirect()->route('admin.users.show', $user)->with('success', 'Utilisateur mis à jour avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour: '.$e->getMessage());
        }
    }

    public function destroy(DeleteUserRequest $request, User $user)
    {
        try {
            $this->userService->deleteUser($user);

            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json(['success' => true, 'message' => 'Utilisateur supprimé avec succès!']);
            }

            return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé avec succès!');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression: '.$e->getMessage()], 500);
            }

            return redirect()->route('admin.users.index')->with('error', 'Erreur lors de la suppression: '.$e->getMessage());
        }
    }

    public function show(ShowUserRequest $request, User $user): View
    {
        $isViewingOwn = auth()->check() && auth()->id() === $user->id;
        $isAdmin = auth()->check() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin();
        $loaded = $this->userService->findUserWithRelations($user->id) ?? $user->load('role', 'address');

        // Calculate permissions and routes
        $canEdit = auth()->check() && ($isViewingOwn || $isAdmin);
        $updateRoute = $isAdmin && ! $isViewingOwn ? route('admin.users.update', $user) : route('admin.users.update', $user);
        $resetRoute = $isAdmin && ! $isViewingOwn
            ? route('admin.users.reset-password', $user)
            : route('admin.users.reset-password', $user);
        $destroyRoute = $isAdmin && ! $isViewingOwn ? route('admin.users.destroy', $user) : route('admin.users.destroy', $user);

        // Load blogs with type-safe helper
        ['blogsList' => $blogsList] = $this->loadUserBlogs($loaded, $isViewingOwn);

        // Load simulations and activity via service
        ['simulations' => $simulations, 'recentActivity' => $recentActivity] = $this->userService->getSimulationsAndRecentActivity($loaded);

        // Calculate profile score via service
        $profileScore = $this->userService->calculateProfileScore($loaded);

        // Load user config options
        $civilStatuses = config('users.civil_statuses');
        $familyStatuses = config('users.family_statuses');

        // Roles options available only to admins
        $rolesOptions = $isAdmin ? $this->roleService->getRolesForSelect() : [];

        return view('admin.users.show', [
            'user' => $loaded,
            'isViewingOwn' => $isViewingOwn,
            'isAdmin' => $isAdmin,
            'canEdit' => $canEdit,
            'updateRoute' => $updateRoute,
            'resetRoute' => $resetRoute,
            'destroyRoute' => $destroyRoute,
            'rolesOptions' => $rolesOptions,
            'blogsList' => $blogsList,
            'simulations' => $simulations,
            'recentActivity' => $recentActivity,
            'profileScore' => $profileScore,
            'civilStatuses' => $civilStatuses,
            'familyStatuses' => $familyStatuses,
            'title' => 'Utilisateur — '.($loaded->full_name ?? $user->full_name),
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Utilisateurs', 'url' => route('admin.users.index')],
                ['label' => $loaded->full_name],
            ],
        ]);
    }

    /**
     * Load user blogs based on viewing context.
     *
     * @return array{'blogsList': \Illuminate\Database\Eloquent\Collection}
     */
    private function loadUserBlogs(User $user, bool $isViewingOwn): array
    {
        // Load blogs based on context
        try {
            if ($isViewingOwn) {
                $blogsList = $this->blogService->getUserBlogsWithTags(
                    profileUserId: $user->id,
                    authUserId: auth()->id(),
                    includeBookmarked: true
                );
            } else {
                $blogsList = collect($this->blogService->getAllBlogs(
                    perPage: 1000,
                    authorId: $user->id,
                    status: 'published'
                )->items());
            }
        } catch (\Throwable) {
            $blogsList = collect();
        }

        return ['blogsList' => $blogsList];
    }

    public function toggleActivate(ToggleUserActiveRequest $request, User $user)
    {
        try {
            $wasTrashed = $user->trashed();
            $this->userService->toggleActive($user);

            return response()->json([
                'success' => true,
                'status' => $wasTrashed ? 'restored' : 'deactivated',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function resetPassword(ResetUserPasswordRequest $request, User $user)
    {
        try {
            // Unverify email and force refresh
            $user = $this->userService->updateUser($user, ['email_verified_at' => null]);

            // Generate password reset token and send email
            $token = Password::createToken($user);
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

            Mail::queue(new PasswordResetMail(
                userName: $user->first_name,
                resetUrl: $resetUrl,
                recipientEmail: $user->email,
            ));

            return response()->json(['success' => true, 'message' => 'Un email de reinitialisation de mot de passe a été envoyé à '.$user->email]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
