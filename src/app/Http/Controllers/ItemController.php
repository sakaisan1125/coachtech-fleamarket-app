<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;

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
                $likedItemIds = Like::where('user_id', auth()->id())->pluck('item_id');
                $items = Item::whereIn('id', $likedItemIds)
                    ->where('user_id', '!=', auth()->id());

                if ($keyword) {
                    $items = $items->where('name', 'like', "%{$keyword}%");
                }
                $items = $items->get();
            } else {
                $items = collect();
            }
        } else {
            $items = Item::query();
            if (auth()->check()) {
                $items = $items->where('user_id', '!=', auth()->id());
            }
            if ($keyword) {
                $items = $items->where('name', 'like', "%{$keyword}%");
            }
            $items = $items->get();
        }

        return view('items.index', compact('items', 'tab', 'keyword'));
    }

    public function show($id)
    {
        $item = Item::with(['categories', 'likes', 'comments.user'])->findOrFail($id);
        return view('items.show', compact('item'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $validated['image_path'] = $path;
        }

        $validated['user_id'] = Auth::id();
        $item = Item::create($validated);

        if ($request->has('category_id')) {
            $item->categories()->sync($request->input('category_id'));
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