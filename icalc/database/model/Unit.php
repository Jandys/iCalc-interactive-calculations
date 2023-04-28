<?php

namespace icalc\db\model;

class Unit extends BaseDatabaseModel
{
    public static function create_table(): bool
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table_name = self::_tableName();
        $primary_key = self::$id;

        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
                  id INT AUTO_INCREMENT PRIMARY KEY,
                  unit VARCHAR(255) NOT NULL UNIQUE
                ) {$wpdb->get_charset_collate()};";

        return maybe_create_table($table_name, $sql);
    }

    public static function insertNew($unit)
    {
        if (parent::get('unit', $unit) != null) {
            return null;
        }

        $data = array('unit' => $unit);
        return parent::insert($data);
    }

    public static function deleteByName($value)
    {
        global $wpdb;
        $sql = sprintf('DELETE FROM %s WHERE %s = %%s', self::_tableName(), 'unit');

        return $wpdb->query($wpdb->prepare($sql, $value)); //phpcs:ignore
    }

    public static function autocomplete($value)
    {
        if (strlen($value) < 2) {
            return null;
        }

        global $wpdb;
        $sql = sprintf('SELECT unit FROM %s WHERE %s LIKE %%s', self::_tableName(), 'unit');

        return $wpdb->get_results($wpdb->prepare($sql, '%' . $value . '%'), ARRAY_A);
    }

}