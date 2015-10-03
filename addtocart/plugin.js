/**
 * Plugin CKEditor AddToCart
 * Copyright (c) <2015> <Jacques Malgrange contacter@boiteasite.fr>
 * License MIT
 */
var atcMem=0,atc4=0;
CKEDITOR.plugins.add('addtocart',{
	icons:'addtocart',
	lang: 'en,fr',
	init:function(editor){
		var lang=editor.lang.addtocart;
		editor.addCommand('addtocartDialog',new CKEDITOR.dialogCommand('addtocartDialog'));
		editor.ui.addButton('addtocart',{
			label:lang.title,
			command:'addtocartDialog',
			toolbar:'cmsuno'
		});
		editor.addContentsCss(this.path+'css/addtocartBtn0.css' );
		editor.on('doubleclick',function(evt){
			var el=evt.data.element;
			if(!el.isReadOnly()&&el.is('a')&&el.getAttribute('class')=='button addtocart'){
				atcMem=el.getAttribute('alt');
				atcMem=((atcMem)?atcMem.split('|'):['','','','']);
				evt.data.dialog='addtocartDialog';
			}
		});
		CKEDITOR.dialog.add('addtocartDialog',this.path+'dialogs/addtocart.js');
	}
});
