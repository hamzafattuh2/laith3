<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    // إيداع أموال لمستخدم معين
    public function deposit(Request $request, $userId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'reference' => 'nullable|string'
        ]);

        $user = User::findOrFail($userId);

        // إنشاء محفظة إذا لم تكن موجودة
        $wallet = $user->wallet ?? $user->wallet()->create(['balance' => 0]);

        // تنفيذ الإيداع
        $wallet->deposit(
            $request->amount,
            $request->description,
            $request->reference
        );

        return response()->json([
            'message' => 'تم الإيداع بنجاح',
            'new_balance' => $wallet->balance,
            'transaction' => $wallet->transactions()->latest()->first()
        ]);
    }

    // عرض رصيد المستخدم
    public function show($userId)
    {
        $user = User::findOrFail($userId);
        $wallet = $user->wallet ?? $user->wallet()->create(['balance' => 0]);

        return response()->json([
            'balance' => $wallet->balance,
            'transactions' => $wallet->transactions()->latest()->take(10)->get()
        ]);
    }

        // سحب أموال
    public function withdraw(Request $request, $userId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'reference' => 'nullable|string'
        ]);

        $user = User::findOrFail($userId);
        $wallet = $user->wallet ?? $user->wallet()->create(['balance' => 0]);

        $transaction = $wallet->withdraw(
            $request->amount,
            $request->description,
            $request->reference
        );

        return response()->json([
            'message' => 'تم السحب بنجاح',
            'new_balance' => $wallet->refresh()->balance,
            'transaction' => $transaction
        ]);
    }

    // تحويل أموال
    public function transfer(Request $request, $senderId)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string'
        ]);

        $sender = User::findOrFail($senderId);
        $recipient = User::findOrFail($request->recipient_id);

        $senderWallet = $sender->wallet ?? $sender->wallet()->create(['balance' => 0]);
        $recipientWallet = $recipient->wallet ?? $recipient->wallet()->create(['balance' => 0]);

        $senderWallet->transfer(
            $recipientWallet,
            $request->amount,
            $request->description
        );

        return response()->json([
            'message' => 'تم التحويل بنجاح',
            'sender_balance' => $senderWallet->refresh()->balance,
            'recipient_balance' => $recipientWallet->refresh()->balance
        ]);
    }
}
