<?php

namespace icalc\fe;


class MainMenuFrontend extends AbstractAdminFrontend
{

    public static function configuration()
    {

        echo '
            <div class="d-flex flex-column icalc-main-wrapper">
                 <button id="toggleBtn" class="icalc-toggle-creation col-2 icalc-reappear" data-toggled-text="'.__("Edit Calculations").'">'.__("Create New Calculation").'</button>
                    <div id="firstDiv" class="content-div visible">
                    <h1>Edit Calculations</h1>
                </div>
                <div id="secondDiv" class="content-div icalc-hidden-slow display-none">
                    <h1>Create new Calculation</h1>
                    <div class="d-flex">
                      <div id="icalc-left-bar">
                          <div class="icalc-draggable" draggable="true" id="component1">Component 1</div>
                          <div class="icalc-draggable" draggable="true" id="component2">Component 2</div>
                          <div class="icalc-draggable" draggable="true" id="component3">Component 3</div>
                      </div>
                      <div id="icalc-dashboard"></div>
                    </div>
                </div>
                 <div id="thirdDiv" class="content-div icalc-hidden-slow display-none">
                    <h1>Edist specific calculation</h1>
                    <ul>
                        
                    </ul>
                </div>
            </div>
           
        ';

    }


    public static function configurationOfEditCalculations(){
        $tagsEP = ICALC_EP_PREFIX . '/icalculations';
        $url = get_rest_url(null, $tagsEP);

        console_log($url);
        $response = wp_remote_get($url);


        console_log($response);

        $tbody = "";
        $html = "";
        if (is_array($response)) {
            console_log("DATA RECIEVED");
            $body = wp_remote_retrieve_body($response);
            console_log($body);
            $data = json_decode($body);
            console_log($data);


            foreach ($data as $item) {
                $modalId = "product" . $item->id . "modal";
                $modalData = [];
                $modalData["name"] = $item->name;
                $modalData["desc"] = $item->description;
                $modalData["price"] = $item->price;
                $modalData["unit"] = $item->unit;
                $modalData["min_quantity"] = $item->min_quantity;
                $modalData["tag"] = $item->tag;
                $modalData["display_type"] = $item->display_type;


                $html = $html . self::configuredModalEdit($modalId, $item->id, $modalData);


                $tbody = $tbody . '
                <tr>
                        <td>' . $item->id . '</td>
                        <td>' . $item->name . '</td>
                        <td>' . $item->description . '</td>
                        <td>' . $item->price . '</td>
                        <td>' . $item->unit . '</td>
                        <td>' . $item->min_quantity . '</td>
                        <td>' . $item->tag . '</td>
                        <td>' . $item->display_type . '</td>
                        <td class="text-center"><button class="btn btn-info" data-toggle="modal" data-target="#' . $modalId . '"><span class="dashicons dashicons-edit"></span></button></td>
                        <td class="text-center"><button class="btn btn-danger" onclick="icalc_process_product_deletion(' . $item->id . ',\'' . $item->name . '\')"><span class="dashicons dashicons-trash"></span></button></td>
                    </tr>';
            }
        } else {
            console_log("ERROR FETCHING");
        }

        $productCreationModal = "productCreationModal";

        $html = $html . self::configureCreationModal($productCreationModal);
        $html = $html . '
    <div class="container pt-5">
        <!-- Additon button -->
        <span><button class="button mb-2" data-toggle="modal" data-target="#' . $productCreationModal . '">+</button> '.__("Add New Product").'</span>
            <!-- Table -->
            <table class="table table-bordered table-striped table-hover col-12">
                <thead class="thead-dark">
                    <tr class="col-12">
                        <th class="p-2 m-2">'.__("ID").'</th>
                        <th class="p-2 m-2">'.__("Name").'</th>
                        <th class="p-2 m-2">'.__("Description").'</th>
                        <th class="p-2 m-2">'.__("Price").'</th>
                        <th class="p-2 m-2">'.__("Unit").'</th>
                        <th class="p-2 m-2">'.__("Minimal Quantity").'</th>
                        <th class="p-2 m-2">'.__("Tag").'</th>
                        <th class="p-2 m-2">'.__("Display Type").'</th>
                        <th class="col-1"></th>
                        <th class="col-1"></th>
                    </tr>
                </thead>
                <tbody id="table-body">
                ' . $tbody . '
                </tbody>
            </table>
        <!-- Pagination -->
        <div class="wp-block-navigation">
            <!-- Add pagination links here -->
        </div>
    </div>';

        echo $html;
    }


}