<?php

namespace icalc\fe;
class Calculation {

	private $calculationId;
	private $calculationName;
	private $calculationDescription;
	private $body;
	private $calculationConfiguration;
	private $customStyles;
	private $form;

	public function __construct( $calculationId ) {
		$this->calculationId = intval( $calculationId );
		$this->parseCalculationData();
	}

	public static function getConfiguredCalculationAsOptions() {
		$allCalculations = \icalc\db\model\IcalculationsDescription::get_all();
		$options         = [ "default_icalc_option0" => __( '-- NONE --', 'icalc' ) ];
		foreach ( $allCalculations as $calculation ) {
			$options[ $calculation["id"] ] = $calculation["name"];
		}

		return $options;
	}

	private function parseCalculationData() {
		$chosenCalculation              = \icalc\db\model\IcalculationsDescription::get( "id", $this->calculationId );
		$this->calculationName          = $chosenCalculation->name;
		$this->calculationDescription   = $chosenCalculation->description;
		$this->body                     = json_decode( $chosenCalculation->body );
		$this->calculationConfiguration = $this->body->configuration;
		$this->customStyles             = $this->body->customStyles;
	}

	public function __toString(): string {
		return "Calculation: " . $this->calculationName .
		       ", with description: " . $this->calculationDescription .
		       ", has body: " . json_encode( $this->body );
	}

	public function render(): string {
		$calculationBlock = '<div class="icalc-calculation-wrapper">';
		$calculationBlock = $calculationBlock . $this->displayTitle();
		$calculationBlock = $calculationBlock . $this->createForm();

		$calculationBlock = $calculationBlock . '</div>';

		$calculationBlock = $calculationBlock . $this->appendScrips();

		return $calculationBlock;
	}


	private function displayTitle(): string {
		error_log( "configuration" );
		$show_title = $this->calculationConfiguration->{'show-title'};

		if ( ! $show_title ) {
			return "";
		}

		return '<h3 class="icalc-calculation-title">' . $this->calculationName . '</h3>';
	}

	private function createForm(): string {
		$this->form = new Form();

		$components = $this->body->components;
		foreach ( $components as $component ) {
			$this->form->addComponent( $component );
		}

		return $this->form->render();
	}

	private function appendScrips(): string {
		//do we have sum in calculation
		$appendScripts = new ScriptWrapper();
		$appendScripts->wrapWithOnLoad( false );

		if ( $this->form->has('sum') ) {
			$sumScript = new ScriptWrapper();
			$sumScript->wrapWithScrip( false );
			$sumScript->wrapWithOnLoad( false );

			$components = $this->form->get_components();
			foreach ( $components as $component ) {
				$sumScript->addToContent( $this->addOnChangeListenerToSum( $component ) );
			}
			$sumScript->addToContent( $this->addSumListeners( $components ) );

			if ( ! $sumScript->isEmpty() ) {
				$appendScripts->addToContent( $sumScript->getScripts() );
			}
		}

		if( $this->form->has('slider')){
			$sliderScript = new ScriptWrapper();
			$sliderScript->wrapWithScrip(false);
			$sliderScript->wrapWithOnLoad(false);

			$components = $this->form->get_components();
			foreach ( $components as $component ) {
				$sliderScript->addToContent( $this->addSliderChangeListener( $component ) );
			}



			if ( ! $sliderScript->isEmpty() ) {
				$appendScripts->addToContent( $sliderScript->getScripts() );
			}
		}



		if ( ! $appendScripts->isEmpty() ) {
			return $appendScripts->getScripts();
		}

		return "";
	}

	const ICALC_COMPONENTS_WITH_NO_ONCHANGE = array(
		"sum",
		"label",
		"hr",
		"horizontal rule",
		"-- none --",
		"--none--",
		"text"
	);

	private function addOnChangeListenerToSum( $component ): string {
		$cleanedType = strtolower( trim( $component->get_display_type() ) );
		if ( in_array( $cleanedType, Calculation::ICALC_COMPONENTS_WITH_NO_ONCHANGE ) ) {
			return "";
		} else {
			return
				"if(typeof " . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
				"var " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}
					" . $this->uniqueName( $component->get_dom_id() ) . ".addEventListener(\"change\", function() {
					    const changedValue = " . $this->uniqueName( $component->get_dom_id() ) . ".value;
					    const myComponentCalculation = " . $component->get_base_value() . " * changedValue; 
					    
					    icalc_update_pre_and_calculation('" . $this->uniqueName( $component->get_dom_id() ) . "'," . $this->calculationId . ",myComponentCalculation);
						icalc_updateCalculation" . $this->calculationId . "();
					});

			";
		}
	}

	private function addSumListeners( $components ): string {
		$sumObjects = [];

		foreach ( $components as $component ) {
			if ( strtolower( trim( $component->get_display_type() ) ) == 'sum' ) {
				$sumObjects[ $component->get_dom_id() ] = $component;
			}
		}

		$function = "function icalc_updateCalculation" . $this->calculationId . "(){" .
		            "if(!icalc_pages_calculations[" . $this->calculationId . "]){icalc_pages_calculations[" . $this->calculationId . "]=[]}";
		foreach ( $sumObjects as $sum ) {
			$function = $function .
			            "if(typeof " . $this->uniqueName( $sum->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $sum->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
			            "var " . $this->uniqueName( $sum->get_dom_id() ) . " = document.getElementById('" . $sum->get_dom_id() . "');}" .
			            $this->uniqueName( $sum->get_dom_id() ) . ".value = '" . $sum->getSumPrefix() . "' + icalc_evaluate_calculation(" . $this->calculationId . ").toString() + '" . $sum->getSumPostFix() . "';";

		}

		return $function . "}";
	}

	private function addSliderChangeListener($component):string{
		$cleanedType = strtolower( trim( $component->get_display_type() ) );
		if ( $cleanedType !== 'slider' ) {
			return "";
		} else {
			return
				"if(typeof " . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
				"var " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}
					" . $this->uniqueName( $component->get_dom_id() ) . ".addEventListener(\"change\", function() { ".
				"if(typeof displayValue_" . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ displayValue_" . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('displayValue-" . $component->get_dom_id() . "')}else{" .
				"var displayValue_" . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('displayValue-" . $component->get_dom_id() . "')}
				 
			    displayValue_" . $this->uniqueName( $component->get_dom_id() ) . ".innerText = " . $this->uniqueName( $component->get_dom_id() ) . ".value;
				});";
		}
	}




	private function uniqueName( $domId ): string {
		return "icalc" . $this->calculationId . "_" . str_replace( '-', '_', $domId );
	}


}