<?php
function getPaypalOrder($id,$sdata)
	{
	$q = file_get_contents('../../data/_sdata-'.$sdata.'/_paypal/'.$id.'.json'); $a = json_decode($q,true);
	$b = array(); $v=1; $w = 0;
	while(isset($a['item_name'.$v]))
		{
		if($a['item_name'.$v]!=_('Shipping cost'))
			{
			$b['prod'][$w]['n'] = $a['item_name'.$v];
			$b['prod'][$w]['p'] = (isset($a['mc_gross_'.$v])?$a['mc_gross_'.$v]:0);
			$b['prod'][$w]['i'] = (isset($a['item_number'.$v])?$a['item_number'.$v]:'');
			$b['prod'][$w]['q'] = (isset($a['quantity'.$v])?$a['quantity'.$v]:1);
			++$w;
			}
		else $b['ship'] = (isset($a['mc_gross_'.$v])?$a['mc_gross_'.$v]:0);
		++$v;
		}
	if(isset($a['custom']) && strpos($a['custom'],'ADRESS|')!==false)
		{
		$c = explode('|',$a['custom']);
		if(is_array($c) && $c[0]=='ADRESS')
			{
			$b['name'] = $c[1];
			$b['adre'] = $c[2];
			$b['mail'] = $c[3];
			}
		}
	if(isset($a['time'])) $b['time'] = $a['time'];
	if(isset($a['treated'])) $b['treated'] = $a['treated']; else $b['treated'] = 0;
	if(isset($a['Utax'])) $b['Utax'] = $a['Utax']; else $b['Utax'] = 0;
	if(isset($a['Ubusy'])) $b['Ubusy'] = $a['Ubusy']; else $b['Ubusy'] = '';
	if(isset($a['mc_currency'])) $b['curr'] = $a['mc_currency']; else $b['curr'] = 'EUR';
	$b['id'] = $id;
	return $b;
	}
function getPayplugOrder($id,$sdata)
	{
	$q = file_get_contents('../../data/_sdata-'.$sdata.'/_payplug/'.$id.'.json'); $a = json_decode($q,true);
	$b = array(); $v = 0;
	if(isset($a['customData'])) $c = explode('|;',$a['customData']);
	if(is_array($c)) foreach($c as $r1)
		{
		$r2 = explode('|',$r1);
		if(is_array($r2) && $r2[0])
			{
			if($r2[0]!='ADRESS')
				{
				$b['prod'][$v]['n'] = $r2[0];
				$b['prod'][$v]['p'] = $r2[1];
				$b['prod'][$v]['i'] = $r2[2];
				$b['prod'][$v]['q'] = $r2[3];
				++$v;
				}
			else
				{
				$b['name'] = $r2[1];
				$b['adre'] = $r2[2];
				$b['mail'] = $r2[3];
				}
			}
		}
	if(isset($a['time'])) $b['time'] = $a['time'];
	if(isset($a['treated'])) $b['treated'] = $a['treated']; else $b['treated'] = 0;
	if(isset($a['Utax'])) $b['Utax'] = $a['Utax']; else $b['Utax'] = 0;
	if(isset($a['Ubusy'])) $b['Ubusy'] = $a['Ubusy']; else $b['Ubusy'] = '';
	$b['curr'] = 'EUR';
	$b['id'] = $id;
	return $b;
	}
function getPaymentOrder($id,$sdata)
	{
	$q = file_get_contents('../../data/_sdata-'.$sdata.'/_payment/'.$id.'.json'); $a = json_decode($q,true);
	return $a;
	}
function getTax($a,$r)
	{
	// a : array - IPN data => Utax
	// r : array - IPN data (this product part only) => t
	// return : tax
	$ta=0; $tb=0; $tc=0; $td=0; $te = 0; $t = 0; $Utax = array(0,0,0,0);
	if(isset($a['Utax'])) $Utax = explode('|',str_replace(',','.',$a['Utax']));
	if(isset($r['t'])) $t = $r['t'];
	// 1. active tax
	if($t>7) {$td = 1; $t -= 8;}
	if($t>3) {$tc = 1; $t -= 4;}
	if($t>1) {$tb = 1; $t -= 2;}
	if($t) $ta = 1;
	// 2. eval tax
	$rp = pt($r['p']);
	// fixed tax first
	if($ta && strpos($Utax[0],'%')===false) {$ta *= floatval($Utax[0]); $rp -= $ta;}
	if($tb && strpos($Utax[1],'%')===false) {$tb *= floatval($Utax[1]); $rp -= $tb;}
	if($tc && strpos($Utax[2],'%')===false) {$tc *= floatval($Utax[2]); $rp -= $tc;}
	if($td && strpos($Utax[3],'%')===false) {$td *= floatval($Utax[3]); $rp -= $td;}
	// proportionnal tax
	if($ta && strpos($Utax[0],'%')) {$te += (floatval(str_replace('%','',$Utax[0]))); $ta = 0;}
	if($tb && strpos($Utax[1],'%')) {$te += (floatval(str_replace('%','',$Utax[1]))); $tb = 0;}
	if($tc && strpos($Utax[2],'%')) {$te += (floatval(str_replace('%','',$Utax[2]))); $tc = 0;}
	if($td && strpos($Utax[3],'%')) {$te += (floatval(str_replace('%','',$Utax[3]))); $td = 0;}
	$te = $rp - $rp / (floatval(str_replace('%','',$te)) * .01 + 1);
	// 3. return tax
	return intval(($ta + $tb + $tc + $td + $te)*100 + .5) / 100;
	}
function pt($f) { return floatval(str_replace(',','.',$f)); }
?>
