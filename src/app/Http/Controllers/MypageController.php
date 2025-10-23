<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Message;
use App\Http\Requests\ProfileRequest;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 'sell');
        $user = Auth::user();

        if ($page === 'buy') {
            $items = Item::whereHas('purchase', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->latest()->get();
        } elseif ($page === 'transactions') {
            // 購入した商品
            $purchasedItems = Purchase::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->with('item')
                ->latest()
                ->get();

            // 出品して売れた商品
            $soldItems = Purchase::where('seller_id', $user->id)
                ->whereNull('completed_at')
                ->with('item')
                ->latest()
                ->get();

            // 購入した商品と売れた商品を統合
            $items = $purchasedItems->merge($soldItems);
            $items = $items->sortByDesc(function ($item) use ($user) {
                return Message::where('purchase_id', $item->id)
                    ->where('sender_id', '!=', $user->id) // 相手のメッセージのみ
                    ->latest()
                    ->value('created_at');
            });
        } else {
            $items = $user->items()->latest()->get();
        }

        // 取引中の商品ごとのメッセージ件数を取得
        $messageCounts = [];
        foreach ($items as $item) {
            $purchaseId = $item->id;
            $messageCounts[$purchaseId] = Message::where('purchase_id', $purchaseId)
                ->where('sender_id', '!=', $user->id) // 相手のメッセージのみ
                ->where('is_read', false) // 未読のみ
                ->count();
        }
        $totalMessageCount = array_sum($messageCounts);

        return view('mypage.index', compact('items', 'page', 'messageCounts', 'totalMessageCount'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('mypage.profile', compact('user'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && \Storage::disk('public')->exists($user->profile_image)) {
                \Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profiles', 'public');
            $data['profile_image'] = $path;
        } else {
            unset($data['profile_image']);
        }

        $user->update($data);

        return redirect('/')->with('success', 'プロフィールを更新しました');
    }

    public function transactionItems()
    {
        $user = Auth::user();

        // 取引中の商品を取得
        $transactionItems = Purchase::where('seller_id', $user->id)
            ->whereNull('completed_at')
            ->with('item')
            ->latest()
            ->get();

        return view('mypage.transactions', compact('transactionItems'));
    }
}