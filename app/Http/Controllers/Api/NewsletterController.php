<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeNewsletterRequest;
use App\Services\NewsletterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Newsletter endpoints
     *
     * @group Newsletter
     */
    public function __construct(
        protected NewsletterService $newsletterService
    ) {}

    /**
     * Subscribe email to newsletter
     *
     * Subscribe a new email address to the newsletter. The endpoint will validate the email format
     * and ensure it hasn't been previously subscribed. Rate limited to prevent abuse.
     *
     * @group Newsletter
     *
     * @response status=200 {
     *   "success": true,
     *   "message": "Successfully subscribed to newsletter.",
     *   "data": {
     *     "id": 1,
     *     "email": "test@example.com",
     *     "ip_address": "192.168.1.1",
     *     "subscribed_at": "2026-02-03T14:30:00.000000Z",
     *     "verified_at": null,
     *     "created_at": "2026-02-03T14:30:00.000000Z",
     *     "updated_at": "2026-02-03T14:30:00.000000Z"
     *   }
     * }
     * @response status=422 {
     *   "message": "The email field must be a valid email.",
     *   "errors": {
     *     "email": [
     *       "The email field must be a valid email."
     *     ]
     *   }
     * }
     */
    public function subscribe(SubscribeNewsletterRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        $ipAddress = $request->ip();

        $result = $this->newsletterService->subscribe($email, $ipAddress);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 409);
    }

    /**
     * Verify newsletter subscription
     *
     * Verify a newsletter subscription using the email address.
     *
     * @group Newsletter
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email:rfc,dns|max:255',
        ]);

        $result = $this->newsletterService->verify($request->input('email'));

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 404);
    }

    /**
     * Unsubscribe from newsletter
     *
     * Remove an email address from the newsletter subscription list.
     *
     * @group Newsletter
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email:rfc,dns|max:255',
        ]);

        $result = $this->newsletterService->unsubscribe($request->input('email'));

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 404);
    }
}
