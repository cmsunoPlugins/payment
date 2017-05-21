//
// CMSUno
// Plugin Payment
//
UconfigNum++;

<?php $a = 0;
if(file_exists(dirname(__FILE__).'/../../data/busy.json'))
	{
	$q = file_get_contents(dirname(__FILE__).'/../../data/busy.json'); $b = json_decode($q,true); $Ubusy = isset($b['nom'])?$b['nom']:false;
	if($Ubusy && file_exists(dirname(__FILE__).'/../../data/'.$Ubusy.'/payment.json'))
		{
		$q = file_get_contents(dirname(__FILE__).'/../../data/'.$Ubusy.'/payment.json');
		$a = json_decode($q,true);
		}
	}
if(empty($a['addtocartoff'])) { ?>

var paymentTaa='',paymentTab='',paymentTac='',paymentTad='',paymentTda='',paymentTdb='',paymentTdc='',paymentTdd='',paymentTin='',paymentCurr='';
jQuery(document).ready(function(){
	jQuery.getJSON("uno/data/"+Ubusy+"/payment.json?r="+Math.random(),function(r){
		if(r.curr!=undefined)paymentCur=r.curr;
		if(r.curr=='EUR')paymentCur='\u20ac';else if(r.curr=='USD'||r.curr=='CAD')paymentCur='$';else if(r.curr=='GBP')paymentCur='\u00A3';
		if(r.taa!=undefined)paymentTaa=r.taa+' '+(r.taa.search('%')==-1&&r.taa.length>0?paymentCur:'');
		if(r.tab!=undefined)paymentTab=r.tab+' '+(r.tab.search('%')==-1&&r.tab.length>0?paymentCur:'');
		if(r.tac!=undefined)paymentTac=r.tac+' '+(r.tac.search('%')==-1&&r.tac.length>0?paymentCur:'');
		if(r.tad!=undefined)paymentTad=r.tad+' '+(r.tad.search('%')==-1&&r.tad.length>0?paymentCur:'');
		if(r.tda!=undefined)paymentTda=r.tda;
		if(r.tdb!=undefined)paymentTdb=r.tdb;
		if(r.tdc!=undefined)paymentTdc=r.tdc;
		if(r.tdd!=undefined)paymentTdd=r.tdd;
		if(r.taxin!=undefined)paymentTin=r.taxin;
	});
});
CKEDITOR.plugins.addExternal('addtocart',UconfigFile[UconfigNum-1]+'/../addtocart/');
CKEDITOR.editorConfig = function(config){
	config.extraPlugins += ',addtocart';
	config.toolbarGroups.push('addtocart');
	config.extraAllowedContent += '; a[alt,class,href,onclick,title](addtocart)';
	if(UconfigFile.length>UconfigNum)config.customConfig=UconfigFile[UconfigNum];
};

<?php } else { ?>

CKEDITOR.editorConfig = function(config){
	if(UconfigFile.length>UconfigNum)config.customConfig=UconfigFile[UconfigNum];
};

<?php } ?>
