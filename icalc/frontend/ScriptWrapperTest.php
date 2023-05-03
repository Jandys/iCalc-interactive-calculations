<?php

namespace icalc\fe;

use PHPUnit\Framework\TestCase;

class ScriptWrapperTest extends TestCase {
	public function testAddToContent()
	{
		$scriptWrapper = new ScriptWrapper();
		$scriptWrapper->addToContent('console.log("Hello World!");');

		$this->assertFalse($scriptWrapper->isEmpty());
	}

	public function testIsEmpty()
	{
		$scriptWrapper = new ScriptWrapper();
		$this->assertTrue($scriptWrapper->isEmpty());
	}

	public function testGetScripts()
	{
		$scriptWrapper = new ScriptWrapper();
		$scriptWrapper->addToContent('console.log("Hello World!");');

		$expectedScript = '<script>window.addEventListener(\'load\', function () {console.log("Hello World!");});</script>';
		$this->assertEquals($expectedScript, $scriptWrapper->getScripts());
	}

	public function testWrapWithOnLoad()
	{
		$scriptWrapper = new ScriptWrapper();
		$scriptWrapper->addToContent('console.log("Hello World!");');
		$scriptWrapper->wrapWithOnLoad(false);

		$expectedScript = '<script>console.log("Hello World!");</script>';
		$this->assertEquals($expectedScript, $scriptWrapper->getScripts());
	}

	public function testWrapWithScrip()
	{
		$scriptWrapper = new ScriptWrapper();
		$scriptWrapper->addToContent('console.log("Hello World!");');
		$scriptWrapper->wrapWithScrip(false);

		$expectedScript = 'window.addEventListener(\'load\', function () {console.log("Hello World!");});';
		$this->assertEquals($expectedScript, $scriptWrapper->getScripts());
	}
}
