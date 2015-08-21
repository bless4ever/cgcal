<?php
class Pet_model extends CI_Model {
    public function __construct() {
    }
    public function petKindToGrade($petKind)
    {
        return array('xue'=>$petKind->xue,'gong'=>$petKind->gong,'fang'=>$petKind->fang,'min'=>$petKind->min,'mo'=>$petKind->mo);
    }
    public function arrayToGrade($arr)
    {
        return array('xue'=>$arr[0],'gong'=>$arr[1],'fang'=>$arr[2],'min'=>$arr[3],'mo'=>$arr[4]);
    }
    public function charsToArray($chars)
    {
        $result = array();
        for ($i=0; $i < strlen($chars); $i++) {
            $result[$i] = hexdec(substr($chars, $i, 1));
        }
        return $result;
    }
    public function genPet($grade, $lv, $diffGrade, $randomGrade, $addBP, $intResult = true, $times = 20, $originLv = 1, $series = 0)
    {
        $this->load->helper('vector');
        $base = array('hp' => 20, 'mp' => 20, 'atk' => 20, 'def' => 20, 'egi' => 20, 'spr' => 100, 'rec' => 100);
        $addBP = $this->pet_model->arrayToGrade(explode(',', $addBP));
        $diffGrade = $this->pet_model->arrayToGrade($this->pet_model->charsToArray($diffGrade));
        $randomGrade = $this->pet_model->arrayToGrade($this->pet_model->charsToArray($randomGrade));

        $base = mul(1000, $base);
        $addBP = mul(100, $addBP);
        $grownGrade = minus($grade, $diffGrade);

        $lvOneTotalBP = mul($times, plus(minus($grade, $diffGrade), $randomGrade));
        //echo "1bp:";print_r($lvOneTotalBP);
        $grownBP = mul(($lv - 1)*100, $this->tnt($grownGrade));
        //echo "grown";print_r($grownBP);
        $totalBP = int(plus($lvOneTotalBP, plus($grownBP, $addBP)));
        //print_r($totalBP);
        foreach ($base as $type => $value) {
            $tmp = $this->getPropFromBP($type, $totalBP);
            $base[$type] += $tmp;
        }
        if ($intResult) {
            return int(mul(0.001, $base));
        } else {
            return mul(0.001, $base);
        }



    }
    public function tnt($d)
    {
        if (is_array($d)) {
            $result = array();
            foreach ($d as $key => $value) {
                $temp = $value %64;
                $result[$key] = max($temp*0.04+ 0.005*(2*intval($temp/5)-($temp%5 == 0 ? 1:0)),0);
            }
            return $result;
        }
        $d %=64;
        return max($d*0.04+ 0.005*(2*intval($d/5)-($d%5 == 0 ? 1:0)),0);
    }
    public function getBPByProp($prop = array())
    {
        if (count($prop)<5) {
            return array();
        }
        $data = array();
        for ($i=0; $i < 5; $i++) {
            $data[] = $i*10000+$prop[$i];
        }
        $this->db->select("sum(xue)/10000 as xue,sum(gong)/10000 as gong,sum(fang)/10000 as fang ,sum(min)/10000 as min,sum(mo)/10000 as mo");
        $this->db->where_in('valueId', $data);
        return $this->db->get('prop_to_bp')->result()[0];
    }
    public function getGradesBySearch($name = '')
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
    public function getPropFromBP($type = '', $data = array())
    {
        switch ($type) {
            case 'hp':
                return (80*$data['xue']+20*$data['gong']+30*$data['fang']+30*$data['min']+ 10*$data['mo']);
                break;
            case 'mp':
                return (10*$data['xue']+20*$data['gong']+20*$data['fang']+20*$data['min']+100*$data['mo']);
                break;
            case 'atk':
                return ( 2*$data['xue']+27*$data['gong']+ 3*$data['fang']+ 3*$data['min']+  2*$data['mo']);
                break;
            case 'def':
                return ( 2*$data['xue']+ 3*$data['gong']+30*$data['fang']+ 3*$data['min']+  2*$data['mo']);
                break;
            case 'egi':
                return ( 1*$data['xue']+ 2*$data['gong']+ 2*$data['fang']+20*$data['min']+  1*$data['mo']);
                break;
            case 'spr':
                return (-3*$data['xue']- 1*$data['gong']+ 2*$data['fang']- 1*$data['min']+  8*$data['mo']);
                break;
            case 'rec':
                return ( 8*$data['xue']- 1*$data['gong']- 1*$data['fang']+ 2*$data['min']-  3*$data['mo']);
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
