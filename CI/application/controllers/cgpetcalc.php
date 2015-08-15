<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cgpetcalc extends CI_Controller {

        public function __construct()
        {
            parent::__construct();
        }
        public function index()
        {
            $data['petData'] ='';
            $data['petResult'] ='';
            $data['petName'] = '';
            $data['petGrade'] = '';
            $data['petSelect'] = '';
            $petName = $this->input->post('petList');
            if (!$petName) {
                $petName = $this->input->post('petName');
            }
            $names = $this->getNamesBySearch($petName);
            if (count($names) == 0) {
                $data['petName'] = $petName;

            } elseif (count($names) == 1) {
                $petName = $names[0]->name;
                $petGrade = array($names[0]->xue, $names[0]->gong, $names[0]->fang, $names[0]->min, $names[0]->mo);
                $petLv = 1;
                $dataBased = array();
                foreach ($petGrade as $key => $g) {
                    $dataBased []= max($g-4,0)*20;
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
                $remain['hp'] = $petData[0]*1000 - 20*1000 - $this->getOriginPropFromBP('hp', $dataBased);
                $remain['mp'] = $petData[1]*1000 - 20*1000 - $this->getOriginPropFromBP('mp', $dataBased);
                $remain['atk'] = $petData[2]*1000 - 20*1000 - $this->getOriginPropFromBP('atk', $dataBased);
                $remain['def'] = $petData[3]*1000 - 20*1000 - $this->getOriginPropFromBP('def', $dataBased);
                $remain['egi'] = $petData[4]*1000 - 20*1000 - $this->getOriginPropFromBP('egi', $dataBased);
                if (count($petData) == 7) {
                    $remain['spr'] = $petData[5]*1000 - 100*1000 - $this->getOriginPropFromBP('spr', $dataBased);
                    $remain['rec'] = $petData[6]*1000 - 100*1000 - $this->getOriginPropFromBP('rec', $dataBased);
                }

                $results = $this->getOriginResultByRemain($remain);
                $outs = 0;
                $petResult = array();
                foreach ($results as $key => $result) {
                    $outs += $result->outs;
                }

                foreach ($results as $key => $result) {
                    $petResult []= $this->sumGrade($result->grade).'d :: '.$result->grade.',    '.round($result->outs/$outs*100, 2).'%<br>';
                }
                $data['petResult'] = $petResult?$petResult:array('无解！！');

            }
            $this->load->view('cgpetcalc',$data);
            return;

    }
        public function getNamesBySearch($name = '')
        {
            if (!$name) {
                return array();
            } else {
                $this->db->where('name',$name);
                $query = $this->db->get('pet_grade')->result();
                if (count($query) == 0) {
                    $this->db->like('name',$name);
                    $query = $this->db->get('pet_grade')->result();
                }
                if (count($query) == 0) {
                    $this->db->like('tokens',$name);
                    $query = $this->db->get('pet_grade')->result();
                }
                return $query;

            }
        }
        public function sumGrade($grade = '')
        {
            $grades = explode('/',$grade);
            $sum = 0;
            foreach ($grades as $key => $value) {
                $sum +=$value;
            }
            return $sum-10;
        }
        public function getOriginResultByRemain($remain = array())
        {
            $hpMax  = $remain['hp']+999;
            $hpMin  = $remain['hp'];
            $mpMax  = $remain['mp']+999;
            $mpMin  = $remain['mp'];
            $atkMax = $remain['atk']+999;
            $atkMin = $remain['atk'];
            $defMax = $remain['def']+999;
            $defMin = $remain['def'];
            $egiMax = $remain['egi']+999;
            $egiMin = $remain['egi'];
            if (count ($remain) == 7) {
                $sprMax = $remain['spr']+999;
                $sprMin = $remain['spr'];
                $recMax = $remain['rec']+999;
                $recMin = $remain['rec'];
                $this->db->where('spr <=', $sprMax);
                $this->db->where('spr >=', $sprMin);
                $this->db->where('rec <=', $recMax);
                $this->db->where('rec >=', $recMin);
            }

            $this->db->where('hp <=', $hpMax);
            $this->db->where('hp >=', $hpMin);
            $this->db->where('mp <=', $mpMax);
            $this->db->where('mp >=', $mpMin);
            $this->db->where('atk <=', $atkMax);
            $this->db->where('atk >=', $atkMin);
            $this->db->where('def <=', $defMax);
            $this->db->where('def >=', $defMin);
            $this->db->where('egi <=', $egiMax);
            $this->db->where('egi >=', $egiMin);

            $query = $this->db->get('pet_random_data')->result();
            return $query;

        }
        public function getOriginPropFromBP($type = '', $data = array(), $level = 1)
        {
            switch ($type) {
                case 'hp':
                    return (1*(($level) * (80*$data[0]+20*$data[1]+30*$data[2]+30*$data[3]+ 10*$data[4])));
                    break;
                case 'mp':
                    return (1*(($level) * (10*$data[0]+20*$data[1]+20*$data[2]+20*$data[3]+100*$data[4])));
                    break;
                case 'atk':
                    return (1*(($level) * ( 2*$data[0]+27*$data[1]+ 3*$data[2]+ 3*$data[3]+  2*$data[4])));
                    break;
                case 'def':
                    return (1*(($level) * ( 2*$data[0]+ 3*$data[1]+30*$data[2]+ 3*$data[3]+  2*$data[4])));
                    break;
                case 'egi':
                    return (1*(($level) * ( 1*$data[0]+ 2*$data[1]+ 2*$data[2]+20*$data[3]+  1*$data[4])));
                    break;
                case 'spr':
                    return (1*(($level) * (-3*$data[0]- 1*$data[1]+ 2*$data[2]- 1*$data[3]+  8*$data[4])));
                    break;
                case 'rec':
                    return (1*(($level) * ( 8*$data[0]- 1*$data[1]- 1*$data[2]+ 2*$data[3]-  3*$data[4])));
                    break;
                default:
                    return 0;
                    break;
            }
        }
        public function init_level_data()
        {
            $line = array();
            $tnt = array(40, 80, 120, 160, 205, 250, 290, 330, 370, 415);
            $datas = array();
            $levelstart = 1;
            for ($level=$levelstart; $level <$levelstart+10 ; $level++) {
                $line['level'] = $level;
                for ($xue=0; $xue < 10; $xue++) {
                    for ($gong=0; $gong < 10; $gong++) {
                        $datas = array();
                        for ($fang=0; $fang < 10; $fang++) {
                            for ($min=0; $min < 10; $min++) {
                                for ($mo=0; $mo < 10; $mo++) {
                                    $line['hp']  = (1*(($level) * (80*$tnt[$xue]+20*$tnt[$gong]+30*$tnt[$fang]+30*$tnt[$min]+ 10*$tnt[$mo])));
                                    $line['mp']  = (1*(($level) * (10*$tnt[$xue]+20*$tnt[$gong]+20*$tnt[$fang]+ 2*$tnt[$min]+100*$tnt[$mo])));
                                    $line['atk'] = (1*(($level) * ( 2*$tnt[$xue]+27*$tnt[$gong]+ 3*$tnt[$fang]+ 3*$tnt[$min]+  2*$tnt[$mo])));
                                    $line['def'] = (1*(($level) * ( 2*$tnt[$xue]+ 3*$tnt[$gong]+30*$tnt[$fang]+ 3*$tnt[$min]+  2*$tnt[$mo])));
                                    $line['egi'] = (1*(($level) * ( 1*$tnt[$xue]+ 2*$tnt[$gong]+ 2*$tnt[$fang]+20*$tnt[$min]+  1*$tnt[$mo])));
                                    $line['spr'] = (1*(($level) * (-3*$tnt[$xue]- 1*$tnt[$gong]+ 2*$tnt[$fang]- 1*$tnt[$min]+  8*$tnt[$mo])));
                                    $line['rec'] = (1*(($level) * ( 8*$tnt[$xue]- 1*$tnt[$gong]- 1*$tnt[$fang]+ 2*$tnt[$min]-  3*$tnt[$mo])));
                                    $line['xue'] = $xue;
                                    $line['gong'] = $gong;
                                    $line['fang'] = $fang;
                                    $line['min'] = $min;
                                    $line['mo'] = $mo;
                                    $line['grade'] = ''.$xue.$gong.$fang.$min.$mo;
                                    $datas []= $line;
                                }
                            }
                        }
                        $this->db->insert_batch('pet_level_data', $datas);
                        unset($datas);
                    }
                }
            }
        }

        public function init_origin_data()
        {
            $line = array();
            $tnt = array(0, 20, 40, 60, 80);
            $datas = array();
            $levelstart = 1;
            for ($level=$levelstart; $level <$levelstart+1 ; $level++) {
                $line['level'] = $level;
                for ($xue=0; $xue < 5; $xue++) {
                    for ($gong=0; $gong < 5; $gong++) {
                        $datas = array();
                        for ($fang=0; $fang < 5; $fang++) {
                            for ($min=0; $min < 5; $min++) {
                                for ($mo=0; $mo < 5; $mo++) {
                                    $line['hp']  = (1*(($level) * (80*$tnt[$xue]+20*$tnt[$gong]+30*$tnt[$fang]+30*$tnt[$min]+ 10*$tnt[$mo])));
                                    $line['mp']  = (1*(($level) * (10*$tnt[$xue]+20*$tnt[$gong]+20*$tnt[$fang]+20*$tnt[$min]+100*$tnt[$mo])));
                                    $line['atk'] = (1*(($level) * ( 2*$tnt[$xue]+27*$tnt[$gong]+ 3*$tnt[$fang]+ 3*$tnt[$min]+  2*$tnt[$mo])));
                                    $line['def'] = (1*(($level) * ( 2*$tnt[$xue]+ 3*$tnt[$gong]+30*$tnt[$fang]+ 3*$tnt[$min]+  2*$tnt[$mo])));
                                    $line['egi'] = (1*(($level) * ( 1*$tnt[$xue]+ 2*$tnt[$gong]+ 2*$tnt[$fang]+20*$tnt[$min]+  1*$tnt[$mo])));
                                    $line['spr'] = (1*(($level) * (-3*$tnt[$xue]- 1*$tnt[$gong]+ 2*$tnt[$fang]- 1*$tnt[$min]+  8*$tnt[$mo])));
                                    $line['rec'] = (1*(($level) * ( 8*$tnt[$xue]- 1*$tnt[$gong]- 1*$tnt[$fang]+ 2*$tnt[$min]-  3*$tnt[$mo])));
                                    $line['xue'] = $xue;
                                    $line['gong'] = $gong;
                                    $line['fang'] = $fang;
                                    $line['min'] = $min;
                                    $line['mo'] = $mo;
                                    $line['grade'] = ''.$xue.$gong.$fang.$min.$mo;
                                    $datas []= $line;
                                }
                            }
                        }
                        $this->db->insert_batch('pet_origin_data', $datas);
                        unset($datas);
                    }
                }
            }
        }
        public function init_random_data()
        {
            $line = array();
            $tnt = array(0, 20, 40, 60, 80, 100, 120, 140, 160, 180, 200, 220, 240, 260, 280, 300);
            $datas = array();
            $levelstart = 1;
            for ($level=$levelstart; $level <$levelstart+1 ; $level++) {
                $line['level'] = $level;
                for ($xue=0; $xue <= 14; $xue++) {
                    for ($gong=0; $gong <= 14; $gong++) {
                        $datas = array();
                        for ($fang=0; $fang <= (30-$xue-$gong>=14?14:(30-$xue-$gong)); $fang++) {
                            for ($min=0; $min <= (30-$xue-$gong-$fang>=14?14:(30-$xue-$gong-$fang)); $min++) {
                                for ($mo=(10-$xue-$gong-$fang-$min<=0?0:(10-$xue-$gong-$fang-$min)); $mo <= (30-$xue-$gong-$fang-$min>=14?14:(30-$xue-$gong-$fang-$min)); $mo++) {

                                    $line['hp']  = (1*(($level) * (80*$tnt[$xue]+20*$tnt[$gong]+30*$tnt[$fang]+30*$tnt[$min]+ 10*$tnt[$mo])));
                                    $line['mp']  = (1*(($level) * (10*$tnt[$xue]+20*$tnt[$gong]+20*$tnt[$fang]+20*$tnt[$min]+100*$tnt[$mo])));
                                    $line['atk'] = (1*(($level) * ( 2*$tnt[$xue]+27*$tnt[$gong]+ 3*$tnt[$fang]+ 3*$tnt[$min]+  2*$tnt[$mo])));
                                    $line['def'] = (1*(($level) * ( 2*$tnt[$xue]+ 3*$tnt[$gong]+30*$tnt[$fang]+ 3*$tnt[$min]+  2*$tnt[$mo])));
                                    $line['egi'] = (1*(($level) * ( 1*$tnt[$xue]+ 2*$tnt[$gong]+ 2*$tnt[$fang]+20*$tnt[$min]+  1*$tnt[$mo])));
                                    $line['spr'] = (1*(($level) * (-3*$tnt[$xue]- 1*$tnt[$gong]+ 2*$tnt[$fang]- 1*$tnt[$min]+  8*$tnt[$mo])));
                                    $line['rec'] = (1*(($level) * ( 8*$tnt[$xue]- 1*$tnt[$gong]- 1*$tnt[$fang]+ 2*$tnt[$min]-  3*$tnt[$mo])));
                                    $line['xue'] = $xue;
                                    $line['gong'] = $gong;
                                    $line['fang'] = $fang;
                                    $line['min'] = $min;
                                    $line['mo'] = $mo;
                                    $line['grade'] = ''.($xue-4).'/'.($gong-4).'/'.($fang-4).'/'.($min-4).'/'.($mo-4);
                                    $line['outs'] = $this->getOutsBy($xue, $gong, $fang, $min, $mo);
                                    $datas []= $line;
                                }
                            }
                        }
                        $this->db->insert_batch('pet_random_data', $datas);
                        unset($datas);
                    }
                }
            }
        }
        public function getOutsBy($x, $g, $f, $m, $o)
        {
            $count = 0;
            $remain = $x+$g+ $f+ $m+ $o-10;
            for ($xue=0; $xue <= min($x, $remain, 4); $xue++) {
                for ($gong=0; $gong <= min($g, $remain-$xue, 4); $gong++) {
                    for ($fang=0; $fang <= min($f, $remain-$xue-$gong, 4); $fang++) {
                        for ($min=0; $min <= min($m, $remain-$xue-$gong-$fang, 4); $min++) {
                            $count++;
                            }
                        }
                    }
                }
                return $count;
        }

        /*
        public function mergeGrade($result = '', $random = '')
        {
            $data = strval($result->grade) + strval($random->grade);
            return str_repeat('0', 5-strlen(''.$data)).$data;
        }
        public function reverseGrade($grade = '')
        {
            $grade = 44444 - strval($grade);
            return str_repeat('0', 5-strlen(''.$grade)).$grade;
        }

        public function getOriginRandomResultByRemain($remain = array(), $r)
        {
            $hpMax  = $remain['hp']  - $r->hp +999;
            $hpMin  = $remain['hp']  - $r->hp;
            $mpMax  = $remain['mp']  - $r->mp +999;
            $mpMin  = $remain['mp']  - $r->mp;
            $atkMax = $remain['atk'] - $r->atk +999;
            $atkMin = $remain['atk'] - $r->atk;
            $defMax = $remain['def'] - $r->def +999;
            $defMin = $remain['def'] - $r->def;
            $egiMax = $remain['egi'] - $r->egi +999;
            $egiMin = $remain['egi'] - $r->egi;
            $sprMax = $remain['spr'] - $r->spr +999;
            $sprMin = $remain['spr'] - $r->spr;
            $recMax = $remain['rec'] - $r->rec +999;
            $recMin = $remain['rec'] - $r->rec;
            $this->db->where('hp <=', $hpMax);
            $this->db->where('hp >=', $hpMin);
            $this->db->where('mp <=', $mpMax);
            $this->db->where('mp >=', $mpMin);
            $this->db->where('atk <=', $atkMax);
            $this->db->where('atk >=', $atkMin);
            $this->db->where('def <=', $defMax);
            $this->db->where('def >=', $defMin);
            $this->db->where('egi <=', $egiMax);
            $this->db->where('egi >=', $egiMin);
            $this->db->where('spr <=', $sprMax);
            $this->db->where('spr >=', $sprMin);
            $this->db->where('rec <=', $recMax);
            $this->db->where('rec >=', $recMin);
            $query = $this->db->get('pet_random_data')->result();
            return $query;
        }
        */
}
