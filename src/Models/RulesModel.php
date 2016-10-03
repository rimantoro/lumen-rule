<?php

namespace Rimantoro\Lumenrule\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RulesModel extends Model
{
    // use SoftDeletes;

    protected $table = 'ruleengine_rules';

    public $primaryKey = 'id';

    public $incrementing = false;

    protected $guarded = [];

    // for accessor ( custom field )
    protected $appends = [];

    protected static $recPerPage = 50;

    protected $paramType = [ 'numeric', 'string', 'date', 'boolean' ];
    protected $operandType = [ '==', '===', '!=', '!==', '>=', '<=', '>', '<', '<>' ];

    /************************
     *  Relationship Methods
     ************************/


    /************************
     *  Accessors & Mutators
     ************************/

    /**
     * Value should be
     * [
     *     [ 'param name', 'operand i.e: == <=', 'valid value', 'param type' ],
     *     [ 'param name', 'operand i.e: == <=', 'valid value', 'param type' ],
     *     [ ... ],
     * ]
     */
    public function setRulesAttribute($values)
    {
        if(!is_array($values)) throw new Exception("Rules must be an array", 500);
        
        $raw = [];

        foreach ($values as $k => $value) {

            if(!count($value)==4) throw new Exception("Rules attribute must consist array of 4 ( [ param_name, operand, valid_value, param_type ] )", 500);

            $raw[] = sprintf("%s|%s|%s|%s", $value[0], $this->validateOperandType($value[1]), $value[2], $this->validateParamType($value[3]));
        }

        $this->attributes['rules'] = json_encode($raw);
    }

    public function getRulesAttribute($value)
    {
        $rules = [];
        $source = json_decode($value, 1);

        if(!$source) return null;

        foreach ($source as $rule) {
            $component = preg_split('/[|,]+/', $rule);
            if(!count($component)==4) continue;
            $rules[] = $component;
        }

        return $rules;
    }

    /*******************************************
    * PRIVATE PROTECTED METHODS
    ********************************************/

    protected function validateOperandType($opr)
    {
        $search = array_search($opr, $this->operandType);
        if($search===false) throw new Exception("Unsupported operand type : $opr", 500);
        return $this->operandType[$search];
    }

    protected function validateParamType($type)
    {
        $search = array_search($type, $this->paramType);
        if($search===false) throw new Exception("Unsupported parameter type : $type", 500);
        return $this->paramType[$search];
    }

    /*******************************************
    * PUBLIC METHODS
    ********************************************/
}
