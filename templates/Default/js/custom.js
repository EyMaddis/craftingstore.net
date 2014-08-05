function focused(element, text){
	if(element.value == text)
	{
		element.value = '';
	}
}
function blured(element, text){
	if(element.value == '')
	{
		element.value = text;
	}
}
var ajaxLoader = '<p class="AjaxLoaderImg"><span>Loading...</span></p>';
function ErrorMessage (message){
/* activate error dialog, if ErrorBox exists: */
	$(".overallMessage").dialog({
		'buttons': {
			Ok: function(){
				$(this).dialog("close");
			}
		},
		'width': 600,
		'modal': false,
		'position': ['center',20],
		'show': 'drop',
		'resizable': false,
		'draggable': false,
		'zIndex': 99999
	});
	return false;
}


$(document).ready(function(){
	/*
	$('.simpledialog').simpleDialog({
	width: 300,
	height: 200,
	showCloseLabel: false,
	closeSelector: "#closeDialog"
	});
	*/
	function getHrefVars(href){
		 var vars = [], hash;
		 var hashes = href.slice(href.indexOf('?') + 1).split('&');
		 for(var i = 0; i < hashes.length; i++)
		 {
			  hash = hashes[i].split('=');
			  vars.push(hash[0]);
			  vars[hash[0]] = hash[1];
		 }
		 return vars;
	}
	function getBoxIdFromHref(href){
		var params = getHrefVars(href);
		if(params['show'])
		{
			var id = params['show'].split('_')[1];
			if(!isNaN(id))
			{
				return id;
			}
		}
		return false;
	}


	// send forms via ajax

	function doHorizontalMenuStyle(){
		$(".header .topmenu  ul li a").button();
	}

	// refresh the jQuery styles
	function doToolTipStyle(){
		$(".InItemsets .ItemWrapper[title]").tooltip({
			effect: 'slide',
			hide: function(){
				$(this).remove();
			}
		});
	}

	// refresh the itembox jQuery style
	function doContentBoxesStyle(){
		//the combo boxes loose their design if a contentbox changes its size - fixed
		$("._ITEMBOX .sortField").selectbox("destroy");
		$("._ITEMBOX .sortDirection").selectbox("destroy");
		$("._ITEMBOX .currentPage").selectbox("destroy");
		// Because every form get refreshed after a content box is loaded,
		// we need to remove the submit eventhandler to prevent multiple submits of the form.
		// same with buttons and links
		$("form.ajaxForm").undelegate();
		$("a.ajaxItemDetailsDialog").undelegate();
		$('a.ajaxlink').undelegate();

		// set jQuery UI style for form fields
		$("._ITEMBOX .noSubgroups").button();
		$("._ITEMBOX .sortField").selectbox();
		$("._ITEMBOX .sortDirection").selectbox();
		$("._ITEMBOX .currentPage").selectbox();
		$("._ITEMBOX .itemSorterSubmit").button();

		$("form.ajaxForm").submit(function(){
			var id = getBoxIdFromHref($(this).attr('action'));
			$('#contentBox' + id).html(ajaxLoader);
			$.ajax({ // create an AJAX call...
				'data': $(this).serialize(), // get the form data
				'type': $(this).attr('method'), // GET or POST
				'url': $(this).attr('action'), // the file to call
				'success': function(response){ // on success..
					$('#contentBox' + id).html(response); // update the DIV
					doContentBoxesStyle();
				}
			});
			return false;
		});

		$('a.ajaxlink').on('click',function(){
			var id = getBoxIdFromHref(this.href);
			if(id !== false){
				$('#contentBox' + id).html(ajaxLoader);
				$('#contentBox' + id).load(this.href, function(){
					doContentBoxesStyle();
				});
				return false;
			}
			$('.main').html(ajaxLoader);
			$('.main').load(this.href, function(){
				doContentBoxesStyle();
			});
			if($(this.parentElement).hasClass('ItemCategory')){
				lastShown = this.href;
			}
			return false;
		});

		//Itemdetails-Button
		$("a.ajaxItemDetailsDialog").click(function(){
			var hrefVars = getHrefVars(this.href);
			return itemDetailsDialog(this.href, this.title, hrefVars['h']);
		});
		//Buy-Button
		styleAddToCartButton();
	}


	{// menu
	//save the last loaded url (called in the main sector)
	var lastToggled = "";
	var lastShown = "";

	function doMenuStyle(){
		// create expandable menu fields
		$(".lmenu .acc").accordion({ autoHeight: false, collapsible: true, active: false });

		//$(".lmenu .menu_ebene1").accordion("activate", 0); //expand top layer

		// called by links without child elements
		$('.lmenu a.singlelink').on('click',function(){
			lastToggled = this.href;
			if ((this.target == "_blank") || (this.target == "_top")){
				return true;
			}
			else if((lastShown !=  this.href) && (this.href.substr(-1,1) != "#")) //letztes Zeichen keine Raute
			{
				lastShown = this.href;
				$('.main').html(ajaxLoader);
				$('.main').load(this.href, function(){
					//updateLocationString(lastShown);
					doContentBoxesStyle();
				});
			}
			return false;
		});

		//called by links with child elements
		$('.lmenu .acc a.grouplink').on('click',function(){
			if ((this.target == "_blank") || (this.target == "_top"))
			{
				return true;
			}
			else if((lastShown != this.href) && (this.href.substr(-1,1) != "#")) //letztes Zeichen keine Raute
			{
				lastShown = this.href;
				$('.main').html(ajaxLoader);
				$('.main').load(this.href, function(){
					//updateLocationString(lastShown);
					doContentBoxesStyle();
				});
			}
			if($(this.parentElement).hasClass("ui-state-active") && (lastToggled != this.href))
			{
				lastToggled = this.href;
				return false;
			}
			//return false;
			lastToggled = this.href;
		});
	}
	}

	{// right sidebar
	function doRightBarStyle(){
		doCartStyle();
		doPendingStyle();
		doAccountStyle();
		doStyleItemsearch();
		$(".LeisteRechts").accordion({ collapsible: true });
	}
	}

	{//Itemsearch
	function doStyleItemsearch(){
		$(".Itemsearch .submit").button();
		$("div.Itemsearch").accordion({
			active: false,
			collapsible: true
		});

		$(".Itemsearch .searchForm").submit(function(){
			lastToggled = "";// reset, so the menu will work correctly again
			lastShown = "";

			$('.main').html(ajaxLoader);
			$.ajax({ // create an AJAX call...
				'data': $(this).serialize(), // get the form data
				'type': $(this).attr('method'), // GET or POST
				'url': $(this).attr('action'), // the file to call
				'success': function(response){ // on success..
					$('.main').html(response); // update the DIV
					doContentBoxesStyle();
				}
			});
			return false;
		});
	}
	}

	{// account box in the right sidebar
	function doAccountStyle(){
		$(".Kontostand").accordion({ collapsible: true });
	}
	// Reload account information in the right sidebar
	function doAccountReload(){
		$('.Account').load('?show=Account', {}, function(responseText, textStatus, XMLHttpRequest){
			doAccountStyle();
		});
	}
	}

	{// shopping cart box
	var cartBox;
	function doCartStyle(){
		$(".Cart").accordion({ collapsible: true });
		$("a.cartPopup").on('click',function(){
			doCartPopup(this.href, this.popuptitle);
			return false;
		});
	}
	var cartPopupOpened = false;
	function doCartPopup(href, title){
		if(cartPopupOpened) return false;
		cartPopupOpened = true;

		cartBox = $('<div></div>').dialog({
			'autoOpen': false,
			'resizable': false,
			'modal': true,
			'width': 600,
			'height': 400,
			'title': title,
			'html': ajaxLoader,
			'show': 'fade',
			'hide': 'drop',
			'closeOnEscape': true,
			'close': function(){
				cartPopupOpened = false;
				$(this).remove();
				doCartReload();
				doAccountReload();
				doPendingReload(false);
			}
		});
		cartBox.html(ajaxLoader);
		cartBox.dialog('open');
		cartBox.load(href,{},function (responseText, textStatus, XMLHttpRequest){
			styleCartPopup();
		});
		return false;
	}
	function styleCartPopup(){
		styleLink($("._CART_LINK"), cartBox, styleCartPopup);
		styleForm($("._CART_FORM"), cartBox, styleCartPopup);
	}
	// Refresh cart in the right sidebar
	function doCartReload(){
		$('.cart').load('?show=Cart', {}, function(responseText, textStatus, XMLHttpRequest){
			doCartStyle();
			return false;
		});
	}

	function styleAddToCartButton(){
		$("a.ajaxAddToCart").undelegate();
		$("a.ajaxAddToCart").on('click',function(){
			if(cartPopupOpened) return false;
			cartPopupOpened = true;

			var d = $('<div></div>').dialog({
				'autoOpen': false,
				'resizable': false,
				'modal': true,
				'width': 300,
				'height': 200,
				'title': '',
				'show': 'fade',
				'hide': 'drop',
				'closeOnEscape': true,
				'close': function(){
					cartPopupOpened = false;
					$(this).remove();
				}
			});

			d.html(ajaxLoader);
			d.dialog('open');
			$('.cart').html(ajaxLoader);
			var popupUrl = this.href + '&popup&afterAdd';
			$('.cart').load(this.href, {}, function(responseText, textStatus, XMLHttpRequest){
				doCartStyle();
				d.load(popupUrl);
			});
			return false;
		});
	}
	}

	{//Pending
	// Refresh jQuery styles for the pending transfers box in the right sidebar
	function doPendingStyle(){
		$(".Pending").accordion({ collapsible: true });

		$(".pendingReload").click(function(){
			var rotateImage = $(this).find("img");
			var angle = 0;
			setInterval(
				function(){
					angle+=5;
					rotateImage.rotate(angle);
				},50);
			doPendingReload(true);
			return false;
		});
		$(".doTransfer").click(function(){
			this.children[0].src = "./templates/" + template + "/images/ico/arrow_refresh_small.png";
			var rotateImage = $(this).find("img");
			var angle = 0;
			setInterval(
				function(){
					angle+=5;
					rotateImage.rotate(angle);
				},50);

			$(".PendingTransfers").load(this.href, function(){
				doPendingStyle();
				return false;
			});

			return false;
		});
		$(".pendingLoadHistory").click(function(){
			lastToggled = "";// reset, so the menu will work correctly again
			lastShown = "";

			$('.main').html(ajaxLoader);
			$('.main').load(this.href, function(){doContentBoxesStyle();});

			return false;
		});
	}
	// Refresh inventory in the right sidebar
	function doPendingReload(fullRefresh){
		$('.PendingTransfers').load('?show=PendingTransfers'+(fullRefresh ? '&fullRefresh=1' : ''), {}, function(responseText, textStatus, XMLHttpRequest){
			doPendingStyle();
			return false;
		});
	}
	}

	{// top panel
	function doTopPanelStyle(){
		// Expand Panel
		$(".openLoginPanel").click(function(){
			$(".toppanel .panel").slideDown("400").delay(500)
				/*.queue(function(){
					$("#LoginUsername").focus();
				})*/;
		});

		// Collapse Panel
		$(".closeLoginPanel").click(function(){
			$(".toppanel .panel").slideUp("400");
		});
		// Switch buttons from "Log In | Register" to "Close Panel" on click
		$(".toggleLoginPanel a").click(function(){
			$(".toggleLoginPanel a").toggle();
		});

		styleInventoryLoginButton();
	}
	function styleInventoryLoginButton(){
		$(".otherOpenLoginPanel").click(function(){
			if ($(".openLoginPanel").css('display') == "block"){
				$(".toggleLoginPanel a").toggle();
				$(".toppanel .panel").slideDown("400").delay(500);
			}
		});
	}
	}

	{// Item Details Popup
	var itemDetailsDialogOpened = false;
	function itemDetailsDialog(href, title, height){
		if(itemDetailsDialogOpened) return false;
		itemDetailsDialogOpened = true;

		if(!height) height = 260;
		var d = $('<div></div>').dialog({
			'autoOpen': false,
			'resizable': false,
			'modal': true,
			'width': 600,
			'height': height,
			'title': title,
			'show': 'fade',
			'hide': 'drop',
			'closeOnEscape': true,
			'close': function(){
				itemDetailsDialogOpened = false;
				$(this).remove();
			}
		});
		d.html(ajaxLoader);
		d.dialog('open');
		d.load(href,{},function (responseText, textStatus, XMLHttpRequest){
			styleAddToCartButton();
			doToolTipStyle();
		});
		return false;
	}
	}

	function styleForm(selector, target, callback){
		selector.submit(function(){
			target.html(ajaxLoader);
			$.ajax({ // create an AJAX call...
				'data': $(this).serialize(), // get the form data
				'type': $(this).attr('method'), // GET or POST
				'url': $(this).attr('action'), // the file to call
				'success': function(response){ // on success..
					target.html(response);
					if(callback)
						callback();
				}
			});
			//prevent the browser to follow the link
			return false;
		});
	}
	function styleLink(selector, target, callback){
		selector.click(function(){
			target.html(ajaxLoader);
			$(target).load(this.href, function(){
				if(callback)
					callback();
			});
		});
		return false;
	}

	// Set style once the page is loaded
	//styleLogin();
	doRightBarStyle();
	doMenuStyle();
	doTopPanelStyle();

	doHorizontalMenuStyle();
	//doToolTipStyle();
	doContentBoxesStyle();

	$(".itemsToGame").click(function(){ return false; });
	$(".moreMoney").click(function(){ return false; });
});