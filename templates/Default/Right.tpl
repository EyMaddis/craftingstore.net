{$main->prepare('Right')}
<div class="ItemSearch">{include file="ItemSearch.tpl"}</div>
<div class="cart">{include file="Cart.tpl"}</div>
{if $IS_LOGGED_IN}
<div class="PendingTransfers">{include file="PendingTransfers.tpl"}</div>
<div class="Account">{include file="Account.tpl"}</div>
{else}{*Warenkorb?*}{/if}