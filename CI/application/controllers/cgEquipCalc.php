<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CgEquip extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pet_model');
    }
    public function index()
    {


        $this->load->view('cgequip');
    }
    
}
