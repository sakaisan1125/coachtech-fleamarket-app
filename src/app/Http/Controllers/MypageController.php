<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
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
        } else {
            $items = $user->items()->latest()->get();
        }

        return view('mypage.index', compact('items', 'page'));
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
}