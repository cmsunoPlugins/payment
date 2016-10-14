<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
?>
<?php
if (isset($_POST['a']))
	{
	include('../../config.php');
	include('lang/lang.php');
	switch ($_POST['a'])
		{
		// JSON : {"prod":{"0":{"n":"clef de 12","p":8.5,"i":"","q":1},"1":{"n":"tournevis","p":1.5,"i":"","q":2},"2":{"n":"papier craft","p":0.21,"i":"","q":30}},"ship":"4","name":"Sting","adre":"rue du lac 33234 PLOUG","Ubusy":"index"}
		// n=nom, p=prix, i=ID, q=quantite
		// ********************************************************************************************
		case 'buy':
		$Ubusy = $_POST['b'];
		$cart = getCookieCart();
		if($cart)
			{
			$cartJson = '';
			$q1 = file_get_contents('../../data/'.$Ubusy.'/payment.json');
			$a1 = json_decode($q1,true);
			$q2 = file_get_contents('../../data/'.$Ubusy.'/addtocart.json');
			$a2 = json_decode($q2,true);
			$n = 0; $p = 0; if(!$a1['ship']) $a1['ship'] = 0; $tax = 0;
			foreach($a2['cart'] as $r2)
				{
				foreach($cart as $r3)
					{
					if($r2['n']==$r3[0])
						{
						$t = 0;
						if($a1['taxout']!='yes') $t = getTax($a1,$r2);
						if($n) $cartJson .= ',';
						$cartJson .= '"'.$n.'":{"n":"'.substr($r3[0],4).'","p":'.($r2['p']+$t).',"i":"'.$r3[0].'","q":'.$r3[1].',"t":'.$r2['t'].'}';
						++$n;
						$p += ($r2['p'] * $r3[1]);
						if($t) $tax += ($t * $r3[1]);
						}
					}
				}
			if($n) $cartJson = '{"prod":{' . $cartJson . '},"ship":"'.$a1['ship'].'","Utax":"'.$a1['taa'].'|'.$a1['tab'].'|'.$a1['tac'].'|'.$a1['tad'].'","curr":"'.$a1['curr'].'"}';
			$o = '<div style="text-align:right">'.T_('Order').' : '.$p.' '.$a1['curr'].'<br/>';
			if($tax)
				{
				$o .= T_('Tax').' : '.$tax.' '.$a1['curr'].'<br/>';
				$p += $tax;
				}
			$o .= T_('Shipping cost').' : '.$a1['ship'].' '.$a1['curr'].'<br/>';
			$p += $a1['ship'];
			$o .= T_("Total").' : '.$p.' ' .$a1['curr'].'</div>';
			$o .= '<div style="text-align:left">'.T_('Shipping').' :</div>';
			$o .= '<table class="popAdress" >';
			$o .= '<tr><td>'.T_("Name").'*</td><td><input style="max-width:100%" type="text" id="popNa" /></td></tr>';
			$o .= '<tr><td>'.T_("Address").'*</td><td><input style="max-width:100%" type="text" id="popAd" /></td></tr>';
			$o .= '<tr><td>'.T_("Mail").'*</td><td><input style="max-width:100%" type="text" id="popMa" /></td></tr></table>';
			$o .= '<div style="text-align:center">';
			if(isset($a1['method']['plug']) && $a1['method']['plug']) $o .= '<a href="JavaScript:void(0);" id="popPlug"><img src="uno/plugins/payment/img/payplug76.png" alt="'.T_("Secure payment by card").'" title="'.T_("Secure payment by card").'" class="logo" /></a>';
			if(isset($a1['method']['ppal']) && $a1['method']['ppal']) $o .= '<a href="JavaScript:void(0);" id="popPpal"><img src="uno/plugins/payment/img/paypal76.png" alt="'.T_("Pay with your Paypal account").'" title="'.T_("Pay with your Paypal account").'" class="logo" /></a>';
			if(isset($a1['method']['cheq']) && $a1['method']['cheq']) $o .= '<a href="JavaScript:void(0);" id="popCheq"><img src="uno/plugins/payment/img/cheque76.png" alt="'.T_("Pay by cheque").'" title="'.T_("Pay by cheque").'" class="logo" /></a>';
			if(isset($a1['method']['vire']) && $a1['method']['vire']) $o .= '<a href="JavaScript:void(0);" id="popVire"><img src="uno/plugins/payment/img/virement76.png" alt="'.T_("Bank transfer").'" title="'.T_("Bank transfer").'" class="logo" /></a>';
			$o .= '</div><div id="popAlert"></div>';
			echo $cartJson.'|;'.$o.'|;'.T_('Fields are mandatory').'|;'.T_('Invalid email');
			}
		break;
		// ********************************************************************************************
		case 'cv':
		$cart = getCookieCart();
		$bio = (isset($_POST['c'])?json_decode(stripslashes($_POST['c'])):false);
		if($cart && ($_POST['d']=='cheq' || $_POST['d']=='vire') && filter_var($bio->mail,FILTER_VALIDATE_EMAIL))
			{
			$Ubusy = $_POST['b'];
			if(file_exists(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/ssite.json'))
				{
				$q = file_get_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/ssite.json'); $b = json_decode($q,true);
				$mailAdmin = $b['mel'];
				}
			else $mailAdmin = false;
			include dirname(__FILE__).'/../../template/mailTemplate.php';
			$q = file_get_contents(dirname(__FILE__).'/../../data/'.$Ubusy.'/site.json'); $b = json_decode($q,true);
			$subject = $b['tit'] . ' - ' . T_('Order');
			$cartJson = '';
			$q1 = file_get_contents('../../data/'.$Ubusy.'/payment.json');
			$a1 = json_decode($q1,true);
			$q2 = file_get_contents('../../data/'.$Ubusy.'/addtocart.json');
			$a2 = json_decode($q2,true);
			$n = 0; $p = 0; $u = '<p style="text-align:right;">'.date("d/m/Y H:i").'</p><p>'; $ua = ''; $cartJson = '{"prod":{';
			foreach($a2['cart'] as $r2)
				{
				foreach($cart as $r3)
					{
					if($r2['n']==$r3[0])
						{
						$t = 0;
						if($a1['taxout']!='yes') $t = getTax($a1,$r2);
						if($n) $cartJson .= ',';
						$cartJson .= '"'.$n.'":{"n":"'.substr($r3[0],4).'","p":"'.($r2['p']+$t).'","i":"'.$r3[0].'","q":"'.$r3[1].'","t":'.$r2['t'].'}';
						$u .= $r3[1].' x '.substr($r3[0],4).' ('.($r2['p']+$t).' '.$a1['curr'].') = '.(($r2['p']+$t) * $r3[1]).' '.$a1['curr'].'<br />';
						++$n;
						$p += (($r2['p']+$t) * $r3[1]);
						}
					}
				}
			$o = '<div style="text-align:right">'.T_('Order').' : '.$p.' '.$a1['curr'].'<br />'.T_('Shipping cost').' : '.$a1['ship'].' '.$a1['curr'].'<br />';
			$u .= '</p><p>'.T_('Order').' : '.$p.' '.$a1['curr'].'<br />'.T_('Shipping cost').' : '.$a1['ship'].' '.$a1['curr'].'<br />';
			$p += $a1['ship'];
			$o .= T_("Total").' : '.$p.' ' .$a1['curr'].'</div>';
			$u .= T_("Total").'<strong> : '.$p.' ' .$a1['curr'].'</strong></p>';
			$u = str_replace(".",",",$u);
			$ref = preg_replace("/[^a-zA-Z]+/","",$bio->name);
			$ref = date("di").substr($ref,0,5).intval($p);
			if(file_exists('../../data/_sdata-'.$sdata.'/_payment/'.$ref.'.json')) $ref = date("di").substr($ref,0,5).(intval($p)+1);
			if(file_exists('../../data/_sdata-'.$sdata.'/_payment/'.$ref.'.json')) $ref = time();
			$cartJson .= '},"ship":"'.$a1['ship'].'","Utax":"'.$a1['taa'].'|'.$a1['tab'].'|'.$a1['tac'].'|'.$a1['tad'].'","time":"'.time().'","treated":"0","payed":"0","curr":"'.$a1['curr'].'","Ubusy":"'.$Ubusy.'","name":"'.$bio->name.'","adre":"'.$bio->adre.'","mail":"'.$bio->mail.'","total":"'.$p.'","id":"'.$ref.'"';
			// Link to destroy order
			$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
			$r = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'payment', $ref.'|'.$bio->mail, MCRYPT_MODE_ECB, $iv));
			$info = "<a href='".stripslashes($b['url']).'/uno/plugins/payment/paymentOrder.php?a=look&b='.urlencode($r)."&t=payment'>".T_("Follow the evolution of your order")."</a>";
			$supp = "<a href='".stripslashes($b['url']).'/uno/plugins/payment/paymentOrder.php?a=del&b='.urlencode($r)."'>".T_("Cancel this order")."</a>";
			$bottom = str_replace('[[unsubscribe]]',$supp, $bottom); // template
			//
			if($_POST['d']=='cheq')
				{
				$o .= '<hr /><div style="text-align:left">'.T_('Send your cheque to the following address').' :<br />'.$a1['adre'].'<br />'.T_('Payable to').' : '.$a1['own'].'</div>';
				$ua = $u . '<hr /><p>'.T_('Name').' : '.$bio->name.'<br />'.T_('Address').' : '.$bio->adre.'<br />'.T_('Mail').' : '.$bio->mail.'</p>';
				$u .= '<hr /><p>'.T_('Send your cheque to the following address').' :<br />'.$a1['adre'].'<br />'.T_('Payable to').' : '.$a1['own'].'<br />'.T_('Ref to mention').' : '.$ref.'</p>';
				$u .= '<p>'.T_('We expect your payment within 10 days. After this time, the order will be destroyed.').'</p>';
				$u .= '<p>'.T_('Thank you for your trust.').'</p>';
				$u .= '<p>'.$info.'</p>';
				if($mailAdmin) mailAdmin(T_('New order by cheque'). ' - '.$ref, $ua, $bottom, $top, $b['url']);
				if($bio->mail) mailUser($bio->mail, $subject, $u, $bottom, $top, $b['url']);
				$cartJson .= ',"cv":"cheq"}';
				$out = stripslashes($cartJson);
				file_put_contents('../../data/_sdata-'.$sdata.'/_payment/'.$ref.'.json', $out);
				}
			else if($_POST['d']=='vire')
				{
				$o .= '<hr /><div style="text-align:left">'.T_('Transfer your payment to the following account').' :<br />'.T_('Name').' : '.$a1['own'].'<br />'.T_('IBAN').' : '.$a1['iban'].'<br />'.T_('BIC').' : '.$a1['bic'].'</div>';
				$ua = $u . '<hr /><p>'.T_('Name').' : '.$bio->name.'<br />'.T_('Address').' : '.$bio->adre.'<br />'.T_('Mail').' : '.$bio->mail.'</p>';
				$u .= '<hr /><p>'.T_('Transfer your payment to the following account').' :<br />'.T_('Name').' : '.$a1['own'].'<br />'.T_('IBAN').' : '.$a1['iban'].'<br />'.T_('BIC').' : '.$a1['bic'].'<br />'.T_('Ref to mention').' : '.$ref.'</p>';
				$u .= '<p>'.T_('We expect your payment within 10 days. After this time, the order will be destroyed.').'</p>';
				$u .= '<p>'.T_('Thank you for your trust.').'</p>';
				$u .= '<p>'.$info.'</p>';
				if($mailAdmin) mailAdmin(T_('New order by bank transfer'). ' - '.$ref, $ua, $bottom, $top, $b['url']);
				if($bio->mail) mailUser($bio->mail, $subject, $u, $bottom, $top, $b['url']);
				$cartJson .= ',"cv":"vire"}';
				$out = stripslashes($cartJson);
				file_put_contents('../../data/_sdata-'.$sdata.'/_payment/'.$ref.'.json', $out);
				}
			$o .= '<hr /><div style="text-align:left">'.T_('A summary email with the elements for payment was sent to you.').'<br />'.T_('Thank you for your trust.').'</div>';
			echo $o;
			}
		else if(!filter_var($bio->mail,FILTER_VALIDATE_EMAIL)) echo T_('Invalid email'). ' : ' . $bio->mail;
		break;
		// ********************************************************************************************
		}
	}
//
function getCookieCart()
	{
	if(isset($_COOKIE['cart']))
		{
		$a = explode("||",$_COOKIE['cart']);
		$b = $a;
		foreach($a as $k=>$r) $b[$k] = explode("|",$r);
		}
	return $b;
	}
//
function getTax($a1,$t1)
	{
	// a1 : array - payment.json 
	// t1 : array - addtocart for this product
	// return : tax
	$ta=0; $tb=0; $tc=0; $td=0; $t=$t1['t'];
	// 1. active tax
	if($t>7) {$td = 1; $t -= 8;}
	if($t>3) {$tc = 1; $t -= 4;}
	if($t>1) {$tb = 1; $t -= 2;}
	if($t) $ta = 1;
	// 2. eval tax
	if($ta && strpos($a1['taa'],'%')) $ta *= floatval(str_replace('%','',$a1['taa'])) * .01 * $t1['p'];
	else if($ta) $ta *= floatval($a1['taa']);
	if($tb && strpos($a1['tab'],'%')) $tb *= floatval(str_replace('%','',$a1['tab'])) * .01 * $t1['p'];
	else if($tb) $tb *= floatval($a1['tab']);
	if($tc && strpos($a1['tac'],'%')) $tc *= floatval(str_replace('%','',$a1['tac'])) * .01 * $t1['p'];
	else if($tc) $tc *= floatval($a1['tac']);
	if($td && strpos($a1['tad'],'%')) $td *= floatval(str_replace('%','',$a1['tad'])) * .01 * $t1['p'];
	else if($td) $td *= floatval($a1['tad']);
	// 3. return tax
	return intval(($ta + $tb + $tc + $td)*100 + .5) / 100;
	}
//
function mailAdmin($tit, $subject, $bottom, $top, $url)
	{
	global $mailAdmin;
	$rn = "\r\n";
	$boundary = "-----=".md5(rand());
	$body = '<b><a href="'.$url.'/uno.php" style="color:#000000;">'.$tit.'</a></b><br />'.$rn.$subject.$rn;
	$msgT = strip_tags($body);
	$msgH = $top . $body . $bottom;
	$sujet = $tit;
	$header  = "From: ".$mailAdmin."<".$mailAdmin.">".$rn."Reply-To:".$mailAdmin."<".$mailAdmin.">";
	$header.= "MIME-Version: 1.0".$rn;
	$header.= "Content-Type: multipart/alternative;".$rn." boundary=\"$boundary\"".$rn;
	$msg= $rn."--".$boundary.$rn;
	$msg.= "Content-Type: text/plain; charset=\"utf-8\"".$rn;
	$msg.= "Content-Transfer-Encoding: 8bit".$rn;
	$msg.= $rn.$msgT.$rn;
	$msg.= $rn."--".$boundary.$rn;
	$msg.= "Content-Type: text/html; charset=\"utf-8\"".$rn;
	$msg.= "Content-Transfer-Encoding: 8bit".$rn;
	$msg.= $rn.$msgH.$rn;
	$msg.= $rn."--".$boundary."--".$rn;
	$msg.= $rn."--".$boundary."--".$rn;
	if(mail($mailAdmin, stripslashes($tit), stripslashes($msg), $header)) return true;
	else return false;
	}
//
function mailUser($dest, $sujet, $message, $bottom, $top, $url)
	{
	global $mailAdmin;
	$rn = "\r\n";
	$boundary = "-----=".md5(rand());
	$body = '<b><a href="'.$url.'" style="color:#000000;">'.$sujet.'</a></b><br />'.$rn.$message.'<br />'.$rn;
	$msgT = strip_tags($body);
	$msgH = $top . $body . $bottom;
	$header = 'From: '.$mailAdmin.$rn.'Reply-To: '.$mailAdmin.$rn.'X-Mailer: PHP/'.phpversion().$rn;
	$header.= "MIME-Version: 1.0".$rn;
	$header.= "Content-Type: multipart/alternative;".$rn." boundary=\"$boundary\"".$rn;
	$msg= $rn."--".$boundary.$rn;
	$msg.= "Content-Type: text/plain; charset=\"utf-8\"".$rn;
	$msg.= "Content-Transfer-Encoding: 8bit".$rn;
	$msg.= $rn.$msgT.$rn;
	$msg.= $rn."--".$boundary.$rn;
	$msg.= "Content-Type: text/html; charset=\"utf-8\"".$rn;
	$msg.= "Content-Transfer-Encoding: 8bit".$rn;
	$msg.= $rn.$msgH.$rn;
	$msg.= $rn."--".$boundary."--".$rn;
	$msg.= $rn."--".$boundary."--".$rn;
	if(mail($dest, stripslashes($sujet), $msg, $header)) return true;
	else return false;
	}
//
?>
