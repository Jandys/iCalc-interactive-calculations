<?php

namespace icalc\fe;

/**
 * The ScriptWrapper class is a helper class that provides a way to build a JavaScript script that can be added to an HTML page.
 */
class ScriptWrapper {


	private string $contents = "" ;
	private bool $wrapWithOnLoad = true;
	private bool $wrapWithScript = true;

	public function __construct() {
	}


    /**
     * Generates the script based on the current configuration of the ScriptWrapper instance.
     *
     * @return string The generated script.
     */
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

    /**
     * Checks whether the ScriptWrapper instance is empty.
     *
     * @return bool True if the ScriptWrapper instance is empty, false otherwise.
     */
	public function isEmpty():bool{
		return empty($this->contents);
	}


    /**
     * Adds content to the script.
     *
     * @param string $addition The content to add.
     */
	public function addToContent($addition):void{
		$this->contents = $this->contents . $addition;
	}

    /**
     * Enables or disables wrapping the script with an onload event.
     *
     * @param bool $doWrapWithOnLoad True to wrap the script with an onload event, false otherwise.
     */
	public function wrapWithOnLoad($doWrapWithOnLoad = true):void{
		$this->wrapWithOnLoad = $doWrapWithOnLoad;
	}

    /**
     * Enables or disables wrapping the script with a script tags.
     *
     * @param bool $doWrapWithScript True to wrap the script with a script tags, false otherwise.
     */
	public function wrapWithScrip($doWrapWithScript = true):void{
		$this->wrapWithScript = $doWrapWithScript;
	}
}