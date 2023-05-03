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

namespace icalc\util;

class DataHelper
{
    public static function parseData(array $data): array
    {
        foreach ($data as $key => $value) {
            if ('object' === gettype($value) || 'array' === gettype($value)) {
                $data[$key] = self::parseData((array)$value);
            } elseif ('null' === $value) {
                $data[$key] = null;
            } else {
                $data[$key] = sanitize_text_field($value);
            }
        }

        return $data;
    }


    /**
     * Replace the 'NULL' string with NULL
     * @param string $query
     * @return string $query
     */
    public static function wp_db_null_value(string $query): string
    {
        return str_ireplace("'NULL'", "NULL", $query); //phpcs:ignore
    }


}