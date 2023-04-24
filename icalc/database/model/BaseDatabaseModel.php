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
        return $wpdb->insert(self::_tableName(), $data);
    }


    public static function update($data, $id){
        global $wpdb;
        return $wpdb->update(self::_tableName(),$data,array( self::$id => $id ));
    }


    public static function last_id() {
        global $wpdb;
	    $result = $wpdb->get_results("SELECT id FROM ". self::_tableName() ." ORDER BY id DESC LIMIT 1;", OBJECT);
	    if ($result) {
			return $result[0]->id;
	    } else {
		    error_log("error while querying");

	    }
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
            sprintf( 'SELECT * FROM %s ORDER BY %s ASC', self::_tableName(), static::$id ), //phpcs:ignore
            ARRAY_A
        );
    }

    /**
     * Delete data from Table by ID
     * @param $id of an object
     * @return mixed
     */
    public static function delete( $value ) {
        global $wpdb;
        $sql = sprintf( 'DELETE FROM %s WHERE %s = %%s', self::_tableName(), static::$id );

        return $wpdb->query( $wpdb->prepare( $sql, $value ) ); //phpcs:ignore
    }



}