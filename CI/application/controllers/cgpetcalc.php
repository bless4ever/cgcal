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
                $petLv = 1;
                $dataBased = array();
                foreach ($petGrade as $key => $g) {
                    $dataBased [$key]= max($g-4,0)*20;
                }
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
            if (!$petData || count($names) != 1) {

            } else {
                $data['petData'] = $petData;
                $petData = explode(' ',$petData);
                $remain = array();
                $remain['hp'] = $petData[0]*1000 - 20*1000 - $this->pet_model->getPropFromBP('hp', $dataBased);
                $remain['mp'] = $petData[1]*1000 - 20*1000 - $this->pet_model->getPropFromBP('mp', $dataBased);
                $remain['atk'] = $petData[2]*1000 - 20*1000 - $this->pet_model->getPropFromBP('atk', $dataBased);
                $remain['def'] = $petData[3]*1000 - 20*1000 - $this->pet_model->getPropFromBP('def', $dataBased);
                $remain['egi'] = $petData[4]*1000 - 20*1000 - $this->pet_model->getPropFromBP('egi', $dataBased);
                if (count($petData) == 7) {
                    $remain['spr'] = $petData[5]*1000 - 100*1000 - $this->pet_model->getPropFromBP('spr', $dataBased);
                    $remain['rec'] = $petData[6]*1000 - 100*1000 - $this->pet_model->getPropFromBP('rec', $dataBased);
                }

                $results = $this->pet_model->getOriginResultByRemain($remain);
                $outs = 0;
                $petResult = array();
                foreach ($results as $key => $result) {
                    $outs += $result->outs;
                }

                foreach ($results as $key => $result) {
                    $petResult []= $this->pet_model->sumGrade($result->grade).'d :: '.$result->grade.',    '.round($result->outs/$outs*100, 2).'%<br>';
                }
                $data['petResult'] = $petResult?$petResult:array('无解！！');

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
        for ($i=0; $i < 100; $i++) {
            echo $i.':'.$this->pet_model->tnt($i).'<br>';
        }

    }
    public function test()
    {
        echo '<pre>';
        echo '<meta charset = utf8>';
        $pets = array();
        $pets[] = '1947 2899 270 254 184 93 星菇 mo 242 158::lv93 00002 162 52 40 52 242';
        $pets[] = '309 230 110 59 46 10 改造僵尸 gong 加攻';
        $pets[] = '430 254 138 84 71 17 改造猎豹 no 00321  17级未加';
        $pets[] = '235 300 55 113 44 10 潜盾 no 未加1D 1血';
        foreach ($pets as $pet) {
            echo $pet;
            echo '<br>';
            $petData = explode(' ', $pet);
            $grade = $this->pet_model->getGradesBySearch($petData[6]);

            $prop = array($petData[0],$petData[1],$petData[2],$petData[3],$petData[4]);
            $propmax = array($petData[0]+1,$petData[1]+1,$petData[2]+1,$petData[3]+1,$petData[4]+1);
            $lv = $petData[5];
            $t1 = microtime(true);
            $bp = $this->pet_model->getBPByProp($prop);
            echo (microtime(true) - $t1).'ms for directly MYSQL<br>';
            $t1 = microtime(true);
            $bpcal = $this->pet_model->getBPByPropByCalc($prop);
            echo (microtime(true) - $t1).'ms for directly calc';
            print_r($bp);
            print_r($bpcal);

            die(0);
            $bpmin = array();

            $bpmax = $this->pet_model->getBPByProp($propmax);
            foreach ($bp as $key => $value ) {
                if ($key==$petData[7]) {
                    $bp->$key-= $lv-1;
                    $bpmax->$key -= $lv-1;
                }

                $bpmin[$key] = $bp->$key-2;
            }
            $grade = array('xue'=>$grade[0]->xue,'gong'=>$grade[0]->gong,'fang'=>$grade[0]->fang,'min'=>$grade[0]->min,'mo'=>$grade[0]->mo);
            echo 'All:';
            echo implode('/',$grade).'<br>';
            echo 'max:';
            foreach ($bpmax as $key => $value ) {
                //echo intval(5*($value - $this->pet_model->tnt(intval($value/(92*$this->pet_model->tnt($grade[$key])/($grade[$key])+0.2)))*92)-$grade[$key]);

                echo round($value/(($lv-1)*$this->pet_model->tnt($grade[$key])/($grade[$key])+0.2),2);
                echo '/';
            }
            echo '<br>';
            echo 'now:';
            foreach ($bp as $key => $value ) {
                /*
                if ($key == 'gong') {
                    echo intval(($value - $this->pet_model->tnt(intval($value/(92*$this->pet_model->tnt($grade[$key])/($grade[$key])+0.2)+1))*92)*5-$grade[$key]);

                } else {
                    echo intval(($value - $this->pet_model->tnt(intval($value/(92*$this->pet_model->tnt($grade[$key])/($grade[$key])+0.2)))*92)*5-$grade[$key]);

                }
                */
                echo round($value/(($lv-1)*$this->pet_model->tnt($grade[$key])/($grade[$key])+0.2),2);
                echo '/';
            }
            echo '<br>';
            echo 'min:';
            foreach ($bpmin as $key => $value ) {
                /*
                if ($key == 'gong') {
                    echo intval(($value - $this->pet_model->tnt(intval($value/(92*$this->pet_model->tnt($grade[$key])/($grade[$key])+0.2)+1))*92)*5-$grade[$key]);

                } else {
                    echo intval(($value - $this->pet_model->tnt(intval($value/(92*$this->pet_model->tnt($grade[$key])/($grade[$key])+0.2)))*92)*5-$grade[$key]);

                }
                */
                echo round($value/(($lv-1)*$this->pet_model->tnt($grade[$key])/($grade[$key])+0.2),2)+1;
                echo '/';
            }
            echo '<br>';
        }


    }

}
