<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Message;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\DB;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 'sell');
        $user = Auth::user();

        $ratingAgg = $user->ratingsReceived()
            ->selectRaw('COUNT(*) as cnt, AVG(rating) as avg_rating')
            ->first();
        $ratingCount = (int) ($ratingAgg->cnt ?? 0);
        $ratingRounded = $ratingCount > 0 ? (int) round($ratingAgg->avg_rating) : null;

        if ($page === 'buy') {
            $items = Item::whereHas('purchase', function ($purchaseQuery) use ($user) {
                    $purchaseQuery->where('user_id', $user->id);
                })
                ->latest()
                ->get();
        } elseif ($page === 'transactions') {
            $purchasedItems = Purchase::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->with('item')
                ->latest()
                ->get();

            $soldItems = Purchase::where('seller_id', $user->id)
                ->whereNull('completed_at')
                ->with('item')
                ->latest()
                ->get();

            $items = $purchasedItems->merge($soldItems);
            $items = $items->sortByDesc(function ($purchase) use ($user) {
                return Message::where('purchase_id', $purchase->id)
                    ->where('sender_id', '!=', $user->id)
                    ->latest()
                    ->value('created_at');
            });
        } else {
            $items = $user->items()->latest()->get();
        }

        $ongoingPurchaseIds = Purchase::where(function ($involvedPurchaseFilter) use ($user) {
                $involvedPurchaseFilter
                    ->where('user_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            })
            ->whereNull('completed_at')
            ->pluck('id')
            ->all();

        $messageCounts = Message::select('purchase_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('purchase_id', $ongoingPurchaseIds)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->groupBy('purchase_id')
            ->pluck('cnt', 'purchase_id')
            ->toArray();

        $totalMessageCount = array_sum($messageCounts);

        return view('mypage.index', compact(
            'items', 'page', 'messageCounts', 'totalMessageCount', 'ratingCount', 'ratingRounded'
        ));
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

        $transactionItems = Purchase::where('seller_id', $user->id)
            ->whereNull('completed_at')
            ->with('item')
            ->latest()
            ->get();

        return view('mypage.transactions', compact('transactionItems'));
    }
}