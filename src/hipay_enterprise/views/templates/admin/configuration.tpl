{**
* 2017 HiPay
*
* NOTICE OF LICENSE
*
*
* @author    HiPay <support.wallet@hipay.com>
* @copyright 2017 HiPay
* @license   https://github.com/hipay/hipay-wallet-sdk-prestashop/blob/master/LICENSE.md
*
*}

<div class="panel">
    <!-- MARKETING START -->
    {* include file marketing.tpl *}
    {include file='./panelHeader/marketing.tpl'}
    <!-- MARKETING END -->
</div>
<!-- ALERTS START -->
{* include file alerts.tpl *}
{include file='./panelHeader/alert.tpl'}
<!-- ALERTS END -->
<div role="tabpanel">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"
            class=" {if ((isset($active_tab) == false) || ($active_tab == 'account_form'))} active{/if}"><a
                href="#account_form" aria-controls="account_form" role="tab" data-toggle="tab">
                <span class="icon icon-cogs"></span> {l s='Account informations' mod='hipay_professional'}</a>
        </li>
        <li role="presentation"
            class=" {if ((isset($active_tab) == true) && ($active_tab == 'payment_form'))} active{/if}"><a
                href="#payment_form" aria-controls="payment_form" role="tab" data-toggle="tab">
                <span class="icon icon-money"></span> {l s='Payment methods' mod='hipay_professional'}</a>
        </li>
        <li role="presentation" class=" {if ((isset($active_tab) == true) && ($active_tab == 'fraud_form'))} active{/if}"><a href="#fraud" aria-controls="fraud" role="tab" data-toggle="tab">
                <span class="icon icon-exclamation"></span> {l s='Fraud' mod='hipay_professional'}</a>
        </li>
        <li role="presentation" class=" {if ((isset($active_tab) == true) && ($active_tab == 'category_form'))} active{/if}"><a href="#category-mapping" aria-controls="category-mapping" role="tab" data-toggle="tab">
                <span class="icon icon-copy"></span> {l s='Category Mapping' mod='hipay_professional'}</a>
        </li>
        <li role="presentation" class=" {if ((isset($active_tab) == true) && ($active_tab == 'carrier_form'))} active{/if}" ><a href="#carrier-mapping" aria-controls="carrier-mapping" role="tab" data-toggle="tab">
                <span class="icon icon-copy"></span> {l s='Carrier Mapping' mod='hipay_professional'}</a>
        </li>
        <li role="presentation"><a href="#faq" aria-controls="faq" role="tab" data-toggle="tab">
                <span class="icon icon-question"></span> {l s='FAQ' mod='hipay_professional'}</a>
        </li>
        <li role="presentation"><a href="#logs" aria-controls="logs" role="tab" data-toggle="tab">
                <span class="icon icon-file-text"></span> {l s='Logs' mod='hipay_professional'}</a>
        </li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel"
             class="tab-pane  {if ((isset($active_tab) == false) || ($active_tab == 'account_form'))} active{/if}"
             id="account_form">
            {include file='./tabs/account.tpl'}
        </div>
        <div role="tabpanel"
             class="tab-pane  {if ((isset($active_tab) == true) && ($active_tab == 'payment_form'))} active{/if}"
             id="payment_form">
            {include file='./tabs/payment-methods.tpl'}
        </div>
        <div role="tabpanel" class="tab-pane {if ((isset($active_tab) == true) && ($active_tab == 'fraud_form'))} active{/if}" id="fraud">
            {include file='./tabs/fraud.tpl'}
        </div>
        <div role="tabpanel" class="tab-pane {if ((isset($active_tab) == true) && ($active_tab == 'category_form'))} active{/if}" id="category-mapping" >
            {include file='./tabs/category-mapping.tpl'}
        </div>
        <div role="tabpanel" class="tab-pane {if ((isset($active_tab) == true) && ($active_tab == 'carrier_form'))} active{/if}" id="carrier-mapping">
            {include file='./tabs/carrier-mapping.tpl'}
        </div>
        <div role="tabpanel" class="tab-pane" id="faq">
            {include file='./tabs/faq.tpl'}
        </div>
        <div role="tabpanel" class="tab-pane" id="logs">
            {include file='./tabs/logs.tpl'}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-credit-card"></i>
                Hipay technical details
            </div>
            <div class="well hidden-print">
                <p>source : {$source}</p>
                <p>IP Address : {$ipaddr}</p>
                <p>Callback url : {$this_callback}</p>
            </div>
        </div>
    </div>
</div>