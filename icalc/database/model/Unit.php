<?php
/*
 *
 *   This file is part of the 'iCalc - Interactive Calculations' project.
 *
 *   Copyright (C) 2023, Jakub Jandák
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 *
 */

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