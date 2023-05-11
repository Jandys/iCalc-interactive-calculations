<?php

namespace icalcus\fe;
class Calculation {

	private $calculationId;
	private $calculationName;
	private $calculationDescription;
	private $body;
	private $calculationConfiguration;
	private $customStyles;
	private $form;

	private $hasFoundCalculationDescription = false;

	public function __construct( $calculationId ) {
		$this->calculationId  = intval( $calculationId );
		$parseCalculationData = $this->parseCalculationData();
		if ( $parseCalculationData == - 1 ) {
			$this->hasFoundCalculationDescription = false;
		} else {
			$this->hasFoundCalculationDescription = true;
		}
	}

	public static function getConfiguredCalculationAsOptions() {
		$allCalculations = \icalcus\db\model\IcalcusulationsDescription::get_all();
		$options         = [ "default_icalcus_option0" => __( '-- NONE --', 'icalcus' ) ];
		foreach ( $allCalculations as $calculation ) {
			$options[ $calculation["id"] ] = $calculation["name"];
		}

		return $options;
	}

	public function parseCalculationData() {
		$chosenCalculation = \icalcus\db\model\IcalcusulationsDescription::get( "id", $this->calculationId );

		if ( ! isset( $chosenCalculation ) || ! isset( $chosenCalculation->body ) || ! isset( $chosenCalculation->description ) || empty( json_decode( $chosenCalculation->body )->components ) ) {
			return - 1;
		}

		$this->calculationName          = $chosenCalculation->name;
		$this->calculationDescription   = $chosenCalculation->description;
		$this->body                     = json_decode( $chosenCalculation->body );
		$this->calculationConfiguration = $this->body->configuration;
		$this->customStyles             = $this->body->customStyles;



		return 1;
	}

	public function __toString(): string {
		return "Calculation: " . $this->calculationName .
		       ", with description: " . $this->calculationDescription .
		       ", has body: " . json_encode( $this->body );
	}

	public function render(): string {
		$calculationBlock = '<div id="icalcus-calculation-' . $this->calculationId . '" class="icalcus-calculation-wrapper">';
		$calculationBlock = $calculationBlock . $this->displayTitle();
		$calculationBlock = $calculationBlock . $this->createForm();

		$calculationBlock = $calculationBlock . '</div>';

		$calculationBlock = $calculationBlock . $this->appendScrips();

		return $calculationBlock;
	}


	private function displayTitle(): string {
		$show_title = $this->calculationConfiguration->{'show-title'};

		if ( ! $show_title ) {
			return "";
		}

		return '<h3 class="icalcus-calculation-title">' . $this->calculationName . '</h3>';
	}

	private function createForm(): string {
		$this->form = new Form();

		$components = $this->body->components;

		error_log( "testCOMPONENTS ARRAY" );
		error_log( json_encode( $components ) );

		foreach ( $components as $component ) {
			$this->form->addComponent( $component );
		}

		return $this->form->render();
	}

	private function appendScrips(): string {
		//do we have sum in calculation
		$appendScripts = new ScriptWrapper();

		if ( $this->form->has( 'sum' ) ) {
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


		if ( $this->form->has( 'subtract calculation' ) ) {
			$subtractScript = new ScriptWrapper();
			$subtractScript->wrapWithScrip( false );
			$subtractScript->wrapWithOnLoad( false );

			$components = $this->form->get_components();
			foreach ( $components as $component ) {
				$subtractScript->addToContent( $this->addOnChangeListenerToSubtract( $component ) );
			}
			$subtractScript->addToContent( $this->addSubtractListeners( $components ) );

			if ( ! $subtractScript->isEmpty() ) {
				$appendScripts->addToContent( $subtractScript->getScripts() );
			}
		}

		if ( $this->form->has( 'product calculation' ) ) {
			$productScript = new ScriptWrapper();
			$productScript->wrapWithScrip( false );
			$productScript->wrapWithOnLoad( false );

			$components = $this->form->get_components();
			foreach ( $components as $component ) {
				$productScript->addToContent( $this->addOnChangeListenerToProduct( $component ) );
			}
			$productScript->addToContent( $this->addProductListeners( $components ) );

			if ( ! $productScript->isEmpty() ) {
				$appendScripts->addToContent( $productScript->getScripts() );
			}
		}

		if ( $this->form->has( 'complex calculation' ) ) {
			$complexScript = new ScriptWrapper();
			$complexScript->wrapWithScrip( false );
			$complexScript->wrapWithOnLoad( false );

			$components          = $this->form->get_components();
			$complexCalculations = $this->getOnlyComponents( $components, "complex calculation" );
			foreach ( $components as $component ) {
				$complexScript->addToContent( $this->addOnChangeListenerToComplexCalculation( $component, $complexCalculations ) );
			}
			$complexScript->addToContent( $this->addComplexCalculationListeners( $components ) );

			if ( ! $complexScript->isEmpty() ) {
				$appendScripts->addToContent( $complexScript->getScripts() );
			}
		}

		if ( $this->form->has( 'slider' ) ) {
			$sliderScript = new ScriptWrapper();
			$sliderScript->wrapWithScrip( false );
			$sliderScript->wrapWithOnLoad( false );

			$components = $this->form->get_components();
			foreach ( $components as $component ) {
				$sliderScript->addToContent( $this->addSliderChangeListener( $component ) );
			}

			if ( ! $sliderScript->isEmpty() ) {
				$appendScripts->addToContent( $sliderScript->getScripts() );
			}
		}

		if ( ! $appendScripts->isEmpty() ) {
			$listenTointeractions = new ScriptWrapper();
			$listenTointeractions->wrapWithScrip( false );
			$listenTointeractions->wrapWithOnLoad( false );
			$interactionScript = "let " . $this->uniqueName( 'icalcus-calculation-' . $this->calculationId ) . " = document.getElementById('icalcus-calculation-" . $this->calculationId . "');" .
			                     "icalcus_register_interactions(" . $this->uniqueName( 'icalcus-calculation-' . $this->calculationId ) . "," . $this->calculationId . ");";
			$listenTointeractions->addToContent( $interactionScript );
			$appendScripts->addToContent( $listenTointeractions->getScripts() );
		}

		if ( ! $appendScripts->isEmpty() ) {
			return $appendScripts->getScripts();
		}

		return "";
	}

	const ICALCUS_COMPONENTS_WITH_NO_ONCHANGE = array(
		"sum",
		"subtract calculation",
		"product calculation",
		"complex calculation",
		"label",
		"hr",
		"horizontal rule",
		"-- none --",
		"--none--",
		"text",
		"spacer"
	);

	private function addOnChangeListenerToSum( $component ): string {
		$cleanedType = $this->getCleaned_DisplayType( $component );
		if ( in_array( $cleanedType, Calculation::ICALCUS_COMPONENTS_WITH_NO_ONCHANGE ) ) {
			return "";
		} else if ( $cleanedType == "checkbox" ) {
			return
				"if(typeof " . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
				"var " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}
					" . $this->uniqueName( $component->get_dom_id() ) . ".addEventListener(\"change\", function() {
					    const changedValue = " . $this->uniqueName( $component->get_dom_id() ) . ".checked? 1 : 0;
					    const myComponentCalculation = " . $component->get_base_value() . " * changedValue; 
					    
					    icalcus_update_pre_and_calculation('" . $this->uniqueName( $component->get_dom_id() ) . "'," . $this->calculationId . ",myComponentCalculation,'sum');
						icalcus_updateSumCalculation" . $this->calculationId . "();
					});

			";
		} else {
			return
				"if(typeof " . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
				"var " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}
					" . $this->uniqueName( $component->get_dom_id() ) . ".addEventListener(\"change\", function() {
					    const changedValue = " . $this->uniqueName( $component->get_dom_id() ) . ".value;
					    const myComponentCalculation = " . $component->get_base_value() . " * changedValue; 
					    
					    icalcus_update_pre_and_calculation('" . $this->uniqueName( $component->get_dom_id() ) . "'," . $this->calculationId . ",myComponentCalculation,'sum');
						icalcus_updateSumCalculation" . $this->calculationId . "();
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

		$function = "function icalcus_updateSumCalculation" . $this->calculationId . "(){" .
		            "if(!icalcus_pages_calculations[" . $this->calculationId . "]){icalcus_pages_calculations[" . $this->calculationId . "]=[]}";
		foreach ( $sumObjects as $sum ) {
			$function = $function .
			            "if(typeof " . $this->uniqueName( $sum->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $sum->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
			            "var " . $this->uniqueName( $sum->get_dom_id() ) . " = document.getElementById('" . $sum->get_dom_id() . "');}" .
			            $this->uniqueName( $sum->get_dom_id() ) . ".value = '" . $sum->getSumPrefix() . "' + icalcus_evaluate_calculation(" . $this->calculationId . ",'sum').toString() + '" . $sum->getSumPostFix() . "';";

		}

		return $function . "}";
	}

	private function addOnChangeListenerToSubtract( $component ): string {
		$cleanedType = $this->getCleaned_DisplayType( $component );
		if ( in_array( $cleanedType, Calculation::ICALCUS_COMPONENTS_WITH_NO_ONCHANGE ) ) {
			return "";
		} else if ( $cleanedType == "checkbox" ) {
			return
				"if(typeof " . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
				"var " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}
					" . $this->uniqueName( $component->get_dom_id() ) . ".addEventListener(\"change\", function() {
					    const changedValue = " . $this->uniqueName( $component->get_dom_id() ) . ".checked? 1 : 0;
					    const myComponentCalculation = " . $component->get_base_value() . " * changedValue; 
					    
					    icalcus_update_pre_and_calculation('" . $this->uniqueName( $component->get_dom_id() ) . "'," . $this->calculationId . ",myComponentCalculation,'subtract');
						icalcus_updateSubtractCalculation" . $this->calculationId . "();
					});

			";
		} else {
			return
				"if(typeof " . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
				"var " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}
					" . $this->uniqueName( $component->get_dom_id() ) . ".addEventListener(\"change\", function() {
					    const changedValue = " . $this->uniqueName( $component->get_dom_id() ) . ".value;
					    const myComponentCalculation = " . $component->get_base_value() . " * changedValue; 
					    
					    icalcus_update_pre_and_calculation('" . $this->uniqueName( $component->get_dom_id() ) . "'," . $this->calculationId . ",myComponentCalculation,'subtract');
						icalcus_updateSubtractCalculation" . $this->calculationId . "();
					});

			";
		}
	}

	private function addSubtractListeners( $components ): string {
		$subtractObjects = [];

		foreach ( $components as $component ) {
			if ( strtolower( trim( $component->get_display_type() ) ) == 'subtract calculation' ) {
				$subtractObjects[ $component->get_dom_id() ] = $component;
			}
		}

		$function = "function icalcus_updateSubtractCalculation" . $this->calculationId . "(){" .
		            "if(!icalcus_pages_calculations[" . $this->calculationId . "]){icalcus_pages_calculations[" . $this->calculationId . "]=[]}";
		foreach ( $subtractObjects as $subtract ) {
			$function = $function .
			            "if(typeof " . $this->uniqueName( $subtract->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $subtract->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
			            "var " . $this->uniqueName( $subtract->get_dom_id() ) . " = document.getElementById('" . $subtract->get_dom_id() . "');}" .
			            "if(typeof " . $this->uniqueName( $subtract->get_dom_id() ) . "_baseValue  !== 'undefined'){ " . $this->uniqueName( $subtract->get_dom_id() ) . "_baseValue =" . $this->uniqueName( $subtract->get_dom_id() ) . ".dataset.startingValue;}else{" .
			            "var " . $this->uniqueName( $subtract->get_dom_id() ) . "_baseValue = " . $this->uniqueName( $subtract->get_dom_id() ) . ".dataset.startingValue;}" .
			            "icalcus_update_pre_and_calculation('" . $this->uniqueName( $subtract->get_dom_id() ) . "'," . $this->calculationId . ", -1 * Number(" . $this->uniqueName( $subtract->get_dom_id() ) . "_baseValue)" . ",'subtract');" .
			            $this->uniqueName( $subtract->get_dom_id() ) . ".value = '" . $subtract->getSumPrefix() . "' + icalcus_evaluate_calculation(" . $this->calculationId . ",'subtract').toString() + '" . $subtract->getSumPostFix() . "';";
		}

		return $function . "}";
	}

	private function addOnChangeListenerToProduct( $component ): string {
		$cleanedType = $this->getCleaned_DisplayType( $component );
		if ( in_array( $cleanedType, Calculation::ICALCUS_COMPONENTS_WITH_NO_ONCHANGE ) ) {
			return "";
		} else if ( $cleanedType == "checkbox" ) {
			return
				"if(typeof " . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
				"var " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}
					" . $this->uniqueName( $component->get_dom_id() ) . ".addEventListener(\"change\", function() {
					    const changedValue = " . $this->uniqueName( $component->get_dom_id() ) . ".checked? 1 : 0;
					    var myComponentCalculation = " . $component->get_base_value() . " * changedValue; 
					    if(myComponentCalculation==0){myComponentCalculation=1;}
					    
					    icalcus_update_pre_and_calculation('" . $this->uniqueName( $component->get_dom_id() ) . "'," . $this->calculationId . ",myComponentCalculation,'product');
						icalcus_updateProductCalculation" . $this->calculationId . "();
					});

			";
		} else {
			return
				"if(typeof " . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
				"var " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}
					" . $this->uniqueName( $component->get_dom_id() ) . ".addEventListener(\"change\", function() {
					    const changedValue = " . $this->uniqueName( $component->get_dom_id() ) . ".value;
					    var myComponentCalculation = " . $component->get_base_value() . " * changedValue; 
                        if(myComponentCalculation==0){myComponentCalculation=1;}

					    
					    icalcus_update_pre_and_calculation('" . $this->uniqueName( $component->get_dom_id() ) . "'," . $this->calculationId . ",myComponentCalculation,'product');
						icalcus_updateProductCalculation" . $this->calculationId . "();
					});

			";
		}
	}

	private function addProductListeners( $components ): string {
		$productObjects = [];

		foreach ( $components as $component ) {
			if ( strtolower( trim( $component->get_display_type() ) ) == 'product calculation' ) {
				$productObjects[ $component->get_dom_id() ] = $component;
			}
		}

		$function = "function icalcus_updateProductCalculation" . $this->calculationId . "(){" .
		            "if(!icalcus_pages_calculations[" . $this->calculationId . "]){icalcus_pages_calculations[" . $this->calculationId . "]=[]}";
		foreach ( $productObjects as $product ) {
			$function = $function .
			            "if(typeof " . $this->uniqueName( $product->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $product->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "')}else{" .
			            "var " . $this->uniqueName( $product->get_dom_id() ) . " = document.getElementById('" . $product->get_dom_id() . "');}" .
			            "icalcus_update_pre_and_calculation('" . $this->uniqueName( $product->get_dom_id() ) . "'," . $this->calculationId . ", 1 ,'product');" .
			            $this->uniqueName( $product->get_dom_id() ) . ".value = '" . $product->getSumPrefix() . "' + icalcus_evaluate_calculation(" . $this->calculationId . ",'product').toString() + '" . $product->getSumPostFix() . "';";
		}

		return $function . "}";
	}


	private function addOnChangeListenerToComplexCalculation( $component, $complexCalculations ): string {
		$cleanedType = $this->getCleaned_DisplayType( $component );
		if ( in_array( $cleanedType, Calculation::ICALCUS_COMPONENTS_WITH_NO_ONCHANGE ) ) {
			return "";
		} else {
			$getValue = ";";
			switch ( $component->get_display_type() ) {
				case "checkbox":
					$getValue = $this->uniqueName( $component->get_dom_id() ) . ".checked ? " . $component->get_base_value() . " : " . $component->get_unchecked_value();
					break;
				default:
					$getValue = $this->uniqueName( $component->get_dom_id() ) . ".value";
			}


			$scriptPart =
				"if(typeof " . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "');}else{" .
				"var " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "');}
					" . $this->uniqueName( $component->get_dom_id() ) . ".addEventListener(\"change\", function() {
					    const value = " . $getValue . ";";

			foreach ( $complexCalculations as $calculation ) {
				$scriptPart = $scriptPart . "icalcus_update_complex_calculation('" . $calculation->get_dom_id() . "','" . $component->get_parent_component() . "',value);";
			}

			return $scriptPart . "});";
		}
	}

	private function addComplexCalculationListeners( $components ): string {
		$calculationObjects = [];

		foreach ( $components as $component ) {
			if ( strcasecmp( $component->get_display_type(), 'complex calculation' ) == 0 ) {
				$calculationObjects[ $component->get_dom_id() ] = $component;
			}
		}

		$function = "";
		foreach ( $calculationObjects as $complexCalculation ) {

			$regex                  = '/\[([^\]]+)\/[^\]]+\]/';
			$calculationDescription = preg_replace( $regex, '[$1]', $complexCalculation->get_complex_calculation() );


			$function = $function .
			            "if(typeof " . $this->uniqueName( $complexCalculation->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $complexCalculation->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "');}else{" .
			            "var " . $this->uniqueName( $complexCalculation->get_dom_id() ) . " = document.getElementById('" . $complexCalculation->get_dom_id() . "');}" .
			            $this->uniqueName( $complexCalculation->get_dom_id() ) . ".dataset.prefix='" . $complexCalculation->getSumPrefix() . "';" .
			            $this->uniqueName( $complexCalculation->get_dom_id() ) . ".dataset.sufix='" . $complexCalculation->getSumPostFix() . "';" .
			            "icalcus_complexCalculations['" . $complexCalculation->get_dom_id() . "']= '" . $calculationDescription . "';";
		}

		return $function;
	}


	private function addSliderChangeListener( $component ): string {
		$cleanedType = $this->getCleaned_DisplayType( $component );
		if ( $cleanedType !== 'slider' ) {
			return "";
		} else {
			return
				"if(typeof " . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "');}else{" .
				"var " . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('" . $component->get_dom_id() . "');}
					" . $this->uniqueName( $component->get_dom_id() ) . ".addEventListener(\"change\", function() { " .
				"if(typeof displayValue_" . $this->uniqueName( $component->get_dom_id() ) . " !== 'undefined'){ displayValue_" . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('displayValue-" . $component->get_dom_id() . "')}else{" .
				"var displayValue_" . $this->uniqueName( $component->get_dom_id() ) . " = document.getElementById('displayValue-" . $component->get_dom_id() . "')}
				 
			    displayValue_" . $this->uniqueName( $component->get_dom_id() ) . ".innerText = " . $this->uniqueName( $component->get_dom_id() ) . ".value;
				});";
		}
	}

	private function getOnlyComponents( $components, $type ): array {
		$returnArr = [];
		foreach ( $components as $component ) {
			if ( strcasecmp( $component->get_display_type(), $type ) == 0 ) {
				$returnArr[] = $component;
			}
		}

		return $returnArr;
	}


	private function uniqueName( $domId ): string {
		return "icalcus" . $this->calculationId . "_" . str_replace( '-', '_', $domId );
	}

	/**
	 * @param $component
	 *
	 * @return string
	 */
	public function getCleaned_DisplayType( $component ): string {
		$cleanedType = strtolower( trim( $component->get_display_type() ) );

		return $cleanedType;
	}

	public function hasFoundCalculationDescription() {
		return $this->hasFoundCalculationDescription;
	}


}