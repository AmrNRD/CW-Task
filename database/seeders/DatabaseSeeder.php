<?php

namespace Database\Seeders;

use App\Models\OrderProduct;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $products=\App\Models\Product::factory(50)->create()->pluck('id')->all();
        for ($i = 0; $i <= 1000; $i++) {
            $rang=rand(2, 10);
            $order= \App\Models\Order::factory()->create();
            $random_products=array_rand($products,$rang);
            foreach ($random_products as $key){
                OrderProduct::create([
                    'product_id'=>$products[$key],
                    'order_id'=>$order->id,
                    'count'=>1
                ]);
            }
        }
    }
}
