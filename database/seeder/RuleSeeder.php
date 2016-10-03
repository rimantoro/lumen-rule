<?php

use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $purchaseLimitSKU100 = json_encode([
            "product_id|==|SKU100|string",
            "purchase_qty|<=|10|numeric",
        ]);

        $priceDiscountSKU100 = json_encode([
            "product_id|==|SKU100|string",
            "purchase_amount|>=|100000|numeric",
        ]);

        $pulsaBack = json_encode([
            "product_id|==|SKU100|string",
        ]);

        $beforeDate = json_encode([
            "product_id|==|SKU100|string",
            "purchase_date|<=|2016-10-30|date",
        ]);

        App\Libraries\Rules\Models\Rules::insert([
                [ 'code' => 'sku100_stock_limit', 'title' => 'SKU100 Stock Limit Purchase', 'event' => 'purchase', 'rules' => $purchaseLimitSKU100, 'active' => 1 ],
                [ 'code' => 'sku100_disc_25', 'title' => 'SKU100 25% Discount', 'event' => 'purchase', 'rules' => $priceDiscountSKU100, 'active' => 1 ],
                [ 'code' => 'sku100_pulsaback_10', 'title' => 'SKU100 Pulsaback 25rb', 'event' => 'purchase', 'rules' => $pulsaBack, 'active' => 1 ],
                [ 'code' => 'sku100_promo_oct', 'title' => 'SKU100 Promo for Oct 2016', 'event' => 'purchase', 'rules' => $beforeDate, 'active' => 1 ],
            ]);
    }
}
