<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cgpetcalc extends CI_Controller {

        public function __construct()
        {
            parent::__construct();
            $this->load->model('pet_model')
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
            $names = $this->pet_model->getNamesBySearch($petName);
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
                $remain['hp'] = $petData[0]*1000 - 20*1000 - $this->pet_model->getOriginPropFromBP('hp', $dataBased);
                $remain['mp'] = $petData[1]*1000 - 20*1000 - $this->pet_model->getOriginPropFromBP('mp', $dataBased);
                $remain['atk'] = $petData[2]*1000 - 20*1000 - $this->pet_model->getOriginPropFromBP('atk', $dataBased);
                $remain['def'] = $petData[3]*1000 - 20*1000 - $this->pet_model->getOriginPropFromBP('def', $dataBased);
                $remain['egi'] = $petData[4]*1000 - 20*1000 - $this->pet_model->getOriginPropFromBP('egi', $dataBased);
                if (count($petData) == 7) {
                    $remain['spr'] = $petData[5]*1000 - 100*1000 - $this->pet_model->getOriginPropFromBP('spr', $dataBased);
                    $remain['rec'] = $petData[6]*1000 - 100*1000 - $this->pet_model->getOriginPropFromBP('rec', $dataBased);
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

}
