<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cgpetcalc extends CI_Controller {

        public function __construct()
        {
            parent::__construct();
            $this->load->model('pet_model');
        }
        public function index()
        {
            $this->load->helper('vector');
            $data['petData'] ='';
            $data['petResult'] ='';
            $data['petName'] = '';
            $data['petGrade'] = '';
            $data['petSelect'] = '';
            $data['petLv'] = '';
            $data['rBP'] = '';
            $data['addBPMethod'] = 'no';

            $petName = $this->input->post('petList');
            if (!$petName) {
                $petName = $this->input->post('petName');
            }
            $names = $this->pet_model->getGradesBySearch($petName);
            if (count($names) == 0) {
                $data['petName'] = $petName;

            } elseif (count($names) == 1) {
                $petName = $names[0]->name;
                $petGrade = $this->pet_model->petKindToGrade($names[0]);
                $petLv = $this->input->post('petLv');

                $data['petLv'] = $petLv;
                $data['petName'] = $petName;
                $data['petGrade'] = implode(' ', $petGrade);
            } else {
                $petNames = array();
                foreach ($names as $key => $name) {
                    $petNames [] = $name->name;
                }

                $data['petSelect']=$petNames;
                $data['petName'] = $petName;

            }
            $petData = $this->input->post('petData');
            $data['petData'] = $petData;
            if (!$petData || count($names) != 1) {

            } else {
                if ($petLv == 1) {
                    $data['petResult'] = $this->pet_model->calcLv1Pet($petData, $petGrade);
                } else {
                    $addBPMethod = $this->input->post('addBPMethod');
                    $rBP = $this->input->post('rBP');
                    $data['rBP'] = $rBP;
                    $data['addBPMethod'] = $addBPMethod;

                    if ($addBPMethod != 'hun') {
                        $data['petResult'] = $this->pet_model->calcLvHighPetPure($petData, $petGrade, $petLv, $addBPMethod, $rBP);
                    } else {
                        $data['petResult'] = array('功能稍后增加！');
                    }

                }



            }
            $this->load->view('cgpetcalc',$data);
            return;

    }
    public function testGenPet()
    {
        echo '<pre>';
        echo '<meta charset = utf8>';
        $name = '螳螂';
        $lv = 85;
        $diffGradeStr = '14223';
        $randomGradeStr = '20125';
        $addBP = '0,84,0,0,0';
        $grade = $this->pet_model->petKindToGrade($this->pet_model->getGradesBySearch($name)[0]);
        echo "grade";print_r($grade);
        $resultPet = $this->pet_model->genPet($grade, $lv, $diffGrade, $randomGrade, $addBP);
        print_r($resultPet);

    }
    public function tnt()
    {
        for ($i=1; $i < 100; $i++) {
            echo $this->pet_model->tnt($i).'<br>';
            //echo $i.':'.$this->pet_model->tnt($i).'::'.($this->pet_model->tnt($i)/$i).'<br>';
        }

    }
    public function test4speed()
    {
        echo '<pre>';
        echo '<meta charset = utf8>';
        $this->load->helper('vector');
        $pet = '1947 2899 270 254 184 93 星菇 mo 242 158::lv93 00002 162 52 40 52 242';
        echo $pet;
        echo '<br>';
        $petData = explode(' ', $pet);
        $grade = $this->pet_model->getGradesBySearch($petData[6]);

        $prop = array($petData[0],$petData[1],$petData[2],$petData[3],$petData[4]);
        $propmax = array($petData[0]+1,$petData[1]+1,$petData[2]+1,$petData[3]+1,$petData[4]+1);
        $lv = $petData[5];

        $t1 = microtime(true);
        $bp = $this->pet_model->getBPByProp($prop);
        $bpmax = $this->pet_model->getBPByProp(plus($prop, array(1,1,1,1,1)));
        echo ((microtime(true) - $t1)*1000).'ms for directly MYSQL, bp range by prop range<br>';

        $t1 = microtime(true);
        $bpcal = $this->pet_model->getBPByPropByCalc($prop);
        $bpcalmax = $this->pet_model->getBPByPropByCalc(plus($prop, array(1,1,1,1,1)));
        echo ((microtime(true) - $t1)*1000).'ms for directly calc, bp range by prop range<br>';

        $t1 = microtime(true);
        $bpsqls = array();
        for ($i=0; $i < 32; $i++) {
            $bpsqls[$i] = $this->pet_model->getBPByProp(plus($prop, array($i/16%2,$i/8%2,$i/4%2,$i/2%2,$i%2)));
        }
        $bpsqlmax = clone $bpsqls[0];
        $bpsqlmin = clone $bpsqls[0];
        for ($i=0; $i < 32; $i++) {
            foreach ($bpsqls[$i] as $key => $value) {
                $bpsqlmax->$key = max($bpsqlmax->$key, $value);
                $bpsqlmin->$key = min($bpsqlmin->$key, $value);
            }
        }
        echo ((microtime(true) - $t1)*1000).'ms for directly MYSQL, maxium and minum searched.<br>';


        $t1 = microtime(true);
        $bpcals = array();
        for ($i=0; $i < 32; $i++) {
            $bpcals[$i] = $this->pet_model->getBPByPropByCalc(plus($prop, array($i/16%2,$i/8%2,$i/4%2,$i/2%2,$i%2)));
        }
        $bpcalmax = $bpcals[0];
        $bpcalmin = $bpcals[0];
        for ($i=0; $i < 32; $i++) {
            foreach ($bpcals[$i] as $key => $value) {
                $bpcalmax[$key] = max($bpcalmax[$key], $value);
                $bpcalmin[$key] = min($bpcalmin[$key], $value);
            }
        }
        echo ((microtime(true) - $t1)*1000).'ms for directly cal, maxium and minum searched.<br>';



        $t1 = microtime(true);
        $bprange = $this->pet_model->getBPRangeByProp($prop);
        $bpsql2min = $bprange[0];
        $bpsql2max = $bprange[1];
        echo ((microtime(true) - $t1)*1000).'ms for directly MYSQL2, maxium and minum searched.<br>';



        die(0);
    }
    public function test()
    {
        echo '<pre>';
        echo '<meta charset = utf8>';
        $this->load->helper('vector');
        $pets = array();
        $pets[] = '1947 2899 270 254 184 93 星菇 0 mo 242 158::lv93 00002 162 52 40 52 242';
        $pets[] = '309 230 110 59 46 10 改造僵尸 0 gong 加攻';
        $pets[] = '430 254 138 84 71 17 改造猎豹 16 no 00321  17级未加';
        $pets[] = '235 300 55 113 44 10 潜盾 9 no 未加1D 1血';
        $pets[] = '553 461 219 153 118 32 螳螂 31 no';
        $pets[] = '391 327 158 113 88 21 螳螂 20 no';
        $pets[] = '244 206 103 76 61 11 螳螂 10 no';
        $pets[] = '156 133 70 53 44 5 螳螂 4 no';
        $pets[] = '97 85 47 39 34 1 螳螂 0 no';


        foreach ($pets as $pet) {
            echo $pet;
            echo '<br>';
            $petData = explode(' ', $pet);
            $grade = $this->pet_model->getGradesBySearch($petData[6]);


            $prop = array($petData[0],$petData[1],$petData[2],$petData[3],$petData[4]);
            $lv = $petData[5];
            $rBP = $petData[7];
            $addBPMethod = $petData[8];
            $addBP = $this->pet_model->getAddBPByCond($lv, $addBPMethod, $rBP);
            if (! $addBP) {
                echo '加点不合法！';
                continue;
            }
            $bp = $this->pet_model->getBPByProp($prop);
            $bprange = $this->pet_model->getBPRangeByProp($prop);
            $bpsql2min = minus($bprange[0], $addBP);
            $bpsql2max = minus($bprange[1], $addBP);
            $grade = array('xue'=>$grade[0]->xue,'gong'=>$grade[0]->gong,'fang'=>$grade[0]->fang,'min'=>$grade[0]->min,'mo'=>$grade[0]->mo);
            //echo implode('/', $grade).'<br>';

            $maxdg = array();
            $mindg = array();
            foreach ($bpsql2max as $key => $value ) {
                $tmp = $value / ( ($lv-1) * 0.04 +0.2);
                //$tmp = ($value/(($lv-1)*$this->pet_model->tnt($grade[$key])/($grade[$key])+0.2));
                $maxdg[$key] = intval($tmp);
                $r = $value - ($lv-1)*$this->pet_model->tnt($tmp);
                //echo $r.'/';
            }
            //echo '<br>';
            foreach ($bpsql2min as $key => $value ) {
                $tmp = $value / ( ($lv-1) * 0.042 +0.2);
                //$tmp = ( $value / ( ($lv-1) * $this->pet_model->tnt($grade[$key]) / ($grade[$key]) + 0.2 ) );
                $mindg[$key] = intval($tmp);
                $r = $value - ($lv-1)*$this->pet_model->tnt($tmp);
                //echo $tmp.'/';
            }
            //print_r($mindg);
            //print_r($maxdg);

            for ($xue=$mindg['xue']; $xue <=$maxdg['xue'] ; $xue++) {
                for ($gong=$mindg['gong']; $gong <=$maxdg['gong'] ; $gong++) {
                    for ($fang=$mindg['fang']; $fang <=$maxdg['fang'] ; $fang++) {
                        for ($min=$mindg['min']; $min <=$maxdg['min'] ; $min++) {
                            for ($mo=$mindg['mo']; $mo <=$maxdg['mo'] ; $mo++) {
                                $summinr = 0;//每项bp扣除这个档次的bp的成长值和初始值的最大值剩下的总和的最小值，应该小于等于2+加点总和
                                $summaxr = 0;
                                $rg = array();//随机档
                                foreach ($bpsql2min as $key => $value ) {
                                    $rg[$key] = array();
                                    $rmin= $bpsql2min[$key] - ($lv-1)*$this->pet_model->tnt($$key) - 0.2*$$key;
                                    $summinr +=$rmin;
                                    $rmax= $bpsql2max[$key] - ($lv-1)*$this->pet_model->tnt($$key) - 0.2*$$key;
                                    $summaxr +=$rmax;
                                    //echo $rmin.'/'.$rmax.'<br>';
                                    for ($i=intval($rmin/0.2)*0.2; $i <= intval($rmax/0.2)*0.2 ; $i += 0.2) {
                                        $rg[$key][] = $i;
                                    }
                                }
                                if ($summinr<=2 && $summaxr>=2) {

                                    for ($i=0; $i < count($rg['xue']) ; $i++) {
                                        for ($j=0; $j < count($rg['gong']) ; $j++) {
                                            for ($k=0; $k < count($rg['fang']) ; $k++) {
                                                for ($m=0; $m < count($rg['min']) ; $m++) {
                                                    for ($n=0; $n < count($rg['mo']) ; $n++) {
                                                        $sum = $rg['xue'][$i] + $rg['gong'][$j] + $rg['fang'][$k] + $rg['min'][$m] + $rg['mo'][$n];
                                                        if ($sum == 2) {

                                                            $rgrade = ($rg['xue'][$i]*5).''.($rg['gong'][$j]*5).''.($rg['fang'][$k]*5).''.($rg['min'][$m]*5).''.($rg['mo'][$n]*5);
                                                            $dgrade = (-$xue+$grade['xue']).''. (-$gong+$grade['gong']).''. (-$fang+$grade['fang']).''. (-$min+$grade['min']).''. (-$mo+$grade['mo']);
                                                            $pettemp = $this->pet_model->genPet($grade, $lv, $dgrade, $rgrade, implode(',',$addBP));
                                                            if ($pettemp['hp'] == $prop[0] && $pettemp['mp'] == $prop[1] && $pettemp['atk'] == $prop[2] && $pettemp['def'] == $prop[3] && $pettemp['egi'] == $prop[4]) {
                                                                //echo ($rg['xue'][$i]*5).'/'.($rg['gong'][$j]*5).'/'.($rg['fang'][$k]*5).'/'.($rg['min'][$m]*5).'/'.($rg['mo'][$n]*5).':::';
                                                                echo $rgrade.'::'.$dgrade.'<br>';

                                                                //echo ($xue-$grade['xue']).'/'. ($gong-$grade['gong']).'/'. ($fang-$grade['fang']).'/'. ($min-$grade['min']).'/'. ($mo-$grade['mo']).'/'.'<br>';

                                                            }

                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
            }
            //die(0);
        }//end foreach pet
    }
}
