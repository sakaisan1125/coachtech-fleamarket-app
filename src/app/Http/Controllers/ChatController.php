<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Rating;
use Illuminate\Http\Request;
use App\Http\Requests\ChatRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

class ChatController extends Controller
{
    public function index(Request $request, $purchaseId)
    {
        $messages = Message::where('purchase_id', $purchaseId)->orderBy('created_at', 'asc')->get();

        Message::where('purchase_id', $purchaseId)
            ->where('sender_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        $otherTransactions = Purchase::where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhere('seller_id', auth()->id());
            })
            ->whereNull('completed_at')
            ->with('item')
            ->get();

        $purchase = Purchase::with('item', 'user', 'seller')->findOrFail($purchaseId);
        $productImageUrl = $purchase->item->image_url ?? 'path/to/default/image.jpg';
        $productName = $purchase->item->name ?? '商品名';
        $productPrice = $purchase->item->price ?? '商品価格';

        $isBuyer = $purchase->user_id === auth()->id();
        $isSeller = $purchase->seller_id === auth()->id();

        $otherUser = $isBuyer ? $purchase->seller : $purchase->user;
        $otherUserIconUrl = $otherUser->profile_image 
            ? \Illuminate\Support\Facades\Storage::url($otherUser->profile_image) 
            : asset('images/default-icon.png');
        $otherUserName = $otherUser->name;

        if ($request->has('edit_message_id')) {
            session(['chat.edit_message_id' => $request->edit_message_id]);
        } else {
            session()->forget('chat.edit_message_id');
        }

        $isCompletedByBuyer = $purchase->is_completed_by_buyer;

        return view('chat.index', compact(
            'messages', 'purchase', 'purchaseId', 'otherTransactions', 
            'productImageUrl', 'productName', 'productPrice', 
            'isBuyer', 'isSeller', 'otherUserIconUrl', 'otherUserName', 'isCompletedByBuyer'
        ));
    }

    public function store(ChatRequest $request)
    {
        $message = new Message();
        $message->purchase_id = $request->purchase_id;
        $message->sender_id = Auth::id();
        $message->content = $request->content;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat-images', 'public');
            $message->image_path = $path;
        }

        $message->save();

        session()->forget('content');

        return redirect()->route('chat.index', ['purchaseId' => $request->purchase_id])
            ->with('success', 'メッセージを送信しました。');
    }

    public function edit($purchaseId, $messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->sender_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('chat.edit', compact('message', 'purchaseId'));
    }

    public function update(Request $request, $purchaseId, $messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->sender_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'content' => 'required|max:400',
        ]);

        $message->content = $request->content;
        $message->save();

        return redirect()->route('chat.index', ['purchaseId' => $purchaseId])
            ->with('success', 'メッセージを更新しました。');
    }

    public function destroy($purchaseId, $messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->sender_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $message->delete();

        return redirect()->route('chat.index', ['purchaseId' => $purchaseId])
            ->with('success', 'メッセージを削除しました。');
    }

    public function completeTransaction(Request $request, $purchaseId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $purchase = Purchase::findOrFail($purchaseId);

        $existingRating = Rating::where('purchase_id', $purchaseId)
            ->where('rater_user_id', Auth::id())
            ->first();

        if ($existingRating) {
            return redirect()->route('chat.index', ['purchaseId' => $purchaseId])
                ->with('error', 'すでに評価を送信しています。');
        }

        $setCompletedByBuyer = false;
        if (Auth::id() === $purchase->user_id) {
            $ratedUserId = $purchase->seller_id;
            $setCompletedByBuyer = true;
        } elseif (Auth::id() === $purchase->seller_id) {
            $ratedUserId = $purchase->user_id;
        } else {
            abort(403, 'Unauthorized action.');
        }

        Rating::create([
            'purchase_id'   => $purchaseId,
            'rated_user_id' => $ratedUserId,
            'rater_user_id' => Auth::id(),
            'rating'        => (int) $request->rating,
        ]);

        if ($setCompletedByBuyer) {
            $purchase->update(['is_completed_by_buyer' => true]);

            $purchase->loadMissing('seller', 'item', 'user');
            if (!empty($purchase->seller?->email)) {
                Mail::to($purchase->seller->email)
                    ->send(new TransactionCompletedMail($purchase));
            }
        }

        return redirect()->route('items.index')
            ->with('success', '評価を送信しました。');
    }
}
