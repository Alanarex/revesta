<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index(IndexUserRequest $request): View
    {
        $totalCount = $this->userService->getUserCount();

        return view('admin.users.index', [
            'title' => 'Gestion des utilisateurs',
            'totalCount' => $totalCount,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Utilisateurs', 'url' => route('admin.users.index')],
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

        $data = collect($users->items())->map(function ($user) {
            return [
                'id' => $user->id,
                'full_name' => $user->full_name ?? '-',
                'email' => $user->email ?? '-',
                'phone' => $user->phone ?? '-',
                'role' => $user->role?->name ?? '-',
                'city' => $user->city ?? ($user->address?->city ?? ''),
                'postal_code' => $user->postal_code ?? ($user->address?->postal_code ?? ''),
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
        return view('admin.users.create', [
            'title' => 'Créer un utilisateur',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Utilisateurs', 'url' => route('admin.users.index')],
                ['label' => 'Créer', 'url' => route('admin.users.create')],
            ],
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            if (isset($data['password'])) {
                // ensure password is hashed by model casting or manually here
            }
            $this->userService->createUser($data);

            return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création: '.$e->getMessage());
        }
    }

    public function edit(EditUserRequest $request, User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'title' => 'Modifier l\'utilisateur',
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Utilisateurs', 'url' => route('admin.users.index')],
                ['label' => $user->full_name, 'url' => route('admin.users.edit', $user)],
                ['label' => 'Modifier', 'url' => route('admin.users.edit', $user)],
            ],
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $data = $request->validated();
            $this->userService->updateUser($user, $data);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Utilisateur mis à jour avec succès!']);
            }

            return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour: '.$e->getMessage());
        }
    }

    public function destroy(DeleteUserRequest $request, User $user): RedirectResponse
    {
        try {
            $this->userService->deleteUser($user);

            return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé avec succès!');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'Erreur lors de la suppression: '.$e->getMessage());
        }
    }

    public function show(User $user): View
    {
        // load relations required for the admin user page
        $loaded = $this->userService->findUserWithRelations($user->id) ?? $user->load('role', 'address');

        $isViewingOwn = auth()->check() && auth()->id() === $user->id;
        
        // Load blogs based on who is viewing
        $publishedBlogs = collect();
        $draftBlogs = collect();
        $bookmarks = collect();
        $blogsList = collect();
        
        try {
            if ($isViewingOwn) {
                // When viewing own profile, load all blogs
                $allBlogs = \App\Models\Blog::where('user_id', $loaded->id)
                    ->orderByDesc('created_at')
                    ->get();
                $blogsList = $allBlogs;
                $publishedBlogs = $allBlogs->where('status', \App\Models\Blog::PUBLISHED)->values();
                $draftBlogs = $allBlogs->where('status', '!=', \App\Models\Blog::PUBLISHED)->values();
                
                // Load bookmarked blogs
                $bookmarks = $loaded->blogBookmarks()->with('blog')->latest()->get();
                // Add bookmarked blogs to the list with a marker
                if ($bookmarks->isNotEmpty()) {
                    foreach ($bookmarks as $bookmark) {
                        $bookmark->blog->is_bookmarked = true;
                    }
                    $blogsList = $blogsList->concat($bookmarks->pluck('blog'));
                }
            } else {
                // When viewing others' profile, show only published blogs
                $publishedBlogs = \App\Models\Blog::where('user_id', $loaded->id)
                    ->where('status', \App\Models\Blog::PUBLISHED)
                    ->orderByDesc('created_at')
                    ->get();
                $blogsList = $publishedBlogs;
            }
            
            // Load bookmark status for authenticated user
            if (auth()->check() && $blogsList->isNotEmpty()) {
                $authUserId = auth()->id();
                $userBookmarks = \App\Models\BlogBookmark::where('user_id', $authUserId)
                    ->whereIn('blog_id', $blogsList->pluck('id'))
                    ->pluck('blog_id')
                    ->toArray();
                
                foreach ($blogsList as $blog) {
                    $blog->bookmarked_by_auth = in_array($blog->id, $userBookmarks) ? 1 : 0;
                }
            }
        } catch (\Throwable $e) {
            $publishedBlogs = collect();
            $draftBlogs = collect();
            $bookmarks = collect();
            $blogsList = collect();
        }

        // Counts and aggregates similar to ProfileController
        $publishedBlogsCount = $publishedBlogs->count();

        $totalLikes = \App\Models\BlogLike::where('likeable_type', \App\Models\Blog::class)
            ->whereIn('likeable_id', function ($query) use ($loaded) {
                $query->select('id')
                    ->from('blogs')
                    ->where('user_id', $loaded->id)
                    ->where('status', \App\Models\Blog::PUBLISHED);
            })->count();

        $totalComments = \App\Models\BlogComment::whereIn('blog_id', function ($query) use ($loaded) {
            $query->select('id')
                ->from('blogs')
                ->where('user_id', $loaded->id)
                ->where('status', \App\Models\Blog::PUBLISHED);
        })->count();

        // Load simulations and recent activity (last week)
        try {
            $simulations = $loaded->simulations()->orderByDesc('created_at')->get();

            $oneWeekAgo = now()->subWeek();

            $recentComments = $loaded->blogComments()->where('created_at', '>=', $oneWeekAgo)->with('blog')->latest()->get();
            $recentLikes = \App\Models\BlogLike::where('user_id', $loaded->id)->where('created_at', '>=', $oneWeekAgo)->with('likeable')->latest()->get();
            $recentBookmarks = $loaded->blogBookmarks()->where('created_at', '>=', $oneWeekAgo)->with('blog')->latest()->get();
            $recentPublished = \App\Models\Blog::where('user_id', $loaded->id)->where('status', \App\Models\Blog::PUBLISHED)->where('created_at', '>=', $oneWeekAgo)->latest()->get();
            $recentSimulations = $loaded->simulations()->where('created_at', '>=', $oneWeekAgo)->latest()->get();

            $recentActivity = collect();
            foreach ($recentComments as $c) {
                $recentActivity->push(['type' => 'comment', 'label' => 'Commented: '.\Illuminate\Support\Str::limit($c->body ?? '', 80), 'url' => $c->blog ? route('admin.blogs.show', $c->blog) : '#', 'created_at' => $c->created_at]);
            }
            foreach ($recentLikes as $l) {
                $label = 'Liked';
                $url = '#';
                if ($l->likeable_type === \App\Models\Blog::class) {
                    $blog = \App\Models\Blog::find($l->likeable_id);
                    $label .= ': '.($blog->title ?? 'Blog');
                    $url = route('admin.blogs.show', $blog);
                }
                $recentActivity->push(['type' => 'like', 'label' => $label, 'url' => $url, 'created_at' => $l->created_at]);
            }
            foreach ($recentBookmarks as $b) {
                $recentActivity->push(['type' => 'bookmark', 'label' => 'Bookmarked: '.($b->blog?->title ?? 'Blog'), 'url' => $b->blog ? route('admin.blogs.show', $b->blog) : '#', 'created_at' => $b->created_at]);
            }
            foreach ($recentPublished as $p) {
                $recentActivity->push(['type' => 'published', 'label' => 'Published: '.($p->title ?? 'Blog'), 'url' => route('admin.blogs.show', $p), 'created_at' => $p->created_at]);
            }
            foreach ($recentSimulations as $s) {
                $recentActivity->push(['type' => 'simulation', 'label' => 'Simulation: '.($s->title ?? 'Simulation'), 'url' => '#', 'created_at' => $s->created_at]);
            }

            // sort by created_at desc
            $recentActivity = $recentActivity->sortByDesc('created_at')->values();
        } catch (\Throwable $e) {
            $simulations = collect();
            $recentActivity = collect();
        }

        // Profile completion score: 5 items -> all_info, address, first_simulation, commented, liked
        try {
            $allInfo = !empty($loaded->first_name) && !empty($loaded->last_name) && !empty($loaded->email) && !empty($loaded->phone);
            $hasAddress = (bool) $loaded->address;
            $hasSimulation = $loaded->simulations()->exists();
            $hasCommented = $loaded->blogComments()->exists();
            $hasLiked = \App\Models\BlogLike::where('user_id', $loaded->id)->exists();

            $scoreItems = [$allInfo, $hasAddress, $hasSimulation, $hasCommented, $hasLiked];
            $profileScore = (int) (array_sum($scoreItems) / count($scoreItems) * 100);
        } catch (\Throwable $e) {
            $profileScore = 0;
        }

        return view('admin.users.show', [
            'user' => $loaded,
            'isViewingOwn' => $isViewingOwn,
            'publishedBlogs' => $publishedBlogs,
            'draftBlogs' => $draftBlogs,
            'blogsList' => $blogsList ?? $publishedBlogs,
            'bookmarks' => $bookmarks,
            'simulations' => $simulations ?? collect(),
            'recentActivity' => $recentActivity ?? collect(),
            'profileScore' => $profileScore ?? 0,
            'publishedBlogsCount' => $publishedBlogsCount,
            'totalLikes' => $totalLikes,
            'totalComments' => $totalComments,
            'title' => 'Utilisateur — '.($loaded->full_name ?? $user->full_name),
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('dashboard.index')],
                ['label' => 'Utilisateurs', 'url' => route('admin.users.index')],
                ['label' => $loaded->full_name, 'url' => route('admin.users.show', $loaded)],
            ],
        ]);
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

            return response()->json(['success' => true, 'message' => 'Mot de passe mis à jour']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
