<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $check = $this->input->post('check');
        if (!$check) {

            $url = "http://www.polchina.com.cn/user/login.php?preview=L3VzZXIvYm9keW1lbWJlci5waHA%3D";//随意一个可以post的

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            $postData = array('pid' => 'test4mbk', 'passwd' => 'mbk4test');//这个证件号是这个测试账号对应的，这个是真的。
            $cookie_jar = tempnam('./tmp','C4TEST');
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);//cookie
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);//post
            $output = curl_exec($ch);
            /*
            echo '<pre>';
            echo '<form action="test" method="post">';
            echo '<input name="check" type="text">';
            echo '<img src="http://www.polchina.com.cn/user/inc/validate_code_dis.php" />';
            echo '<input value="go" type="submit">';
            */
            $url = "http://www.polchina.com.cn/user/inc/validate_code_dis.php";
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);//上面那句position=0左边，1右边。
            $output = curl_exec($ch);
            print_r($output);
            /*
            $ch = curl_init();
            $url = "http://www.polchina.com.cn/user/pass_card.php";
            curl_setopt($ch, CURLOPT_URL, $url);
            parse_str('region=23&gid='.$targetGID.'&galaxies=muyang&role_position=0&agree=checkboxValue&security_select=1&certificate_type=0&certificate_id=13080219890223081X&security_question=0&answer=&buttonName=%E3%80%80+%E7%A1%AE%E5%AE%9A+%E3%80%80', $setNameOri);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);//上面那句position=0左边，1右边。
            curl_setopt($ch, CURLOPT_POSTFIELDS, $setNameOri);
            $output = curl_exec($ch);
            curl_close($ch);
            */
        } else {
            echo 'ok';
        }
        /*
        $url = "http://www.polchina.com.cn/user/login.php?preview=L3VzZXIvYm9keW1lbWJlci5waHA%3D";//随意一个可以post的

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $postData = array('pid' => 'test4mbk', 'passwd' => 'mbk4test');//这个证件号是这个测试账号对应的，这个是真的。
        $cookie_jar = tempnam('./tmp','C4TEST');
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);//cookie
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);//post
        $output = curl_exec($ch);

        $targetGID = 'ystart19812';//解锁加锁的GID

        $ch = curl_init();
        $url = "http://www.polchina.com.cn/user/pass_card.php";
        curl_setopt($ch, CURLOPT_URL, $url);
        parse_str('region=23&gid='.$targetGID.'&galaxies=muyang&role_position=0&agree=checkboxValue&security_select=1&certificate_type=0&certificate_id=13080219890223081X&security_question=0&answer=&buttonName=%E3%80%80+%E7%A1%AE%E5%AE%9A+%E3%80%80', $setNameOri);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);//上面那句position=0左边，1右边。
        curl_setopt($ch, CURLOPT_POSTFIELDS, $setNameOri);
        $output = curl_exec($ch);
        curl_close($ch);
        */
    }


}
