<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        echo '<pre>';
        $url = "http://www.polchina.com.cn/user/login.php?preview=L3VzZXIvcGFzc19jYXJkLnBocA==";
        //$url = "http://www.cgcal.com";
        $urls = array();
        $post = array();
        for ($i=0; $i < 10; $i++) {
            $urls[] = $url.'&spa='.$i;
            if ($i != 8) {
                $post[] = array('pid' => 'test4mbk', 'passwd' => 'mbk4test'.$i);
            } else {
                $post[] = array('pid' => 'test4mbk', 'passwd' => 'mbk4test');
            }
        }
        $res = $this->curl($urls, $post);
        print_r($res);
        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $output = array();
        for ($i=0; $i < 1000; $i++) {
            $postData = array('pid' => 'test4mbk', 'passwd' => 'mbk4test'.$i);
            $i == 588 && $postData = array('pid' => 'test4mbk', 'passwd' => 'mbk4test');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            $output[] = strlen(curl_exec($ch));
        }
        curl_close($ch);
        print_r($output);
        //print_r(json_encode($output));
        */
    }

    function curl($urls,$post) {
        $queue = curl_multi_init();
        $map = array();
        foreach ($urls as $key => $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post[$key]);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOSIGNAL, true);

            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
            curl_setopt($ch, CURLOPT_PROXY, "14.139.172.171"); //代理服务器地址
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128); //代理服务器端口
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式

            curl_multi_add_handle($queue, $ch);
            $map[(string) $ch] = $url;
        }
        $responses = array();
        do {
            while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;
            if ($code != CURLM_OK) { break; }
            while ($done = curl_multi_info_read($queue)) {
                $error = curl_error($done['handle']);
                $results = curl_multi_getcontent($done['handle']);
                $results = strlen($results);
                $responses[$map[(string) $done['handle']]] = compact('error', 'results');
                curl_multi_remove_handle($queue, $done['handle']);
                curl_close($done['handle']);
            }
            if ($active > 0) {
                curl_multi_select($queue, 0.5);
            }
        } while ($active);
        curl_multi_close($queue);
        return $responses;
    }
}
