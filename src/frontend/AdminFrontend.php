<?php

namespace icalc\fe;

class AdminFrontend
{


    public static function tagsConfiguration()
    {
        $html = '
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
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr>
                        <td>1</td>
                        <td>Tag</td>
                        <td>This is Tag</td>
                    </tr>
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

