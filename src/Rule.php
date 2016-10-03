<?php

namespace Rimantoro\Lumenrule;

use Exception;
use Rimantoro\Lumenrule\Models\RulesModel;

class Rule {

	protected $id;
	protected $event;
	protected $code;
	protected $title;
	protected $rules;
	protected $active;

	protected $now;
	protected $actualParams;
	protected $ruleSet;

	protected function getDataAndParse()
	{
		$rule = RulesModel::where('event', $this->event)
			->where('code', $this->code)
			->where('active', 1)
			->first();

		if(!$rule) throw new Exception("Rule not found or inactive for event=".$this->event." and code=".$this->code, 422);

		$this->ruleSet = $rule->rules;

		// parse object attr
		$this->id = $rule->id;
		$this->event = $rule->event;
		$this->code = $rule->code;
		$this->title = $rule->title;
		$this->active = $rule->active;
		$this->rules = $this->ruleSet;

		return $this->ruleSet;
	}

	// ========================
	// PUBLIC
	// ========================

	public function __construct($event, $code, Array $actualParams)
	{
		$this->now = date('Y-m-d H:i:s');
		$this->actualParams = $actualParams;
		$this->event = $event;
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
		$logicVal = [];
        $result = false;
        foreach ($ruleSet as $k => $rule) {
            $param = $rule[0];
            $opr = $rule[1];
            $value = $rule[2];
            $type = strtolower($rule[3]);

            if(isset($actualParams[$param])) {

            	switch ($type) {
            		case 'string':
            			eval("\$logicVal[] = (\"$actualParams[$param]\" $opr \"$value\") ? 1 : 0;");
            			break;
            		
            		case 'numeric':
            			eval("\$logicVal[] = ($actualParams[$param] $opr $value) ? 1 : 0;");
            			break;

            		case 'date':
            			eval("\$logicVal[] = (strtotime(\"$actualParams[$param]\") $opr strtotime(\"$value\")) ? 1 : 0;");
            			break;
            		
            		case 'boolean':
            			eval("\$logicVal[] = ($actualParams[$param] $opr $value) ? 1 : 0;");
            			break;
            		
            		default:
            			eval("\$logicVal[] = (\"$actualParams[$param]\" $opr \"$value\") ? 1 : 0;");
            			break;
            	}
            } else {
            	$logicVal[] = 0;
            }
        }

        $strCompare = implode(" && ", $logicVal);

        eval("\$result = $strCompare;");

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

		$strLogic = rtrim($strLogic, " AND ");

		return $strLogic;
	}

}