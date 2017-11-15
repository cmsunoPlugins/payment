<?php
if(!isset($_GET['k']) || !isset($_GET['s']) || !isset($_GET['t']) || !isset($_GET['i'])) {sleep(2);exit;}
?>
<?php
include('../../config.php');
$b = openssl_decrypt(base64_decode($_GET['k']), 'AES-256-CBC', substr($Ukey,0,32), OPENSSL_RAW_DATA, base64_decode($_GET['i']));
$b = rtrim($b, "\0");
$b = explode('|',$b);
if(!is_array($b)) {sleep(2);exit;}
$id = $b[0]; $mail = $b[1]; $sys = $_GET['s'];
if(!file_exists('../../data/_sdata-'.$sdata.'/_'.$sys.'/'.$id.'.json')) {sleep(2);exit;}
include('lang/lang.php');
include('paymentGetData.php');
include('fpdf/fpdf.php');
$q = file_get_contents('../../data/_sdata-'.$sdata.'/_'.$sys.'/'.$id.'.json');
if(isset($q) && $q)
	{
	$a = json_decode($q,true);
	$q = file_get_contents('../../data/busy.json'); $a1 = json_decode($q,true); $Ubusy = $a1['nom'];
	$q = file_get_contents('../../data/'.$Ubusy.'/site.json'); $a1 = json_decode($q,true); $site = $a1['tit']; $url = $a1['url'];
	$q = file_get_contents('../../data/payment.json'); $a1 = json_decode($q,true); $curr = $a1['curr']; $ship = $a1['ship'];
	if($curr=='EUR') $curr = chr(128);
	else if($curr=='USD' || $curr=='CAD') $curr = '$';
	else if($curr=='GBP') $curr = '£';
	$t = 0; $p = 0; $tax = 0;
	//
	if($_GET['t']==1) // FACTURE
		{
		$pdf = new FPDF('P','mm','A4'); // portrait (P / L) - milimetre - A4
		$pdf->AddPage(); // Ajoute une nouvelle page au document
		$pdf->SetMargins(20, 20); // left, top (mm)
		$pdf->SetFillColor(224,235,255);
		$pdf->SetTextColor(0);
		$pdf->SetDrawColor(70,82,103);
		$pdf->SetLineWidth(.3);
		$pdf->SetFont('Arial','B',18);
		$pdf->Ln();
		$pdf->Cell(170,10,utf8_decode($site),'',0,'C');
		$pdf->Ln(20); $pdf->SetFont('','',16); 
		$pdf->Cell(170,10,utf8_decode(T_("Invoice")),'',0,'C');
		$pdf->Ln(20);
		// 1 ORDER
		$pdf->SetFont('','B',14);
		$pdf->Cell(170,10,utf8_decode(T_("Order")));
		$pdf->Ln(10); $pdf->SetFont('','B',10);
		$pdf->Cell(20,6,utf8_decode(T_("Ref")),1,0,'L',true);
		$pdf->SetFont('');
		$pdf->Cell(50,6,$a['id'],1,0,'L',false);
		$pdf->Ln(); $pdf->SetFont('','B');
		$pdf->Cell(20,6,utf8_decode(T_("Date")),1,0,'L',true);
		$pdf->SetFont('');
		$pdf->Cell(50,6,date("d/m/Y H:i",$a['time']),1,0,'L',false);
		$pdf->Ln(); $pdf->SetFont('','B');
		$pdf->Cell(20,6,utf8_decode(T_("Payment")),1,0,'L',true);
		$pdf->SetFont('');
		$pdf->Cell(50,6,utf8_decode(ucfirst($sys=='payment'?($a['cv']=='cheq'?T_("Cheque"):T_("Bank Transfer")):$sys)),1,0,'L',false);
		// 2. DETAIL
		$t1 = array(utf8_decode(T_("Name")), utf8_decode(T_("Ref")), utf8_decode(T_("Price")), utf8_decode(T_("Tax")), 'Nb', utf8_decode(T_("Tax")), utf8_decode(T_("Total")));
		// Couleurs, épaisseur du trait et police grasse
		$pdf->Ln(20); $pdf->SetFont('','B',14);
		$pdf->Cell(170,10,utf8_decode(T_("Order Details")));
		$pdf->Ln(10);  $pdf->SetFont('','',10); 
		$pdf->SetFont('','B');
		$w = array(50, 25, 20, 20, 10, 20, 25); // 170
		for($i=0;$i<count($t1);$i++) $pdf->Cell($w[$i],7,$t1[$i],1,0,'C',true);
		$pdf->Ln(); // Ln([float h]) : saut de ligne
		// Restauration des couleurs et de la police
		$pdf->SetFont('');
		// Données
		foreach($a['prod'] as $r)
			{
			$t = getTax($a,$r);
			$p += (pt($r['p'])*$r['q']);
			$tax += ($t*$r['q']);
			$pdf->Cell($w[0],6,utf8_decode($r['n']),'LR',0,'L',false);
			$pdf->Cell($w[1],6,utf8_decode(substr($r['i'],0,12)),'LR',0,'L',false);
			$pdf->Cell($w[2],6,$r['p'].' '.$curr,'LR',0,'R',false);
			$pdf->Cell($w[3],6,$t.' '.$curr,'LR',0,'R',false);
			$pdf->Cell($w[4],6,$r['q'],'LR',0,'C',false);
			$pdf->Cell($w[5],6,($t*$r['q']).' '.$curr,'LR',0,'R',false);
			$pdf->Cell($w[6],6,(pt($r['p'])*$r['q']).' '.$curr,'LR',0,'R',false);
			$pdf->Ln();
			}
		// Trait de terminaison
		$pdf->Cell(array_sum($w),0,'','T');
		$pdf->Ln();
		$pdf->Cell($w[0]+$w[1]+$w[2]+$w[3]+$w[4],6,utf8_decode(T_("Subtotal")),'',0,'R');
		$pdf->Cell($w[5],6,$tax.' '.$curr,'LRB',0,'R');
		$pdf->Cell($w[6],6,$p.' '.$curr,'LRB',0,'R');
		if($ship)
			{
			$pdf->Ln();
			$pdf->Cell($w[0]+$w[1]+$w[2]+$w[3]+$w[4]+$w[5],6,utf8_decode(T_("Shipping cost")),'',0,'R');
			$pdf->Cell($w[6],6,$ship.' '.$curr,'LRB',0,'R');
			$p += pt($ship);
			}
		$pdf->Ln();
		$pdf->Cell($w[0]+$w[1]+$w[2]+$w[3]+$w[4]+$w[5],6,utf8_decode(T_("Total")),'',0,'R');
		$pdf->SetFont('','B');
		$pdf->Cell($w[6],6,$p.' '.$curr,'LRB',0,'R');
		// 3. SHIPPING
		$pdf->Ln(20); $pdf->SetFont('','B',14);
		$pdf->Cell(170,10,utf8_decode(T_("Shipping address")));
		$pdf->Ln(10); $pdf->SetFont('','B',10);
		$pdf->Cell(30,6,utf8_decode(T_("Name")),1,0,'L',true);
		$pdf->SetFont('');
		$pdf->Cell(140,6,utf8_decode($a['name']),1,0,'L',false);
		$pdf->Ln(); $pdf->SetFont('','B');
		$pdf->Cell(30,6,utf8_decode(T_("Address")),1,0,'L',true);
		$pdf->SetFont('');
		$pdf->Cell(140,6,utf8_decode($a['adre']),1,0,'L',false);
		$pdf->Ln(); $pdf->SetFont('','B');
		$pdf->Cell(30,6,utf8_decode(T_("Mail")),1,0,'L',true);
		$pdf->SetFont('');
		$pdf->Cell(140,6,utf8_decode($a['mail']),1,0,'L',false);
		$pdf->Ln();
		//$pdf->Cell(40,10,'Hello World ! '.$id); // Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]])
		// 4. FOOTER
		$pdf->SetY(-30);
		$pdf->SetFont('','',10); $pdf->SetTextColor(60);
		$pdf->Cell(170,6,utf8_decode(T_('Thank you for your trust.').' '.$site.' - '.$url),'',0,'C');
		$pdf->Output('facture.pdf','D');
		}
	else if($_GET['t']==2) // BON DE LIVRAISON
		{
		}
	}
?>
