<?php

namespace Database\Seeders;

use App\Models\LinkProduct;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LinkProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $linkProducts = \DB::connection('referrer_mysql')->table('link_products')->get();

        foreach ($linkProducts as $linkProduct) {
            LinkProduct::create([
                'id' => $linkProduct->id,
                'link_id' => $linkProduct->link_id,
                'product_id' => $linkProduct->product_id,
            ]);
        }
    }
}
