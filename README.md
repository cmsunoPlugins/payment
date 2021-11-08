CMSUno - Plugins
================

<pre>
 uuuu      uuuu        nnnnnn           ooooooooo
u::::u    u::::u    nn::::::::nn     oo:::::::::::oo
u::::u    u::::u   nn::::::::::nn   o:::::::::::::::o
u::::u    u::::u  n::::::::::::::n  o:::::ooooo:::::o
u::::u    u::::u  n:::::nnnn:::::n  o::::o     o::::o
u::::u    u::::u  n::::n    n::::n  o::::o     o::::o
u::::u    u::::u  n::::n    n::::n  o::::o     o::::o
u:::::uuuu:::::u  n::::n    n::::n  o::::o     o::::o
u::::::::::::::u  n::::n    n::::n  o:::::ooooo:::::o
 u::::::::::::u   n::::n    n::::n  o:::::::::::::::o
  uu::::::::uu    n::::n    n::::n   oo:::::::::::oo
     uuuuuu        nnnn      nnnn       ooooooooo
        ___                                __
       / __\            /\/\              / _\
      / /              /    \             \ \
     / /___           / /\/\ \            _\ \
     \____/           \/    \/            \__/
</pre>

## Payment ##

Allows you to create a small e-commerce site from CKEditor.
It adds a "add to cart" button to the editor.
It adds a complete cart system with order registration, email sending, invoice in PDF, multi-tax, shipping cost, payment by cheque and by bank transfer.
It can work with other payment plugin (Paypal, Payplug).
Very usefull and powerfull.

[CMSUno](https://github.com/boiteasite/cmsuno)

### Gateway ###

You can create a Gateway plugin for another payment system and link it with Payment. Connectors are as follow :

* The name of the plugin must begin with __pay__ (payfoo).
* To Enabled/Disabled the plugin in Payment, it's needed to write in __data/payment.json__ : 

``` data['method']['payfoo'] = 1
data['method']['payfoo'] = 0 ```

* The image button of you payment method should named __payfoo-btn.png__. Recommended size : 76x48. Folder : payfoo/img/.
* You should include a JS script with a function named __payfooCart(cart)__. This function will be called when the buyer clic the image button. This is the gateway.
"cart" is a JSON var :

``` {"prod":{
	{"n":"screwdriver","p":3.90,"i":"id765","q":5},
	{"n":"saw","p":7.60,"i":"string id","q":3},
	{"n":"hammer","p":12.5,"i":"string id","q":2}
},
"digital":"index|readme",
"ship":"4",
"name":"Bob Dylan",
"adre":"1250 Edouard street 33234 ERZ",
"mail":"bob@example.com"} ```


### Versions ###

* 1.3.1 - 08/11/2021 : Update fpdf to 1.8.3 => fix issue on PHP 8+
* 1.3 - 26/12/2017 : Compatible with W3.CSS
* 1.2 - 15/11/2017 : Change connection with external payment gateway
* 1.1.3 - 10/11/2017 : Add Gateway with Bitcoin Paycoin plugin
* 1.1.2 - 21/05/2017 : Option to hide the CKEditor Add To Cart Button
* 1.1.1 - 15/03/2017 :
	* Use PHPMailer if Newsletter plugin exists
	* Replace MCrypt by OpenSSL
	* Fix issue when unknow lang
* 1.1 - 14/10/2016 : Use PHP-Gettext in place of gettext
* 1.0.2 - 16/11/2015 : Fix a bug
* 1.0.1 - 15/11/2015 : ColorPicker
* 1.0 - 05/10/2015 : First stable version