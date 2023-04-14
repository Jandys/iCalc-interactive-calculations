<?php

namespace icalc\fe;


use icalc\fe\displayTypes\ChooseList;

class MainMenuFrontend extends AbstractAdminFrontend
{

//       $numberInput = new Number("icalc-numbar","Label input:","Nameee",0,2000,1,10);
//        $myChoseList = new ChooseList("icalc-list","MyName","icalc-test-class",["opt1"=>"test","opt2"=>"value"],"opt2");

    public static function configuration()
    {
        self::populateIcalcJSData();
        $calculationsDescriptionsAdminFrontend = new CalculationsDescriptionsAdminFrontend();

        echo self::draggableConfiguration();

        echo '
            <div class="d-flex flex-column icalc-main-wrapper">
                 <button id="toggleBtn" class="icalc-toggle-creation col-2 icalc-reappear" data-toggled-text="' . __("Edit Calculations") . '">' . __("Create New Calculation") . '</button>
                    <div id="firstDiv" class="content-div visible">
                    <h1>' . __("Edit Calculations") . '</h1>
                    ' .
            $calculationsDescriptionsAdminFrontend::configuration()
            . '
                </div>
                <div id="secondDiv" class="content-div icalc-hidden-slow display-none">
                    <h1>' . __("Create New Calculation") . '</h1>
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
                    <h1>' . __("Edit Specific Calculation") . '</h1>
                    <ul>
                        
                    </ul>
                </div>
            </div>
           
        ';

    }


    static function getAllDraggableComponents(): string
    {
        $dragables = self::getDraggableProduct();
        $dragables = $dragables . self::getDraggableService();
        $dragables = $dragables . self::getDraggableDisplayType();
        return $dragables . self::getDraggableComponent();
    }


    static function getDraggableProduct(): string
    {
        return '<div class="icalc-draggable" draggable="true" id="draggableProduct" data-component="product-component" data-next-id="1">' . __("Product") . '</div>';
    }

    static function getDraggableService(): string
    {
        return '<div class="icalc-draggable" draggable="true" id="draggableService" data-component="service-component" data-next-id="1">' . __("Service") . '</div>';
    }

    static function getDraggableDisplayType(): string
    {
        return '<div class="icalc-draggable" draggable="true" id="draggableDisplayType" data-component="display-component" data-next-id="1">' . __("Generic Display Type") . '</div>';
    }

    static function getDraggableComponent(): string
    {
        return '<div class="icalc-draggable" draggable="true" id="draggableComponent" data-component="component-component" data-next-id="1">' . __("Generic Component") . '</div>';
    }


    static function draggableConfiguration()
    {
//        $products = self::callGetOnEPWithAuthCookie('/products');
        $services = self::callGetOnEPWithAuthCookie('/services');

//        $productNamesWithID = array_map('\icalc\fe\MainMenuFrontend::generateIdAndName', $products);
//        $productNames = array_column($productNamesWithID, 'name');
//        $productNames = array_combine(array_column($productNamesWithID, 'id'), $productNames);
//
        $serviceNamesWithID = array_map('\icalc\fe\MainMenuFrontend::generateIdAndName', $services);
        $serviceNames = array_column($serviceNamesWithID, 'name');
        $serviceNames = array_combine(array_column($serviceNamesWithID, 'id'), $serviceNames);
//
//
//        $productChooseList = new ChooseList('productChooseList','productChooseList','icalc-component-chooselist',$productNames);
        $serviceChooseList = new ChooseList('serviceChooseList','serviceChooseList','icalc-component-chooselist',$serviceNames);



        $returnDiv = '<div id="product-component" class="icalc-draggable-option hidden" draggable="true">
                        <h3>'.__("Product Component").'</h3>
                        <div id="icalc-dashboard-products" class="icalc-choose-list"></div>     
                      </div>';
//
//                            $productChooseList->render()

        $returnDiv = $returnDiv . '<div id="service-component" class="icalc-draggable-option hidden" draggable="true"> 
                                <h3>'.__("Service Component").'</h3>

            '.
            $serviceChooseList->render()
            .'</div>';
        $returnDiv = $returnDiv . '<div id="display-component" class="icalc-draggable-option hidden" draggable="true">Totally Different option</div>';
        $returnDiv = $returnDiv . '<div id="component-component" class="icalc-draggable-option hidden" draggable="true">Totally Different option</div>';


        return $returnDiv;
    }


    static function generateIdAndName($product): array
    {
        return array('id' => $product->id,  'name' => $product->name);
    }




}

