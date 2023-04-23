<?php
namespace icalc\fe;

use function icalc\util\console_log;

class CalculationsDescriptionsAdminFrontend extends AbstractAdminFrontend
{

    public static function configuration()
    {
        self::populateIcalcJSData();
        $data = self::callGetOnEPWithAuthCookie('/icalculation-descriptions');

        if(is_null($data)) {
            error_log("ERROR Fetching data from API");
        }
        $tbody = "";
        $html = "";

        foreach ($data as $item) {
            $modalId = "product" . $item->id . "modal";
            $modalData = [];
            $modalData["name"] = $item->name;
            $modalData["desc"] = $item->description;
            $modalData["body"] = $item->body;
            $modalData["created_at"] = $item->created_at;
            $modalData["modified_at"] = $item->modified_at;


            $tbody = $tbody . '
            <tr>
                    <td>' . $item->id . '</td>
                    <td>' . $item->name . '</td>
                    <td>' . $item->description . '</td>
                    <td class="icalc-long-text-clipping">' . $item->body . '</td>
                    <td>' . $item->created_at . '</td>
                    <td>' . $item->modified_at . '</td>
                    <td class="text-center"><button class="btn btn-info" onclick="icalc_process_calculation_edit_action(\'' . $item->id . '\')"><span class="dashicons dashicons-edit"></span></button></td>
                    <td class="text-center"><button class="btn btn-danger" onclick="icalc_process_calculation_delete_action(' . $item->id . ',\'' . $item->name . '\')"><span class="dashicons dashicons-trash"></span></button></td>
                </tr>';
        }

      $html = $html . '
    <div class="container pt-5">
            <!-- Table -->
            <table class="table table-bordered table-striped table-hover col-12">
                <thead class="thead-dark">
                    <tr class="col-12">
                        <th class="p-2 m-2">'.__("ID").'</th>
                        <th class="p-2 m-2">'.__("Name").'</th>
                        <th class="p-2 m-2">'.__("Description").'</th>
                        <th class="p-2 m-2 icalc-long-text-clipping">'.__("Body").'</th>
                        <th class="p-2 m-2">'.__("Created At").'</th>
                        <th class="p-2 m-2">'.__("Modified At").'</th>
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

        return $html;
    }
}