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

class Icalculations extends BaseDatabaseModel
{
    public static function create_table(): bool
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table_name = self::_tableName();
        $primary_key = self::$id;

        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          calculationDescriptionId INT,
                          body JSON NOT NULL,
                          userId VARCHAR(40),
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        ) {$wpdb->get_charset_collate()};";

        return maybe_create_table($table_name, $sql);
    }

    public static function insertNew(
        $calculationId,
        $body,
        $userId
    )
    {
        $data = array(
            'calculationDescriptionId' => $calculationId,
            'body' => $body,
            'userId' => $userId
        );

        return parent::insert($data);
    }
}