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

class BaseDatabaseModel
{
    public static $id = 'id';
    protected static $prefix = 'interactivecalculations_';


    /**
     * Retrieves the table name for the database table associated with the model class.
     *
     * @return string The table name for the database table associated with the model class.
     * @since 1.0.0
     */
    public static function _tableName(): string
    {
        global $wpdb;

        // Extract the name of the model class and concatenate the table prefix with the actual name of the table.
        $className = explode('\\', strtolower(get_called_class()));
        $tableName = self::$prefix . end($className);

        // Return the table name with the WordPress database prefix.
        return $wpdb->prefix . $tableName;
    }

    /**
     * Inserts a new row into the database table associated with the model class using the provided data.
     *
     * @param array $data The data to insert into the database table.
     * @return int|false The number of rows affected by the query, or false if the query failed.
     * @since 1.0.0
     */
    public static function insert($data)
    {
        global $wpdb;
        return $wpdb->insert(self::_tableName(), $data);
    }

    /**
     * Updates an existing row in the database table associated with the model class with the provided data and ID.
     *
     * @param array $data The data to update in the database table.
     * @param int $id The ID of the row to update.
     *
     * @return int|false The number of rows affected by the query, or false if the query failed.
     * @since 1.0.0
     */
    public static function update($data, $id)
    {
        global $wpdb;
        return $wpdb->update(self::_tableName(), $data, array(self::$id => $id));
    }

    /**
     * Retrieves the ID of the last row inserted into the database table associated with the model class.
     *
     * @return int The ID of the last row inserted into the database table associated with the model class, or -1 if an error occurred while querying for the ID.
     * @since 1.0.0
     */
    public static function last_id()
    {
        global $wpdb;
        $result = $wpdb->get_results("SELECT id FROM " . self::_tableName() . " ORDER BY id DESC LIMIT 1;", OBJECT);

        // Log an error and return -1 if the query did not return any results.
        if ($result) {
            return $result[0]->id;
        } else {
            error_log("Error occurred while querying for the last ID.");
            return -1;
        }
    }


    /**
     * Generates a SQL query to fetch a row from the database table associated with the model class with the provided key-value pair.
     *
     * @param string $key The field name to match.
     * @param mixed $value The field value to match.
     * @return string The prepared SQL query to fetch the row with the provided key-value pair.
     * @since 1.0.0
     */
    private static function _fetch_sql($key, $value)
    { //phpcs:ignore
        global $wpdb;
        $sql = 'SELECT * FROM %s WHERE %s = %s';
        return $wpdb->prepare($sql, self::_tableName(), $key, $value); //phpcs:ignore
    }

    /**
     * Retrieves a single row from the database table associated with the model class that matches the provided key-value pair.
     *
     * @param string $key The field name to match.
     * @param mixed $value The field value to match.
     * @return object|null The first row of the query result set as an object, or null if the query returned no rows.
     * @since 1.0.0
     */
    public static function get($key, $value)
    {
        global $wpdb;
        return $wpdb->get_row(self::_fetch_sql($key, $value)); //phpcs:ignore
    }

    /**
     * Retrieves all rows from the database table associated with the model class.
     *
     * @return array An array of associative arrays, each representing a row from the table.
     * @since 1.0.0
     */
    public static function get_all()
    {
        global $wpdb;
        return $wpdb->get_results(
            $wpdb->prepare('SELECT * FROM %s ORDER BY %s ASC', self::_tableName(), static::$id), //phpcs:ignore
            ARRAY_A
        );
    }

    /**
     * Deletes a row from the database table associated with the model class that matches the provided $value.
     *
     * @param mixed $value The value to match against the static $id field of the model class.
     * @return int The number of rows affected by the delete query.
     * @since 1.0.0
     */
    public static function delete($value)
    {
        global $wpdb;
        $sql = 'DELETE FROM %s WHERE %s = %s';

        return $wpdb->query($wpdb->prepare($sql, self::_tableName(), static::$id, $value)); //phpcs:ignore
    }

    public static function clearAll()
    {
        global $wpdb;
        $table_name = self::_tableName();

        $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS %s", $table_name));
    }
}