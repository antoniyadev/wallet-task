<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class TransactionController
 *
 * Provides endpoints for merchants to view their own wallet transactions.
 * Transactions are returned with the related creator information.
 *
 * @package App\Http\Controllers\Api
 */
class TransactionController extends Controller
{
    /**
     * List transactions for the authenticated user.
     *
     * Transactions are returned in descending order of creation date,
     * including the creator relationship (e.g., admin or system).
     *
     * @param  Request $request The current HTTP request, used to get the authenticated user.
     * @return AnonymousResourceCollection A collection of transactions formatted via TransactionResource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $transactions = $request->user()
            ->transactions()
            ->with('creator')
            ->latest()
            ->get();

        return TransactionResource::collection($transactions);
    }
}
