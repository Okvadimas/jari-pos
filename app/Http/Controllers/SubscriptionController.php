<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\SubscriptionRequest;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Check applied vouchers (Regular & Affiliate) and return calculated prices
     */
    public function checkVouchers(SubscriptionRequest $request)
    {
        $result = $this->subscriptionService->calculateVouchers($request->validated());
        return response()->json($result);
    }

    /**
     * Checkout subscription and insert to AppSale
     */
    public function checkout(SubscriptionRequest $request)
    {
        $result = $this->subscriptionService->checkout($request->validated(), Auth::user());
        
        $status = isset($result['status']) && $result['status'] ? 200 : 500;
        if (isset($result['status']) && !$result['status'] && strpos($result['message'], 'harus') !== false) {
            $status = 400; // Client error like missing company
        }

        return response()->json($result, $status == 500 && isset($result['status']) && !$result['status'] ? 200 : $status); // Returning 200 with status=false is fine for AJAX as requested by frontend
    }
}
