<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $now = date('Y-m-d H:i:s');
        $today11 = date('Y-m-d 23:00:00');
        $deadNow = date('Y-m-d 20:00:00', strtotime('+28 days 4 hours '.$now));
        $deadToday11 = date('Y-m-d 20:00:00', strtotime('+28 days 4 hours '.$today11));

        echo $now.' '.$deadNow.'<br>';
        echo $today11.' '.$deadToday11.'<br>';

    }
}
