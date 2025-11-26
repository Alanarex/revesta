<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profile
    ) {
    }

    /**
     * Show a public user profile (list published blogs for the user).
     */
    public function show(int $userId)
    {
        $user = \App\Models\User::findOrFail($userId);

        $isViewingOwnProfile = auth()->check() && auth()->id() === $user->id;

        if ($isViewingOwnProfile) {
            return redirect('/profile');
        }

        // Published blogs for display to any viewer
        $publishedBlogs = \App\Models\Blog::where('user_id', $user->id)
            ->published()
            ->orderByDesc('published_at')
            ->get();

        // Count published blogs
        $publishedBlogsCount = $publishedBlogs->count();

        // Calculate total likes received on all published blogs for this user
        $totalLikes = \App\Models\BlogLike::where('likeable_type', \App\Models\Blog::class)
            ->whereIn('likeable_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('blogs')
                    ->where('user_id', $user->id)
                    ->where('status', \App\Models\Blog::PUBLISHED);
            })->count();

        // Calculate total comments received on all published blogs for this user
        $totalComments = \App\Models\BlogComment::whereIn('blog_id', function ($query) use ($user) {
            $query->select('id')
                ->from('blogs')
                ->where('user_id', $user->id)
                ->where('status', \App\Models\Blog::PUBLISHED);
        })->count();

        // If viewing own profile, also load drafts/pending/rejected and bookmarks
        $draftBlogs = collect();
        $bookmarks = collect();
        if ($isViewingOwnProfile) {
            $draftBlogs = \App\Models\Blog::where('user_id', $user->id)
                ->whereIn('status', ['draft', 'pending', 'rejected'])
                ->orderByDesc('created_at')
                ->get();

            $bookmarks = $user->blogBookmarks()->with('blog')->latest()->get();
        }

        return view('profile.show', [
            'user' => $user,
            'publishedBlogs' => $publishedBlogs,
            'publishedBlogsCount' => $publishedBlogsCount,
            'totalLikes' => $totalLikes,
            'totalComments' => $totalComments,
            'draftBlogs' => $draftBlogs,
            'bookmarks' => $bookmarks,
            'isAuthenticated' => auth()->check(),
            'isViewingOwnProfile' => $isViewingOwnProfile,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Published blogs with bookmark status
        $publishedBlogs = \App\Models\Blog::where('user_id', $user->id)
            ->published()
            ->withCount([
                'bookmarks as bookmarked_by_auth' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }
            ])
            ->orderByDesc('published_at')
            ->get();

        $publishedBlogsCount = $publishedBlogs->count();

        // Draft/Pending/Rejected blogs with bookmark status
        $draftBlogs = \App\Models\Blog::where('user_id', $user->id)
            ->whereIn('status', ['draft', 'pending', 'rejected'])
            ->withCount([
                'bookmarks as bookmarked_by_auth' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }
            ])
            ->orderByDesc('created_at')
            ->get();

        // Calculate total likes received on all published blogs
        $totalLikes = \App\Models\BlogLike::where('likeable_type', \App\Models\Blog::class)
            ->whereIn('likeable_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('blogs')
                    ->where('user_id', $user->id)
                    ->where('status', \App\Models\Blog::PUBLISHED);
            })->count();

        // Calculate total comments received on all published blogs
        $totalComments = \App\Models\BlogComment::whereIn('blog_id', function ($query) use ($user) {
            $query->select('id')
                ->from('blogs')
                ->where('user_id', $user->id)
                ->where('status', \App\Models\Blog::PUBLISHED);
        })->count();

        // Bookmarks with bookmark status for each blog
        $bookmarks = $user->blogBookmarks()
            ->with([
                'blog' => function ($query) use ($user) {
                    $query->withCount([
                        'bookmarks as bookmarked_by_auth' => function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        }
                    ]);
                }
            ])
            ->latest()
            ->get();

        return view('profile.index', [
            'user' => $user,
            'publishedBlogs' => $publishedBlogs,
            'publishedBlogsCount' => $publishedBlogsCount,
            'totalLikes' => $totalLikes,
            'totalComments' => $totalComments,
            'draftBlogs' => $draftBlogs,
            'bookmarks' => $bookmarks,
            'isViewingOwnProfile' => true,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Profile', 'url' => route('profile.edit')],
            ],
            'title' => 'My Profile',
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        Gate::authorize('update', $request->user());

        $this->profile->updateProfile($request->user(), $request->validated());

        return back()->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        Gate::authorize('updatePassword', $request->user());

        $this->profile->changePassword($request->user(), $request->validated('new_password'));

        return back()->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Gate::authorize('delete', $request->user());

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $this->profile->deleteAccount($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'account-deleted');
    }
}
