<?php
class WXAction_Base
{
	public $aData;
	public $token;
	function __construct(array $aData = array(), $token='')
	{
		$this->aData = $aData;
		$this->token = $token;
	}

	function get($sKey = '', $sDefault = null){
		if($sKey AND isset($this->aData[$sKey])){
			return $this->aData[$sKey];
		}
		return $sDefault;
	}
}
