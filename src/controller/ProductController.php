<?php

namespace iccalc\controller;

use icalc\util\DataHelper;

class ProductController
{

    static $errors = array();

    public static function create()
    {

        $data = DataHelper::parseData(json_decode(stripslashes($_POST['data'])));
        self::validation($data);

        if(!empty($data['product'])){
            wp_send_json_success(
                array(
                    'status' => 'success',
                )
            );
        }

        if (empty(self::$errors) && $_SERVER['REQUEST_METHOD'] === 'POST'){

        }
    }

    public static function read()
    {

    }

    public static function update()
    {

    }

    public static function delete()
    {

    }

    private static function validation($data)
    {
        if ( ! array_key_exists( 'id', $data ) || ! $data['id'] || empty( $data['id'] ) ) {
            self::$errors['id'] = __( 'No calculator id' );
        }
    }
}