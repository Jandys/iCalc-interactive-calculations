<?php


namespace icalc\db\model;
class BaseDatabaseModel
{
    public static $id = 'id';
    protected static $prefix = 'icalc_';


    public static function _tableName(): string
    {
        global $wpdb;
        $className = explode('\\', strtolower(get_called_class()));
        $tableName = self::$prefix . end($className);
        return $wpdb->prefix . $tableName;
    }

    public static function insert($data)
    {
        global $wpdb;

        add_filter('query', array(self::class, 'wp_db_null_value'));

        $result = $wpdb->insert(self::_tableName(), $data);
        remove_filter('query', array(self::class, 'wp_db_null_value'));
        return $result;
    }


    public static function last_id() {
        global $wpdb;
        return $wpdb->insert_id;
    }


    /**
     * SQL Fetch from Table
     * @param $key
     * @param $value
     * @return mixed
     */
    private static function _fetch_sql( $key, $value ) { //phpcs:ignore
        global $wpdb;
        $sql = sprintf( 'SELECT * FROM %s WHERE %s = %%s', self::_tableName(), $key );
        return $wpdb->prepare( $sql, $value ); //phpcs:ignore
    }

    /**
     * Get Row by ID
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function get( $key, $value ) {
        global $wpdb;
        return $wpdb->get_row( self::_fetch_sql( $key, $value ) ); //phpcs:ignore
    }

    /**
     * Get All Rows
     */
    public static function get_all() {
        global $wpdb;
        return $wpdb->get_results(
            sprintf( 'SELECT * FROM %s ORDER BY %s DESC', self::_tableName(), static::$primary_key ), //phpcs:ignore
            ARRAY_A
        );
    }

    /**
     * Delete data from Table by ID
     * @param $id
     * @return mixed
     */
    public static function delete( $value ) {
        global $wpdb;
        $sql = sprintf( 'DELETE FROM %s WHERE %s = %%s', self::_table(), static::$primary_key );

        return $wpdb->query( $wpdb->prepare( $sql, $value ) ); //phpcs:ignore
    }



}