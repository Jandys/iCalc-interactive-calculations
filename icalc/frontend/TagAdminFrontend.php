<?php

namespace icalc\fe;


class TagAdminFrontend extends AbstractAdminFrontend
{

    public static function configuration()
    {
        self::populateIcalcJSData();

        $data = self::callGetOnEPWithAuthCookie('/tags');

        if(is_null($data)) {
            error_log("ERROR Fetching data from API");
        }

        $tbody = "";
        $html = "";


        foreach ($data as $item) {
            $modalId = "tag" . $item->id . "modal";

            $modalData = [];
            $modalData["name"] = $item->name;
            $modalData["desc"] = $item->description;

            $html = $html . self::configuredModalEdit( $modalId, $item->id, $modalData);


            $tbody = $tbody . '
            <tr>
                    <td>' . $item->id . '</td>
                    <td>' . $item->name . '</td>
                    <td>' . $item->description . '</td>
                    <td class="text-center"><button class="btn btn-info" data-toggle="modal" data-target="#' . $modalId . '"><span class="dashicons dashicons-edit"></span></button></td>
                    <td class="text-center"><button class="btn btn-danger" onclick="icalc_process_tag_deletion(' . $item->id . ',\'' . $item->name . '\')"><span class="dashicons dashicons-trash"></span></button></td>
                </tr>';
        }

        $html = $html . self::configureCreationModal("tagCreationModal");
        $html = $html . '
    <div class="container pt-5">
        <!-- Additon button -->
        <span><button class="button mb-2" data-toggle="modal" data-target="#tagCreationModal">+</button> '.__("Add New Tag").'</span>
            <!-- Table -->
            <table class="table table-bordered table-striped table-hover col-12">
                <thead class="thead-dark">
                    <tr class="col-12">
                        <th class="p-2 m-2">'.__("ID").'</th>
                        <th class="p-2 m-2">'.__("Name").'</th>
                        <th class="p-2 m-2">'.__("Description").'</th>
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
        return  '<div class="modal mt-5 fade w-100 p-3" id="' . $modalId . '" role="dialog">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body p-5">
                              <h4 class="modal-title">'.__("Edit Tag").'</h4>
                              <form id="' . $modalId . '_form">
                              <div class="form-row">
                                <div class="col">
                                  <label for="' . $modalId . '_id_form">Id</label>
                                  <input id="' . $modalId . '_id_form" type="text" class="form-control" value="' . $id . '" readonly>
                                </div>
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">'.__("Name").'</label>
                                  <input id="' . $modalId . '_name_form" type="text" class="form-control" placeholder="'.__("Tag Name").'" value="' . $formFields['name'] . '">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">'.__("Description").'</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="'.__("Tag description").'" value="' . $formFields['desc'] . '">
                                </div>
                              </div>
                                <div class="d-flex justify-content-end">
                                </div>
                            </form>
                            </div>
                            <div class="modal-footer">
                            
                              <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">'.__("Close").'</button>
                              <button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="icalc_process_tag_edition(\''.$id.'\',\'' . $modalId . '_name_form\',\'' . $modalId . '_desc_form\')">'.__("Edit").'</button>
                            </div>
                          </div>
                        </div>
                      </div>';
    }

    public static function configureCreationModal($modalId): string
    {
        return  '<div class="modal mt-5 fade w-100 p-3" id="' . $modalId . '"  role="dialog">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body p-5">
                              <h4 class="modal-title">'.__("Create New Tag").'</h4>
                              <form id="' . $modalId . '_form">
                              <div class="form-row">
                                <div class="col">
                                  <label for="' . $modalId . '_name_form">'.__("Name").'</label>
                                  <input id="' . $modalId . '_name_form" type="text" class="form-control" placeholder="'.__("Tag Name").'">
                                </div>
                                 <div class="col">
                                  <label for="' . $modalId . '_desc_form">'.__("Description").'</label>
                                  <input id="' . $modalId . '_desc_form" type="text" class="form-control" placeholder="'.__("Tag Description").'" >
                                </div>
                              </div>
                                <div class="d-flex justify-content-end">
                                </div>
                            </form>
                            </div>
                            <div class="modal-footer">
                            
                              <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">'.__("Close").'</button>
                              <button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="icalc_process_tag_creation(\'' . $modalId . '_name_form\',\'' . $modalId . '_desc_form\')">'.__("Save").'</button>
                            </div>
                          </div>
                        </div>
                      </div>';    }



}