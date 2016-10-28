<?php

namespace Rimantoro\Lumenrule;

use Exception;
use Rimantoro\Lumenrule\Models\RulesModel;

class Rule {

	protected $id;
	protected $code;
	protected $title;
	protected $rules;
	protected $group_logic;
	protected $active;

	protected $now;
	protected $actualParams;
	protected $ruleSet;

	protected function getDataAndParse()
	{
		$rule = RulesModel::where('code', $this->code)
			->where('active', 1)
			->first();

		if(!$rule) throw new Exception("Rule not found or inactive for code=".$this->code, 422);

		$this->ruleSet = $rule->rules;

		// parse object attr
		$this->id = $rule->id;
		$this->code = $rule->code;
		$this->title = $rule->title;
		$this->active = $rule->active;
		$this->rules = $this->ruleSet;
		$this->group_logic = $rule->group_logic;

		return $this->ruleSet;
	}

	// ========================
	// PUBLIC
	// ========================

	public function __construct($code, Array $actualParams)
	{
		$this->now = date('Y-m-d H:i:s');
		$this->actualParams = $actualParams;
		$this->code = $code;

		$this->getDataAndParse();

	}

	public function getInfo()
	{	
		return RulesModel::find($this->id);
	}

	public function validate($actionCallback=null)
	{

		if($actionCallback!==null) {
			return $actionCallback($this->ruleSet);
		} else {
			return $this->defaultActionCB($this->ruleSet);
		}
	}
	
	public function defaultActionCB($ruleSet)
	{
		return $this->result($ruleSet);
	}

	public function result($ruleSet)
	{
        if(!$ruleSet) throw new Exception("Rule is not valid", 500);
	
		$actualParams = $this->actualParams;
		$boolResult = [];
        $result = false;
        foreach ($ruleSet as $k => $rule) {
            $param = $rule[0];
            $opr = $rule[1];
            $value = $rule[2];
            $type = strtolower($rule[3]);

            if(isset($actualParams[$param])) {

            	switch ($type) {
            		case 'string':
            			eval("\$boolResult[] = (\"$actualParams[$param]\" $opr \"$value\") ? 1 : 0;");
            			break;
            		
            		case 'numeric':
            			eval("\$boolResult[] = ($actualParams[$param] $opr $value) ? 1 : 0;");
            			break;

            		case 'date':
            			eval("\$boolResult[] = (strtotime(\"$actualParams[$param]\") $opr strtotime(\"$value\")) ? 1 : 0;");
            			break;
            		
            		case 'boolean':
            			eval("\$boolResult[] = ($actualParams[$param] $opr $value) ? 1 : 0;");
            			break;
            		
            		default:
            			eval("\$boolResult[] = (\"$actualParams[$param]\" $opr \"$value\") ? 1 : 0;");
            			break;
            	}
            } else {
            	$boolResult[] = 0;
            }
        }

        if(!empty($this->group_logic)) {
        	// parse logic group string
        	$patterns = [];
        	preg_match_all("/\{[^}]*?\}/", $this->group_logic, $patterns);
        	
        	// return FALSE if not found (invalid logic string)
        	if(!count($patterns[0])) throw new Exception("Invalid logic string. Logic string should include only AND, OR, NOT, (, ) and Rule index with brace. i.e: {0} AND ( {1} OR {2} )", 500);
        	
        	$patterns = $patterns[0];
        	$replacements = $boolResult;

        	foreach ($patterns as $k => $v) {
        		$clean = trim($v, '{');
        		$clean = trim($clean, '}');
        		if(array_search($clean, $replacements)===false):
        			// cannot find valid replacement
        			throw new Exception("Cannot found valid rule replacement for index $clean", 500);
        		endif;
        	}

        	ksort($patterns);
        	ksort($replacements);

        	$strCompare = str_replace($patterns, $replacements, $this->group_logic);
        } else {
        	$strCompare = implode(" && ", $boolResult);
        }

        @eval("\$result = ($strCompare);");

    	return $result;
	}

	public function parseRuleAsString()
	{	
		$rulesLogic = $this->ruleSet;		// $this->getRuleSet();
		
		if(!$rulesLogic) return null;

		$strLogic = "";

		foreach ($rulesLogic as $k => $rule) {
			$strLogic .=  sprintf("%s %s %s AND ", $rule[0], $rule[1], $rule[2]);
		}

		$strLogic = rtrim($strLogic, " AND ") . " || actual param values are " . json_encode($this->actualParams);

		return $strLogic;
	}

}