<?php

namespace App\Http\Controllers\Api;

use App\Transaction;
use Illuminate\Http\Request;
use App\Models\DetailTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'total' => 'required|integer',
                'payment_method' => 'required|string',
                'status' => 'required|string',
                'details' => 'required|array',
                'details.*.product_id' => 'required|exists:products,id',
            ]);

            $transaction = DB::transaction(function () use ($request) {
                $transactionCode = Transaction::generateTransactionCode();

                $transaction = Transaction::create([
                    'transaction_code' => $transactionCode,
                    'user_id' => $request->user_id,
                    'total' => $request->total,
                    'payment_method' => $request->payment_method,
                    'status' => $request->status,
                ]);

                foreach ($request->details as $detail) {
                    DetailTransaction::create([
                        'transaction_id' => $transaction->transaction_code,
                        'product_id' => $detail['product_id'],
                    ]);
                }

                return $transaction->load('detailTransactions');
            });

            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function index(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $userId = $request->input('user_id');

            // Retrieve transactions for the specified user
            $transactions = Transaction::where('user_id', $userId)->get();

            // Return the transactions data
            return response()->json($transactions, 200);
        } catch (\Exception $e) {
            // Return an error response in case of an exception
            return response()->json(['error' => 'An error occurred while retrieving transactions.', 'message' => $e->getMessage()], 500);
        }
    }
}
