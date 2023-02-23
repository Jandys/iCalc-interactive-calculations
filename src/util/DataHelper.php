<?php

namespace icalc\util;

class DataHelper
{
    public static function parseData( array $data ): array
    {
        foreach ( $data as $key => $value ) {
            if ( 'object' === gettype( $value ) || 'array' === gettype( $value ) ) {
                $data[ $key ] = self::parseData( (array) $value );
            } elseif ( 'null' === $value ) {
                $data[ $key ] = null;
            } else {
                $data[ $key ] = sanitize_text_field( $value );
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