<?php

namespace icalc\fe;

use icalc\fe\displayTypes\AutocompleteText;
use icalc\fe\displayTypes\ChooseList;
use icalc\fe\displayTypes\DisplayTypeManager;
use function icalc\util\console_log;

class ProductAdminFrontend extends AbstractAdminFrontend
{

    public static function configuration()
    {
        $tagsEP = ICALC_EP_PREFIX . '/products';
        $url = get_rest_url(null, $tagsEP);


        self::setIcalcTokenCookie();

        $headers = array(
            'Content-Type' => 'application/json',
            'user' => wp_get_current_user()->ID,
            'session' => wp_get_session_token(),
            'icalc-token' => $_COOKIE['icalc-token']
        );

        $args = array(
            'headers' => $headers
        );;

        $response = wp_remote_get($url, $args);


        console_log($response);


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
        <span><button class="button mb-2" data-toggle="modal" data-target="#' . $productCreationModal . '">+</button> ' . __("Add New Product") . '</span>
            <!-- Table -->
            <table class="table table-bordered table-striped table-hover col-12">
                <thead class="thead-dark">
                    <tr class="col-12">
                        <th class="p-2 m-2">' . __("ID") . '</th>
                        <th class="p-2 m-2">' . __("Name") . '</th>
                        <th class="p-2 m-2">' . __("Description") . '</th>
                        <th class="p-2 m-2">' . __("Price") . '</th>
                        <th class="p-2 m-2">' . __("Unit") . '</th>
                        <th class="p-2 m-2">' . __("Minimal Quantity") . '</th>
                        <th class="p-2 m-2">' . __("Tag") . '</th>
                        <th class="p-2 m-2">' . __("Display Type") . '</th>
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

    public static function configuredModalEdit($modalId, $id, $formFields): string
    {
        $displayTypeList = new ChooseList($modalId . "_display_type_form", "display_type", "form-control", DisplayTypeManager::getAllDisplayTypes(), $formFields['display_type']);
        $unitAutocomplete = new AutocompleteText($modalId . "_unit_form");

        $unitAutocomplete->autocomplete();
        return '<div class="modal mt-5 fade w-100 p-3" id="' . $modalId . '" role="dialog">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body p-5">
                              <h4 class="modal-title">' . __("Edit Product") . '</h4>
                              <form id="' . $modalId . '_form">
                              <div class="form-row icalc-product-form-row">
                                <div class="col">
                                  <label for="' . $modalId . '_id_form">' . __("ID") . '</label>
                                  <input id="' . $modalId . '_id_form" type="text" class="form-control" value="' . $id . '" readonly>
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">' . __("Name") . '</label>
                                  <input id="' . $modalId . '_name_form" type="text" class="form-control" placeholder="' . __("Name") . '" value="' . $formFields['name'] . '">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">' . __("Description") . '</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="' . __("Description") . '" value="' . $formFields['desc'] . '">
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_price_form">' . __("Price") . '</label>
                                  <input id="' . $modalId . '_price_form" type="text" class="form-control" placeholder="' . __("Price") . '" value="' . $formFields['price'] . '">
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_unit_form">' . __("Unit") . '</label>
                                  ' .
            $unitAutocomplete->render()
            . '
                                  <input id="' . $modalId . '_unit_form" type="text" class="form-control" placeholder="' . __("Unit") . '" value="' . $formFields['unit'] . '">
                                </div> 
                                <div class="col">
                                  <label for="' . $modalId . '_min_quantity_form">' . __("Minimal Quantity") . '</label>
                                  <input id="' . $modalId . '_min_quantity_form" type="text" class="form-control" placeholder="' . __("Minimal Quantity") . '" value="' . $formFields['min_quantity'] . '">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_tag_form">' . __("Tag") . '</label>
                                  <input id="' . $modalId . '_tag_form" type="text" class="form-control" placeholder="' . __("Tag") . '" value="' . $formFields['tag'] . '">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_display_type_form">' . __("Display Type") . '</label>
                                    ' .
            $displayTypeList->render()
            . '
                              </div>
                              </div>
                                <div class="d-flex justify-content-end">
                                </div>
                            </form>
                            </div>
                            <div class="modal-footer">
                            
                              <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">' . __("Close") . '</button>
                              <button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="icalc_process_product_edition(\'' . $id . '\',\'' . $modalId . '\')">' . __("Edit") . '</button>
                            </div>
                          </div>
                        </div>
                      </div>';
    }

    public static function configureCreationModal($modalId): string
    {
        $displayTypeList = new ChooseList($modalId . "_display_type_form", "display_type", "form-control", DisplayTypeManager::getAllDisplayTypes());
        $unitAutocomplete = new AutocompleteText($modalId . "_unit_form");

        $unitAutocomplete->autocomplete();
        return '<div class="modal mt-5 fade w-100 p-3" id="' . $modalId . '"  role="dialog">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body p-5">
                              <h4 class="modal-title">' . __("Create New Product") . '</h4>
                              <form id="' . $modalId . '_form">
                              <div class="form-row icalc-product-form-row">
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">' . __("Name") . '</label>
                                  <input id="' . $modalId . '_name_form" type="text" class="form-control" placeholder="' . __("Product Name") . '">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">' . __("Description") . '</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="' . __("Product Description") . '" >
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_price_form">' . __("Price") . '</label>
                                  <input id="' . $modalId . '_price_form" type="number" class="form-control" placeholder="' . __("Product Price") . '" >
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_unit_form">' . __("Unit") . '</label>
                                  <input id="' . $modalId . '_unit_form" type="text" class="form-control" placeholder="' . __("Product Unit") . '" >
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_min_quantity_form">' . __("Minimal Quantity") . '</label>
                                  <input id="' . $modalId . '_min_quantity_form" type="number" class="form-control" placeholder="' . __("Product Minimal Quantity") . '" >
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_tag_form">' . __("Tag") . '</label>
                                  <input id="' . $modalId . '_tag_form" type="text" class="form-control" placeholder="' . __("Product Tag") . '" >
                                </div> 
                                 <div class="col">
                                  <label for="' . $modalId . '_display_type_form">' . __("Display Type") . '</label>
                                  ' .
            $displayTypeList->render()
            . '
                                </div>
                              </div>
                            <div class="d-flex justify-content-end">
                            </div>
                            </form>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">' . __("Close") . '</button>
                              <button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="icalc_process_product_creation(\'' . $modalId . '\')">' . __("Save") . '</button>
                            </div>
                          </div>
                        </div>
                      </div>';
    }


}