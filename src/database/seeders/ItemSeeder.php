<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'user_id'     => 1,
                'name'        => '腕時計',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'brand'       => 'Rolax',
                'price'       => 15000,
                'condition'   => '良好',
                'image_path'  => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'is_sold'     => false,
            ],
            [
                'user_id'     => 1,
                'name'        => 'HDD',
                'description' => '高速で信頼性の高いハードディスク',
                'brand'       => '西芝',
                'price'       => 5000,
                'condition'   => '目立った傷や汚れなし',
                'image_path'  => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'is_sold'     => false,
            ],
            [
                'user_id'     => 1,
                'name'        => '玉ねぎ3束',
                'description' => '新鮮な玉ねぎの3束のセット',
                'brand'       => 'なし',
                'price'       => 300,
                'condition'   => 'やや傷や汚れあり',
                'image_path'  => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'is_sold'     => false,
            ],
            [
                'user_id'     => 1,
                'name'        => '革靴',
                'description' => 'クラシックなデザインの革靴',
                'brand'       => null,
                'price'       => 4000,
                'condition'   => '状態が悪い',
                'image_path'  => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'is_sold'     => false,
            ],
            [
                'user_id'     => 1,
                'name'        => 'ノートPC',
                'description' => '高性能ノートパソコン',
                'brand'       => null,
                'price'       => 45000,
                'condition'   => '良好',
                'image_path'  => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'is_sold'     => false,
            ],
            [
                'user_id'     => 1,
                'name'        => 'マイク',
                'description' => '高音質のレコーディング用マイク',
                'brand'       => 'なし',
                'price'       => 8000,
                'condition'   => '目立った傷や汚れなし',
                'image_path'  => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'is_sold'     => false,
            ],
            [
                'user_id'     => 1,
                'name'        => 'ショルダーバッグ',
                'description' => 'おしゃれなショルダーバッグ',
                'brand'       => null,
                'price'       => 3500,
                'condition'   => 'やや傷や汚れあり',
                'image_path'  => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'is_sold'     => false,
            ],
                        [
                'user_id'     => 1,
                'name'        => 'タンブラー',
                'description' => '使いやすいタンブラー',
                'brand'       => 'なし',
                'price'       => 500,
                'condition'   => '状態が悪い',
                'image_path'  => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'is_sold'     => false,
            ],

            [
                'user_id'     => 1,
                'name'        => 'コーヒーミル',
                'description' => '手動のコーヒーミル',
                'brand'       => 'Starbacks',
                'price'       => 4000,
                'condition'   => '良好',
                'image_path'  => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'is_sold'     => false,
            ],
            [
                'user_id'     => 1,
                'name'        => 'メイクセット',
                'description' => '便利なメイクアップセット',
                'brand'       => null,
                'price'       => 2500,
                'condition'   => '目立った傷や汚れなし',
                'image_path'  => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'is_sold'     => false,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
