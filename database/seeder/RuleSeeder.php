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


        $purchaseLimitSKU100 = [
            ['product_id', '==', 'SKU100', 'string'],
            ['purchase_qty', '<=', 10, 'numeric']
        ];
        $model = new \Rimantoro\Lumenrule\Models\RulesModel;
        $model->code = 'sku100_stock_limit';
        $model->title = 'SKU100 Stock Limit Purchase';
        $model->rules = $purchaseLimitSKU100;
        $model->group_logic = '{0} AND {1}';
        $model->active = 1;
        $model->save();


        $priceDiscountSKU100 = [
            ['product_id', '==', 'SKU100', 'string'],
            ['purchase_amount', '>=', 100000, 'numeric'],
        ];
        $model = new \Rimantoro\Lumenrule\Models\RulesModel;
        $model->code = 'sku100_disc_25';
        $model->title = 'SKU100 25% Discount';
        $model->rules = $priceDiscountSKU100;
        $model->group_logic = '{0} AND {1}';
        $model->active = 1;
        $model->save();

        $pulsaBack = [
            ['product_id', '==', 'SKU100', 'string'],
        ];
        $model = new \Rimantoro\Lumenrule\Models\RulesModel;
        $model->code = 'sku100_pulsaback_10';
        $model->title = 'SKU100 Pulsaback 25rb';
        $model->rules = $pulsaBack;
        $model->group_logic = null;
        $model->active = 1;
        $model->save();

        $beforeDate = [
            ['product_id', '==', 'SKU100', 'string'],
            ['purchase_date', '<=', '2016-10-30', 'date'],
        ];
        $model = new \Rimantoro\Lumenrule\Models\RulesModel;
        $model->code = 'sku100_promo_oct';
        $model->title = 'SKU100 Promo for Oct 2016';
        $model->rules = $beforeDate;
        $model->group_logic = '{0} AND {1}';
        $model->active = 1;
        $model->save();


    }
}
