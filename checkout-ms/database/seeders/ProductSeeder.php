<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = \DB::connection('referrer_mysql')->table('products')->get();

        foreach ($products as $product) {
            Product::create([
                'id' => $product->id,
                'title' => $product->title,
                'description' => $product->description,
                'image' => $product->image,
                'price' => $product->price,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ]);
        }
    }
}
