<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManageNewsletterSubscriberRequest;
use App\Http\Requests\VerifyNewsletterSubscriberRequest;
use App\Http\Requests\DeleteNewsletterSubscriberRequest;
use App\Models\Newsletter;
use App\Repositories\NewsletterRepository;
use App\Services\NewsletterSubscriberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsletterSubscriberController extends Controller
{
    public function __construct(
        protected NewsletterSubscriberService $subscriberService,
        protected NewsletterRepository $subscriberRepository
    ) {
    }

    /**
     * Display subscribers list (datatable index).
     */
    public function index(ManageNewsletterSubscriberRequest $request): View
    {
        $totalCount = $this->subscriberRepository->count();

        return view('admin.newsletters.subscribers.subscribers', [
            'totalCount' => $totalCount,
        ]);
    }

    /**
     * Get subscribers list for datatable (API endpoint).
     */
    public function list(ManageNewsletterSubscriberRequest $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 20);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $status = $request->get('status', '');

        $subscribers = $this->subscriberRepository->search($search, $sort, $direction, $status, $perPage);

        return response()->json([
            'data' => $subscribers->items(),
            'total' => $subscribers->total(),
            'per_page' => $perPage,
            'current_page' => $subscribers->currentPage(),
            'last_page' => $subscribers->lastPage(),
        ]);
    }

    /**
     * Verify a subscriber.
     */
    public function verify(VerifyNewsletterSubscriberRequest $request, Newsletter $subscriber): RedirectResponse
    {
        $result = $this->subscriberService->verify($subscriber->id);

        if ($result['success']) {
            return redirect()->route('admin.newsletter-subscribers.index')
                ->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }

    /**
     * Delete a subscriber (unsubscribe).
     */
    public function destroy(DeleteNewsletterSubscriberRequest $request, Newsletter $subscriber): RedirectResponse
    {
        $result = $this->subscriberService->delete($subscriber->id);

        if ($result['success']) {
            return redirect()->route('admin.newsletter-subscribers.index')
                ->with('success', $result['message']);
        }

        return back()->withErrors(['error' => $result['message']]);
    }
}
