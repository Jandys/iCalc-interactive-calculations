<?php

namespace icalc\db\model;

class Tag extends BaseDatabaseModel
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
                  description VARCHAR(255) NOT NULL
                ) {$wpdb->get_charset_collate()};";

        return maybe_create_table( $table_name, $sql );
    }

    public static function insertNew($name, $description)
    {
        $data = array('name' => $name, 'description' => $description);
        return parent::insert($data);
    }

    public static function updateById($id, $name, $description){
        $data = array('name' => $name, 'description' => $description);
        return parent::update($data, $id);
    }
}