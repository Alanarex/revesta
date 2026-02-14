<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\BlogService;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 50);

        $users = $this->userService->getAllUsers($perPage, $search, $sort, $direction);
        $currentUser = auth()->user();
        $isAdmin = $currentUser && method_exists($currentUser, 'isAdmin') && $currentUser->isAdmin();

        $data = collect($users->items())->map(function ($user) use ($isAdmin) {
            $actions = [];

            if ($isAdmin) {
                // Admin users see Edit and Delete actions
                $actions[] = [
                    'type' => 'edit',
                    'label' => 'Modifier',
                    'icon' => 'fa-edit',
                    'route' => route('admin.users.show', $user),
                    'class' => '',
                ];
                $actions[] = [
                    'type' => 'delete',
                    'label' => 'Supprimer',
                    'icon' => 'fa-trash',
                    'route' => route('admin.users.destroy', $user),
                    'needs_confirm' => true,
                    'confirm_message' => "Êtes-vous sûr de vouloir supprimer {$user->full_name} ?",
                    'class' => 'text-danger',
                ];
            } else {
                // Non-admin users see only Show action
                $actions[] = [
                    'type' => 'show',
                    'label' => 'Voir',
                    'icon' => 'fa-eye',
                    'route' => route('admin.users.show', $user),
                    'class' => '',
                ];
            }

            return [
                'id' => $user->id,
                'label' => $user->full_name ?? '-',
                'full_name' => $user->full_name ?? '-',
                'email' => $user->email ?? '-',
                'phone' => $user->phone ?? '-',
                'role' => $user->role?->name ?? '-',
                'city' => $user->city ?? ($user->address?->city ?? ''),
                'postal_code' => $user->postal_code ?? ($user->address?->postal_code ?? ''),
                'actions' => $actions,
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $users->total(),
            'per_page' => $perPage,
            'current_page' => $page,
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

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Utilisateur mis à jour avec succès!']);
            }

            return redirect()->back()->with('success', 'Utilisateur mis à jour avec succès!');
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

    public function show(User $user): View
    {
        $loaded = $this->userService->findUserWithRelations($user->id) ?? $user->load('role', 'address');
        $isViewingOwn = auth()->check() && auth()->id() === $user->id;
        $isAdmin = auth()->check() && (auth()->user()->role && auth()->user()->role->name === 'admin');
        $rolesOptions = $isAdmin ? $this->roleService->getRolesForSelect() : [];

        // Calculate permissions and routes
        $canEdit = auth()->check() && ($isViewingOwn || $isAdmin);
        $updateRoute = $isAdmin && !$isViewingOwn ? route('admin.users.update', $user) : route('users.update', $user);
        $resetRoute = $isAdmin && !$isViewingOwn
            ? route('admin.users.reset-password', $user)
            : route('users.reset-password', $user);
        $destroyRoute = $isAdmin && !$isViewingOwn ? route('admin.users.destroy', $user) : route('users.destroy', $user);

        // Load blogs with type-safe helper
        ['blogsList' => $blogsList] = $this->loadUserBlogs($loaded, $isViewingOwn);

        // Load simulations and activity via service
        ['simulations' => $simulations, 'recentActivity' => $recentActivity] = $this->userService->getSimulationsAndRecentActivity($loaded);

        // Calculate profile score via service
        $profileScore = $this->userService->calculateProfileScore($loaded);

        // Load user config options
        $civilStatuses = config('users.civil_statuses');
        $familyStatuses = config('users.family_statuses');

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

    public function toggleActivate(\App\Http\Requests\ToggleUserActiveRequest $request, User $user)
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

    public function resetPassword(\App\Http\Requests\ResetUserPasswordRequest $request, User $user)
    {
        try {
            $data = $request->validated();
            $this->userService->setPassword($user, $data['password']);
            
            // Set user as unverified when password is reset
            $user->update(['email_verified_at' => null]);

            return response()->json(['success' => true, 'message' => 'Mot de passe mis à jour. L\'utilisateur doit reverifier son email pour se connecter.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
