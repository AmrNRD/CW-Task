<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuggestedProductsRequest;
use App\Models\ConversionRate;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{



    public function showForm()
    {
        $products=Product::all();
        return view('welcome',compact('products'));
    }

    public function getSuggestedProducts(SuggestedProductsRequest $request)
    {
        $data=$request->validated();
        $rate = $this->calcRate($data['suggestion_date']);

        $all_suggested_products=DB::select("SELECT order_products.product_id,products.name as name,products.price as price,(products.price/".$rate.") as price_in_eur, COUNT(order_products.order_id) AS number_of_orders
                                                FROM order_products
                                                    INNER JOIN products ON products.id = order_products.product_id
                                                WHERE order_id IN (select id from `orders` where exists (select * from `order_products` where `orders`.`id` = `order_products`.`order_id` and `product_id` = ".$data['product1'].")
                                                                   and exists (select * from `order_products` where `orders`.`id` = `order_products`.`order_id` and `product_id` = ".$data['product2']."))
                                                    AND product_id NOT IN(".$data['product1'].",".$data['product2'].")
                                                GROUP BY product_id
                                                ORDER BY `number_of_orders` DESC");
        $suggested_products = $this->getBestMatchedSumOfProducts($all_suggested_products, $data['eur_amount']);
        return $suggested_products;

    }

    /**
     * @param $suggestion_date
     * @return int
     */
    public function calcRate($suggestion_date): int
    {
        $conversion_rate = ConversionRate::where('currency', 'EUR')->where('date','<=', $suggestion_date)->orderBy('date', 'desc')->first();
        return $conversion_rate ? $conversion_rate->rate : 1;
    }

    /**
     * @param array $all_suggested_products
     * @param $eur_amount
     * @return array
     */
    public function getBestMatchedSumOfProducts(array $all_suggested_products, $eur_amount): array
    {

        $suggested_products = [];
        $total = 0;
        foreach ($all_suggested_products as $suggested_product) {
            if($total==$eur_amount)
                break;
            if (($total + $suggested_product->price_in_eur) > $eur_amount)
                continue;
            array_push($suggested_products, $suggested_product);
            $total += $suggested_product->price_in_eur;
        }
        return $suggested_products;
    }


}
