<?php
namespace icalc\fe;
class Calculation {

	private $calculationId;
	private $calculationName;
	private $calculationDescription;
	private $body;
	private $calculationConfiguration;
	private $customStyles;

	public function __construct($calculationId) {
		$this->calculationId= intval($calculationId);
		$this->parseCalculationData();
	}

	public static function getConfiguredCalculationAsOptions(){
		$allCalculations = \icalc\db\model\IcalculationsDescription::get_all();
		$options = ["default_icalc_option0"=>__( '-- NONE --', 'icalc' )];
		foreach($allCalculations as $calculation){
			$options[$calculation["id"]] = $calculation["name"];
		}
		return $options;
	}

	private function parseCalculationData() {
		$chosenCalculation = \icalc\db\model\IcalculationsDescription::get( "id", $this->calculationId );
		$this->calculationName = $chosenCalculation->name;
		$this->calculationDescription = $chosenCalculation->description;
		$this->body = json_decode($chosenCalculation->body);
		$this->calculationConfiguration = $this->body->configuration;
		$this->customStyles = $this->body->customStyles;
	}

	public function __toString(): string {
		return "Calculation: " . $this->calculationName.
		       ", with description: " . $this->calculationDescription.
		       ", has body: " . json_encode($this->body);
	}

	public function render():string{
		$calculationBlock = '<div class="icalc-calculation-wrapper">';
		$calculationBlock = $calculationBlock . $this->displayTitle();
		$calculationBlock = $calculationBlock . $this->createForm();

		$calculationBlock = $calculationBlock . '</div>';
		return $calculationBlock;
	}


	private function displayTitle():string{
		error_log("configuration");
		$show_title = $this->calculationConfiguration->{'show-title'};

		if(!$show_title){
			return "";
		}
		return '<h3 class="icalc-calculation-title">'.$this->calculationName.'</h3>';
	}

	private function createForm():string{
		$form = new Form();

		$components = $this->body->components;
		foreach($components as $component){
			$form->addComponent($component);
		}

		return $form->render();
	}


}