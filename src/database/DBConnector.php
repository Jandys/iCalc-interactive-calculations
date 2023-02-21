<?php

namespace icalc\db;
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

class DBConnector
{

    private $wpdb;

    public function __construct()
    {
        $dbuser = defined('DB_USER') ? DB_USER : '';
        $dbpassword = defined('DB_PASSWORD') ? DB_PASSWORD : '';
        $dbname = defined('DB_NAME') ? DB_NAME : '';
        $dbhost = defined('DB_HOST') ? DB_HOST : '';
        console_log(array($dbhost, $dbname, $dbpassword));
        $this->wpdb = new \wpdb($dbuser, $dbpassword, $dbname, $dbhost);
    }


    public function createTable($tableName, $additionalSettings)
    {

        $table_name = $this->wpdb->prefix . $tableName;
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(50),
            email VARCHAR(50),
            PRIMARY KEY (id)
        );";

        return $this->wpdb->query( $sql );

    }
//        console_log("create table");
//
//        $table_name = $this->wpdb->prefix . $tableName;
//
//        $sql = "CREATE TABLE IF NOT EXISTS %s " . $additionalSettings;
//        $additionalSettings = preg_replace('/\n?\s+/', ' ', $additionalSettings);
//        $args = array($table_name);
//
//        console_log($additionalSettings);
//
//        return $this->execute($this->prepareQuery($sql, $args));
//    }


    private function prepareQuery($sql, $args)
    {
        console_log("prepare query");
        console_log($this->wpdb->prepare($sql, $args));
        return $this->wpdb->prepare($sql, $args);
    }

    private function execute($query)
    {
        $status = $this->wpdb->query($query);
        console_log("db update status " . $status);
        return $status;
    }


    public function test($sql, $args)
    {
        global $wpdb;

        $wpdb->get_results();

    }


}