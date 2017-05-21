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
		<style>
		.del{background:transparent url(<?php echo $_POST['udep']; ?>includes/img/close.png) no-repeat center center;cursor:pointer;padding:0 20px;margin-left:10px}
		</style>
		<div class="blocForm">
			<div id="paymentA" class="bouton fr" onClick="f_paymentArchiv();" title="<?php echo T_("Archives");?>"><?php echo T_("Archives");?></div>
			<div id="paymentC" class="bouton fr" onClick="f_paymentConfig();" title="<?php echo T_("Configure Payment");?>"><?php echo T_("Config");?></div>
			<div id="paymentV" class="bouton fr current" onClick="f_paymentVente();" title="<?php echo T_("Orders");?>"><?php echo T_("Orders");?></div>
			<div id="paymentD" class="bouton fr current" title="<?php echo T_("Order Details");?>" style="display:none;"><?php echo T_("Order Details");?></div>
			<h2><?php echo T_("Payment and Cart");?></h2>
			<div id="paymentConfig" style="display:none;">
				<p><?php echo T_("This plugin allows to use one or more payment system. It also adds a cart system.");?>
				<?php echo T_("Bank transfer and check included. Other payments systems require specific plugins (Paypal...).");?></p>
				<p><?php echo T_("This plugin also add an 'add to cart' button") .'<img src="uno/plugins/payment/addtocart/icons/addtocart.png" style="border:1px solid #aaa;padding:3px;margin:0 6px -5px;border-radius:2px;" />' . T_("in the text editor.").'&nbsp;'. T_("It's very practical to put on sale some articles from the text editor."); ?></p>
				<p><?php echo T_("To use this button and the cart system, you have to add the shortcode");?>&nbsp;<code>[[paymentCart]]</code>&nbsp;<?php echo T_("in your page or in the template. That will display the cart content."); ?></p>
				<h3><?php echo T_("Active payment :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo T_("Paypal");?></label></td>
						<td><input type="checkbox" class="input" name="pml" id="pml" /></td>
						<td><em><?php echo T_("Paypal Plugin needed. EXT mode must be activated.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Payplug");?></label></td>
						<td><input type="checkbox" class="input" name="pmg" id="pmg" /></td>
						<td><em><?php echo T_("Payplug Plugin needed. EXT mode must be activated.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Bank Transfer");?></label></td>
						<td><input type="checkbox" class="input" name="pmv" id="pmv" /></td>
						<td><em><?php echo T_("Enable the payment by bank transfer");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Cheque");?></label></td>
						<td><input type="checkbox" class="input" name="pmc" id="pmc" /></td>
						<td><em><?php echo T_("Enable the payment by cheque");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Payment Address");?></label></td>
						<td><input type="text" class="input" name="pma" id="pma" /></td>
						<td><em><?php echo T_("Cheque only. What name-address should the payment be sent ?");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Account owner");?></label></td>
						<td><input type="text" class="input" name="pmo" id="pmo" /></td>
						<td><em><?php echo T_("Cheque and Bank transfer. Cheque payable to (name, society...)");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("IBAN");?></label></td>
						<td><input type="text" class="input" name="pmi" id="pmi" /></td>
						<td><em><?php echo T_("Bank transfer only. International Bank Account Number");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("BIC");?></label></td>
						<td><input type="text" class="input" name="pmb" id="pmb" /></td>
						<td><em><?php echo T_("Bank transfer only. Bank Identification Code");?></em></td>
					</tr>
				</table>
				<h3><?php echo T_("Tax and shipping :");?></h3>
				<p><?php echo T_("You can create up to 4 fixed or proportional taxes.").' '.T_("A proportional tax is written with the sign"); ?>&nbsp;<strong style="text-decoration:underline;">%</strong>.</p>
				<table class="hForm">
					<tr>
						<td><label><?php echo T_("Tax");?> alpha</label></td>
						<td><input type="text" class="input" name="taa" id="taa" style="width:50px;" />
						<span style="padding-left:5px;"><?php echo T_("Enabled by default"); ?></span><input type="checkbox" name="tda" id="tda" /></td>
						<td><em><?php echo T_("Example : Fixed (0.55$) => 0.55 ; Proportional (19.6%) => 19.6%");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Tax");?> beta</label></td>
						<td><input type="text" class="input" name="tab" id="tab" style="width:50px;" />
						<span style="padding-left:5px;"><?php echo T_("Enabled by default"); ?></span><input type="checkbox" name="tdb" id="tdb" /></td>
						<td><em></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Tax");?> gamma</label></td>
						<td><input type="text" class="input" name="tac" id="tac" style="width:50px;" />
						<span style="padding-left:5px;"><?php echo T_("Enabled by default"); ?></span><input type="checkbox" name="tdc" id="tdc" /></td>
						<td><em></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Tax");?> delta</label></td>
						<td><input type="text" class="input" name="tad" id="tad" style="width:50px;" />
						<span style="padding-left:5px;"><?php echo T_("Enabled by default"); ?></span><input type="checkbox" name="tdd" id="tdd" /></td>
						<td><em></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("I enter my prices :");?></label></td>
						<td>
							<select name="taxin" id="taxin">
								<option value="yes"><?php echo T_("Tax included");?></option>
								<option value="no"><?php echo T_("Duty free");?></option>
							</select>
						</td>
						<td><em><?php echo T_("When I set the price of an item, the tax is ...");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Prices displayed :");?></label></td>
						<td>
							<select name="taxout" id="taxout">
								<option value="yes"><?php echo T_("Tax included");?></option>
								<option value="no"><?php echo T_("Duty free");?></option>
							</select>
						</td>
						<td><em><?php echo T_("On the site, the price of the item is displayed...");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Shipping cost");?></label></td>
						<td><input type="text" class="input" name="shi" id="shi" style="width:50px;" /></td>
						<td><em><?php echo T_("Shipping cost");?>.</em></td>
					</tr>
				</table>
				<h3><?php echo T_("Cart :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo T_("No Add To Cart button");?></label></td>
						<td><input type="checkbox" name="addtocartoff" id="addtocartoff" /></td>
						<td><em><?php echo T_("You don't want to use the CKEditor Add To Cart Button.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Currency");?></label></td>
						<td>
							<select name="cur" id="cur">
								<option value="EUR"><?php echo T_("Euro");?></option>
								<option value="USD"><?php echo T_("US Dollar");?></option>
								<option value="CAD"><?php echo T_("Canadian Dollar");?></option>
								<option value="GBP"><?php echo T_("Pound Sterling");?></option>
								<option value="CHF"><?php echo T_("Swiss Franc");?></option>
								<option value="DKK"><?php echo T_("Danish Krone");?></option>
								<option value="NOK"><?php echo T_("Norwegian Krone");?></option>
								<option value="SEK"><?php echo T_("Swedish Krona");?></option>
								<option value="PLN"><?php echo T_("Polish Zloty");?></option>
								<option value="RUB"><?php echo T_("Russian Ruble");?></option>
							</select>
						</td>
						<td><em><?php echo T_("What is the currency of payment.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Integration");?></label></td>
						<td>
							<select name="it" id="it">
								<option value="shortcode"><?php echo T_("Shortcode");?></option>
								<option value="menu"><?php echo T_("Menu");?></option>
							</select>
						</td>
						<td><em><?php echo T_("Use the shortcode [[paymentCart]] or use auto integration in the menu.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Box alignment");?></label></td>
						<td>
							<select name="ali" id="ali">
								<option value="left"><?php echo T_("Left");?></option>
								<option value="right"><?php echo T_("Right");?></option>
							</select>
						</td>
						<td><em><?php echo T_("Use Right if the cart appears to the right of the window.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Box color");?></label></td>
						<td><input type="text" class="input color" name="col" id="col" style="width:100px;" /><span class="del" onclick="f_del_payment(this);"></span></td>
						<td><em><?php echo T_("Background color for the cart. HTML format (ex : #9f9f9f). Leave blank for automatic choice.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Icon color");?></label></td>
						<td>
							<select name="ico" id="ico">
								<option value="black"><?php echo T_("Black");?></option>
								<option value="white"><?php echo T_("White");?></option>
							</select>
						</td>
						<td><em><?php echo T_("Color of the cart icon and the text.");?></em></td>
					</tr>
				</table>
				<div class="bouton fr" onClick="f_save_payment();" title="<?php echo T_("Save");?>"><?php echo T_("Save");?></div>
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
		$a['addtocartoff'] = ($_POST['addtocartoff']?1:0);
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
		if (file_put_contents('../../data/'.$Ubusy.'/payment.json', $out)) echo T_('Backup performed');
		else echo '!'.T_('Impossible backup');
		break;
		// ********************************************************************************************
		case 'vente':
		echo '<h3>'.T_("List of orders").' :</h3>';
		echo '<style>
			#paymentVente table tr{border-bottom:1px solid #888;}
			#paymentVente table th{text-align:center;padding:5px 2px;font-weight:700;}
			#paymentVente table td{text-align:center;padding:2px 6px;vertical-align:middle;color:#0b4a6a;}
			#paymentVente table td:nth-child(5){text-align:left;}
			#paymentVente table tr.PayTreatedYes td{color:green;}
			#paymentVente table tr.PayNo td, #paymentVente table tr.PayNo td a{color:#ff3b00;}
			#paymentVente table td.yesno{text-decoration:underline;cursor:pointer;}
		</style>';
		$tab = array();
		if(file_exists('../../data/_sdata-'.$sdata.'/_paypal/'))
			{
			$d = '../../data/_sdata-'.$sdata.'/_paypal/';
			if($dh=opendir($d))
				{
				while(($file = readdir($dh))!==false) { if ($file!='.' && $file!='..') $tab[] = $d.$file; }
				closedir($dh);
				}
			}
		if(file_exists('../../data/_sdata-'.$sdata.'/_payplug/'))
			{
			$d = '../../data/_sdata-'.$sdata.'/_payplug/';
			if($dh=opendir($d))
				{
				while(($file = readdir($dh))!==false) { if ($file!='.' && $file!='..') $tab[] = $d.$file; }
				closedir($dh);
				}
			}
		$d = '../../data/_sdata-'.$sdata.'/_payment/';
		if($dh=opendir($d))
			{
			while(($file = readdir($dh))!==false) { if ($file!='.' && $file!='..') $tab[] = $d.$file; }
			closedir($dh);
			}
		if(count($tab) && is_array($tab))
			{
			echo '<br /><table>';
			echo '<tr><th>'.T_("Date").' - ID</th><th>'.T_("Type").'</th><th>'.T_("Name").'</th><th>'.T_("Address").'</th><th>'.T_("Article").'</th><th>'.T_("Price").'</th><th>'.T_("Treated").'</th></tr>';
			$b = array();
			foreach($tab as $r)
				{
				$q = @file_get_contents($r);
				$a = json_decode($q,true);
				$b[] = $a;
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
						$item = ((isset($r['item_name']) && isset($r['quantity']))?$r['item_name'].(($r['quantity']!="0")?' ('.$r['quantity'].')':''):'');
						if(!$item)
							{
							$v = 1;
							while(isset($r['item_name'.$v]))
								{
								$item .= ($item?'<br />':'').$r['item_name'.$v].' ('.$r['quantity'.$v].')';
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
						echo '<td '.(!$r['treated']?'onClick="f_treated_payment(this,\''.$r['txn_id'].'\',\''.T_("Yes").'\',\'paypal\')"':'').($r['treated']?'>'.T_("Yes"):' class="yesno">'.T_("Not treated")).'</td>';
						echo '</tr>';
						}
					else if(isset($r['idTransaction']) && isset($r['customData']) && strpos($r['customData'],'ADRESS|')!==false)
						{ // Payplug
						$item = ''; $adr = ''; $name = ''; $mail = '';
						if(isset($r['customData'])) $c = explode('|;',$r['customData']);
						if(is_array($c)) foreach($c as $r1)
							{
							$r2 = explode('|',$r1);
							if(is_array($r2) && $r2[0] && $r2[0]!='ADRESS') $item .= ($item?'<br />':'').$r2[0].' ('.$r2[3].')'; // name, price, id, quantity
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
						echo '<td '.(!$r['treated']?'onClick="f_treated_payment(this,\''.$r['idTransaction'].'\',\''.T_("Yes").'\',\'payplug\')"':'').($r['treated']?'>'.T_("Yes"):' class="yesno">'.T_("Not treated")).'</td>';
						echo '</tr>';
						}
					else if(isset($r['cv']) && isset($r['prod']))
						{ // Payment Cheque & Bank transfer (virement)
						$item = ''; $adr = ''; $name = ''; $mail = '';
						foreach($r['prod'] as $r1)
							{
							$item .= ($item?'<br />':'').$r1['n'].' ('.$r1['q'].')'; // name, price, id, quantity
							}
						echo '<tr'.($r['payed']?($r['treated']?' class="PayTreatedYes"':''):' class="PayNo"').'>';
						echo '<td>'.(isset($r['time'])?date("dMy H:i", $r['time']):'').'<br /><span style="font-size:.8em;text-decoration:underline;cursor:pointer;" onClick="f_paymentDetail(\''.$r['id'].'\',\'payment\')">'.$r['id'].'</span></td>';
						if($r['cv']=='cheq') echo '<td>'.T_('Cheque').'</td>';
						else if($r['cv']=='vire') echo '<td>'.T_('Transfer').'</td>';
						else echo '<td>?</td>';
						echo '<td>'.$r['name'].'<br />'.$r['mail'].'</td>';
						echo '<td>'.$r['adre'].'</td>';
						echo '<td>'.$item.'</td>';
						echo '<td>'.$r['total'].'&euro;</td>';
						if(!$r['payed']) echo '<td onClick="f_payed_payment(this,\''.$r['id'].'\',\''.T_("Not treated").'\',\''.T_("Yes").'\')" class="yesno">'.T_("Not paid").'</td>';
						else if(!$r['treated']) echo '<td onClick="f_treated_payment(this,\''.$r['id'].'\',\''.T_("Yes").'\',\'payment\')" class="yesno">'.T_("Not treated").'</td>';
						else echo '<td>'.T_("Yes").'</td>';
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
			if (file_put_contents('../../data/_sdata-'.$sdata.'/_payment/'.$_POST['id'].'.json', $out)) echo T_('Paid');
			else echo '!'.T_('Error');
			}
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'treated':
		$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json');
		if(!empty($q))
			{
			$a = json_decode($q,true);
			$a['treated'] = 1;
			$out = json_encode($a);
			if($_POST['typ']=='paypal' && file_put_contents('../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['id'].'.json', $out)) echo T_('Treated');
			else if($_POST['typ']=='payplug' && file_put_contents('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json', $out)) echo T_('Treated');
			else if($_POST['typ']=='payment' && isset($a['payed']) && $a['payed']==1 && file_put_contents('../../data/_sdata-'.$sdata.'/_payment/'.$_POST['id'].'.json', $out)) echo T_('Treated');
			else echo '!'.T_('Error');
			}
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'reset':
		$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json');
		if(!empty($q))
			{
			$a = json_decode($q,true);
			$a['treated'] = 0;
			if($_POST['typ']=='payment') $a['payed'] = 0;
			$out = json_encode($a);
			if($_POST['typ']=='paypal' && file_put_contents('../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['id'].'.json', $out)) echo T_('Reset');
			else if($_POST['typ']=='payplug' && file_put_contents('../../data/_sdata-'.$sdata.'/_payplug/'.$_POST['id'].'.json', $out)) echo T_('Reset');
			else if($_POST['typ']=='payment' && file_put_contents('../../data/_sdata-'.$sdata.'/_payment/'.$_POST['id'].'.json', $out)) echo T_('Reset');
			else echo '!'.T_('Error');
			}
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'del':
		if(file_exists('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json') && unlink('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json')) echo T_('Deleted');
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'archiv':
		if(!is_dir('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/archive')) mkdir('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/archive',0711);
		if(file_exists('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json') && rename('../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/'.$_POST['id'].'.json','../../data/_sdata-'.$sdata.'/_'.$_POST['typ'].'/archive/'.$_POST['id'].'.json')) echo T_('Archived');
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'restaur':
		if(file_exists('../../data/_sdata-'.$sdata.'/_payment/archive/'.$_POST['f']) && rename('../../data/_sdata-'.$sdata.'/_payment/archive/'.$_POST['f'],'../../data/_sdata-'.$sdata.'/_payment/'.$_POST['f'])) echo T_('Restored');
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'viewArchiv':
		if(is_dir('../../data/_sdata-'.$sdata.'/_payment/archive') && $h = opendir('../../data/_sdata-'.$sdata.'/_payment/archive'))
			{
			$o = '<div id="paymentArchData"></div><div>';
			while(($d = readdir($h))!==false)
				{
				$ext = explode('.',$d); $ext=$ext[count($ext)-1];
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
			$a = json_decode($q,true);
			$o = '<h3>'.T_('Archives').'</h3><table class="paymentTO">';
			foreach($a as $k=>$v)
				{
				if($k=='time') $v .= ' => '.date("d/m/Y H:i",$v);
				$o .= '<tr><td>'.$k.'</td><td>'.(is_array($v)?json_encode($v):$v).'</td></tr>';
				}
			echo $o.'</table><div class="bouton fr" onClick="f_paymentRestaurOrder(\''.$_POST['arch'].'\');" title="'.T_("Restore").'">'.T_("Restore").'</div><div style="clear:both;"></div>';
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
			$o .= '<p>'.T_("Order").' : '.$_POST['id']. ' - '.date("d/m/Y H:i",$a['time']).'</p>';
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
			if($_POST['sys']=='payment' && isset($a['cv']) && $a['cv']=='cheq') $typ = T_("Cheque");
			else if($_POST['sys']=='payment' && isset($a['cv']) && $a['cv']=='vire') $typ = T_("Bank Transfer");
			$o .= '<h3 style="text-transform:capitalize;">'.T_("Payment").' : '.($typ?$typ:$_POST['sys']).'</h3>';
			$o .= '<div id="Bdel" class="bouton fr" onClick="f_delOrderPayment(\''.$_POST['id'].'\',\''.T_("Are you sure ?").'\',\''.$_POST['sys'].'\')" title="">'.T_("Delete").'</div>';
			$o .= '<table><tr><td>';
			$o .= '<p id="Opayed">'.T_("Paid").' : '.((isset($a['payed']) && $a['payed']==0)?T_("No"):T_("Yes")).'</p>';
			$o .= '<p id="Otreated">'.T_("Treated").' : '.((isset($a['treated']) && $a['treated']==0)?T_("No"):T_("Yes")).'</p>';
			$o .= '</td><td style="vertical-align:middle">';
			$o .= '<div id="Bpayed" '.((!isset($a['payed']) || $a['payed']==1)?'style="display:none;" ':'').'class="bouton" onClick="f_payedOrderPayment(\''.$_POST['id'].'\',\''.T_("Paid").' : '.T_("Yes").'\')" title="">'.T_("Paid").'</div>';
			$o .= '<div id="Btreated" '.((isset($a['payed']) && $a['payed']==0 || isset($a['treated']) && $a['treated']==1)?'style="display:none;"':'').'class="bouton" onClick="f_treatedOrderPayment(\''.$_POST['id'].'\',\''.T_("Treated").' : '.T_("Yes").'\',\''.$_POST['sys'].'\')" title="">'.T_("Treated").'</div>';
			$o .= '<div id="Breset" '.((isset($a['treated']) && $a['treated']==0)?'style="display:none;"':'').'class="bouton" onClick="f_resetOrderPayment(\''.$_POST['id'].'\',\''.T_("Paid").' : '.T_("No").'\',\''.T_("Treated").' : '.T_("No").'\',\''.$_POST['sys'].'\')" title="">'.T_("Reset").'</div>';
			$iv = openssl_random_pseudo_bytes(16);
			$r = base64_encode(openssl_encrypt($_POST['id'].'|'.$a['mail'], 'AES-256-CBC', substr($Ukey,0,32), OPENSSL_RAW_DATA, $iv));
			$o .= '<a href="uno/plugins/payment/paymentPdf.php?k='.urlencode($r).'&i='.base64_encode($iv).'&s='.$_POST['sys'].'&t=1" target="_blank" id="Bfacture" '.((isset($a['payed']) && $a['payed']==0)?'style="display:none;"':'').'class="bouton" title="">'.T_("Invoice in PDF").'</a>';
			$o .= '<div id="Barchiv" '.((isset($a['treated']) && $a['treated']==0)?'style="display:none;"':'').'class="bouton" onClick="f_archivOrderPayment(\''.$_POST['id'].'\',\''.T_("Are you sure ?").'\',\''.$_POST['sys'].'\')" title="">'.T_("Archive").'</div>';
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
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
//
?>
