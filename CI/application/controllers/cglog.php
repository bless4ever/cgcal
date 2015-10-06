<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cglog extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('cglog_model');
    }
    public function checkreg($key = 0)
    {
        $this->cglog_model->log($key, $this->input->ip_address());
        if ($key && $this->cglog_model->checkKey($key)) {
            echo $this->config->item('response_valid_key');
            return;
        } else {
            return '';
        }
    }

}
