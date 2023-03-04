<?php

namespace icalc\db\model;

class Service extends BaseDatabaseModel
{
    public static function create_table(): bool
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table_name  = self::_tableName();
        $primary_key = self::$id;

        $sql = "CREATE TABLE IF NOT EXISTS ".$table_name." (
                  id INT AUTO_INCREMENT PRIMARY KEY,
                  name VARCHAR(255) NOT NULL,
                  description VARCHAR(255) NOT NULL,
                  price DECIMAL(10,2) NOT NULL,
                  unit VARCHAR(50) NOT NULL,
                  tag VARCHAR(255) NOT NULL,
                  min_quantity INT NOT NULL,
                  display_type VARCHAR(50) NOT NULL
                ) {$wpdb->get_charset_collate()};";

        return maybe_create_table($table_name, $sql);
    }

    public static function insertNew($name,
                                     $description,
                                     $price,
                                     $unit,
                                     $tag,
                                     $min_quantity,
                                     $display_type)
    {
        $data = array('name' => $name,
            'description' => $description,
            'price' => $price,
            'unit' => $unit,
            'tag' => $tag,
            'min_quantity' => $min_quantity,
            'display_type' => $display_type);
        return parent::insert($data);
    }

    public static function updateById($id,
                                      $name,
                                      $description,
                                      $price,
                                      $unit,
                                      $tag,
                                      $min_quantity,
                                      $display_type)
    {
        $data = array('name' => $name,
            'description' => $description,
            'price' => $price,
            'unit' => $unit,
            'tag' => $tag,
            'min_quantity' => $min_quantity,
            'display_type' => $display_type);
        return parent::update($data, $id);
    }

}