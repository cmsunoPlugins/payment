<?php
if (isset($_GET['a']) && isset($_GET['b']))
	{
	include('../../config.php');
	include('lang/lang.php');
	$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
	$b = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, 'payment', base64_decode($_GET['b']), MCRYPT_MODE_ECB, $iv);
	$b = rtrim($b, "\0");
	$b = explode('|',$b);
	if(is_array($b))
		{
		$o = '<html><head><meta charset="utf-8"><style>.paymentTO td{padding:4px 10px;}.paymentTN{text-align:right;}</style></head><body style="background-color:#f6f6f6;"><div style="max-width:860px;margin:20px auto;padding:15px;background-color:#fff;">';
		switch ($_GET['a'])
			{
			// ********************************************************************************************
			case 'del':
			if(file_exists('../../data/_sdata-'.$sdata.'/_payment/'.$b[0].'.json'))
				{
				$q = file_get_contents('../../data/_sdata-'.$sdata.'/_payment/'.$b[0].'.json');
				$a = json_decode($q,true);
				if($a['mail']==$b[1] && $a['payed']==0)
					{
					unlink('../../data/_sdata-'.$sdata.'/_payment/'.$b[0].'.json');
					$o .= '<h1>'.T_('The order is canceled').'</h1>';
					}
				else $o .= '<h1>'.T_('Error').'</h1>';
				}
			else $o .= '<h1>'.T_('Error').'</h1>';
			break;
			// ********************************************************************************************
			case 'look':
			if(isset($_GET['t']) && file_exists('../../data/_sdata-'.$sdata.'/_'.$_GET['t'].'/'.$b[0].'.json'))
				{
				include('paymentGetData.php');
				$sys = $_GET['t'];
				$a = array(); $p = 0; $tax = 0; $typ = '';
				if($sys=='paypal') $a = getPaypalOrder($b[0],$sdata);
				else if($sys=='payplug') $a = getPayplugOrder($b[0],$sdata);
				else if($sys=='payment') $a = getPaymentOrder($b[0],$sdata);
				$curr = $a['curr'];
				$Ubusy = $a['Ubusy'];
				if($curr=='EUR') $curr = '&euro;';
				else if($curr=='USD' || $curr=='CAD') $curr = '$';
				else if($curr=='GBP') $curr = '£';
				$q = file_get_contents(dirname(__FILE__).'/../../data/'.$Ubusy.'/site.json'); $b1 = json_decode($q,true);
				$o .= '<h1 style="text-align:center;"><a href="'.$b1['url'].'">'.$b1['tit'].'</a></h1>';
				$o .= '<p>'.T_("Order").' : '.$b[0]. ' - '.date("d/m/Y H:i",$a['time']).'</p>';
				$o .= '<h3>'.T_("Order Details").'</h3>';
				$o .= '<table class="paymentTO"><tr><th>'.T_("Name").'</th><th>'.T_("Ref").'</th><th>'.T_("Price").'</th><th>'.T_("Tax").'</th><th>'.T_("Quantity").'</th><th>'.T_("Tax").'</th><th>'.T_("Total").'</th></tr>';
				foreach($a['prod'] as $r)
					{
					$t = getTax($a,$r);
					$o .= '<tr><td>'.$r['n'].'</td><td style="font-size:.8em">'.$r['i'].'</td><td>'.$r['p'].' '.$curr.'</td><td style="font-style:italic">'.$t.' '.$curr.'</td><td>'.$r['q'].'</td><td style="font-style:italic">'.($t*$r['q']).' '.$curr.'</td><td>'.(pt($r['p'])*$r['q']).' '.$curr.'</td><tr>';
					$p += (pt($r['p'])*$r['q']);
					$tax += ($t*$r['q']);
					}
				$o .= '<tr><td colspan="2" class="paymentTN">&nbsp;</td><td colspan="3" class="paymentTN">'.T_("Subtotal").' : </td><td>'.$tax.' '.$curr.'</td><td>'.$p.' '.$curr.'</td></tr>';
				if(isset($a['ship']))
					{
					$o .= '<tr><td colspan="2" class="paymentTN">&nbsp;</td><td colspan="4" class="paymentTN">'.T_("Shipping cost").' : </td><td>'.$a['ship'].' '.$curr.'</td></tr>';
					$p += pt($a['ship']);
					}
				$o .= '<tr><td colspan="2" class="paymentTN">&nbsp;</td><td colspan="4" class="paymentTN">'.T_("Total").' : </td><td style="font-weight:700">'.$p.' '.$curr.'</td></tr>';
				$o .= '</table>';
				$o .='<h3>'.T_("Shipping address").'</h3>';
				$o .= '<table class="paymentTO">';
				if($a['name'])
					{
					$o .= '<tr><td>'.T_("Name").' :</td><td>'.$a['name'].'</td></tr>';
					$o .= '<tr><td>'.T_("Address").' :</td><td>'.$a['adre'].'</td></tr>';
					$o .= '<tr><td>'.T_("Mail").' :</td><td>'.$a['mail'].'</td></tr>';
					}
				$o .= '</table>';
				if($sys=='payment' && isset($a['cv']) && $a['cv']=='cheq') $typ = T_("Cheque");
				else if($sys=='payment' && isset($a['cv']) && $a['cv']=='vire') $typ = T_("Bank Transfer");
				$o .= '<h3 style="text-transform:capitalize;">'.T_("Payment").' : '.($typ?$typ:$sys).'</h3>';
				$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
				$r = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'payment', $b[0].'|'.$a['mail'], MCRYPT_MODE_ECB, $iv));
				if(isset($a['payed']) && !$a['payed']) $o .= '<p>'.T_("Not paid").'</p>';
				else 
					{
					$o .= '<p>'.T_("Paid").'</p>';
					if(!$a['treated']) $o .= '<p>'.T_("Not treated").'</p>';
					else $o .= '<p>'.T_("Out for delivery").'</p>';
					$o .= '<p><a href="paymentPdf.php?k='.urlencode($r).'&s='.$sys.'&t=1" target="_blank" title="">'.T_("Invoice in PDF").'</a></p>';
					}
				}
			else $o .= '<h1>'.T_('Error').'</h1>';
			// else {sleep(2);exit;}
			break;
			// ********************************************************************************************
			}
		$o .= "</div></body></html>";
		echo $o;
		}
	else {sleep(2);exit;}
	}
else {sleep(2);exit;}
//
?>
