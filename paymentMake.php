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
	$it = (!empty($a1['it'])?$a1['it']:'shortcode');
	$ali = ((!empty($a1['ali'])&&$a1['ali']=='right')?1:0);
	$col = (!empty($a1['col'])?$a1['col']:(empty($Ua['w3'])?'#eee':''));
	$ico = ((!empty($a1['ico'])&&$a1['ico']=='white')?1:0);
	if(empty($Ua['w3'])) $Ustyle .= '.cart{position:relative;display:inline-block;height:auto;padding:0;font-size:.9em;margin:0;line-height:1.1em;white-space:nowrap;}.cart .w3-button{cursor:pointer}
.cartBox{z-index:999;position:absolute;padding:1.4em;text-align:left;border-bottom-left-radius:5px;border-bottom-right-radius:5px;box-shadow:0 1px 2px #666666;-webkit-box-shadow:0 1px 2px #666666;}.popPayment h3{margin:4px 0}}
.cartTable td{padding-right:4px;}.cartBox .w3-button{cursor:pointer;}'."\r\n";
	$Ustyle .= '.cartTable td{vertical-align:middle;white-space:nowrap;}'."\r\n";
	$tmp0 = ''; $tmp1 = ''; // version directe (le panier est en ajax)
	$div0 = ''; $div1 = ''; $hover = '';
	if(!empty($Ua['w3']))
		{
		$tmp0 = '<div class="w3-center"><div class="w3-bar">'; $tmp1 = '</div></div>';
		$div0 = '<div class="w3-button w3-hover-white">'; $div1 = '</div>';
		$hover = 'w3-hover-opacity ';
		}
	foreach($a1['method'] as $k=>$v)
		{
		if($k=='cheq' && $v) $tmp0 .= $div0.'<a href="JavaScript:void(0);" onClick="paymentCVCart(paymentC,0);"><img src="uno/plugins/payment/img/cheque-btn.png" class="'.$hover.'logo" /></a>'.$div1;
		else if($k=='vire' && $v) $tmp0 .= $div0.'<a href="JavaScript:void(0);" onClick="paymentCVCart(paymentC,1);"><img src="uno/plugins/payment/img/virement-btn.png" class="'.$hover.'logo" /></a>'.$div1;
		else if(isset($Ua['plug'][$k]) && $v) $tmp0 .= $div0.'<a href="JavaScript:void(0);" onClick="'.$k.'Cart(paymentC);"><img src="uno/plugins/'.$k.'/img/'.$k.'-btn.png" class="'.$hover.'logo" /></a>'.$div1;
		}
	$tmp0 .= $tmp1;
	$tmp = "<script type=\"text/javascript\">var paymentC,paymentBtn='".T_('Order')."';function paymentCart(f){paymentC=f;var g=eval('('+f+')');if(g['prod']){unoPop('".$tmp0."',0);}};</script>"."\r\n";

	$cart = '<div class="'.(isset($Uw3['dropdown']['w3-dropdown-click'])?$Uw3['dropdown']['w3-dropdown-click']:'w3-dropdown-click').(!empty($ali)?' w3-right':'').' cart" id="cart">
	<div class="w3-button" onclick="paymentOpenCart(1)"><img src="uno/plugins/payment/img/panier16'.(!empty($ico)?'blanc':'').'.png" alt="" /><span class="w3-badge w3-green" id="cartNb"></span></div>
	<div class="'.(isset($Uw3['dropdown']['w3-dropdown-content'])?$Uw3['dropdown']['w3-dropdown-content']:'w3-dropdown-content').' '.(isset($Uw3['card']['w3-card'])?$Uw3['card']['w3-card']:'w3-card').' cartBox w3-hide" id="cartBox" style="'.(!empty($col)?'background-color:'.$col.'!important;':'').(!empty($ali)?'right:0':'').'">
		<div class="w3-container">
			<table class="w3-table w3-bordered cartTable" id="cartTable">
			</table>
			<div class="w3-section">
				<button class="'.(isset($Uw3['card']['w3-button'])?$Uw3['card']['w3-button']:'w3-button').' w3-block" onClick="paymentBuy()">Order</button>
			</div>
		</div>
	</div></div><!-- #cart -->'."\r\n";


	if(strpos($Uhtml.$Ucontent,'[[paymentCart]]')!==false || strpos($Uhtml.$Ucontent,'paymentAddC')!==false)
		{
		if($it=='shortcode')
			{
			$Uhtml = str_replace('[[paymentCart]]',$cart,$Uhtml);
			$Ucontent = str_replace('[[paymentCart]]',$cart,$Ucontent);
			}
		else
			{
			$Umenu .= '<li>'.$cart.'</li>';
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
