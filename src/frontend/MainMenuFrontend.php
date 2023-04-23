<?php

namespace icalc\fe;


use icalc\fe\displayTypes\ChooseList;

class MainMenuFrontend extends AbstractAdminFrontend {

//       $numberInput = new Number("icalc-numbar","Label input:","Nameee",0,2000,1,10);
//        $myChoseList = new ChooseList("icalc-list","MyName","icalc-test-class",["opt1"=>"test","opt2"=>"value"],"opt2");

	public static function configuration() {
		self::populateIcalcJSData();
		$calculationsDescriptionsAdminFrontend = new CalculationsDescriptionsAdminFrontend();

		echo self::configureCurrentCalculation();

		echo self::draggableConfiguration();

		echo '
            <div class="d-flex flex-column icalc-main-wrapper">
            <span class="mb-2 d-flex row col-11">
                <button id="toggleBtn" class="m-2 icalc-toggle-creation col-2 icalc-reappear" data-toggled-text="' . __( "Calculations List" ) . '">' . __( "Create New Calculation" ) . '</button>
                <div class="col-5"></div>
                <button id="editConfiguration" class="m-2 icalc-toggle-creation icalc-button-edit-color btn-info col-2 hidden"><i class="dashicons dashicons-admin-generic w-auto"></i>'. __( "Edit Current Configuration" ) . '</button>
                <button id="saveCalculation" class="m-2 icalc-toggle-creation col-2 icalc-button-save-color btn-success hidden">' . __( "Save Calculation" ) . '</button>
                <button id="editCalculation" class="m-2 icalc-toggle-creation col-2 icalc-button-save-color btn-success hidden">' . __( "Edit Calculation" ) . '</button>
			</span>
                    <div id="firstDiv" class="content-div visible">
                    <h1>' . __( "Calculations List" ) . '</h1>
                    ' .
		     $calculationsDescriptionsAdminFrontend::configuration()
		     . '
                </div>
                <div id="secondDiv" class="content-div icalc-hidden-slow display-none">
                    <h1>' . __( "Create New Calculation" ) . ' <input type="text" id="icalc-calulation-new-name" class="ml-1 border-0 font-weight-bold w-auto" placeholder="Calculation name" /> </h1>
                   
                    <div class="d-flex flex-row">
                      <div id="icalc-left-bar" class="col-3">
                      ' .
		     self::getAllDraggableComponents()
		     . '
                      
                      </div>
                      <div id="icalc-dashboard" class="col-6 d-flex flex-column"></div>
                      <div id="icalc-preview" class="col-3 icalc-preview-box"></div>
                      </div>
                      
                </div>
                 <div id="thirdDiv" class="content-div icalc-hidden-slow display-none">
                    <h1>' . __( "Edit" ) . ' <input type="text" id="icalc-calulation-edit-name" class="ml-1 border-0 font-weight-bold w-auto" placeholder="Calculation name" /> </h1>
                   
                    <div class="d-flex flex-row">
                      <div id="icalc-left-bar" class="col-3">
		                      ' .
				     self::getAllDraggableComponents()
				     . '
                      
                      </div>
                      <div id="icalc-dashboard-edit" class="col-6 d-flex flex-column"></div>
                      <div id="icalc-preview-edit" class="col-3 icalc-preview-box"></div>
                      </div>
                   
                </div>
            </div>
           
        ';

	}


	static function getAllDraggableComponents(): string {
		$dragables = self::getDraggableProduct();
		$dragables = $dragables . self::getDraggableService();
		$dragables = $dragables . self::getDraggableDisplayType();

		return $dragables . self::getDraggableComponent();
	}


	static function getDraggableProduct(): string {
		return '<div class="icalc-draggable" draggable="true" id="draggableProduct" data-component="product-component" data-next-id="1">' . __( "Product" ) . '</div>';
	}

	static function getDraggableService(): string {
		return '<div class="icalc-draggable" draggable="true" id="draggableService" data-component="service-component" data-next-id="1">' . __( "Service" ) . '</div>';
	}

	static function getDraggableDisplayType(): string {
		return '<div class="icalc-draggable" draggable="true" id="draggableDisplayType" data-component="display-component" data-next-id="1">' . __( "Generic Display Type" ) . '</div>';
	}

	static function getDraggableComponent(): string {
		return '<div class="icalc-draggable" draggable="true" id="draggableComponent" data-component="component-component" data-next-id="1">' . __( "Generic Component" ) . '</div>';
	}


	static function draggableConfiguration() {

		$returnDiv = '<div id="product-component" class="icalc-draggable-option hidden" draggable="true">
                                    <h3>' . __( "Product Component" ) . '</h3>
                                    <span class="icalc-configuration-bar"></span>
                                    <div id="icalc-dashboard-products" class="icalc-choose-list"></div>     
                                  </div>';

		$returnDiv = $returnDiv . '<div id="service-component" class="icalc-draggable-option hidden" draggable="true"> 
                                <h3>' . __( "Service Component" ) . '</h3>
                                <span class="icalc-configuration-bar"></span>
                                <div id="icalc-dashboard-services" class="icalc-choose-list"></div>     
                                </div>';


		$returnDiv = $returnDiv . '<div id="display-component" class="icalc-draggable-option hidden" draggable="true">Totally Different option</div>';


		$returnDiv = $returnDiv . '<div id="component-component" class="icalc-draggable-option hidden" draggable="true">
                                <h3>' . __( "Generic Component" ) . '</h3>
                                <span class="icalc-configuration-bar"></span>
                                <div id="icalc-dashboard-components" class="icalc-choose-list"></div>     
                                </div>';


		return $returnDiv;
	}


	static function generateIdAndName( $product ): array {
		return array( 'id' => $product->id, 'name' => $product->name );
	}

	private static function configureCurrentCalculation() {
		echo '<div class="icalc-modal-wrapper hidden">
		        <div id="configure-calculation-modal" class="icalc-config-modal">
			        <div class="modal-content p-3">
			        <span>
			          	<h2>Personal Customization</h2>
			           <button class="icalc-config-btn btn-danger mt-2 close-btn icalc-float-right"><i class="dashicons dashicons-no"></i></button>
			        </span>
			        
		          	<span id="show-title-configuration">
			              <label class="col-2" for="show-title">Show label:</label>
			              <input type="checkbox" id="show-title" name="show-title" class="icalc-custom-input form-check form-switch mb-2 ml-2 mr-4" data-previous=""/> 
			        </span>
			        
			        <span id="wrapper-custom-class">
			              <label class="col-2" for="wrapper-classes">Wrapper custom class:</label>
			              <input type="text" id="wrapper-classes" name="wrapper-classes" class="icalc-custom-input form-text form-switch mb-2 ml-2 mr-4" data-previous=""/> 
			        </span>
			        
			        
	              <label for="calculation-description">Calculation Description:</label>
                  <textarea class="icalc-custom-input icalc-custom-styler mt-0 mb-4 ml-4 mr-4"  id="calculation-description" name="calculation-description" rows="3" cols="50" placeholder="description" data-previous=""></textarea>
			        
			          
		          	<button class="icalc-config-btn btn-success mt-2 save-btn icalc-float-right"><i class="dashicons dashicons-saved"></i></button>
	        		</div>
             	</div>
    		</div>';
}


}

