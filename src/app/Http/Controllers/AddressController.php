<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    /**
     * 住所編集画面の表示
     */
    public function edit(Request $request)
    {
        $user = Auth::user();

        if ($request->has('item_id')) {
            session(['last_item_id' => $request->input('item_id')]);
        }

        return view('address.edit', compact('user'));
    }

    /**
     * 住所更新処理
     */
    public function update(AddressRequest $request)
    {
        $user = Auth::user();
        $user->zipcode = $request->input('zipcode');
        $user->address = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        $itemId = session('last_item_id');
        if ($itemId) {
            return redirect()->route('purchase.show', ['item' => $itemId])
                             ->with('success', '住所を更新しました');
        } else {
            return redirect()->route('mypage.index')
                             ->with('success', '住所を更新しました');
        }
    }
}