# Lumen Rule Engine

Simple rule engine for Lumen framework. 

  - Set your rule and save it to database, and compare it with in your event
  - Logic grouping
  - Custom callback action

## Usage

Let say you have a Promo for two product SKU with the same action and identical purchase limit for both SKU.

This promo valid if :
- Product ID = SKU100 or SKU200
- Purchase limit not more than 200

Then you need to create the rule first and then validate later.

```php
// Create Rule
$Obj = new \Rimantoro\Lumenrule\Models\RulesModel;
$Obj->code = 'promo_for_sku100_and_sku200';
$Obj->title = 'Promo for SKU100 and SKU200 purchase';
$Obj->rules = [
    [ 'product_id', '==', 'SKU200', 'string' ],
    [ 'product_id', '==', 'SKU100', 'string' ],
    [ 'purchase_qty', '<', '200', 'numeric' ],
];
$Obj->group_logic = "( {0} OR {1} ) AND {2}";       // create logic group based on rules
$Obj->active = 1;
$Obj->save();

// Validate
$Rule = new \Rimantoro\Lumenrule\Rule('promo_for_sku100_and_sku200', [
        'product_id' => 'SKU100', 'purchase_qty' => 100
    ]);
$result1 = $Rule->validate();    // This true

// Another purchase to validate
$Rule = new \Rimantoro\Lumenrule\Rule('promo_for_sku100_and_sku200', [
        'product_id' => 'SKU200', 'purchase_qty' => 100
    ]);
$result2 = $Rule->validate();    // This also true

// Debuging rule

$humanize = $Rule->parseRuleAsString();         // will print "( \"SKU200\" == \"SKU200\" OR \"SKU200\" == \"SKU100\" ) AND 100 < 200"

$rawHumanize = $Rule->parseRuleAsString(0);     // will print  "( \"product_id\" == \"SKU200\" OR \"product_id\" == \"SKU100\" ) AND purchase_qty < 200"

```

### Logic Grouping

If you have many single rules component and you need to grouping for more complex validation, do this when you want to save your rule.

```php
...
$Obj->rules = [
    [ 'product_id', '==', 'SKU200', 'string' ],
    [ 'product_id', '==', 'SKU100', 'string' ],
    [ 'purchase_qty', '<', '200', 'numeric' ],
];
$Obj->group_logic = "( {0} OR {1} ) AND {2}";       // create logic group based on rules
...
``` 

### Generating Human Readable Rule

For debuging purpose, you can parse your and print it as a human readable string with "parseRuleAsString" method.

```php
$actualValue = [
    'product_id' => 'SKU100',
    'purchase_date' => '2016-10-03'
];
$Rule = new \Rimantoro\Lumenrule\Rule('sku100_promo_oct', $actualValue);
$check = $Rule->validate();

// Return rule in string
var_dump($Rule->parseRuleAsString());

// Return rule object
var_dump($Rule->getInfo());
```

### Custom Action Callback Function

Despite only to return TRUE or FALSE, you can set your own logic with action callback in validate method.

note : you need to setup anonymous function with $ruleSet as argument to pass in validate method. Also for Rule object itself need to pass through your callback.

```php
$Rule = \Rimantoro\Lumenrule\Rule('sku100_promo_oct', $actualValue);
$check = $Rule->validate(function($ruleSet) use ($Rule){
    // this will return your rule logic value compared to actual value
    $validate = $Rule->result($ruleSet);
    if($validate)
        // .... Here is your custom TRUE logic
    else
        // .... Here is your custom FALSE logic
});
```


## Update Existing Rule

When updatin rule, make sure you also update the group logic to follow your new rule set.

```php
$Rule = new \Rimantoro\Lumenrule\Rule('sku100_promo_oct', $actualValue);
$Obj = $Rule->getInfo();
$Obj->rules = [
    [ 'product_id', '==', 'SKU200', 'string' ],
    [ 'purchase_qty', '<', '200', 'numeric' ],
];
$Obj->group_logic = "{0} AND {1}";
$Obj->save();
var_dump($Obj);
```

## Delete Existing Rule

```php
$Rule = new \Rimantoro\Lumenrule\Rule('sku100_promo_oct', $actualValue);
$Obj = $Rule->getInfo();
$del = $Obj->delete();
var_dump($del);
```
