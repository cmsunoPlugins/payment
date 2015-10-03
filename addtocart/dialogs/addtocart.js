/**
 * Plugin CKEditor AddToCart
 * Copyright (c) <2015> <Jacques Malgrange contacter@boiteasite.fr>
 * License MIT
 */
CKEDITOR.dialog.add('addtocartDialog',function(editor){
	var lang=editor.lang.addtocart,atc={},tax;
	return{
		title:lang.title,
		minWidth:250,
		minHeight:75,
		contents:[{
			id:'addtocart0',
			label:'',
			title:'',
			expand:false,
			padding:0,
			elements:[{
				type:'text',
				id:'nam',
				label:lang.labelNam,
				style:'width:200px;',
				commit:function(){atc.nam=addtocartKey(4)+this.getValue().replace(/[|"]/g,'');}
 			},{
				type:'text',
				id:'pri',
				label:lang.labelPri+' ('+(paymentTin=='yes'?lang.taxon:lang.taxoff)+')',
				style:'width:50px;margin-top:5px;',
				commit:function(){atc.pri=this.getValue().replace(/[|"]/g,'');}
 			},{
				type:'text',
				id:'but',
				label:lang.labelBut,
				'default':lang.title,
				style:'width:150px;margin-top:5px;margin-bottom:10px;',
				commit:function(){atc.but=this.getValue().replace(/[|"]/g,'');}
 			},{
				type:'html',
				html:'<h3>Taxe</h3>',
				style:'margin-bottom:5px;',
			},{
				type:'checkbox',
				id:'taa',
				label:'Alpha ('+paymentTaa+')',
				'default':(paymentTda==1?true:false),
				style:'float:left;margin-left:3px;',
				commit:function(){atc.taa=(this.getValue()?1:0);}
 			},{
				type:'checkbox',
				id:'tab',
				label:'Beta ('+paymentTab+')',
				'default':(paymentTdb==1?true:false),
				style:'float:left;margin-left:3px;',
				commit:function(){atc.tab=(this.getValue()?1:0);}
 			},{
				type:'checkbox',
				id:'tac',
				label:'Gamma ('+paymentTac+')',
				'default':(paymentTdc==1?true:false),
				style:'float:left;margin-left:3px;',
				commit:function(){atc.tac=(this.getValue()?1:0);}
 			},{
				type:'checkbox',
				id:'tad',
				label:'Delta ('+paymentTad+')',
				'default':(paymentTdd==1?true:false),
				style:'float:left;margin-left:3px;',
				commit:function(){atc.tad=(this.getValue()?1:0);}
 			}]
		}],
		onOk:function(){
			this.commitContent();
			var f=new Array(atc.nam,1);
			tax=atc.taa+2*atc.tab+4*atc.tac+8*atc.tad;
			editor.insertHtml('<a alt="'+atc.nam+'|'+atc.pri+'|'+atc.but+'|'+tax+'" class="button addtocart" href="JavaScript:void(0);" onClick="paymentAddC(\''+atc.nam+'|1\')" title="'+atc.nam.substring(4)+' : '+atc.pri+'">'+atc.but+'</a>');
			atcMem=0;
			return;
		},
		onCancel:function(){atcMem=0;return;},
		onShow:function(){
			var dia=CKEDITOR.dialog.getCurrent();atc4=0;
			if(atcMem.length>0){
				atc4=atcMem[0].substring(0,4);
				if(atc4=='0000')atc4=0;
				dia.getContentElement('addtocart0','nam').setValue(atcMem[0].substring(4));
				dia.getContentElement('addtocart0','pri').setValue(atcMem[1]);
				dia.getContentElement('addtocart0','but').setValue(atcMem[2]);
				tax=atcMem[3]||0;
				if(tax<8)dia.getContentElement('addtocart0','tad').setValue(false);else{dia.getContentElement('addtocart0','tad').setValue(true);tax-=8;}
				if(tax<4)dia.getContentElement('addtocart0','tac').setValue(false);else{dia.getContentElement('addtocart0','tac').setValue(true);tax-=4;}
				if(tax<2)dia.getContentElement('addtocart0','tab').setValue(false);else{dia.getContentElement('addtocart0','tab').setValue(true);tax-=2;}
				if(tax==0)dia.getContentElement('addtocart0','taa').setValue(false);else dia.getContentElement('addtocart0','taa').setValue(true);
			}
			return;
		}
	};
});
function addtocartKey(n){if(atc4!=0)return atc4;else{var r='',p="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",i;for(i=0;i<n;i++)r+=p.charAt(Math.floor(Math.random()*p.length));return r;}}