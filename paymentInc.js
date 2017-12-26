// Cookie format : ABCDname|quantite||FGDEname|quantite||7UgTname|quantite||
function paymentAddC(f){f=f.split('|');var a=paymentGetC('cart');if(a){b=a.split('||');a='';for(v=0;v<b.length;v++){c=b[v].split('|');if(f[0]==c[0]){c[1]++;f=0;}a+=c[0]+'|'+c[1]+'||';};if(f!=0)a+=f[0]+'|'+f[1]+'||';}else a=f[0]+'|'+f[1]+'||';
	paymentWriteC(a.substring(0,a.length-2));paymentMajcart(a.substring(0,a.length-2));
}
function paymentDelC(f){var a=paymentGetC('cart');if(a){b=a.split('||');a='';for(v=0;v<b.length;v++){c=b[v].split('|');if(f!=c[0])a+=c[0]+'|'+c[1]+'||';};if(a=='') document.cookie='cart=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';else paymentWriteC(a.substring(0,a.length-2));paymentMajcart(a.substring(0,a.length-2));}}
function paymentWriteC(f){document.cookie="cart="+f;}
function paymentGetC(f){var r=document.cookie.match('(^|;) ?'+f+'=([^;]*)(;|$)');if(r)return(unescape(r[2]));else return null;}
function paymentMajcart(f){
	var a,b,c,d,e,s=0,t=f.split('||'),p=0,v;
	for(v=0;v<t.length;v++){t[v]=t[v].split('|');if(t[v][1])s+=parseInt(t[v][1]);};
	a=document.getElementById("cartTable");a.innerHTML='';
	if(f){
		for(v=0;v<t.length;v++){
			b=document.createElement("tr");
			c=document.createElement("td");d=document.createTextNode(t[v][0].substring(4,40));c.appendChild(d);b.appendChild(c);
			c=document.createElement("td");d=document.createTextNode(price[t[v][0]]+curr);c.appendChild(d);b.appendChild(c);
			c=document.createElement("td");d=document.createTextNode('('+parseInt(t[v][1])+')');c.appendChild(d);b.appendChild(c);
			c=document.createElement("td");d=document.createTextNode((parseInt((parseInt(t[v][1])*price[t[v][0]])*100+.5)/100)+curr);c.appendChild(d);b.appendChild(c);
			c=document.createElement("td");d=document.createElement("span");d.className='w3-button w3-large';d.dataset.cart=t[v][0];e=document.createTextNode("X");d.appendChild(e);d.onclick=function(){paymentDelC(this.dataset.cart);};c.appendChild(d);b.appendChild(c);
			a.appendChild(b);
			p+=parseInt((parseInt(t[v][1])*price[t[v][0]])*100+.5)/100;
		}
		p=parseInt(p*100+.5)/100;
		b=document.createElement("tr");
		c=document.createElement("td");c.colSpan="3";b.appendChild(c);
		c=document.createElement("td");c.colSpan="2";c.className="w3-large";d=document.createTextNode(p+curr);c.appendChild(d);b.appendChild(c);
		a.appendChild(b);
	}
	b=parseInt(s);document.getElementById("cartNb").innerHTML=(b!=0?b:'');
	if(b==0)paymentOpenCart(0);
}
function paymentOpenCart(f){
	var a=document.getElementById('cartBox');
	if(f!=0&&document.getElementById('cartTable').childElementCount!=0&&a.className.indexOf('w3-hide')!=-1)a.className=a.className.replace('w3-hide','w3-show');
	else{
		a.className=a.className.replace('w3-show','w3-hide');
		if(a.className.indexOf('w3-hide')==-1)a.className+=' w3-hide';
	}
}
function paymentBuy(){
	var a,b,c
	var d=function(c){
		var f=document.getElementById("popAlert");f.innerHTML='';
		if(c>4)paymentAlert(a[3]);
		else paymentAlert(a[2]);
	};
	var e=function(c){
		if(document.getElementById("popNa").value.length<2)++c;
		if(document.getElementById("popAd").value.length<10)++c;
		if(document.getElementById("popMa").value.match(/^[a-z0-9\._-]+@([a-z0-9_-]+\.)+[a-z]{2,6}$/i)==null)c+=5;
		return c;
	};
	var g=function(f,z){return(z==1?f.substr(0,f.length-1)+',':'{')+'"name":"'+document.getElementById("popNa").value.replace(/[|"]/g,'')+'","adre":"'+document.getElementById("popAd").value.replace(/[|"]/g,'')+'","mail":"'+document.getElementById("popMa").value.replace(/[|"]/g,'')+'","Ubusy":"'+Ubusy+'"}';},
	params='a=buy&b='+Ubusy;
	x=new XMLHttpRequest();x.open('POST','uno/plugins/payment/paymentInc.php',true);
	x.setRequestHeader('Content-type','application/x-www-form-urlencoded;charset=utf-8');
	x.setRequestHeader('X-Requested-With','XMLHttpRequest');
	x.onreadystatechange=function(){
		if(x.readyState==4&&x.status==200&&x.responseText){
			a=x.responseText.split('|;');
			unoPop(a[1],0);
			var po=/^pop/,t=document.getElementById("unoPop").getElementsByTagName('SPAN'),i;
			for(i=t.length;i--;)if(po.test(t[i].id)){
				if(t[i].id=="popCheq")t[i].onclick=function(){var c=e(0),h=g(a[0],0);if(c==0)paymentCVCart(h,0);else d(c);};
				else if(t[i].id=="popVire")t[i].onclick=function(){var c=e(0),h=g(a[0],0);if(c==0)paymentCVCart(h,1);else d(c);};
				else t[i].onclick=function(){var c=e(0),h=g(a[0],1),x=this.id.substr(3)+'Cart';if(c==0)window[x](h);else d(c);};
			}
		}
	};
	x.send(params);
	b=document.getElementById("cartBox");b.className=b.className.replace('w3-show','w3-hide');if(b.className.indexOf('w3-hide')==-1)b.className+=' w3-hide';
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
	x.setRequestHeader('X-Requested-With','XMLHttpRequest');
	x.onreadystatechange=function(){
		if(x.readyState==4&&x.status==200&&x.responseText){unoPop(x.responseText,0);document.cookie='cart=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';document.getElementById("cart").innerHTML='';}
	};
	x.send(params);
}
function paymentAlert(f){
	var a=document.getElementById('popAlert');
	a.className=a.className.replace('w3-hide','w3-show');
	a.innerHTML=f;
	setTimeout(function(){
		a.innerHTML="";
		a.className=a.className.replace('w3-show','w3-hide');
		if(a.className.indexOf('w3-hide')==-1)a.className+=' w3-hide';
	},5000);
}

//
var price=[],curr;
paymentGetPrice();
