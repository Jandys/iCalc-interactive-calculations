<?php

namespace icalc\fe;

use function icalc\util\console_log;

class AdminFrontend
{


    public static function serviceConfiguration()
    {
        $tagsEP = ICALC_EP_PREFIX . '/services';
        $url = get_rest_url(null, $tagsEP);

        console_log($url);
        $response = wp_remote_get($url);


        console_log($response);
        if (is_array($response)) {
            console_log("DATA RECIEVED");
            $body = wp_remote_retrieve_body($response);
            console_log($body);
            $data = json_decode($body);
            console_log($data);

            $tbody = "";
            $html = "";


            foreach ($data as $item) {
                $modalId = "tag" . $item->id . "modal";

                $modalData = [];
                $modalData["name"] = $item->name;
                $modalData["desc"] = $item->description;
                $modalData["price"] = $item->price;
                $modalData["unit"] = $item->unit;
                $modalData["min_quantity"] = $item->min_quantity;
                $modalData["tag"] = $item->tag;
                $modalData["display_type"] = $item->display_type;



                $html = $html . self::configuredModalServiceEdit($modalData, $modalId, $item->id);


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
                        <td class="text-center"><button class="btn btn-danger" onclick="icalc_process_service_deletion(' . $item->id . ',\'' . $item->name . '\')"><span class="dashicons dashicons-trash"></span></button></td>
                    </tr>';
            }
        } else {
            console_log("ERROR FETCHING");
        }

        $serviceCreationModal = "serviceCreationModal";

        $html = $html . self::configureServiceCreationModal($serviceCreationModal);
        $html = $html . '
    <div class="container pt-5">
        <!-- Additon button -->
        <span><button class="button mb-2" data-toggle="modal" data-target="#' . $serviceCreationModal . '">+</button> Přidat novu službu</span>
            <!-- Table -->
            <table class="table table-bordered table-striped table-hover col-12">
                <thead class="thead-dark">
                    <tr class="col-12">
                        <th class="p-2 m-2">ID</th>
                        <th class="p-2 m-2">Name</th>
                        <th class="p-2 m-2">Description</th>
                        <th class="p-2 m-2">Price</th>
                        <th class="p-2 m-2">Unit</th>
                        <th class="p-2 m-2">Minimal Quantity</th>
                        <th class="p-2 m-2">Tag</th>
                        <th class="p-2 m-2">Display type</th>
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


    private static function configuredModalServiceEdit($formFields, $modalId, $id)
    {
        return  '<div class="modal mt-5 fade w-100 p-3" id="' . $modalId . '" role="dialog">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body p-5">
                              <h4 class="modal-title">Upravit Službu</h4>
                              <form>
                              <div class="form-row icalc-service-form-row">
                                <div class="col">
                                  <label for="' . $modalId . '_id_form">Id</label>
                                  <input id="' . $modalId . '_id_form" type="text" class="form-control" value="' . $id . '" readonly>
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">Name</label>
                                  <input id="' . $modalId . '_name_form" type="text" class="form-control" placeholder="Service name" value="' . $formFields['name'] . '">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">Description</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="Service description" value="' . $formFields['desc'] . '">
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_id_form">Price</label>
                                  <input id="' . $modalId . '_id_form" type="text" class="form-control" placeholder="Service name" value="' . $formFields['price'] . '">
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">Unit</label>
                                  <input id="' . $modalId . '_name_form" type="text" class="form-control" placeholder="Service unit" value="' . $formFields['unit'] . '">
                                </div> 
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">Minimal Quantity</label>
                                  <input id="' . $modalId . '_name_form" type="text" class="form-control" placeholder="Service minimal quantity" value="' . $formFields['min_quantity'] . '">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">Tag</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="Service tag" value="' . $formFields['tag'] . '">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">Display Type</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="Service display type" value="' . $formFields['display_type'] . '">
                                </div>
                              </div>
                                <div class="d-flex justify-content-end">
                                </div>
                            </form>
                            </div>
                            <div class="modal-footer">
                            
                              <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">Zavřít</button>
                              <button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="icalc_process_tag_edition(\''.$id.'\',\'' . $modalId . '_name_form\',\'' . $modalId . '_desc_form\')">Upravit</button>
                            </div>
                          </div>
                        </div>
                      </div>';
    }

    private static function configureServiceCreationModal($modalId): string
    {
        return  '<div class="modal mt-5 fade w-100 p-3" id="' . $modalId . '"  role="dialog">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body p-5">
                              <h4 class="modal-title">Vytvořit nový Tag</h4>
                              <form>
                              <div class="form-row">
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">Name</label>
                                  <input id="' . $modalId . '_name_form" type="text" class="form-control" placeholder="Service name">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">Description</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="Service description" >
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_id_form">Price</label>
                                  <input id="' . $modalId . '_id_form" type="number" class="form-control" placeholder="Service Price" >
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">Unit</label>
                                  <input id="' . $modalId . '_name_form" type="text" class="form-control" placeholder="Service Unit" >
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">Minimal Quantity</label>
                                  <input id="' . $modalId . '_name_form" type="number" class="form-control" placeholder="Service minimal quantity" >
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">Tag</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="Service tag" >
                                </div> </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">Display Type</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="Service display type" >
                                </div>
                              </div>
                              </div>
                                <div class="d-flex justify-content-end">
                                </div>
                            </form>
                            </div>
                            <div class="modal-footer">
                            
                              <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">Zavřít</button>
                              <button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="icalc_process_tag_creation(\'' . $modalId . '_name_form\',\'' . $modalId . '_desc_form\')">Uložit</button>
                            </div>
                          </div>
                        </div>
                      </div>';
    }




    public static function tagsConfiguration()
    {
        $tagsEP = ICALC_EP_PREFIX . '/tags';
        $url = get_rest_url(null, $tagsEP);

        console_log($url);
        $response = wp_remote_get($url);


        console_log($response);
        if (is_array($response)) {
            console_log("DATA RECIEVED");
            $body = wp_remote_retrieve_body($response);
            console_log($body);
            $data = json_decode($body);
            console_log($data);

            $tbody = "";
            $html = "";


            foreach ($data as $item) {
                $modalId = "tag" . $item->id . "modal";

                $modalData = [];
                $modalData["name"] = $item->name;
                $modalData["desc"] = $item->description;

                $html = $html . self::configuredModalTagEdit($modalData, $modalId, $item->id);


                $tbody = $tbody . '
                <tr>
                        <td>' . $item->id . '</td>
                        <td>' . $item->name . '</td>
                        <td>' . $item->description . '</td>
                        <td class="text-center"><button class="btn btn-info" data-toggle="modal" data-target="#' . $modalId . '"><span class="dashicons dashicons-edit"></span></button></td>
                        <td class="text-center"><button class="btn btn-danger" onclick="icalc_process_tag_deletion(' . $item->id . ',\'' . $item->name . '\')"><span class="dashicons dashicons-trash"></span></button></td>
                    </tr>';
            }
        } else {
            console_log("ERROR FETCHING");
        }

        $html = $html . self::configureTagCreationModal("tagCreationModal");
        $html = $html . '
    <div class="container pt-5">
        <!-- Additon button -->
        <span><button class="button mb-2" data-toggle="modal" data-target="#tagCreationModal">+</button> Add new Tag</span>
            <!-- Table -->
            <table class="table table-bordered table-striped table-hover col-12">
                <thead class="thead-dark">
                    <tr class="col-12">
                        <th class="p-2 m-2">ID</th>
                        <th class="p-2 m-2">Name</th>
                        <th class="p-2 m-2">Description</th>
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

    private static function configuredModalTagEdit($formFields, $modalId, $id)
    {
       return  '<div class="modal mt-5 fade w-100 p-3" id="' . $modalId . '" role="dialog">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body p-5">
                              <h4 class="modal-title">Upravit Tag</h4>
                              <form>
                              <div class="form-row">
                                <div class="col">
                                  <label for="' . $modalId . '_id_form">Id</label>
                                  <input id="' . $modalId . '_id_form" type="text" class="form-control" value="' . $id . '" readonly>
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">Name</label>
                                  <input id="' . $modalId . '_name_form" type="text" class="form-control" placeholder="Tag name" value="' . $formFields['name'] . '">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">Description</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="Tag description" value="' . $formFields['desc'] . '">
                                </div>
                              </div>
                                <div class="d-flex justify-content-end">
                                </div>
                            </form>
                            </div>
                            <div class="modal-footer">
                            
                              <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">Zavřít</button>
                              <button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="icalc_process_tag_edition(\''.$id.'\',\'' . $modalId . '_name_form\',\'' . $modalId . '_desc_form\')">Upravit</button>
                            </div>
                          </div>
                        </div>
                      </div>';
    }

    private static function configureTagCreationModal($modalId): string
    {
        return  '<div class="modal mt-5 fade w-100 p-3" id="' . $modalId . '"  role="dialog">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body p-5">
                              <h4 class="modal-title">Vytvořit nový Tag</h4>
                              <form>
                              <div class="form-row">
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">Name</label>
                                  <input id="' . $modalId . '_name_form" type="text" class="form-control" placeholder="Tag name">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">Description</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="Tag description" >
                                </div>
                              </div>
                                <div class="d-flex justify-content-end">
                                </div>
                            </form>
                            </div>
                            <div class="modal-footer">
                            
                              <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">Zavřít</button>
                              <button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="icalc_process_tag_creation(\'' . $modalId . '_name_form\',\'' . $modalId . '_desc_form\')">Uložit</button>
                            </div>
                          </div>
                        </div>
                      </div>';
    }
}

