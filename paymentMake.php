<?php
if (!isset($_SESSION['cmsuno'])) exit();
?>
<?php
if(file_exists('data/payment.json'))
	{
	// JSON : {"prod":{"0":{"n":"clef de 12","p":8.5,"i":"","q":1},"1":{"n":"tournevis","p":1.5,"i":"","q":2},"2":{"n":"papier craft","p":0.21,"i":"","q":30}}}
	// n=nom, p=prix, i=ID, q=quantite
	// Cookie format : nom|quantite||nom|quantite||nom|quantite||
	// OK : ?payplug=ok&digit=mapage|monplugin|123456789123
	$q1 = file_get_contents('data/payment.json');
	$a1 = json_decode($q1,true);
	$it = (isset($a1['it'])?$a1['it']:'shortcode');
	$ali = (isset($a1['ali'])?$a1['ali']:'left');
	$col = (isset($a1['col'])?$a1['col']:'#eee');
	$ico = (isset($a1['ico'])?$a1['ico']:'black');
	$Ustyle .= '.cart{position:relative;display:inline-block;height:auto;padding:0;font-size:.9em;margin:0;line-height:1.1em;white-space:nowrap;}
.cart>a{display:block;width:auto;text-align:right;color:'.$ico.';text-decoration:none;padding:4px 0 4px 20px!important;background-image:url(uno/plugins/payment/img/panier16'.($ico=='white'?'blanc':'').'.png);background-repeat:no-repeat;background-position:0px 2px;}
.cart>a.on{color:red;}.cartBox{position:relative;display:none;width:40px;}
.cartTable{z-index:999;position:absolute;top:4px;'.$ali.':1px;color:'.$ico.';background-color:'.$col.';padding:1.4em;text-align:left;border-bottom-left-radius:5px;border-bottom-right-radius:5px;box-shadow:0 1px 2px #666666;-webkit-box-shadow:0 1px 2px #666666;}
.cartTable th{border-top:1px solid '.$ico.';}.cartTable td{padding-right:4px;}
.cartTable .button{float:right;width:auto;margin:0;background:-moz-linear-gradient(center top,#f3f3f3,#dddddd);background:-webkit-gradient(linear,left top,left bottom,from(#f3f3f3),to(#dddddd));background: -o-linear-gradient(top,#f3f3f3,#dddddd);filter:progid:DXImageTransform.Microsoft.gradient(startColorStr="#f3f3f3",EndColorStr="#dddddd");border-color:#000;border-width:1px;-moz-border-radius:4px;-webkit-border-radius:4px;color:#333;cursor:pointer;padding:4px 7px;font-size:1em;line-height:1.1em;}
.cartTable .button:hover{background:#ddd;}'."\r\n";
	$tmp1 = ''; // version directe (le panier est en ajax)
	foreach($a1['method'] as $k=>$v)
		{
		if($k=='cheq' && $v) $tmp1 .= '<a href="JavaScript:void(0);" onClick="payCheqCart(paymentC);"><img src="uno/plugins/payment/img/cheque-btn.png" class="logo" /></a>';
		else if($k=='vire' && $v) $tmp1 .= '<a href="JavaScript:void(0);" onClick="payVireCart(paymentC);"><img src="uno/plugins/payment/img/virement-btn.png" class="logo" /></a>';
		else if(isset($Ua['plug'][$k]) && $v) $tmp1 .= '<a href="JavaScript:void(0);" onClick="'.$k.'Cart(paymentC);"><img src="uno/plugins/'.$k.'/img/'.$k.'-btn.png" class="logo" /></a>';
		}
	$tmp = "<script type=\"text/javascript\">var paymentC,paymentBtn='".T_('Order')."';function paymentCart(f){paymentC=f;var g=eval('('+f+')');if(g['prod']){unoPop('".$tmp1."',0);}};</script>"."\r\n";
	if(strpos($Uhtml.$Ucontent,'[[paymentCart]]')!==false || strpos($Uhtml.$Ucontent,'paymentAddC')!==false)
		{
		if($it=='shortcode')
			{
			$Uhtml = str_replace('[[paymentCart]]','<div id="cart" class="cart"></div>',$Uhtml);
			$Ucontent = str_replace('[[paymentCart]]','<div id="cart" class="cart"></div>',$Ucontent);
			}
		else
			{
			$Umenu .= '<li><div id="cart" class="cart"></div></li>';
			$Uhtml = str_replace('[[paymentCart]]','',$Uhtml);
			$Ucontent = str_replace('[[paymentCart]]','',$Ucontent);
			}
		$tmp .= '<script type="text/javascript" src="uno/plugins/payment/paymentInc.js"></script>'."\r\n";
		}
	$Ufoot .= $tmp;
	$unoPop=1; // include unoPop.js in output
	$unoUbusy=1;
	$a = Array();
	preg_match_all("#<a(.*?)class=\"button addtocart\"#", $Ucontent, $match);
	if($match)
		{
		foreach($match[1] as $r1)
			{
			preg_match("#alt=\"(.*?)\"#", $r1, $s1);
			if($s1)
				{
				$t1 = explode("|", $s1[1]);
				$p = applyTax($a1,$t1);
				$a['cart'][] = array("n"=>$t1[0], "p"=>$p, "t"=>$t1[3]);
				}
			}
		}
	if($a)
		{
		$a['curr'] = $a1['curr'];
		$out = json_encode($a);
		file_put_contents('data/'.$Ubusy.'/addtocart.json', $out);
		}
	}
function applyTax($a1,$t1)
	{
	// a1 : array - payment.json 
	// t1 : array - alt content
	// return : price
	if($a1['taxin']!=$a1['taxout'])
		{
		$ta=0; $tb=0; $tc=0; $td=0; $t=$t1[3];
		// 1. active tax
		if($t>7) {$td = 1; $t -= 8;}
		if($t>3) {$tc = 1; $t -= 4;}
		if($t>1) {$tb = 1; $t -= 2;}
		if($t) $ta = 1;
		// 2. display tax + or - ?
		if($a1['taxin']=='yes') {$ta *= -1; $tb *= -1; $tc *= -1; $td *= -1;} // Tax deducted
		// 3. eval tax
		if($ta==1 && strpos($a1['taa'],'%')) $ta *= floatval(str_replace('%','',$a1['taa'])) * .01 * $t1[1];
		else if($ta==-1 && strpos($a1['taa'],'%')) $ta *= $t1[1] - $t1[1] / (floatval(str_replace('%','',$a1['taa'])) * .01 + 1) ;
		else if($ta) $ta *= floatval($a1['taa']);
		if($tb==1 && strpos($a1['tab'],'%')) $tb *= floatval(str_replace('%','',$a1['tab'])) * .01 * $t1[1];
		else if($tb==-1 && strpos($a1['tab'],'%')) $tb *= $t1[1] - $t1[1] / (floatval(str_replace('%','',$a1['tab'])) * .01 + 1) ;
		else if($tb) $tb *= floatval($a1['tab']);
		if($tc==1 && strpos($a1['tac'],'%')) $tc *= floatval(str_replace('%','',$a1['tac'])) * .01 * $t1[1];
		else if($tc==-1 && strpos($a1['tac'],'%')) $tc *= $t1[1] - $t1[1] / (floatval(str_replace('%','',$a1['tac'])) * .01 + 1) ;
		else if($tc) $tc *= floatval($a1['tac']);
		if($td==1 && strpos($a1['tad'],'%')) $td *= floatval(str_replace('%','',$a1['tad'])) * .01 * $t1[1];
		else if($td==-1 && strpos($a1['tad'],'%')) $td *= $t1[1] - $t1[1] / (floatval(str_replace('%','',$a1['tad'])) * .01 + 1) ;
		else if($td) $td *= floatval($a1['tad']);
		// 4. return price
		return intval(max(0,(($t1[1] + $ta + $tb + $tc + $td)*100 + .5))) / 100;
		}
	else return $t1[1];
	}
?>
