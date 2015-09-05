<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CgPetSimulator extends CI_Controller {

        public function __construct()
        {
            parent::__construct();
            $this->load->model('pet_model');
        }
        public function index($showRet = 0)
        {
            $this->load->helper('vector');
            $data['petData'] ='';
            $data['result'] ='';
            $data['petName'] = '';
            $data['petGrade'] = '';
            $data['petSelect'] = '';
            $data['petLv'] = '100';
            $data['petDiffGrade'] = '';
            $data['petRandomGrade'] = '';
            $data['addBP'] = '';

            $petGrade = '';
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
            $petLv = $this->input->post('petLv');
            $petDiffGrade = $this->input->post('petDiffGrade');
            $petRandomGrade = $this->input->post('petRandomGrade');
            $addBP = $this->input->post('addBP');
            $data['petLv'] = $petLv;
            $data['petDiffGrade'] = $petDiffGrade;
            $data['petRandomGrade'] = $petRandomGrade;
            $data['addBP'] = $addBP;
            if (! $petLv) {
                $petLv = 100;
            }
            if (! $petRandomGrade) {
                $petRandomGrade = '22222';
            }
            if (! $addBP) {
                $addBP = '0,0,0,0,0';
            }

            if ($petLv && $petDiffGrade && $petGrade) {
                $resultPet = $this->pet_model->genPet($petGrade, $petLv, $petDiffGrade, $petRandomGrade, $addBP)[0];
                $tmpStr = '亲，你要的'.$petLv.'级的'.$petName.'来了。<br>';
                $tmpStr .= '掉档：'.$petDiffGrade.'，随机档：'.$petRandomGrade.'<br>';
                $tmpStr .= '血, 魔, 攻, 防, 敏, 精神, 回复分别是：<br>';
                $tmpStr .= implode(' ', $resultPet);
                $data['result'] = $tmpStr;
                $data['resulttmp'] = $resultPet['hp'].' '.$resultPet['mp'].' '.$resultPet['atk'].' '.$resultPet['def'].' '.$resultPet['egi'].' ';
                $data['resulttmp'] .= $petLv.' '.$petName.' '.($petLv - 1).' no';
            }
            $this->load->view('cgPetSimulator',$data);
            return;
        }
    }
