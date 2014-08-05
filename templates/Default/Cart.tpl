{$main->prepare('Cart')}
{if $POPUP}
<div id="_CART_CONTENT">
	{if $CART_AFTER_ADD}{$CART_AFTER_ADD}
	{elseif $CART_BOUGHT}<div{if $CART_ERROR} class="error"{/if}>{$CART_BOUGHT}</div>
	{elseif $CART_BUY}
	<form action="?show=Cart&amp;popup&amp;buy&amp;confirm" class="_CART_FORM" method="post">
		<div>
			{include file="Cart.1.tpl"}
			{if $BUYITEM_AGREEMENT}
			<div style="margin-top:10px;font-weight:bold">{$BUYITEM_AGREEMENT_LABEL}</div>
			<div class="shopagreement">{$BUYITEM_AGREEMENT}</div>
			<div><input type="checkbox" name="agree" id="agree" /><label for="agree" {if $BUYITEM_AGREEMENT_ERROR}style="color: red; "{/if}> {$BUYITEM_AGREEMENT_ACCEPT}</label></div>
			{else}<input type="hidden" name="agree" value="1" />{/if}
			<div style="margin:15px 0;overflow:auto">
				<div class="CartLink left"><a href="?show=Cart&amp;popup" class="_CART_LINK greyButton">{$CHECKOUT_BACK_TO_CART}</a></div>
				<div class="CartButton right"><input type="submit" value="{$CART_BUY}" class="blueButton" /></div>
			</div>
		</div>
	</form>
	{elseif $CART_NEED_LOGIN}{$CART_NEED_LOGIN}<br />
	<a href="{$SECURE_URL}?show=Login&shop={$SHOP_ID}&setLang={$LANG}">{$CART_LOGIN_NOW}</a>
	{else}
	<form action="?show=Cart&amp;popup" class="_CART_FORM" method="post">
		<div>
			{include file="Cart.1.tpl"}
			{if !$CART_EMPTY}
			<div class="CartButton left"><input type="submit" value="{$CART_UPDATE}" class="greyButton" /></div>
			<div class="CartLink right"><a href="?show=Cart&amp;popup&amp;buy" class="_CART_LINK blueButton">{$CART_CHECKOUT}</a></div>
			{/if}
		</div>
	</form>
	{/if}
</div>
{else}
<div class="Cart">
	<h3><a href="#">{$CART_TITLE}</a></h3>
	<div>
		{if $CART_EMPTY}{$CART_EMPTY}{else}
		<table class="cartInfo">
			{foreach from=$CART_DATA item=product}
			<tr>
				<td><img src="{$product.Image}" /></td>
				<td>{$product.Label}</td>
				<td>{$product.Amount}x</td>
			</tr>
			{/foreach}
		</table>
		<div style="float:left"><a href="?show=Cart&amp;popup" class="cartPopup">{$CART_SHOW_FULL}</a></div>
		<div style="float:right"><a href="?show=Cart&amp;popup&amp;buy" class="cartPopup">{$CART_SHOW_BUY}</a></div>
		{/if}
	</div>
</div>
{/if}