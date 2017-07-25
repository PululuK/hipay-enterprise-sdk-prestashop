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
<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-credit-card"></i>
                {l s='Hipay actions' mod='hipay_enterprise'}
            </div>
            {if $errorHipay }
                <p class="alert alert-danger">{$errorHipay}</p>
            {/if}
            {if $messagesHipay }
                <p class="alert alert-success">{$messagesHipay}</p>
            {/if}
            <div class="well hidden-print row">
                {if $showChallenge}
                    <div class="col-lg-10 panel">
                        {include file='../admin/actions/panel-challenge.tpl'}
                    </div>
                {/if}
                {if $showCapture && $stillToCapture > 0 && $manualCapture}
                    <div class="col-lg-10 panel">
                     {include file='../admin/actions/capture.partial.tpl'}
                    </div>
                {/if}
                {if $showRefund && $alreadyCaptured && $refundableAmount > 0}
                    <div class="col-lg-10 panel">
                        {include file='../admin/actions/refund.partial.tpl'}
                    </div>
                {/if}
            </div>
    </div>
</div>

<script>
    $("#hipay_refund_type").change(function () {
        if ($(this).val() == "complete") {
            $("#block-refund-amount").hide();
        } else {
            $("#block-refund-amount").show();
        }
    });

    $("#hipay_capture_type").change(function () {
        if ($(this).val() == "complete") {
            $("#block-capture-amount").hide();
        } else {
            $("#block-capture-amount").show();
        }
    });

</script>