<?php

namespace App\Http\Controllers;

use App\Models\Addressable;
use App\Models\Blog;
use App\Models\BlogBookmark;
use App\Models\BlogComment;
use App\Models\Notification;
use App\Models\Simulation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->loadMissing('role');
        $isAdmin = $user->isAdmin();

        $months = $this->buildLastMonths(6);
        $latestBlogsToRead = $this->latestBlogsToRead($user->id);

        if ($isAdmin) {
            $dashboardData = $this->adminDashboardData($months, $latestBlogsToRead);
        } else {
            $dashboardData = $this->userDashboardData($user->id, $months, $latestBlogsToRead);
        }

        return view('dashboard', [
            'title' => 'Tableau de bord',
            'header' => 'Bienvenue '.$user->first_name.' !',
            'breadcrumbs' => [
                [
                    'label' => 'Accueil',
                ],
            ],
            'isAdmin' => $isAdmin,
            'dashboardData' => $dashboardData,
        ]);
    }

    private function adminDashboardData(Collection $months, Collection $latestBlogsToRead): array
    {
        $usersTotal = User::count();
        $verifiedUsersTotal = User::whereNotNull('email_verified_at')->count();
        $simulationsTotal = Simulation::count();
        $simulationsThisMonth = Simulation::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $simulationsLast30Days = Simulation::whereBetween('created_at', [now()->subDays(30)->startOfDay(), now()->endOfDay()])->count();
        $aidAmountTotal = (float) Simulation::sum('montant_total_aides');
        $avgAidPerSimulation = $simulationsTotal > 0 ? $aidAmountTotal / $simulationsTotal : 0;
        $avgEnergyGain = (float) (Simulation::avg('gain_energetique') ?? 0);
        $publishedBlogsTotal = Blog::where('status', Blog::PUBLISHED)->count();
        $pendingBlogsTotal = Blog::where('status', Blog::PENDING)->count();
        $commentsTotal = BlogComment::count();
        $unreadNotificationsTotal = Notification::where('read', false)->count();

        $usersPerMonth = $this->countByMonth(
            User::query()->whereBetween('created_at', [$months->first()['start'], $months->last()['end']])->get(),
            $months
        );

        $simulationsPerMonth = $this->countByMonth(
            Simulation::query()->whereBetween('created_at', [$months->first()['start'], $months->last()['end']])->get(),
            $months
        );

        $blogStatus = [
            'draft' => Blog::where('status', Blog::DRAFT)->count(),
            'pending' => $pendingBlogsTotal,
            'published' => $publishedBlogsTotal,
            'rejected' => Blog::where('status', Blog::REJECTED)->count(),
        ];

        $adminRanges = $this->buildAdminRangeDatasets();

        [
            'aid_per_month' => $simulationAidPerMonth,
            'avg_gain_per_month' => $simulationAvgGainPerMonth,
        ] = $this->simulationMonthlySeries($months);

        $citiesWithCoordinates = Addressable::query()
            ->join('addresses', 'addressables.address_id', '=', 'addresses.id')
            ->where('addressables.addressable_type', User::class)
            ->whereNotNull('addresses.lat')
            ->whereNotNull('addresses.lng')
            ->select(
                'addresses.city',
                'addresses.lat',
                'addresses.lng',
                DB::raw('COUNT(*) as users_count')
            )
            ->groupBy('addresses.city', 'addresses.lat', 'addresses.lng')
            ->orderByDesc('users_count')
            ->limit(40)
            ->get();

        $pendingBlogs = Blog::query()
            ->where('status', Blog::PENDING)
            ->with('user:id,first_name,last_name')
            ->latest('created_at')
            ->take(6)
            ->get();

        $recentSimulations = Simulation::query()
            ->with('user:id,first_name,last_name')
            ->latest('created_at')
            ->take(8)
            ->get();

        return [
            'isAdmin' => true,
            'cards' => [
                'users_total' => $usersTotal,
                'users_verified_total' => $verifiedUsersTotal,
                'simulations_total' => $simulationsTotal,
                'simulations_this_month' => $simulationsThisMonth,
                'simulations_last_30_days' => $simulationsLast30Days,
                'aid_amount_total' => $aidAmountTotal,
                'avg_aid_per_simulation' => $avgAidPerSimulation,
                'avg_energy_gain' => $avgEnergyGain,
                'blogs_published_total' => $publishedBlogsTotal,
                'blogs_pending_total' => $pendingBlogsTotal,
                'comments_total' => $commentsTotal,
                'unread_notifications_total' => $unreadNotificationsTotal,
            ],
            'charts' => [
                'labels' => $months->pluck('label')->values(),
                'users_per_month' => $usersPerMonth,
                'simulations_per_month' => $simulationsPerMonth,
                'blog_status' => $blogStatus,
                'admin_ranges' => $adminRanges,
                'simulation_aid_per_month' => $simulationAidPerMonth,
                'simulation_avg_gain_per_month' => $simulationAvgGainPerMonth,
            ],
            'map' => [
                'cities' => $citiesWithCoordinates,
            ],
            'pending_blogs' => $pendingBlogs,
            'recent_simulations' => $recentSimulations,
            'latest_blogs_to_read' => $latestBlogsToRead,
        ];
    }

    private function userDashboardData(int $userId, Collection $months, Collection $latestBlogsToRead): array
    {
        $mySimulationsTotal = Simulation::where('user_id', $userId)->count();
        $myAidAmountTotal = (float) Simulation::where('user_id', $userId)->sum('montant_total_aides');
        $myAvgAidPerSimulation = $mySimulationsTotal > 0 ? $myAidAmountTotal / $mySimulationsTotal : 0;
        $myBestAidAmount = (float) (Simulation::where('user_id', $userId)->max('montant_total_aides') ?? 0);
        $myAvgEnergyGain = (float) (Simulation::where('user_id', $userId)->avg('gain_energetique') ?? 0);
        $mySimulationsThisMonth = Simulation::where('user_id', $userId)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $myBookmarksTotal = BlogBookmark::where('user_id', $userId)->count();
        $myUnreadNotificationsTotal = Notification::where('user_id', $userId)->where('read', false)->count();
        $myPublishedBlogs = Blog::where('user_id', $userId)->where('status', Blog::PUBLISHED)->count();

        [
            'aid_per_month' => $mySimulationAidPerMonth,
            'avg_gain_per_month' => $mySimulationAvgGainPerMonth,
        ] = $this->simulationMonthlySeries($months, $userId);

        $myRecentSimulations = Simulation::query()
            ->where('user_id', $userId)
            ->latest('created_at')
            ->take(6)
            ->get();

        $mySimulationsPerMonth = $this->countByMonth(
            Simulation::query()
                ->where('user_id', $userId)
                ->whereBetween('created_at', [$months->first()['start'], $months->last()['end']])
                ->get(),
            $months
        );

        $myBlogStatus = [
            'draft' => Blog::where('user_id', $userId)->where('status', Blog::DRAFT)->count(),
            'pending' => Blog::where('user_id', $userId)->where('status', Blog::PENDING)->count(),
            'published' => $myPublishedBlogs,
            'rejected' => Blog::where('user_id', $userId)->where('status', Blog::REJECTED)->count(),
        ];

        return [
            'isAdmin' => false,
            'cards' => [
                'my_simulations_total' => $mySimulationsTotal,
                'my_simulations_this_month' => $mySimulationsThisMonth,
                'my_aid_amount_total' => $myAidAmountTotal,
                'my_avg_aid_per_simulation' => $myAvgAidPerSimulation,
                'my_best_aid_amount' => $myBestAidAmount,
                'my_avg_energy_gain' => $myAvgEnergyGain,
                'my_bookmarks_total' => $myBookmarksTotal,
                'my_unread_notifications_total' => $myUnreadNotificationsTotal,
                'my_published_blogs_total' => $myPublishedBlogs,
            ],
            'charts' => [
                'labels' => $months->pluck('label')->values(),
                'my_simulations_per_month' => $mySimulationsPerMonth,
                'my_blog_status' => $myBlogStatus,
                'my_simulation_aid_per_month' => $mySimulationAidPerMonth,
                'my_simulation_avg_gain_per_month' => $mySimulationAvgGainPerMonth,
            ],
            'my_recent_simulations' => $myRecentSimulations,
            'latest_blogs_to_read' => $latestBlogsToRead,
        ];
    }

    private function buildLastMonths(int $numberOfMonths): Collection
    {
        $months = collect();

        for ($index = $numberOfMonths - 1; $index >= 0; $index--) {
            $month = Carbon::now()->subMonths($index);
            $months->push([
                'key' => $month->format('Y-m'),
                'label' => ucfirst($month->translatedFormat('M Y')),
                'start' => $month->copy()->startOfMonth(),
                'end' => $month->copy()->endOfMonth(),
            ]);
        }

        return $months;
    }

    private function buildLastDays(int $numberOfDays): Collection
    {
        $days = collect();

        for ($index = $numberOfDays - 1; $index >= 0; $index--) {
            $day = Carbon::now()->subDays($index);
            $days->push([
                'key' => $day->format('Y-m-d'),
                'label' => $day->format('d/m'),
                'start' => $day->copy()->startOfDay(),
                'end' => $day->copy()->endOfDay(),
            ]);
        }

        return $days;
    }

    private function countByMonth(Collection $items, Collection $months): array
    {
        $countByMonth = $items
            ->groupBy(fn ($item) => Carbon::parse($item->created_at)->format('Y-m'))
            ->map(fn (Collection $collection) => $collection->count());

        return $months
            ->map(fn (array $month) => $countByMonth[$month['key']] ?? 0)
            ->values()
            ->toArray();
    }

    private function countByDay(Collection $items, Collection $days): array
    {
        $countByDay = $items
            ->groupBy(fn ($item) => Carbon::parse($item->created_at)->format('Y-m-d'))
            ->map(fn (Collection $collection) => $collection->count());

        return $days
            ->map(fn (array $day) => $countByDay[$day['key']] ?? 0)
            ->values()
            ->toArray();
    }

    private function blogStatusWithinRange(Carbon $start, Carbon $end): array
    {
        return [
            'draft' => Blog::where('status', Blog::DRAFT)->whereBetween('created_at', [$start, $end])->count(),
            'pending' => Blog::where('status', Blog::PENDING)->whereBetween('created_at', [$start, $end])->count(),
            'published' => Blog::where('status', Blog::PUBLISHED)->whereBetween('created_at', [$start, $end])->count(),
            'rejected' => Blog::where('status', Blog::REJECTED)->whereBetween('created_at', [$start, $end])->count(),
        ];
    }

    private function buildAdminRangeDatasets(): array
    {
        $ranges = [7, 30, 90];
        $datasets = [];

        foreach ($ranges as $range) {
            $days = $this->buildLastDays($range);
            $start = $days->first()['start'];
            $end = $days->last()['end'];

            $users = User::query()->whereBetween('created_at', [$start, $end])->get();
            $simulations = Simulation::query()->whereBetween('created_at', [$start, $end])->get();

            $datasets[(string) $range] = [
                'labels' => $days->pluck('label')->values()->toArray(),
                'users_per_day' => $this->countByDay($users, $days),
                'simulations_per_day' => $this->countByDay($simulations, $days),
                'blog_status' => $this->blogStatusWithinRange($start, $end),
            ];
        }

        return $datasets;
    }

    private function simulationMonthlySeries(Collection $months, ?int $userId = null): array
    {
        $baseQuery = Simulation::query();

        if ($userId !== null) {
            $baseQuery->where('user_id', $userId);
        }

        $aidPerMonth = [];
        $avgGainPerMonth = [];

        foreach ($months as $month) {
            $start = $month['start'];
            $end = $month['end'];

            $sumAid = (float) (clone $baseQuery)->whereBetween('created_at', [$start, $end])->sum('montant_total_aides');
            $avgGain = (float) ((clone $baseQuery)->whereBetween('created_at', [$start, $end])->avg('gain_energetique') ?? 0);

            $aidPerMonth[] = round($sumAid, 2);
            $avgGainPerMonth[] = round($avgGain, 2);
        }

        return [
            'aid_per_month' => $aidPerMonth,
            'avg_gain_per_month' => $avgGainPerMonth,
        ];
    }

    private function latestBlogsToRead(int $currentUserId): Collection
    {
        return Blog::query()
            ->where('status', Blog::PUBLISHED)
            ->where('user_id', '!=', $currentUserId)
            ->with('user:id,first_name,last_name')
            ->withCount([
                'allComments as comments_count',
                'likes as likes_count',
                'bookmarks as bookmarks_count',
            ])
            ->latest('published_at')
            ->take(6)
            ->get();
    }
}
