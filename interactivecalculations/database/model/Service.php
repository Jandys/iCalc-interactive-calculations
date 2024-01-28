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

namespace interactivecalculations\db\model;

class Service extends BaseDatabaseModel
{
    public static function create_table(): bool
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table_name = self::_tableName();

        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
                  id INT AUTO_INCREMENT PRIMARY KEY,
                  name VARCHAR(255) NOT NULL,
                  description VARCHAR(255) NOT NULL,
                  price DECIMAL(10,3) NOT NULL,
                  unitId INT NOT NULL,
                  min_quantity INT NOT NULL,
                  display_type VARCHAR(50) NOT NULL,
                  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) {$wpdb->get_charset_collate()};";

        return maybe_create_table($table_name, $sql);
    }

    public static function insertNew(
        $name,
        $description,
        $price,
        $unit,
        $min_quantity,
        $display_type
    )
    {
        $unitId = Unit::insertNew($unit);
        $data = array(
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'unitId' => $unitId,
            'min_quantity' => $min_quantity,
            'display_type' => $display_type
        );

        return parent::insert($data);
    }

    public static function updateById(
        $id,
        $name,
        $description,
        $price,
        $unit,
        $min_quantity,
        $display_type
    )
    {
        $unitId = Unit::insertNew($unit);
        $data = array(
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'unitId' => $unitId,
            'min_quantity' => $min_quantity,
            'display_type' => $display_type
        );

        return parent::update($data, $id);
    }

    public static function get_all_with_unit()
    {
        global $wpdb;
        $unitTable = Unit::_tableName();

        return $wpdb->get_results(
            $wpdb->prepare('SELECT * FROM %s JOIN %s ON %s.unitId = %s.id ORDER BY %s ASC', self::_tableName(), $unitTable, self::_tableName(), $unitTable, self::_tableName() . "." . static::$id), //phpcs:ignore
            ARRAY_A
        );
    }
}