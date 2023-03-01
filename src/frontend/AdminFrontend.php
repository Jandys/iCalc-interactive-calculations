<?php

namespace icalc\fe;

use function icalc\util\console_log;

class AdminFrontend
{


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


        $html = $html . '
    <div class="container pt-5">
        <!-- Additon button -->
        <span><button class="button mb-2">+</button> Add new Tag</span>
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


}

