<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemCategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 1. 腕時計 → ファッション, メンズ, アクセサリー
            ['item_id' => 1, 'category_id' => 1],  // ファッション
            ['item_id' => 1, 'category_id' => 5],  // メンズ
            ['item_id' => 1, 'category_id' => 12], // アクセサリー

            // 2. HDD → 家電, インテリア
            ['item_id' => 2, 'category_id' => 2],  // 家電
            ['item_id' => 2, 'category_id' => 3],  // インテリア

            // 3. 玉ねぎ3束 → キッチン
            ['item_id' => 3, 'category_id' => 10], // キッチン

            // 4. 革靴 → ファッション, メンズ
            ['item_id' => 4, 'category_id' => 1],  // ファッション
            ['item_id' => 4, 'category_id' => 5],  // メンズ

            // 5. ノートPC → 家電, ゲーム
            ['item_id' => 5, 'category_id' => 2],  // 家電
            ['item_id' => 5, 'category_id' => 8],  // ゲーム

            // 6. マイク → 家電, インテリア
            ['item_id' => 6, 'category_id' => 2],  // 家電
            ['item_id' => 6, 'category_id' => 3],  // インテリア

            // 7. ショルダーバッグ → ファッション, レディース
            ['item_id' => 7, 'category_id' => 1],  // ファッション
            ['item_id' => 7, 'category_id' => 4],  // レディース

            // 8. タンブラー → キッチン, インテリア
            ['item_id' => 8, 'category_id' => 10], // キッチン
            ['item_id' => 8, 'category_id' => 3],  // インテリア

            // 9. コーヒーミル → キッチン, ハンドメイド
            ['item_id' => 9, 'category_id' => 10], // キッチン
            ['item_id' => 9, 'category_id' => 11], // ハンドメイド

            // 10. メイクセット → コスメ
            ['item_id' => 10, 'category_id' => 6], // コスメ
        ];

        DB::table('item_category')->insert($data);
    }
}