// Cookie format : ABCDname|quantite||FGDEname|quantite||7UgTname|quantite||
function paymentAddC(f){f=f.split('|');var a=paymentGetC('cart');if(a){b=a.split('||');a='';for(v=0;v<b.length;v++){c=b[v].split('|');if(f[0]==c[0]){c[1]++;f=0;}a+=c[0]+'|'+c[1]+'||';};if(f!=0)a+=f[0]+'|'+f[1]+'||';}else a=f[0]+'|'+f[1]+'||';
	paymentWriteC(a.substring(0,a.length-2));paymentMajcart(a.substring(0,a.length-2));
}
function paymentDelC(f){var a=paymentGetC('cart');if(a){b=a.split('||');a='';for(v=0;v<b.length;v++){c=b[v].split('|');if(f!=c[0])a+=c[0]+'|'+c[1]+'||';};if(a=='') document.cookie='cart=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';else paymentWriteC(a.substring(0,a.length-2));paymentMajcart(a.substring(0,a.length-2));}}
function paymentWriteC(f){document.cookie="cart="+f;}
function paymentGetC(f){var r=document.cookie.match('(^|;) ?'+f+'=([^;]*)(;|$)');if(r)return(unescape(r[2]));else return null;}
function paymentMajcart(f){
	var a,b,c,d,e,h,s=0,t,p=0;
	t=f.split('||');for(v=0;v<t.length;v++){t[v]=t[v].split('|');if(t[v][1])s+=parseInt(t[v][1]);};
	a=document.getElementById("cart");a.innerHTML='';
	h=document.createElement("div");h.id="cartBox";h.className="cartBox";h.style.display='none';
	b=document.createElement("a");if(s)b.className="on";b.href="Javascript:void(0)";b.onclick=function(){
		if(document.getElementById("cartBox").style.display=='none')a=1;else a=0;
		document.getElementById("cartBox").style.display=(a?'block':'none');
	};c=document.createTextNode('('+parseInt(s)+')');b.appendChild(c);a.appendChild(b);
	b=document.createElement("table");
	b.id="cartTable";b.className="cartTable";
	if(f){
		for(v=0;v<t.length;v++){
			c=document.createElement("tr");d=document.createElement("th");d.colSpan="4";c.appendChild(d);b.appendChild(c);
			c=document.createElement("tr");
			d=document.createElement("td");e=document.createTextNode(t[v][0].substring(4,40)+' : '+price[t[v][0]]+curr);d.appendChild(e);c.appendChild(d);
			d=document.createElement("td");e=document.createTextNode('('+parseInt(t[v][1])+')');d.appendChild(e);c.appendChild(d);
			d=document.createElement("td");e=document.createTextNode((parseInt((parseInt(t[v][1])*price[t[v][0]])*100+.5)/100)+curr);d.appendChild(e);c.appendChild(d);
			d=document.createElement("td");e=document.createTextNode("X");d.style.cursor='pointer';d.value=t[v][0];d.onclick=function(){paymentDelC(this.value);};d.appendChild(e);c.appendChild(d);
			b.appendChild(c);
			p+=parseInt((parseInt(t[v][1])*price[t[v][0]])*100+.5)/100;
		}
		p=parseInt(p*100+.5)/100;
		c=document.createElement("tr");d=document.createElement("th");d.colSpan="4";c.appendChild(d);b.appendChild(c);
		c=document.createElement("tr");d=document.createElement("td");c.appendChild(d);d=document.createElement("td");c.appendChild(d);d=document.createElement("td");e=document.createTextNode(p+curr);d.appendChild(e);c.appendChild(d);d=document.createElement("td");c.appendChild(d);b.appendChild(c);
		c=document.createElement("tr");
		d=document.createElement("td");d.colSpan="4";
		e=document.createElement("input");e.value=paymentBtn;e.type='button';e.className="button";e.onclick=function(){paymentBuy();};d.appendChild(e);
		c.appendChild(d);b.appendChild(c);h.appendChild(b);a.appendChild(h);
	}
}
function paymentBuy(){
	var a,b,c,d=function(c){var f=document.getElementById("popAlert");f.innerHTML='';if(c>4)f.appendChild(document.createTextNode(a[3]));else f.appendChild(document.createTextNode(a[2]));},
	e=function(c){if(document.getElementById("popNa").value.length<2)++c;if(document.getElementById("popAd").value.length<10)++c;if(document.getElementById("popMa").value.match(/^[a-z0-9\._-]+@([a-z0-9_-]+\.)+[a-z]{2,6}$/i)==null)c+=5;return c;},
	g=function(f,z){return (z==1?f.substr(0,f.length-1)+',':'{')+'"name":"'+document.getElementById("popNa").value.replace(/[|"]/g,'')+'","adre":"'+document.getElementById("popAd").value.replace(/[|"]/g,'')+'","mail":"'+document.getElementById("popMa").value.replace(/[|"]/g,'')+'","Ubusy":"'+Ubusy+'"}';},
	params='a=buy&b='+Ubusy;
	x=new XMLHttpRequest();x.open('POST','uno/plugins/payment/paymentInc.php',true);
	x.setRequestHeader('Content-type','application/x-www-form-urlencoded;charset=utf-8');
	x.setRequestHeader('Content-length',params.length);
	x.setRequestHeader('X-Requested-With','XMLHttpRequest');
	x.setRequestHeader('Connection','close');
	x.onreadystatechange=function(){
		if(x.readyState==4&&x.status==200&&x.responseText){
			a=x.responseText.split('|;');
			unoPop(a[1],0);
			var po=/^pop/,t=document.getElementById("unoPop").getElementsByTagName('A'),i;
			for(i=t.length;i--;)if(po.test(t[i].id)){
				if(t[i].id=="popCheq")t[i].onclick=function(){var c=e(0),h=g(a[0],0);if(c==0)paymentCVCart(h,0);else d(c);};
				else if(t[i].id=="popVire")t[i].onclick=function(){var c=e(0),h=g(a[0],0);if(c==0)paymentCVCart(h,1);else d(c);};
				else t[i].onclick=function(){var c=e(0),h=g(a[0],1),x=this.id.substr(3)+'Cart';alert(x);if(c==0)window[x](h);else d(c);};
			}
		}
	};
	x.send(params);
	document.getElementById("cartBox").style.display='none';
}
function paymentGetPrice(){
	var x=new XMLHttpRequest();
	x.open('POST','uno/data/'+Ubusy+'/addtocart.json');
	x.setRequestHeader('Content-type','application/x-www-form-urlencoded;charset=utf-8');
	x.onreadystatechange=function(){
		if(x.readyState==4&&x.status==200&&x.responseText){
			p=document.getElementById('support');
			d=JSON.parse(x.responseText);curr=d.curr;d=d.cart;
			for(v in d)price[d[v].n]=parseFloat(d[v].p);
			if(curr=='EUR')curr='\u20ac';else if(curr=='USD'||curr=='CAD')curr='$';else if(curr=='GBP')curr='\u00A3';
			if(f=paymentGetC('cart'))paymentMajcart(f);else paymentMajcart('');
		}
	};
	x.send();
}
function paymentCVCart(f,g){
	document.body.removeChild(document.getElementById('unoPop'));
	x=new XMLHttpRequest();
	x.open('POST','uno/plugins/payment/paymentInc.php',true),params='a=cv&b='+Ubusy+'&c='+f+'&d='+(g==0?'cheq':'vire');
	x.setRequestHeader('Content-type','application/x-www-form-urlencoded;charset=utf-8');
	x.setRequestHeader('Content-length',params.length);
	x.setRequestHeader('X-Requested-With','XMLHttpRequest');
	x.setRequestHeader('Connection','close');
	x.onreadystatechange=function(){
		if(x.readyState==4&&x.status==200&&x.responseText){unoPop(x.responseText,0);document.cookie='cart=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';document.getElementById("cart").innerHTML='';}
	};
	x.send(params);
}
//
var price=[],curr;
paymentGetPrice();
