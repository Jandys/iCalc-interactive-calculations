<?php

namespace icalc\fe;

class ScriptWrapper {


	private string $contents = "" ;
	private bool $wrapWithOnLoad = true;
	private bool $wrapWithScript = true;

	public function __construct() {
	}


	public function getScripts():string{
		$retVal = "";
		if ($this->wrapWithScript){
			$retVal = $retVal . '<script>';
		}

		if ($this->wrapWithOnLoad){
			$retVal = $retVal . 'window.addEventListener(\'load\', function () {';
		}

		$retVal = $retVal . $this->contents;

		if ($this->wrapWithOnLoad){
			$retVal = $retVal . '});';
		}

		if ($this->wrapWithScript){
			$retVal = $retVal . '</script>';
		}

		return $retVal;
	}

	public function isEmpty():bool{
		return empty($this->contents);
	}


	public function addToContent($addition):void{
		$this->contents = $this->contents . $addition;
	}

	public function wrapWithOnLoad($doWrapWithOnLoad = true):void{
		$this->wrapWithOnLoad = $doWrapWithOnLoad;
	}

	public function wrapWithScrip($doWrapWithScript = true):void{
		$this->wrapWithScript = $doWrapWithScript;
	}
}