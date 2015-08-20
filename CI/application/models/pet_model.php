<?php

class Pet_model extends CI_Model {
    public function __construct() {
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
