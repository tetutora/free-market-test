<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // デフォルトユーザーを取得
        $defaultUser = User::where('email', 'default@example.com')->first();

        if (!$defaultUser) {
            throw new \Exception('Default user not found. Please run the UserSeeder first.');
        }

        $products = [
            [
                'name' => '腕時計',
                'brand_name' => 'セイコー',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'status' => '良好',
                'user_id' => $defaultUser->id,
                'category_ids' => [2,5,12]
            ],
            [
                'name' => 'HDD',
                'brand_name' => 'パナソニック',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'status' => '目立った傷や汚れなし',
                'user_id' => $defaultUser->id,
                'category_ids' => [1]
            ],
            [
                'name' => '玉ねぎ3束',
                'brand_name' => '農協',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'status' => 'やや傷や汚れあり',
                'user_id' => $defaultUser->id,
                'category_ids' => [10]
            ],
            [
                'name' => '革靴',
                'brand_name' => 'ドクターマーチン',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'status' => '状態が悪い',
                'user_id' => $defaultUser->id,
                'category_ids' => [2,5]
            ],
            [
                'name' => 'ノートPC',
                'brand_name' => 'アップル',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'status' => '良好',
                'user_id' => $defaultUser->id,
                'category_ids' => [1]
            ],
            [
                'name' => 'マイク',
                'brand_name' => 'ソニー',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'status' => '目立った傷や汚れなし',
                'user_id' => $defaultUser->id,
                'category_ids' => [1]
            ],
            [
                'name' => 'ショルダーバッグ',
                'brand_name' => 'コーチ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'status' => 'やや傷や汚れあり',
                'user_id' => $defaultUser->id,
                'category_ids' => [2,4]
            ],
            [
                'name' => 'タンブラー',
                'brand_name' => 'タイガー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'status' => '状態が悪い',
                'user_id' => $defaultUser->id,
                'category_ids' => [10]
            ],
            [
                'name' => 'コーヒーミル',
                'brand_name' => 'スターバックス',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'status' => '良好',
                'user_id' => $defaultUser->id,
                'category_ids' => [10]
            ],
            [
                'name' => 'メイクセット',
                'brand_name' => 'メイベリン',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'status' => '目立った傷や汚れなし',
                'user_id' => $defaultUser->id,
                'category_ids' => [6]
            ],
        ];

        foreach ($products as $product) {
            // 商品作成
            $productModel = Product::create([
                'name' => $product['name'],
                'brand_name' => $product['brand_name'],
                'price' => $product['price'],
                'description' => $product['description'],
                'image' => $product['image'],
                'status' => $product['status'],
                'user_id' => $product['user_id'],
            ]);

            // 指定されたカテゴリーIDを関連付け
            $productModel->categories()->attach($product['category_ids'], ['user_id' => $defaultUser->id]);
        }

        // foreach ($products as $product) {
        //     Product::create($product);
        // }
        //     $categoryIds = $productData['category_ids'];
        //     $product->categories()->attach($categoryIds, ['user_id' => $defaultUser->id]);
    }
}
