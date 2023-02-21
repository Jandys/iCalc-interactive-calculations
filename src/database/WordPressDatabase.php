<?php
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

class WordPressDatabase {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function create($table, $data) {
        $result = $this->wpdb->insert($table, $data);
        return $result;
    }

    public function read($table, $id) {
        $result = $this->wpdb->get_row("SELECT * FROM $table WHERE id = $id");
        return $result;
    }

    public function update($table, $data, $where) {
        $result = $this->wpdb->update($table, $data, $where);
        return $result;
    }

    public function delete($table, $id) {
        $result = $this->wpdb->delete($table, array('id' => $id));
        return $result;
    }
}