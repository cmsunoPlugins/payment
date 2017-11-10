//
// CMSUno
// Plugin Payment
//
function f_save_payment(){
	jQuery(document).ready(function(){
		var ppal=document.getElementById('pml').checked?1:0;
		var plug=document.getElementById('pmg').checked?1:0;
		var coin=document.getElementById('pmo').checked?1:0;
		var vire=document.getElementById('pmv').checked?1:0;
		var cheq=document.getElementById('pmc').checked?1:0;
		var adre=document.getElementById("pma").value;
		var own=document.getElementById("pmo").value;
		var iban=document.getElementById("pmi").value;
		var bic=document.getElementById("pmb").value;
		var taa=document.getElementById("taa").value;
		var tab=document.getElementById("tab").value;
		var tac=document.getElementById("tac").value;
		var tad=document.getElementById("tad").value;
		var tda=document.getElementById('tda').checked?1:0;
		var tdb=document.getElementById('tdb').checked?1:0;
		var tdc=document.getElementById('tdc').checked?1:0;
		var tdd=document.getElementById('tdd').checked?1:0;
		var taxin=document.getElementById("taxin").options[document.getElementById("taxin").selectedIndex].value;
		var taxout=document.getElementById("taxout").options[document.getElementById("taxout").selectedIndex].value;
		var ship=document.getElementById("shi").value;
		var off=(document.getElementById('addtocartoff').checked?1:0);
		var curr=document.getElementById("cur").options[document.getElementById("cur").selectedIndex].value;
		var it=document.getElementById("it").options[document.getElementById("it").selectedIndex].value;
		var ali=document.getElementById("ali").options[document.getElementById("ali").selectedIndex].value;
		var col=document.getElementById("col").value;
		var ico=document.getElementById("ico").options[document.getElementById("ico").selectedIndex].value;
		jQuery.post('uno/plugins/payment/payment.php',{'action':'save','unox':Unox,'ppal':ppal,'plug':plug,'coin':coin,'vire':vire,'cheq':cheq,'adre':adre,'own':own,'iban':iban,'bic':bic,'taa':taa,'tab':tab,'tac':tac,'tad':tad,'tda':tda,'tdb':tdb,'tdc':tdc,'tdd':tdd,'taxin':taxin,'taxout':taxout,'ship':ship,'addtocartoff':off,'curr':curr,'it':it,'ali':ali,'col':col,'ico':ico},function(r){
			f_alert(r);
		});
	});
}
function f_load_payment(){
	jQuery(document).ready(function(){
		jQuery.getJSON("uno/data/"+Ubusy+"/payment.json?r="+Math.random(),function(data){
			if(data.method.ppal==1)document.getElementById('pml').checked=true;else document.getElementById('pml').checked=false;
			if(data.method.plug==1)document.getElementById('pmg').checked=true;else document.getElementById('pmg').checked=false;
			if(data.method.coin==1)document.getElementById('pmo').checked=true;else document.getElementById('pmo').checked=false;
			if(data.method.vire==1)document.getElementById('pmv').checked=true;else document.getElementById('pmv').checked=false;
			if(data.method.cheq==1)document.getElementById('pmc').checked=true;else document.getElementById('pmc').checked=false;
			if(data.adre!=undefined)document.getElementById('pma').value=data.adre;
			if(data.own!=undefined)document.getElementById('pmo').value=data.own;
			if(data.iban!=undefined)document.getElementById('pmi').value=data.iban;
			if(data.bic!=undefined)document.getElementById('pmb').value=data.bic;
			if(data.taa!=undefined)document.getElementById('taa').value=data.taa;
			if(data.tab!=undefined)document.getElementById('tab').value=data.tab;
			if(data.tac!=undefined)document.getElementById('tac').value=data.tac;
			if(data.tad!=undefined)document.getElementById('tad').value=data.tad;
			if(data.tda==1)document.getElementById('tda').checked=true;else document.getElementById('tda').checked=false;
			if(data.tdb==1)document.getElementById('tdb').checked=true;else document.getElementById('tdb').checked=false;
			if(data.tdc==1)document.getElementById('tdc').checked=true;else document.getElementById('tdc').checked=false;
			if(data.tdd==1)document.getElementById('tdd').checked=true;else document.getElementById('tdd').checked=false;
			if(data.taxin){
				t=document.getElementById("taxin");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==data.taxin){to[v].selected=true;v=to.length;}}
			}
			if(data.taxout){
				t=document.getElementById("taxout");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==data.taxout){to[v].selected=true;v=to.length;}}
			}
			if(data.ship!=undefined)document.getElementById('shi').value=data.ship;
			if(data.addtocartoff!=undefined&&data.addtocartoff)document.getElementById('addtocartoff').checked=true;
			if(data.curr){
				t=document.getElementById("cur");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==data.curr){to[v].selected=true;v=to.length;}}
			}
			if(data.it){
				t=document.getElementById("it");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==data.it){to[v].selected=true;v=to.length;}}
			}
			if(data.ali){
				t=document.getElementById("ali");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==data.ali){to[v].selected=true;v=to.length;}}
			}
			if(data.col!=undefined)document.getElementById('col').value=data.col;
			if(data.ico){
				t=document.getElementById("ico");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==data.ico){to[v].selected=true;v=to.length;}}
			}
		});
		jQuery('#paymentConfig .color').colorPicker();
	});
}
function f_payed_payment(f,g,h,i){
	jQuery.post('uno/plugins/payment/payment.php',{'action':'payed','unox':Unox,'id':g},function(r){f_alert(r);
		if(r.substr(0,1)!='!'){f.parentNode.className="";f.innerHTML=h;f.onclick=function(){f_treated_payment(f,g,i,'payment')};}
	});
}
function f_treated_payment(f,g,h,i){
	jQuery.post('uno/plugins/payment/payment.php',{'action':'treated','unox':Unox,'id':g,'typ':i},function(r){f_alert(r);
		if(r.substr(0,1)!='!'){f.parentNode.className="PayTreatedYes";f.innerHTML=h;f.className="";f.onclick="";}
	});
}
function f_payedOrderPayment(f,g){
	jQuery.post('uno/plugins/payment/payment.php',{'action':'payed','unox':Unox,'id':f},function(r){f_alert(r);
		if(r.substr(0,1)!='!'){jQuery('#Bpayed').hide();jQuery('#Btreated').show();jQuery('#Bfacture').show();document.getElementById('Opayed').innerHTML=g;}
	});
}
function f_treatedOrderPayment(f,g,h){
	jQuery.post('uno/plugins/payment/payment.php',{'action':'treated','unox':Unox,'id':f,'typ':h},function(r){f_alert(r);
		if(r.substr(0,1)!='!'){jQuery('#Btreated').hide();jQuery('#Breset').show();jQuery('#Barchiv').show();document.getElementById('Otreated').innerHTML=g;}
	});
}
function f_resetOrderPayment(f,g,h,i){
	jQuery.post('uno/plugins/payment/payment.php',{'action':'reset','unox':Unox,'id':f,'typ':i},function(r){f_alert(r);
		if(r.substr(0,1)!='!'){jQuery('#Breset').hide();jQuery('#Barchiv').hide();jQuery('#Bfacture').hide();
			if(i=='payment'){jQuery('#Bpayed').show();document.getElementById('Opayed').innerHTML=g;document.getElementById('Otreated').innerHTML=h;}
			else{jQuery('#Btreated').show();document.getElementById('Otreated').innerHTML=h;}
		}
	});
}
function f_delOrderPayment(f,g,h){if(confirm(g)){jQuery.post('uno/plugins/payment/payment.php',{'action':'del','unox':Unox,'id':f,'typ':h},function(r){f_alert(r);if(r.substr(0,1)!='!')f_paymentVente();});}}
function f_archivOrderPayment(f,g,h){if(confirm(g)){jQuery.post('uno/plugins/payment/payment.php',{'action':'archiv','unox':Unox,'id':f,'typ':h},function(r){f_alert(r);if(r.substr(0,1)!='!')f_paymentVente();});}}
function f_paymentRestaurOrder(f){jQuery.post('uno/plugins/payment/payment.php',{'action':'restaur','unox':Unox,'f':f},function(r){f_alert(r);f_paymentArchiv();});}
function f_paymentViewA(f){
	jQuery('#paymentArchData').empty();
	jQuery.post('uno/plugins/payment/payment.php',{'action':'viewA','unox':Unox,'arch':f},function(r){jQuery('#paymentArchData').append(r);jQuery('#paymentArchData').show();});
}
function f_paymentArchiv(){
	jQuery('#paymentArchiv').empty();
	document.getElementById('paymentArchiv').style.display="block";
	document.getElementById('paymentConfig').style.display="none";
	document.getElementById('paymentVente').style.display="none";
	document.getElementById('paymentDetail').style.display="none";
	document.getElementById('paymentA').className="bouton fr current";
	document.getElementById('paymentC').className="bouton fr";
	document.getElementById('paymentV').className="bouton fr";
	document.getElementById('paymentD').style.display="none";
	jQuery.post('uno/plugins/payment/payment.php',{'action':'viewArchiv','unox':Unox},function(r){jQuery('#paymentArchiv').append(r);jQuery('#archData').hide();});
}
function f_paymentConfig(){
	document.getElementById('paymentArchiv').style.display="none";
	document.getElementById('paymentConfig').style.display="block";
	document.getElementById('paymentVente').style.display="none";
	document.getElementById('paymentDetail').style.display="none";
	document.getElementById('paymentA').className="bouton fr";
	document.getElementById('paymentC').className="bouton fr current";
	document.getElementById('paymentV').className="bouton fr";
	document.getElementById('paymentD').style.display="none";
}
function f_paymentVente(){
	document.getElementById('paymentArchiv').style.display="none";
	document.getElementById('paymentConfig').style.display="none";
	jQuery('#paymentVente').empty();document.getElementById('paymentVente').style.display="block";
	document.getElementById('paymentDetail').style.display="none";
	document.getElementById('paymentA').className="bouton fr";
	document.getElementById('paymentC').className="bouton fr";
	document.getElementById('paymentV').className="bouton fr current";
	document.getElementById('paymentD').style.display="none";
	jQuery.post('uno/plugins/payment/payment.php',{'action':'vente','unox':Unox},function(r){jQuery('#paymentVente').append(r);});
}
function f_paymentDetail(f,g){
	jQuery('#paymentDetail').empty();
	document.getElementById('paymentArchiv').style.display="none";
	document.getElementById('paymentConfig').style.display="none";
	document.getElementById('paymentVente').style.display="none";
	document.getElementById('paymentDetail').style.display="block";
	document.getElementById('paymentA').className="bouton fr";
	document.getElementById('paymentC').className="bouton fr";
	document.getElementById('paymentV').className="bouton fr";
	document.getElementById('paymentD').style.display="block";
	jQuery.post('uno/plugins/payment/payment.php',{'action':'detail','unox':Unox,'id':f,'sys':g},function(r){
		if(r.substr(0,1)!='!')jQuery('#paymentDetail').append(r);
		else f_alert(r);
	});
}
function f_del_payment(f){
	var g=f.parentNode.firstChild;
	jQuery(g).parent().empty().append('<input type="text" class="input color" name="col" id="col" style="width:100px;" /><span class="del" onclick="f_del_payment(this);"></span>');
}
//
f_load_payment();f_paymentVente();
