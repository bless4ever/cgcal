<?php
class Cglog_model extends CI_Model {
    public function __construct() {
    }

    public function checkKey($key)
    {
        $this->db->where('diskCode', $key);
        $this->db->where('isAllowed', 1);
        $query = $this->db->get('cglog_key');
        if (count($query->result()) > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function log($key, $ip)
    {
        $l = new stdClass();
        $l->diskCode = $key;
        $l->ip = $ip;
        $this->db->insert('cglog_acc_log', $l);
    }
}
