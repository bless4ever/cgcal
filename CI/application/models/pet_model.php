<?php
class Pet_model extends CI_Model {
    public function __construct() {
        $this->load->helper('vector');
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
    public function petToProp($pet)
    {
        return array($pet['hp'], $pet['mp'], $pet['atk'], $pet['def'], $pet['egi']);
    }
    public function getPropDist($prop_cal, $prop)
    {
        return normAbsPropWeight($prop_cal, $prop);
    }
    public function getAddBPByCond($lv, $addBPMethod, $rBP)
    {
        if ($lv <= 0) {
            return array();
        }
        if ($rBP >= $lv) {
            return array();
        }
        $result = $this->arrayToGrade(array(0,0,0,0,0));
        switch ($addBPMethod) {
            case 'xue':
            case 'gong':
            case 'fang':
            case 'min':
            case 'mo':
                $result[$addBPMethod] += $lv-$rBP-1;
                return $result;
                break;
            case 'no':
                return $result;
                break;
            case 'hun':
                break;
            default:
                return array();
                break;
        }
    }
    public function genResultForHighPetPure($resultAll, $percentage = 0.2)
    {
        function resultSort($a, $b)
        {
            if ($a['dist'] == $b['dist']) {
                return 0;
            }
            return $a['dist'] > $b['dist'] ? 1:-1;
        }
        usort($resultAll, 'resultSort');
        $views = array();
        $totalResult = count($resultAll);
        $bestResults = array();
        $type = '';
        if ($resultAll[0]['dist'] == 0) {
            $type = '准确解';
            foreach ($resultAll as $key => $result) {
                if ($result['dist'] == 0) {
                    $bestResults[] = $result;
                } else {
                    break;
                }
            }
        } else {
            $type = '最优解';
            $tmpDiff = 0;
            foreach ($resultAll as $key => $result) {
                if ($key<$percentage * $totalResult) {
                    $bestResults[] = $result;
                    continue;
                } elseif ($tmpDiff == 0) {
                    $tmpDiff = $result['dist'];
                    $bestResults[] = $result;
                    continue;
                }
                if ($result['dist'] <= $tmpDiff) {
                    $bestResults[] = $result;
                } else {
                    break;
                }
            }
        }
        $dGradeMapRGrades = array();
        foreach ($bestResults as $key => $result) {
            if (array_key_exists($result['dg'], $dGradeMapRGrades)) {
                $dGradeMapRGrades[$result['dg']] .= ','.$result['rg'];
            } else {
                $dGradeMapRGrades[$result['dg']] = $result['rg'];
            }
        }
        foreach ($dGradeMapRGrades as $key => $gradeMap) {
            $views[] = '掉档: '.$key. ', 随机档:('.$gradeMap.') '.' 概率'.round((strlen($gradeMap)+1)/6/count($bestResults)*100,2).'%';
        }


        return array('type' => $type, 'view' => $views);
    }
    public function genResultForLv1($resultAll)
    {
        return array('type' => '1级宠物查询综合解', 'view' => $resultAll);
    }
    public function calcLv1Pet($petData, $petGrade)
    {
        $dataBased = array();
        foreach ($petGrade as $key => $g) {
            $dataBased [$key]= max($g-4,0)*20;
        }
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
        return $petResult?$petResult:array();
    }
    public function calcLvHighPetPure($petData, $petGrade, $lv, $addBPMethod, $rBP)
    {

            $petData = explode(' ', $petData);
            $grade = $petGrade;


            $prop = array($petData[0],$petData[1],$petData[2],$petData[3],$petData[4]);
            $addBP = $this->pet_model->getAddBPByCond($lv, $addBPMethod, $rBP);
            if (! $addBP) {
                return array('加点不合法！');
            }
            $bp = $this->pet_model->getBPByProp($prop);
            $bprange = $this->pet_model->getBPRangeByProp($prop);
            $bpsql2min = minus($bprange[0], $addBP);
            $bpsql2max = minus($bprange[1], $addBP);
            //print_r($grade);
            //$grade = array('xue'=>$grade[0]->xue,'gong'=>$grade[0]->gong,'fang'=>$grade[0]->fang,'min'=>$grade[0]->min,'mo'=>$grade[0]->mo);
            //echo implode('/', $grade).'<br>';

            $maxdg = array();
            $mindg = array();
            foreach ($bpsql2max as $key => $value ) {
                $tmp = $value / ( ($lv-1) * 0.04 +0.2);
                //$tmp = ($value/(($lv-1)*$this->pet_model->tnt($grade[$key])/($grade[$key])+0.2));
                $maxdg[$key] = min(intval($tmp), $grade[$key]);
                $r = $value - ($lv-1)*$this->pet_model->tnt($tmp);
                //echo $r.'/';
            }
            //echo '<br>';
            foreach ($bpsql2min as $key => $value ) {
                $tmp = $value / ( ($lv-1) * 0.042 +0.2);
                //$tmp = ( $value / ( ($lv-1) * $this->pet_model->tnt($grade[$key]) / ($grade[$key]) + 0.2 ) );
                $mindg[$key] = max(intval($tmp), $grade[$key] - 4);
                $r = $value - ($lv-1)*$this->pet_model->tnt($tmp);
                //echo $tmp.'/';
            }
            //print_r($mindg);
            //print_r($maxdg);
            $result = array();
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
                                    for ($i=max(intval($rmin/0.2)*0.2,0); $i <= min(intval($rmax/0.2)*0.2, 2) ; $i += 0.2) {
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
                                                            $propDist = $this->pet_model->getPropDist($this->pet_model->petToProp($pettemp), $prop);
                                                            $possibleResult = array();
                                                            $possibleResult['rg'] = $rgrade;
                                                            $possibleResult['dg'] = $dgrade;
                                                            $possibleResult['dist'] = $propDist;
                                                            $result[] = $possibleResult;

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
        foreach ($base as $type => $value) {
            $tmp = $this->getPropFromBP($type, $totalBP);
            $base[$type] += $tmp;
        }
        $base = array_merge($base, mul(10, $totalBP));
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
    public function getBPByPropByCalc($prop = array())
    {
        $this->load->helper('vector');
        $invTnt = array();
        //注意！invTnt[0-4]*[hmade]^-1 = 100000 *[xgfmm]!!!! BP是100000倍，而random表是100倍。
        $invTnt[] = array( 13.24901855,	-0.806318907,	-6.785929774,	-10.93898148,   -16.40847223);
        $invTnt[] = array(-0.782811067,	-0.608853052,	 38.607709,	    -2.429143453,	-3.643715179);
        $invTnt[] = array(-0.69583206,	-0.541202713,	-2.719073478,	 34.87779841,	-3.238857937);
        $invTnt[] = array(-0.467806013,	-0.363849121,	-2.954151876,	-2.452651293,	 51.87657862);
        $invTnt[] = array(-0.935612027,	 10.38341287,	-5.908303753,	-4.905302585,	-7.357953878);
        $result = array();
        $prop = minus($prop, mul(1, array(20, 20, 20, 20, 20)));
        for ($i=0; $i <5 ; $i++) {
            $result[$i] = 0;
            for ($j=0; $j < 5; $j++) {
                $result[$i] += $invTnt[$i][$j] * $prop[$j]/100;
            }
        }
        return $result;

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
    public function getBPRangeByProp($prop = array())
    {
        if (count($prop)<5) {
            return array();
        }
        $data = array();
        for ($i=0; $i < 5; $i++) {
            $data[] = $i*10000+$prop[$i];
        }
        $sql = "SELECT
                max(a.xue+b.xue+c.xue+d.xue+e.xue) as xuemax,
                max(a.gong+b.gong+c.gong+d.gong+e.gong) as gongmax,
                max(a.fang+b.fang+c.fang+d.fang+e.fang) as fangmax,
                max(a.min+b.min+c.min+d.min+e.min) as minmax,
                max(a.mo+b.mo+c.mo+d.mo+e.mo )as momax,
                min(a.xue+b.xue+c.xue+d.xue+e.xue) as xuemin,
                min(a.gong+b.gong+c.gong+d.gong+e.gong) as gongmin,
                min(a.fang+b.fang+c.fang+d.fang+e.fang) as fangmin,
                min(a.min+b.min+c.min+d.min+e.min) as minmin,
                min(a.mo+b.mo+c.mo+d.mo+e.mo ) as momin
                FROM `prop_to_bp` as a
                join `prop_to_bp` as b
                join `prop_to_bp` as c
                join `prop_to_bp` as d
                join `prop_to_bp` as e
                where a.valueid in (".($data[0]).",".($data[0]+1).")
                and b.valueid in (".($data[1]).",".($data[1]+1).")
                and c.valueid in (".($data[2]).",".($data[2]+1).")
                and d.valueid in (".($data[3]).",".($data[3]+1).")
                and e.valueid in (".($data[4]).",".($data[4]+1).")";
        $result = $this->db->query($sql)->result()[0];
        $range = array();
        $range[0] = array();
        $range[1] = array();
        $range[0]['xue'] = $result->xuemin/10000;
        $range[0]['gong'] = $result->gongmin/10000;
        $range[0]['fang'] = $result->fangmin/10000;
        $range[0]['min'] = $result->minmin/10000;
        $range[0]['mo'] = $result->momin/10000;
        $range[1]['xue'] = $result->xuemax/10000;
        $range[1]['gong'] = $result->gongmax/10000;
        $range[1]['fang'] = $result->fangmax/10000;
        $range[1]['min'] = $result->minmax/10000;
        $range[1]['mo'] = $result->momax/10000;
        return $range;
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
