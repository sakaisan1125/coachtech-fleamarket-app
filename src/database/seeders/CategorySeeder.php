<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'ファッション'],     // id:1
            ['name' => '家電'],   // id:2
            ['name' => 'インテリア'],     // id:3
            ['name' => 'レディース'],     // id:4
            ['name' => 'メンズ'],     // id:5
            ['name' => 'コスメ'], // id:6
            ['name' => '本'], // id:7
            ['name' => 'ゲーム'],   // id:8
            ['name' => 'スポーツ'],   // id:9
            ['name' => 'キッチン'],   // id:10
            ['name' => 'ハンドメイド'], // id:11
            ['name' => 'アクセサリー'], // id:12
            ['name' => 'おもちゃ'], // id:13
            ['name' => 'ベビー・キッズ'], // id:14
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
