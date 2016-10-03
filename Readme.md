# Lumen Rule Engine

Simple rule engine for Lumen framework. 

  - Set your rule and save it to database, and compare it with the actual value in related "event"
  - Custom "event" which you can put in your wanted places
  - Support for custom callback action

## Usage

Let say you have rule with code "sku100_stock_limit" and for event "purchase". This rule have a TRUE value if :

- product_id == 'SKU100' (AND)
- purchase_qty <= 10

What you need to do is to put this code in your desired event logic.

```php
$Rule = new \Rimantoro\Lumenrule\Rule('purchase', 'sku100_promo_oct', $actualValue);
$check = $Rule->validate(function($ruleSet) use ($Rule) {
    $cek = $Rule->result($ruleSet);
    if($cek)
        echo "OK";
    else
        echo "NOT OK";
});

// Return rule in string
var_dump($Rule->parseRuleAsString());

// Return rule object
var_dump($Rule->getInfo());
```

### Custom Action Callback Function

Despite only to return TRUE or FALSE, you can set your own logic with action callback in validate method.

note : you need to setup anonymous function with $ruleSer as argument to pass in validate method. Also for Rule object itself need to pass through your callback.

```php
$actualValue = [
    'product_id' => 'SKU100',
    'purchase_qty' => 5
];

$Rule = \Rimantoro\Lumenrule\Rule('purchase', 'sku100_stock_limit', $actualValue);
$check = $Rule->validate(function($ruleSet) use ($Rule){
    // this will return your rule logic value compared to actual value
    $validate = $Rule->result($ruleSet);
    if($validate)
        // .... Here is your custom TRUE logic
    else
        // .... Here is your custom FALSE logic
});
```


## Create New Rule

```php
$Obj = new \Rimantoro\Lumenrule\Models\RulesModel;
$Obj->code = 'sku200_purchase_qty_limit';
$Obj->title = 'Purchase Qty Limit For SKU200';
$Obj->event = 'purchase';
$Obj->rules = [
    [ 'product_id', '==', 'SKU200', 'string' ],
    [ 'purchase_qty', '<', '20', 'numeric' ],
];
$Obj->active = 1;
$Obj->save();
```

## Update Existing Rule

```php
$Rule = new \Rimantoro\Lumenrule\Rule('purchase', 'sku200_purchase_qty_limit', $actualValue);
$Obj = $Rule->getInfo();
$Obj->rules = [
    [ 'product_id', '==', 'SKU200', 'string' ],
    [ 'purchase_qty', '<', '200', 'numeric' ],
];
$Obj->save();
var_dump($Obj);
```

## Delete Existing Rule

```php
$Rule = new \Rimantoro\Lumenrule\Rule('purchase', 'sku200_purchase_qty_limit', $actualValue);
$Obj = $Rule->getInfo();
$del = $Obj->delete();
var_dump($del);
```


To Be Continued .....
