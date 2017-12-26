<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
?>
<?php
if(isset($_POST['a']))
	{
	include('../../config.php');
	include('lang/lang.php');
	switch($_POST['a'])
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
			$q1 = file_get_contents('../../data/payment.json');
			$a1 = json_decode($q1,true);
			$q2 = file_get_contents('../../data/'.$Ubusy.'/addtocart.json');
			$a2 = json_decode($q2,true);
			$n = 0; $p = 0; if(!$a1['ship']) $a1['ship'] = 0; $tax = 0; $o = '';
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
			$p += $a1['ship'];
			$o .= '<div class="w3-panel w3-hide w3-red popAlert" id="popAlert"></div>';
			$o .= '<div class="w3-row-padding popPayment"><div class="w3-col m7"><h3>'.T_('Shipping').'</h3><table class="popAdress">';
			$o .= '<tr><td>'.T_("Name").'*</td><td><input class="w3-input" id="popNa" type="text"></td></tr>';
			$o .= '<tr><td>'.T_("Address").'*</td><td><input class="w3-input" id="popAd" type="text"></td></tr>';
			$o .= '<tr><td>'.T_("Mail").'*</td><td><input class="w3-input" id="popMa" type="text"></td></tr>';
			$o .= '</table></div><div class="w3-col m5"><h3>'.T_('Order').'</h3>';
			$o .= '<div>'.T_('Order').' : '.$p.' '.$a1['curr'].'<br>';
			if($tax)
				{
				$o .= T_('Tax').' : '.$tax.' '.$a1['curr'].'<br/>';
				$p += $tax;
				}
			$o .= T_('Shipping cost').' : '.$a1['ship'].' '.$a1['curr'].'<br/>'.T_("Total").' : '.$p.' ' .$a1['curr'].'</div>';
			$o .= '</div></div>';
			$o .= '<div class="w3-section w3-center"><div class="w3-bar">';
			foreach($a1['method'] as $k=>$v)
				{
				if($k=='cheq' && $v) $o .= '<span class="w3-button w3-hover-white" id="popCheq"><img src="uno/plugins/payment/img/cheque-btn.png" class="w3-hover-opacity" alt="'.T_("Pay by cheque").'" title="'.T_("Pay by cheque").'" /></span>';
				else if($k=='vire' && $v) $o .= '<span class="w3-button w3-hover-white" id="popVire"><img src="uno/plugins/payment/img/virement-btn.png" class="w3-hover-opacity" alt="'.T_("Bank transfer").'" title="'.T_("Bank transfer").'" /></span>';
				else if($v) $o .= '<span class="w3-button w3-hover-white" id="pop'.$k.'""><img src="uno/plugins/'.$k.'/img/'.$k.'-btn.png" class="w3-hover-opacity" alt="'.$k.'" title="'.$k.'" /></span>';
				}
			$o .= '</div></div><div id="popAlert"></div>';
			echo $cartJson.'|;'.$o.'|;'.T_('Fields are mandatory').'|;'.T_('Invalid email');
			}
		break;
		// ********************************************************************************************
		case 'cv':
		$cart = getCookieCart();
		$bio = (isset($_POST['c'])?json_decode(stripslashes($_POST['c']),true):false);
		$bio['mail'] = (!empty($bio['mail'])?$bio['mail']:false);
		if(($_POST['d']=='cheq' || $_POST['d']=='vire') && (!empty($bio['digital']) || (filter_var($bio['mail'],FILTER_VALIDATE_EMAIL) && $cart)))
			{
			$Ubusy = $_POST['b'];
			if(file_exists(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/ssite.json'))
				{
				$q = file_get_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/ssite.json'); $b = json_decode($q,true);
				$mailAdmin = $b['mel'];
				}
			else $mailAdmin = false;
			$q1 = file_get_contents('../../data/payment.json');
			$a1 = json_decode($q1,true);
			if(empty($bio['digital']))
				{
				include dirname(__FILE__).'/../../template/mailTemplate.php';
				$q = file_get_contents(dirname(__FILE__).'/../../data/'.$Ubusy.'/site.json'); $b = json_decode($q,true);
				$subject = $b['tit'] . ' - ' . T_('Order');
				$cartJson = '';
				if(empty($a1['ship'])) $a1['ship'] = 0;
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
					
				$p += floatval($a1['ship']);
				$o = '<div class="w3-row-padding popPayment"><div class="w3-col m7">';
				if($_POST['d']=='cheq') $o .= '<h3>'.T_("Pay by cheque").'</h3><div>'.T_('Send your cheque to the following address').' :<br />'.$a1['adre'].'<br />'.T_('Payable to').' : '.$a1['own'].'</div>';
				else if($_POST['d']=='vire') $o .= '<h3>'.T_("Bank transfer").'</h3><div>'.T_('Transfer your payment to the following account').' :<br />'.T_('Name').' : '.$a1['own'].'<br />'.T_('IBAN').' : '.$a1['iban'].'<br />'.T_('BIC').' : '.$a1['bic'].'</div>';
				$o .= '</div><div class="w3-col m5"><h3>'.T_('Order').'</h3>';
				$o .= '<div>'.T_('Order').' : '.$p.' '.$a1['curr'].'<br />'.T_('Shipping cost').' : '.$a1['ship'].' '.$a1['curr'].'<br />'.T_("Total").' : '.$p.' ' .$a1['curr'].'</div></div></div>';
				$o .= '<div class="w3-section">'.T_('A summary email with the elements for payment was sent to you.').'<br />'.T_('Thank you for your trust.').'</div>';
				$u .= '</p><p>'.T_('Order').' : '.$p.' '.$a1['curr'].'<br />'.T_('Shipping cost').' : '.$a1['ship'].' '.$a1['curr'].'<br />';
				$u .= T_("Total").'<strong> : '.$p.' ' .$a1['curr'].'</strong></p>';
				$u = str_replace(".",",",$u);
				$ref = preg_replace("/[^a-zA-Z]+/","",$bio['name']);
				$ref = date("di").substr($ref,0,5).intval($p);
				if(file_exists('../../data/_sdata-'.$sdata.'/_payment/'.$ref.'.json')) $ref = date("di").substr($ref,0,5).(intval($p)+1);
				if(file_exists('../../data/_sdata-'.$sdata.'/_payment/'.$ref.'.json')) $ref = time();
				$cartJson .= '},"ship":"'.$a1['ship'].'","Utax":"'.$a1['taa'].'|'.$a1['tab'].'|'.$a1['tac'].'|'.$a1['tad'].'","time":"'.time().'","treated":"0","payed":"0","curr":"'.$a1['curr'].'","Ubusy":"'.$Ubusy.'","name":"'.$bio['name'].'","adre":"'.$bio['adre'].'","mail":"'.$bio['mail'].'","total":"'.$p.'","id":"'.$ref.'"';
				// Link to destroy order
				$iv = openssl_random_pseudo_bytes(16);
				$r = base64_encode(openssl_encrypt($ref.'|'.$bio['mail'], 'AES-256-CBC', substr($Ukey,0,32), OPENSSL_RAW_DATA, $iv));
				$info = "<a href='".stripslashes($b['url']).'/uno/plugins/payment/paymentOrder.php?a=look&b='.urlencode($r).'&i='.base64_encode($iv)."&t=payment'>".T_("Follow the evolution of your order")."</a>";
				$supp = "<a href='".stripslashes($b['url']).'/uno/plugins/payment/paymentOrder.php?a=del&b='.urlencode($r).'&i='.base64_encode($iv)."'>".T_("Cancel this order")."</a>";
				$bottom = str_replace('[[unsubscribe]]',$supp, $bottom); // template
				//
				if($_POST['d']=='cheq')
					{
					$ua = $u . '<hr /><p>'.T_('Name').' : '.$bio['name'].'<br />'.T_('Address').' : '.$bio['adre'].'<br />'.T_('Mail').' : '.$bio['mail'].'</p>';
					$u .= '<hr /><p>'.T_('Send your cheque to the following address').' :<br />'.$a1['adre'].'<br />'.T_('Payable to').' : '.$a1['own'].'<br />'.T_('Ref to mention').' : '.$ref.'</p>';
					$u .= '<p>'.T_('We expect your payment within 10 days. After this time, the order will be destroyed.').'</p>';
					$u .= '<p>'.T_('Thank you for your trust.').'</p>';
					$u .= '<p>'.$info.'</p>';
					if($mailAdmin) mailAdmin(T_('New order by cheque'). ' - '.$ref, $ua, $bottom, $top, $b['url']);
					if($bio['mail']) mailUser($bio['mail'], $subject, $u, $bottom, $top, $b['url']);
					$cartJson .= ',"cv":"cheq"}';
					$out = stripslashes($cartJson);
					file_put_contents('../../data/_sdata-'.$sdata.'/_payment/'.$ref.'.json', $out);
					}
				else if($_POST['d']=='vire')
					{
					$ua = $u . '<hr /><p>'.T_('Name').' : '.$bio['name'].'<br />'.T_('Address').' : '.$bio['adre'].'<br />'.T_('Mail').' : '.$bio['mail'].'</p>';
					$u .= '<hr /><p>'.T_('Transfer your payment to the following account').' :<br />'.T_('Name').' : '.$a1['own'].'<br />'.T_('IBAN').' : '.$a1['iban'].'<br />'.T_('BIC').' : '.$a1['bic'].'<br />'.T_('Ref to mention').' : '.$ref.'</p>';
					$u .= '<p>'.T_('We expect your payment within 10 days. After this time, the order will be destroyed.').'</p>';
					$u .= '<p>'.T_('Thank you for your trust.').'</p>';
					$u .= '<p>'.$info.'</p>';
					if($mailAdmin) mailAdmin(T_('New order by bank transfer'). ' - '.$ref, $ua, $bottom, $top, $b['url']);
					if($bio['mail']) mailUser($bio['mail'], $subject, $u, $bottom, $top, $b['url']);
					$cartJson .= ',"cv":"vire"}';
					$out = stripslashes($cartJson);
					file_put_contents('../../data/_sdata-'.$sdata.'/_payment/'.$ref.'.json', $out);
					}
				}
			else
				{
				$o = '<div class="w3-row-padding popPayment"><div class="w3-col m7">';
				if($_POST['d']=='cheq') $o .= '<h3>'.T_("Pay by cheque").'</h3><div>'.T_('Send your cheque to the following address').' :<br />'.$a1['adre'].'<br />'.T_('Payable to').' : '.$a1['own'].'</div>';
				else if($_POST['d']=='vire') $o .= '<h3>'.T_("Bank transfer").'</h3><div>'.T_('Transfer your payment to the following account').' :<br />'.T_('Name').' : '.$a1['own'].'<br />'.T_('IBAN').' : '.$a1['iban'].'<br />'.T_('BIC').' : '.$a1['bic'].'</div>';
				$o .= '</div><div class="w3-col m5"><h3>'.T_('Order').'</h3>';
				$o .= '<div>'.$bio['prod'][0]['n'].' : '.$bio['prod'][0]['p'].' '.$a1['curr'].'</div></div></div>';
				$o .= '<div class="w3-section">'.T_('Do not forget to also send your email address. We will send you the file.').'<br />'.T_('Thank you for your trust.').'</div>';
				}
			echo $o;
			}
		else if(!filter_var($bio['mail'],FILTER_VALIDATE_EMAIL)) echo T_('Invalid email'). ' : ' . $bio['mail'];
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
		return $b;
		}
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
function mailAdmin($tit, $message, $bottom, $top, $url)
	{
	global $mailAdmin;
	$body = '<b><a href="'.$url.'/uno.php" style="color:#000000;">'.$tit.'</a></b><br />'."\r\n".$message."\r\n";
	$msgT = strip_tags($body);
	$msgH = $top . $body . $bottom;
	if(file_exists(dirname(__FILE__).'/../newsletter/PHPMailer/PHPMailerAutoload.php'))
		{
		// PHPMailer
		require_once(dirname(__FILE__).'/../newsletter/PHPMailer/PHPMailerAutoload.php');
		$phm = new PHPMailer();
		$phm->charSet = "UTF-8";
		$phm->setFrom($mailAdmin);
		$phm->addReplyTo($mailAdmin);
		$phm->addAddress($mailAdmin);
		$phm->isHTML(true);
		$phm->subject = stripslashes($tit);
		$phm->body = stripslashes($msgH);		
		$phm->altBody = stripslashes($msgT);
		if($phm->send()) return true;
		else return false;
		}
	else
		{
		$rn = "\r\n";
		$boundary = "-----=".md5(rand());
		$header  = "From: ".$mailAdmin."<".$mailAdmin.">".$rn."Reply-To:".$mailAdmin."<".$mailAdmin.">";
		$header .= "MIME-Version: 1.0".$rn;
		$header .= "Content-Type: multipart/alternative;".$rn." boundary=\"$boundary\"".$rn;
		$msg = $rn."--".$boundary.$rn;
		$msg .= "Content-Type: text/plain; charset=\"utf-8\"".$rn;
		$msg .= "Content-Transfer-Encoding: 8bit".$rn;
		$msg .= $rn.$msgT.$rn;
		$msg .= $rn."--".$boundary.$rn;
		$msg .= "Content-Type: text/html; charset=\"utf-8\"".$rn;
		$msg .= "Content-Transfer-Encoding: 8bit".$rn;
		$msg .= $rn.$msgH.$rn;
		$msg .= $rn."--".$boundary."--".$rn;
		$msg .= $rn."--".$boundary."--".$rn;
		if(mail($mailAdmin, stripslashes($tit), stripslashes($msg), $header)) return true;
		else return false;
		}
	}
//
function mailUser($dest, $tit, $message, $bottom, $top, $url)
	{
	global $mailAdmin;
	$body = '<b><a href="'.$url.'" style="color:#000000;">'.$tit.'</a></b><br />'."\r\n".$message.'<br />'."\r\n";
	$msgT = strip_tags($body);
	$msgH = $top . $body . $bottom;
	if(file_exists(dirname(__FILE__).'/../newsletter/PHPMailer/PHPMailerAutoload.php'))
		{
		// PHPMailer
		require_once(dirname(__FILE__).'/../newsletter/PHPMailer/PHPMailerAutoload.php');
		$phm = new PHPMailer();
		$phm->charSet = "UTF-8";
		$phm->setFrom($mailAdmin);
		$phm->addReplyTo($mailAdmin);
		$phm->addAddress($dest);
		$phm->isHTML(true);
		$phm->subject = stripslashes($tit);
		$phm->body = stripslashes($msgH);		
		$phm->altBody = stripslashes($msgT);
		if($phm->send()) return true;
		else return false;
		}
	else
		{
		$rn = "\r\n";
		$boundary = "-----=".md5(rand());
		$header = 'From: '.$mailAdmin.$rn.'Reply-To: '.$mailAdmin.$rn.'X-Mailer: PHP/'.phpversion().$rn;
		$header .= "MIME-Version: 1.0".$rn;
		$header .= "Content-Type: multipart/alternative;".$rn." boundary=\"$boundary\"".$rn;
		$msg = $rn."--".$boundary.$rn;
		$msg .= "Content-Type: text/plain; charset=\"utf-8\"".$rn;
		$msg .= "Content-Transfer-Encoding: 8bit".$rn;
		$msg .= $rn.$msgT.$rn;
		$msg .= $rn."--".$boundary.$rn;
		$msg .= "Content-Type: text/html; charset=\"utf-8\"".$rn;
		$msg .= "Content-Transfer-Encoding: 8bit".$rn;
		$msg .= $rn.$msgH.$rn;
		$msg .= $rn."--".$boundary."--".$rn;
		$msg .= $rn."--".$boundary."--".$rn;
		if(mail($dest, stripslashes($tit), $msg, $header)) return true;
		else return false;
		}
	}
//
?>
