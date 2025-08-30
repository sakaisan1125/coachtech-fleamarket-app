<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check() && !auth()->user()->hasVerifiedEmail()) {
            return redirect('/email/verify');
        }

        $tab = $request->query('tab');
        $keyword = $request->query('keyword'); 

        if ($tab === 'mylist') {
            if (auth()->check()) {
                // ✅ 修正：いいねした商品IDを先に取得
                $likedItemIds = Like::where('user_id', auth()->id())->pluck('item_id');
                
                // ✅ 修正：Itemクエリビルダーを作成
                $items = Item::whereIn('id', $likedItemIds);

                // ✅ 追加：自分の商品を除外
                $items = $items->where('user_id', '!=', auth()->id());

                // ✅ 検索条件追加（マイリストでも検索可能）
                if ($keyword) {
                    $items = $items->where('name', 'like', "%{$keyword}%");
                }
                
                $items = $items->get();
            } else {
                $items = collect();
            }
        } else {
            // ✅ 修正：全商品（自分の商品除外）
            $items = Item::query();
            
            // ✅ 追加：ログイン時は自分の商品を除外
            if (auth()->check()) {
                $items = $items->where('user_id', '!=', auth()->id());
            }
            
            // ✅ 検索条件追加
            if ($keyword) {
                $items = $items->where('name', 'like', "%{$keyword}%");
            }
            
            $items = $items->get();
        }

        // ✅ 検索キーワードもビューに渡す
        return view('items.index', compact('items', 'tab', 'keyword'));
    }

    public function show($id)
    {
        $item = Item::with(['categories', 'likes', 'comments.user'])->findOrFail($id);
        return view('items.show', compact('item'));
    }

    // 商品出品画面表示
    public function create()
    {
        // カテゴリー一覧も取得して渡す（多対多用）
        $categories = \App\Models\Category::all();
        return view('items.create', compact('categories'));
    }

    // 商品登録
    public function store(ExhibitionRequest $request)
    {
        // バリデーション済みデータ取得
        $validated = $request->validated();

        // 画像がアップロードされていれば保存
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $validated['image_path'] = $path;
        }

        // ログインユーザーを出品者として紐づけ
        $validated['user_id'] = Auth::id();

        // Item作成
        $item = Item::create($validated);

        // カテゴリー（多対多）
        if ($request->has('category_id')) {
            $item->categories()->sync($request->input('category_id')); // 配列で送る
        }

        return redirect()->route('items.index')->with('success', '商品を出品しました！');
    }

    public function like(Item $item)
    {
        $user = auth()->user();
        if (!$user->likes()->where('item_id', $item->id)->exists()) {
            $user->likes()->create(['item_id' => $item->id]);
        }
        return back();
    }

    public function unlike(Item $item)
    {
        $user = auth()->user();
        $user->likes()->where('item_id', $item->id)->delete();
        return back();
    }
}