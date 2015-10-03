<?php
session_start(); 
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
?>
<?php
include('../../config.php');
if (!is_dir('../../data/_sdata-'.$sdata.'/_payment/')) mkdir('../../data/_sdata-'.$sdata.'/_payment/',0711);
include('lang/lang.php');
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'plugin': ?>
		<link rel="stylesheet" type="text/css" media="screen" href="uno/plugins/payment/payment.css" />
		<div class="blocForm">
			<div id="paymentA" class="bouton fr" onClick="f_paymentArchiv();" title="<?php echo _("Archives");?>"><?php echo _("Archives");?></div>
			<div id="paymentC" class="bouton fr" onClick="f_paymentConfig();" title="<?php echo _("Configure Payment");?>"><?php echo _("Config");?></div>
			<div id="paymentV" class="bouton fr current" onClick="f_paymentVente();" title="<?php echo _("Orders");?>"><?php echo _("Orders");?></div>
			<div id="paymentD" class="bouton fr current" title="<?php echo _("Order Details");?>" style="display:none;"><?php echo _("Order Details");?></div>
			<h2><?php echo _("Payment and Cart");?></h2>
			<div id="paymentConfig" style="display:none;">
				<p><?php echo _("This plugin allows to use one or more payment system. It also adds a cart system.");?>
				<?php echo _("Bank transfer and check included. Other payments systems require specific plugins (Paypal...).");?></p>
				<p><?php echo _("This plugin also add an 'add to cart' button") .'<img src="uno/plugins/payment/addtocart/icons/addtocart.png" style="border:1px solid #aaa;padding:3px;margin:0 6px -5px;border-radius:2px;" />' . _("in the text editor.").'&nbsp;'. _("It's very practical to put on sale some articles from the text editor."); ?></p>
				<p><?php echo _("To use this button and the cart system, you have to add the shortcode");?>&nbsp;<code>[[paymentCart]]</code>&nbsp;<?php echo _("in your page or in the template. That will display the cart content."); ?></p>
				<h3><?php echo _("Active payment :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo _("Paypal");?></label></td>
						<td><input type="checkbox" class="input" name="pml" id="pml" /></td>
						<td><em><?php echo _("Paypal Plugin needed. EXT mode must be activated.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Payplug");?></label></td>
						<td><input type="checkbox" class="input" name="pmg" id="pmg" /></td>
						<td><em><?php echo _("Payplug Plugin needed. EXT mode must be activated.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Bank Transfer");?></label></td>
						<td><input type="checkbox" class="input" name="pmv" id="pmv" /></td>
						<td><em><?php echo _("Enable the payment by bank transfer");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Cheque");?></label></td>
						<td><input type="checkbox" class="input" name="pmc" id="pmc" /></td>
						<td><em><?php echo _("Enable the payment by cheque");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Payment Address");?></label></td>
						<td><input type="text" class="input" name="pma" id="pma" /></td>
						<td><em><?php echo _("Cheque only. What name-address should the payment be sent ?");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Account owner");?></label></td>
						<td><input type="text" class="input" name="pmo" id="pmo" /></td>
						<td><em><?php echo _("Cheque and Bank transfer. Cheque payable to (name, society...)");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("IBAN");?></label></td>
						<td><input type="text" class="input" name="pmi" id="pmi" /></td>
						<td><em><?php echo _("Bank transfer only. International Bank Account Number");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("BIC");?></label></td>
						<td><input type="text" class="input" name="pmb" id="pmb" /></td>
						<td><em><?php echo _("Bank transfer only. Bank Identification Code");?></em></td>
					</tr>
				</table>
				<h3><?php echo _("Tax and shipping :");?></h3>
				<p><?php echo _("You can create up to 4 fixed or proportional taxes.").' '._("A proportional tax is written with the sign"); ?>&nbsp;<strong style="text-decoration:underline;">%</strong>.</p>
				<table class="hForm">
					<tr>
						<td><label><?php echo _("Tax");?> alpha</label></td>
						<td><input type="text" class="input" name="taa" id="taa" style="width:50px;" />
						<span style="padding-left:5px;"><?php echo _("Enabled by default"); ?></span><input type="checkbox" name="tda" id="tda" /></td>
						<td><em><?php echo _("Example : Fixed (0.55$) => 0.55 ; Proportional (19.6%) => 19.6%");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Tax");?> beta</label></td>
						<td><input type="text" class="input" name="tab" id="tab" style="width:50px;" />
						<span style="padding-left:5px;"><?php echo _("Enabled by default"); ?></span><input type="checkbox" name="tdb" id="tdb" /></td>
						<td><em></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Tax");?> gamma</label></td>
						<td><input type="text" class="input" name="tac" id="tac" style="width:50px;" />
						<span style="padding-left:5px;"><?php echo _("Enabled by default"); ?></span><input type="checkbox" name="tdc" id="tdc" /></td>
						<td><em></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Tax");?> delta</label></td>
						<td><input type="text" class="input" name="tad" id="tad" style="width:50px;" />
						<span style="padding-left:5px;"><?php echo _("Enabled by default"); ?></span><input type="checkbox" name="tdd" id="tdd" /></td>
						<td><em></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("I enter my prices :");?></label></td>
						<td>
							<select name="taxin" id="taxin">
								<option value="yes"><?php echo _("Tax included");?></option>
								<option value="no"><?php echo _("Duty free");?></option>
							</select>
						</td>
						<td><em><?php echo _("When I set the price of an item, the tax is ...");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Prices displayed :");?></label></td>
						<td>
							<select name="taxout" id="taxout">
								<option value="yes"><?php echo _("Tax included");?></option>
								<option value="no"><?php echo _("Duty free");?></option>
							</select>
						</td>
						<td><em><?php echo _("On the site, the price of the item is displayed...");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Shipping cost");?></label></td>
						<td><input type="text" class="input" name="shi" id="shi" style="width:50px;" /></td>
						<td><em><?php echo _("Shipping cost");?>.</em></td>
					</tr>
				</table>
				<h3><?php echo _("Cart :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo _("Currency");?></label></td>
						<td>
							<select name="cur" id="cur">
								<option value="EUR"><?php echo _("Euro");?></option>
								<option value="USD"><?php echo _("US Dollar");?></option>
								<option value="CAD"><?php echo _("Canadian Dollar");?></option>
								<option value="GBP"><?php echo _("Pound Sterling");?></option>
								<option value="CHF"><?php echo _("Swiss Franc");?></option>
								<option value="DKK"><?php echo _("Danish Krone");?></option>
								<option value="NOK"><?php echo _("Norwegian Krone");?></option>
								<option value="SEK"><?php echo _("Swedish Krona");?></option>
								<option value="PLN"><?php echo _("Polish Zloty");?></option>
								<option value="RUB"><?php echo _("Russian Ruble");?></option>
							</select>
						</td>
						<td><em><?php echo _("What is the currency of payment.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Integration");?></label></td>
						<td>
							<select name="it" id="it">
								<option value="shortcode"><?php echo _("Shortcode");?></option>
								<option value="menu"><?php echo _("Menu");?></option>
							</select>
						</td>
						<td><em><?php echo _("Use the shortcode [[paymentCart]] or use auto integration in the menu.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Box alignment");?></label></td>
						<td>
							<select name="ali" id="ali">
								<option value="left"><?php echo _("Left");?></option>
								<option value="right"><?php echo _("Right");?></option>
							</select>
						</td>
						<td><em><?php echo _("Use Right if the cart appears to the right of the window.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Box color");?></label></td>
						<td><input type="text" class="input" name="col" id="col" style="width:50px;" /></td>
						<td><em><?php echo _("Background color for the cart. HTML format (ex : #9f9f9f). Leave blank for automatic choice.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Icon color");?></label></td>
						<td>
							<select name="ico" id="ico">
								<option value="black"><?php echo _("Black");?></option>
								<option value="white"><?php echo _("White");?></option>
							</select>
						</td>
						<td><em><?php echo _("Color of the cart icon and the text.");?></em></td>
					</tr>
				</table>
				<div class="bouton fr" onClick="f_save_payment();" title="<?php echo _("Save");?>"><?php echo _("Save");?></div>
			</div>
			<div id="paymentDetail" style="display:none;"></div>
			<div id="paymentArchiv" style="display:none;"></div>
			<div id="paymentVente"></div>
			<div style="clear:both;"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		$q = file_get_contents('../../data/busy.json'); $a = json_decode($q,true); $Ubusy = $a['nom'];
		$q = @file_get_contents('../../data/'.$Ubusy.'/payment.json');
		if($q) $a = json_decode($q,true);
		else $a = Array();
		$a['method']['ppal'] = $_POST['ppal'];
		$a['method']['plug'] = $_POST['plug'];
		$a['method']['vire'] = $_POST['vire'];
		$a['method']['cheq'] = $_POST['cheq'];
		$a['taa'] = $_POST['taa'];
		$a['tab'] = $_POST['tab'];
		$a['tac'] = $_POST['tac'];
		$a['tad'] = $_POST['tad'];
		$a['tda'] = $_POST['tda'];
		$a['tdb'] = $_POST['tdb'];
		$a['tdc'] = $_POST['tdc'];
		$a['tdd'] = $_POST['tdd'];
		$a['taxin'] = $_POST['taxin'];
		$a['taxout'] = $_POST['taxout'];
		$a['ship'] = $_POST['ship'];
		$a['curr'] = $_POST['curr'];
		$a['it'] = $_POST['it'];
		$a['ali'] = $_POST['ali'];
		$a['col'] = $_POST['col'];
		$a['ico'] = $_POST['ico'];
		$a['adre'] = $_POST['adre'];
		$a['own'] = $_POST['own'];
		$a['iban'] = $_POST['iban'];
		$a['bic'] = $_POST['bic'];
		$out = json_encode($a);
		if (file_put_contents('../../data/'.$Ubusy.'/payment.json', $out)) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'vente':
		echo '<h3>'._("List of orders").' :</h3>';
		echo '<style>
			#paymentVente table tr{border-bottom:1px solid #888;}
			#paymentVente table th{text-align:center;padding:5px 2px;font-weight:700;}
			#paymentVente table td{text-align:center;padding:2px 6px;vertical-align:middle;color:#0b4a6a;}
			#paymentVente table td:nth-child(5){text-align:left;}
			#paymentVente table tr.PayTreatedYes td{color:green;}
			#paymentVente table tr.PayNo td, #paymentVente table tr.PayNo td a{color:#ff3b00;}
			#paymentVente table td.yesno{text-decoration:underline;cursor:pointer;}
		</style>';
		$tab='';
		if(file_exists('../../data/_sdata-'.$sdata.'/_paypal/'))
			{
			$d='../../data/_sdata-'.$sdata.'/_paypal/';
			if ($dh=opendir($d))
				{
				while (($file = readdir($dh))!==false) { if ($file!='.' && $file!='..') $tab[]=$d.$file; }
				closedir($dh);
				}
			}
		if(file_exists('../../data/_sdata-'.$sdata.'/_payplug/'))
			{
			$d='../../data/_sdata-'.$sdata.'/_payplug/';
			if ($dh=opendir($d))
				{
				while (($file = readdir($dh))!==false) { if ($file!='.' && $file!='..') $tab[]=$d.$file; }
				closedir($dh);
				}
			}
		$d='../../data/_sdata-'.$sdata.'/_payment/';
		if ($dh=opendir($d))
			{
			while (($file = readdir($dh))!==false) { if ($file!='.' && $file!='..') $tab[]=$d.$file; }
			closedir($dh);
			}
		if(count($tab) && is_array($tab))
			{
			echo '<br /><table>';
			echo '<tr><th>'._("Date").' - ID</th><th>'._("Type").'</th><th>'._("Name").'</th><th>'._("Address").'</th><th>'._("Article").'</th><th>'._("Price").'</th><th>'._("Treated").'</th></tr>';
			$b = array();
			foreach($tab as $r)
				{
				$q=@file_get_contents($r);
				$a=json_decode($q,true);
				$b[]=$a;
				}
			function sortTime($u1,$u2) {return (isset($u2['time'])?$u2['time']:0) - (isset($u1['time'])?$u1['time']:0);}
			usort($b, 'sortTime');
			foreach($b as $r)
				{
				if($r)
					{
					if(isset($r['txn_id']) && !isset($r['subscr_id']) && ((isset($r['quantity']) && $r['quantity']!="0") || (isset($r['txn_type']) && $r['txn_type']=="cart")) && isset($r['custom']) && strpos($r['custom'],'DIGITAL|')===false)
						{ // Paypal Payment (not DON or SUBSCR)
						$adr = ''; $name = ''; $mail = '';
						if(isset($r['custom']) && strpos($r['custom'],'ADRESS|')!==false) $c = explode('|',$r['custom']);
						if(is_array($c) && $c[0]=='ADRESS')
							{
							$name = $c[1];
							$adr = $c[2];
							$mail = $c[3];
							}
						$item=((isset($r['item_name']) && isset($r['quantity']))?$r['item_name'].(($r['quantity']!="0")?' ('.$r['quantity'].')':''):'');
						if(!$item)
							{
							$v=1;
							while(isset($r['item_name'.$v]))
								{
								$item.=($item?'<br />':'').$r['item_name'.$v].' ('.$r['quantity'.$v].')';
								++$v;
								}
							}
						echo '<tr'.($r['treated']?' class="PayTreatedYes"':'').'>';
						echo '<td>'.(isset($r['time'])?date("dMy H:i", $r['time']):'').'<br /><span style="font-size:.8em;text-decoration:underline;cursor:pointer;" onClick="f_paymentDetail(\''.$r['txn_id'].'\',\'paypal\')">'.$r['txn_id'].'</span></td>';
						echo '<td>Paypal</td>';
						if($name && $mail) echo '<td>'.$name.'<br />'.$mail.'</td>';
						else echo '<td>'.$r['first_name'].'&nbsp;'.$r['last_name'].'<br />'.$r['payer_email'].'</td>';
						if($adr) echo '<td>'.$adr.'</td>';
						else echo '<td>'.$r['address_street'].'<br />'.$r['address_zip'].' - '.$r['address_city'].'<br />'.$r['address_state'].' - '.$r['address_country'].'</td>';
						echo '<td>'.$item.'</td>';
						echo '<td>'.$r['mc_gross'].' '.($r['mc_currency']=='EUR'?'&euro;':$r['mc_currency']).'</td>';
						echo '<td '.(!$r['treated']?'onClick="f_treated_payment(this,\''.$r['txn_id'].'\',\''._("Yes").'\',\'paypal\')"':'').($r['treated']?'>'._("Yes"):' class="yesno">'._("Not treated")).'</td>';
						echo '</tr>';
						}
					else if(isset($r['idTransaction']) && isset($r['customData']) && strpos($r['customData'],'ADRESS|')!==false)
						{ // Payplug
						$item = ''; $adr = ''; $name = ''; $mail = '';
						if(isset($r['customData'])) $c = explode('|;',$r['customData']);
						if(is_array($c)) foreach($c as $r1)
							{
							$r2 = explode('|',$r1);
							if(is_array($r2) && $r2[0] && $r2[0]!='ADRESS') $item.=($item?'<br />':'').$r2[0].' ('.$r2[3].')'; // name, price, id, quantity
							else if(is_array($r2) && $r2[0]=='ADRESS')
								{
								$name = $r2[1];
								$adr = $r2[2];
								$mail = $r2[3];
								}
							}
						echo '<tr'.($r['treated']?' class="PayTreatedYes"':'').'>';
						echo '<td>'.(isset($r['time'])?date("dMy H:i", $r['time']):'').'<br /><span style="font-size:.8em;text-decoration:underline;cursor:pointer;" onClick="f_paymentDetail(\''.$r['idTransaction'].'\',\'payplug\')">'.$r['idTransaction'].'</span></td>';
						echo '<td>Payplug</td>';
						if($name && $mail) echo '<td>'.$name.'<br />'.$mail.'</td>';
						else echo '<td>'.$r['firstName'].'&nbsp;'.$r['lastName'].'<br />'.$r['email'].'</td>';
						echo '<td>'.$adr.'</td>';
						echo '<td>'.$item.'</td>';
						echo '<td>'.($r['amount']/100).'&euro;</td>';
						echo '<td '.(!$r['treated']?'onClick="f_treated_payment(this,\''.$r['idTransaction'].'\',\''._("Yes").'\',\'payplug\')"':'').($r['treated']?'>'._("Yes"):' class="yesno">'._("Not treated")).'</td>';
						echo '</tr>';
						}
					else if(isset($r['cv']) && isset($r['prod']))
						{ // Payment Cheque & Bank transfer (virement)
						$item = ''; $adr = ''; $name = ''; $mail = '';
						foreach($r['prod'] as $r1)
							{
							$item.=($item?'<br />':'').$r1['n'].' ('.$r1['q'].')'; // name, price, id, quantity
							}
						echo '<tr'.($r['payed']?($r['treated']?' class="PayTreatedYes"':''):' class="PayNo"').'>';
						echo '<td>'.(isset($r['time'])?date("dMy H:i", $r['time']):'').'<br /><span style="font-size:.8em;text-decoration:underline;cursor:pointer;" onClick="f_paymentDetail(\''.$r['id'].'\',\'payment\')">'.$r['id'].'</span></td>';
						if($r['cv']=='cheq') echo '<td>'._('Cheque').'</td>';
						else if($r['cv']=='vire') echo '<td>'._('Transfer').'</td>';
						else echo '<td>?</td>';
						echo '<td>'.$r['name'].'<br />'.$r['mail'].'</td>';
						echo '<td>'.$r['adre'].'</td>';
						echo '<td>'.$item.'</td>';
						echo '<td>'.$r['total'].'&euro;</td>';
						if(!$r['payed']) echo '<td onClick="f_payed_payment(this,\''.$r['id'].'\',\''._("Not treated").'\',\''._("Yes").'\')" class="yesno">'._("Not paid").'</td>';
						else if(!$r['treated']) echo '<td onClick="f_treated_payment(this,\''.$r['id'].'\',\''._("Yes").'\',\'payment\')" class="yesno">'._("Not treated").'</td>';
						else echo '<td>'._("Yes").'</td>';
						echo '</tr>';
						}
					}
				}
			echo '</table>';
			}
		break;
		// ********************************************************************************************
		case 'payed':
		$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_payment/'.$_POST['id'].'.json');
		if($q)
			{
			$a = json_decode($q,true);
			$a['payed'] = 1;
			$out = json_encode($a);
			if (file_put_contents('../../data/_sdata-'.$sdata.'/_payment/'.$_POST['id'].'.json', $out)) echo _('Paid');
			else echo '!'._('Error');
			}
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'treated':
		$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json');
		if(isset($q) && $q)
			{
			$a = json_decode($q,true);
			$a['treated'] = 1;
			$out = json_encode($a);
			if($_POST['typ']=='paypal' && file_put_contents('../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['id'].'.json', $out)) echo _('Treated');
			else if($_POST['typ']=='payplug' && file_put_contents('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json', $out)) echo _('Treated');
			else if($_POST['typ']=='payment' && isset($a['payed']) && $a['payed']==1 && file_put_contents('../../data/_sdata-'.$sdata.'/_payment/'.$_POST['id'].'.json', $out)) echo _('Treated');
			else echo '!'._('Error');
			}
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'reset':
		$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json');
		if(isset($q) && $q)
			{
			$a = json_decode($q,true);
			$a['treated'] = 0;
			if($_POST['typ']=='payment') $a['payed'] = 0;
			$out = json_encode($a);
			if($_POST['typ']=='paypal' && file_put_contents('../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['id'].'.json', $out)) echo _('Reset');
			else if($_POST['typ']=='payplug' && file_put_contents('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json', $out)) echo _('Reset');
			else if($_POST['typ']=='payment' && file_put_contents('../../data/_sdata-'.$sdata.'/_payment/'.$_POST['id'].'.json', $out)) echo _('Reset');
			else echo '!'._('Error');
			}
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'del':
		if(file_exists('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json') && unlink('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json')) echo _('Deleted');
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'archiv':
		if(!is_dir('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/archive')) mkdir('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/archive',0711);
		if(file_exists('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json') && rename('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json','../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/archive/'.$_POST['id'].'.json')) echo _('Archived');
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'restaur':
		if(file_exists('../../data/_sdata-'.$sdata.'/_payment/archive/'.$_POST['f']) && rename('../../data/_sdata-'.$sdata.'/_payment/archive/'.$_POST['f'],'../../data/_sdata-'.$sdata.'/_payment/'.$_POST['f'])) echo _('Restored');
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'viewArchiv':
		if (is_dir('../../data/_sdata-'.$sdata.'/_payment/archive') && $h=opendir('../../data/_sdata-'.$sdata.'/_payment/archive'))
			{
			$o = '<div id="paymentArchData"></div><div>';
			while(($d=readdir($h))!==false)
				{
				$ext=explode('.',$d); $ext=$ext[count($ext)-1];
				if($d!='.' && $d!='..' && $ext=='json')
					{
					$o .= '<div class="paymentListArchiv" onClick="f_paymentViewA(\''.$d.'\');">'.$d.'</div>';
					}
				}
			closedir($h);
			echo $o.'</div><div style="clear:left;"></div>';
			}
		break;
		// ********************************************************************************************
		case 'viewA':
		if(isset($_POST['arch']) && file_exists('../../data/_sdata-'.$sdata.'/_payment/archive/'.$_POST['arch']))
			{
			$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_payment/archive/'.$_POST['arch']);
			$a = json_decode($q,true); $o = '<h3>'._('Archives').'</h3><table class="paymentTO">';
			foreach($a as $k=>$v)
				{
				if($k=='time') $v .= ' => '.date("d/m/Y H:i",$v);
				$o .= '<tr><td>'.$k.'</td><td>'.(is_array($v)?json_encode($v):$v).'</td></tr>';
				}
			echo $o.'</table><div class="bouton fr" onClick="f_paymentRestaurOrder(\''.$_POST['arch'].'\');" title="'._("Restore").'">'._("Restore").'</div><div style="clear:both;"></div>';
			}
		break;
		// ********************************************************************************************
		case 'detail':
		if(isset($_POST['id']) && isset($_POST['sys']) && file_exists('../../data/_sdata-'.$sdata.'/_'.$_POST['sys'].'/'.$_POST['id'].'.json'))
			{
			include('paymentGetData.php');
			$q = file_get_contents('../../data/busy.json'); $a1 = json_decode($q,true); $Ubusy = $a1['nom'];
			$q = file_get_contents('../../data/'.$Ubusy.'/payment.json'); $a1 = json_decode($q,true); $curr = $a1['curr'];
			$q = file_get_contents('../../data/'.$Ubusy.'/addtocart.json'); $a2 = json_decode($q,true);
			if($curr=='EUR') $curr = '&euro;';
			else if($curr=='USD' || $curr=='CAD') $curr = '$';
			else if($curr=='GBP') $curr = '£';
			$a = array(); $o = ''; $typ = ''; $p = 0; $tax = 0;
			if($_POST['sys']=='paypal') $a = getPaypalOrder($_POST['id'],$sdata);
			else if($_POST['sys']=='payplug') $a = getPayplugOrder($_POST['id'],$sdata);
			else if($_POST['sys']=='payment') $a = getPaymentOrder($_POST['id'],$sdata);
			$o .= '<p>'._("Order").' : '.$_POST['id']. ' - '.date("d/m/Y H:i",$a['time']).'</p>';
			$o .= '<h3>'._("Order Details").'</h3>';
			$o .= '<table class="paymentTO"><tr><th>'._("Name").'</th><th>'._("Ref").'</th><th>'._("Price").'</th><th>'._("Tax").'</th><th>'._("Quantity").'</th><th>'._("Tax").'</th><th>'._("Total").'</th></tr>';
			foreach($a['prod'] as $r)
				{
				$t = getTax($a,$r);
				$o .= '<tr><td>'.$r['n'].'</td><td style="font-size:.8em">'.$r['i'].'</td><td>'.$r['p'].' '.$curr.'</td><td style="font-style:italic">'.$t.' '.$curr.'</td><td>'.$r['q'].'</td><td style="font-style:italic">'.($t*$r['q']).' '.$curr.'</td><td>'.(pt($r['p'])*$r['q']).' '.$curr.'</td><tr>';
				$p += (pt($r['p'])*$r['q']);
				$tax += ($t*$r['q']);
				}
			$o .= '<tr><td colspan="2" class="paymentTN">&nbsp;</td><td colspan="3" class="paymentTN">'._("Subtotal").' : </td><td>'.$tax.' '.$curr.'</td><td>'.$p.' '.$curr.'</td></tr>';
			if(isset($a['ship']))
				{
				$o .= '<tr><td colspan="2" class="paymentTN">&nbsp;</td><td colspan="4" class="paymentTN">'._("Shipping cost").' : </td><td>'.$a['ship'].' '.$curr.'</td></tr>';
				$p += pt($a['ship']);
				}
			$o .= '<tr><td colspan="2" class="paymentTN">&nbsp;</td><td colspan="4" class="paymentTN">'._("Total").' : </td><td style="font-weight:700">'.$p.' '.$curr.'</td></tr>';
			$o .= '</table>';
			$o .='<h3>'._("Shipping address").'</h3>';
			$o .= '<table class="paymentTO">';
			if($a['name'])
				{
				$o .= '<tr><td>'._("Name").' :</td><td>'.$a['name'].'</td></tr>';
				$o .= '<tr><td>'._("Address").' :</td><td>'.$a['adre'].'</td></tr>';
				$o .= '<tr><td>'._("Mail").' :</td><td>'.$a['mail'].'</td></tr>';
				}
			$o .= '</table>';
			if($_POST['sys']=='payment' && isset($a['cv']) && $a['cv']=='cheq') $typ = _("Cheque");
			else if($_POST['sys']=='payment' && isset($a['cv']) && $a['cv']=='vire') $typ = _("Bank Transfer");
			$o .= '<h3 style="text-transform:capitalize;">'._("Payment").' : '.($typ?$typ:$_POST['sys']).'</h3>';
			$o .= '<div id="Bdel" class="bouton fr" onClick="f_delOrderPayment(\''.$_POST['id'].'\',\''._("Are you sure ?").'\',\''.$_POST['sys'].'\')" title="">'._("Delete").'</div>';
			$o .= '<table><tr><td>';
			$o .= '<p id="Opayed">'._("Paid").' : '.((isset($a['payed']) && $a['payed']==0)?_("No"):_("Yes")).'</p>';
			$o .= '<p id="Otreated">'._("Treated").' : '.((isset($a['treated']) && $a['treated']==0)?_("No"):_("Yes")).'</p>';
			$o .= '</td><td style="vertical-align:middle">';
			$o .= '<div id="Bpayed" '.((!isset($a['payed']) || $a['payed']==1)?'style="display:none;" ':'').'class="bouton" onClick="f_payedOrderPayment(\''.$_POST['id'].'\',\''._("Paid").' : '._("Yes").'\')" title="">'._("Paid").'</div>';
			$o .= '<div id="Btreated" '.((isset($a['payed']) && $a['payed']==0 || isset($a['treated']) && $a['treated']==1)?'style="display:none;"':'').'class="bouton" onClick="f_treatedOrderPayment(\''.$_POST['id'].'\',\''._("Treated").' : '._("Yes").'\',\''.$_POST['sys'].'\')" title="">'._("Treated").'</div>';
			$o .= '<div id="Breset" '.((isset($a['treated']) && $a['treated']==0)?'style="display:none;"':'').'class="bouton" onClick="f_resetOrderPayment(\''.$_POST['id'].'\',\''._("Paid").' : '._("No").'\',\''._("Treated").' : '._("No").'\',\''.$_POST['sys'].'\')" title="">'._("Reset").'</div>';
			$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
			$r = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'payment', $_POST['id'].'|'.$a['mail'], MCRYPT_MODE_ECB, $iv));
			$o .= '<a href="uno/plugins/payment/paymentPdf.php?k='.urlencode($r).'&s='.$_POST['sys'].'&t=1" target="_blank" id="Bfacture" '.((isset($a['payed']) && $a['payed']==0)?'style="display:none;"':'').'class="bouton" title="">'._("Invoice in PDF").'</a>';
			$o .= '<div id="Barchiv" '.((isset($a['treated']) && $a['treated']==0)?'style="display:none;"':'').'class="bouton" onClick="f_archivOrderPayment(\''.$_POST['id'].'\',\''._("Are you sure ?").'\',\''.$_POST['sys'].'\')" title="">'._("Archive").'</div>';
			$o .= '</tr></table>';
			//
			$o .= '<hr /><h3 style="text-transform:capitalize;">'.$_POST['sys'].'</h3>';
			$o .= '<table>';
			foreach($a as $k=>$v)
				{
				if(is_array($v))
					{
					$o .= '<tr><td>'.$k.'&nbsp:&nbsp</td><td>';
					foreach($v as $v1) $o .= implode(" - ", $v1).'<br />';
					$o .= '</td></tr>';
					}
				else $o .= '<tr><td>'.$k.'&nbsp:&nbsp</td><td>'.$v.'</td></tr>';
				}
			$o .= '</table>';
			echo $o;
			}
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
//
?>
