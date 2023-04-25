<?php

namespace icalc\db\model;

class IcalculationsDescription extends BaseDatabaseModel {
	public static function create_table(): bool {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name  = self::_tableName();
		$primary_key = self::$id;

		$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          name VARCHAR(255) NOT NULL,
                          description VARCHAR(255) NOT NULL,
                          body JSON NOT NULL,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) {$wpdb->get_charset_collate()};";

		return maybe_create_table( $table_name, $sql );
	}

	public static function insertNew(
		$name,
		$description,
		$body,
	) {
		$data = array(
			'name'        => $name,
			'description' => $description,
			'body'        => $body
		);
		return parent::insert( $data );
	}


}