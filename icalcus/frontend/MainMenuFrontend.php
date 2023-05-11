<?php
/*
 *
 *   This file is part of the 'iCalcus - Interactive Calculations' project.
 *
 *   Copyright (C) 2023, Jakub Jandák
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 *
 */

namespace icalcus\fe;


class MainMenuFrontend extends AbstractAdminFrontend
{

    public static function configuration()
    {
        self::populateIcalcusJSData();
        $calculationsDescriptionsAdminFrontend = new CalculationsDescriptionsAdminFrontend();

        echo self::configureCurrentCalculation();

        echo self::draggableConfiguration();

        echo '
            <div class="d-flex flex-column icalcus-main-wrapper">
            <span class="mb-2 d-flex row col-11">
                <button id="toggleBtn" class="m-2 icalcus-toggle-creation col-2 icalcus-reappear" data-toggled-text="' . __("Calculations List") . '">' . __("Create New Calculation") . '</button>
                <div class="col-5"></div>
                <button id="editConfiguration" class="m-2 icalcus-toggle-creation icalcus-button-edit-color btn-info col-2 hidden"><i class="dashicons dashicons-admin-generic w-auto"></i>' . __("Edit Current Configuration") . '</button>
                <button id="saveCalculation" class="m-2 icalcus-toggle-creation col-2 icalcus-button-save-color btn-success hidden">' . __("Save Calculation") . '</button>
                <button id="editCalculation" class="m-2 icalcus-toggle-creation col-2 icalcus-button-save-color btn-success hidden">' . __("Edit Calculation") . '</button>
			</span>
                    <div id="firstDiv" class="content-div visible">
                    <h1>' . __("Calculations List") . '</h1>
                    ' .
            $calculationsDescriptionsAdminFrontend::configuration()
            . '
                </div>
                <div id="secondDiv" class="content-div icalcus-hidden-slow display-none">
                    <h1>' . __("Create New Calculation") . ' <input type="text" id="icalcus-calulation-new-name" class="ml-1 border-0 font-weight-bold w-auto" placeholder="Calculation name" /> </h1>
                   
                    <div class="d-flex flex-row">
                      <div id="icalcus-left-bar" class="col-3">
                      ' .
            self::getAllDraggableComponents()
            . '
                      
                      </div>
                      <div id="icalcus-dashboard" class="col-6 d-flex flex-column"></div>
                      <div id="icalcus-preview" class="col-3 icalcus-preview-box"></div>
                      </div>
                      
                </div>
                 <div id="thirdDiv" class="content-div icalcus-hidden-slow display-none">
                    <h1>' . __("Edit") . ' <input type="text" id="icalcus-calulation-edit-name" class="ml-1 border-0 font-weight-bold w-auto" placeholder="Calculation name" /> </h1>
                   
                    <div class="d-flex flex-row">
                      <div id="icalcus-left-bar" class="col-3">
		                      ' .
            self::getAllDraggableComponents()
            . '
                      
                      </div>
                      <div id="icalcus-dashboard-edit" class="col-6 d-flex flex-column"></div>
                      <div id="icalcus-preview-edit" class="col-3 icalcus-preview-box"></div>
                      </div>
                   
                </div>
            </div>
           
        ';

    }


    static function getAllDraggableComponents(): string
    {
        $dragables = self::getDraggableProduct();
        $dragables = $dragables . self::getDraggableService();
        $dragables = $dragables . self::getDraggableComponent();
        return $dragables . self::getDraggableCalculations();
    }


    static function getDraggableProduct(): string
    {
        return '<div class="icalcus-draggable" draggable="true" id="draggableProduct" data-component="product-component" data-next-id="1">' . __("Product") . '</div>';
    }

    static function getDraggableService(): string
    {
        return '<div class="icalcus-draggable" draggable="true" id="draggableService" data-component="service-component" data-next-id="1">' . __("Service") . '</div>';
    }


    static function getDraggableComponent(): string
    {
        return '<div class="icalcus-draggable" draggable="true" id="draggableComponent" data-component="component-component" data-next-id="1">' . __("Generic Component") . '</div>';
    }

    static function getDraggableCalculations(): string
    {
        return '<div class="icalcus-draggable" draggable="true" id="draggableCalculations" data-component="calculation-component" data-next-id="1">' . __("Calculation Component") . '</div>';
    }


    static function draggableConfiguration()
    {

        $returnDiv = '<div id="product-component" class="icalcus-draggable-option hidden" draggable="true">
                                    <h3>' . __("Product Component") . '</h3>
                                    <span class="icalcus-configuration-bar"></span>
                                    <div id="icalcus-dashboard-products" class="icalcus-choose-list"></div>     
                                  </div>';

        $returnDiv = $returnDiv . '<div id="service-component" class="icalcus-draggable-option hidden" draggable="true"> 
                                <h3>' . __("Service Component") . '</h3>
                                <span class="icalcus-configuration-bar"></span>
                                <div id="icalcus-dashboard-services" class="icalcus-choose-list"></div>     
                                </div>';

        $returnDiv = $returnDiv . '<div id="component-component" class="icalcus-draggable-option hidden" draggable="true">
                                <h3>' . __("Generic Component") . '</h3>
                                <span class="icalcus-configuration-bar"></span>
                                <div id="icalcus-dashboard-components" class="icalcus-choose-list"></div>     
                                </div>';

        $returnDiv = $returnDiv . '<div id="calculation-component" class="icalcus-draggable-option hidden" draggable="true">
                                <h3>' . __("Calculation Component") . '</h3>
                                <span class="icalcus-configuration-bar"></span>
                                <div id="icalcus-dashboard-calculations" class="icalcus-choose-list"></div>     
                                </div>';


        return $returnDiv;
    }


    static function generateIdAndName($product): array
    {
        return array('id' => $product->id, 'name' => $product->name);
    }

    private static function configureCurrentCalculation()
    {
        echo '<div class="icalcus-modal-wrapper hidden">
		        <div id="configure-calculation-modal" class="icalcus-config-modal">
			        <div class="modal-content p-3">
			        <span>
			          	<h2>Personal Customization</h2>
			           <button class="icalcus-config-btn btn-danger mt-2 close-btn icalcus-float-right"><i class="dashicons dashicons-no"></i></button>
			        </span>
			        
		          	<span id="show-title-configuration">
			              <label class="col-2" for="show-title">Show label:</label>
			              <input type="checkbox" id="show-title" name="show-title" class="icalcus-custom-input form-check form-switch mb-2 ml-2 mr-4" data-previous=""/> 
			        </span>
			        
			        <span id="wrapper-custom-class">
			              <label class="col-2" for="wrapper-classes">Wrapper custom class:</label>
			              <input type="text" id="wrapper-classes" name="wrapper-classes" class="icalcus-custom-input form-text form-switch mb-2 ml-2 mr-4" data-previous=""/> 
			        </span>
			        
			        
	              <label for="calculation-description">Calculation Description:</label>
                  <textarea class="icalcus-custom-input icalcus-custom-styler mt-0 mb-4 ml-4 mr-4"  id="calculation-description" name="calculation-description" rows="3" cols="50" placeholder="description" data-previous=""></textarea>
			        
			          
		          	<button class="icalcus-config-btn btn-success mt-2 save-btn icalcus-float-right"><i class="dashicons dashicons-saved"></i></button>
	        		</div>
             	</div>
    		</div>';
    }


}

