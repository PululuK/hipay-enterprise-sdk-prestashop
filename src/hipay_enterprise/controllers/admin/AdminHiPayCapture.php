<?php
/**
 * 2017 HiPay
 *
 * NOTICE OF LICENSE
 *
 * @author    HiPay <support.wallet@hipay.com>
 * @copyright 2017 HiPay
 * @license   https://github.com/hipay/hipay-wallet-sdk-prestashop/blob/master/LICENSE.md
 */

require_once(dirname(__FILE__) . '/../../classes/helper/apiHandler/ApiHandler.php');
require_once(dirname(__FILE__) . '/../../classes/helper/tools/hipayDBQuery.php');

use HiPay\Fullservice\Enum\Transaction\Operation;

class AdminHiPayCaptureController extends ModuleAdminController
{
    public function __construct()
    {
        $this->module = 'hipay_enterprise';
        $this->bootstrap = true;
        $this->context = Context::getContext();

        parent::__construct();

        $this->apiHandler = new ApiHandler(
            $this->module,
            $this->context
        );
        $this->db = new HipayDBQuery($this->module);
    }

    public function postProcess()
    {
        $context = Context::getContext();


        if (Tools::isSubmit('id_order') && Tools::getValue('id_order') > 0) {
            $order = new Order(Tools::getValue('id_order'));
            if (!Validate::isLoadedObject($order)) {
                throw new PrestaShopException('Can\'t load Order object');
            }
            ShopUrl::cacheMainDomainForShop((int)$order->id_shop);
            $transactionReference = $this->db->getTransactionReference($order->id);
            $paymentProduct = $this->db->getPaymentProductFromMessage($order->id);
            $params = array("method" => $paymentProduct);
        }

        // First check
        if (Tools::isSubmit('hipay_capture_submit')) {
            //capture with no basket
            if (Tools::isSubmit('hipay_capture_type')) {
                $capture_type = Tools::getValue('hipay_capture_type');
                $capture_amount = Tools::getValue('hipay_capture_amount');
                $capture_amount = str_replace(
                    ' ',
                    '',
                    $capture_amount
                );
                $capture_amount = (float)str_replace(
                    ',',
                    '.',
                    $capture_amount
                );
            }

            if (!$capture_amount) {
                $hipay_redirect_status = $this->module->l(
                    'Please enter an amount',
                    'capture'
                );
                Tools::redirectAdmin(
                    $context->link->getAdminLink(
                        'AdminOrders'
                    ) . '&id_order=' . (int)$order->id . '&vieworder&hipay_err_capture=' . $hipay_redirect_status . '#hipay'
                );
                die('');
            }
            if ($capture_amount <= 0) {
                $hipay_redirect_status = $this->module->l(
                    'Please enter an amount greater than zero',
                    'capture'
                );
                Tools::redirectAdmin(
                    $context->link->getAdminLink(
                        'AdminOrders'
                    ) . '&id_order=' . (int)$order->id . '&vieworder&hipay_err_capture=' . $hipay_redirect_status . '#hipay'
                );
                die('');
            }

            if (!is_numeric($capture_amount)) {
                $hipay_redirect_status = $this->module->l(
                    'Please enter an amount',
                    'capture'
                );
                Tools::redirectAdmin(
                    $context->link->getAdminLink(
                        'AdminOrders'
                    ) . '&id_order=' . (int)$order->id . '&vieworder&hipay_err_capture=' . $hipay_redirect_status . '#hipay'
                );
                die('');
            }

            // total captured amount
            $totalPaid = $order->getTotalPaid();
            // remaining amount to capture
            $stillToCapture = $order->total_paid_tax_incl - $totalPaid;

            if (round(
                    $capture_amount,
                    2
                ) > round(
                    $stillToCapture,
                    2
                )
            ) {
                $hipay_redirect_status = $this->module->l(
                    'Amount exceeding authorized amount',
                    'capture'
                );
                Tools::redirectAdmin(
                    $context->link->getAdminLink(
                        'AdminOrders'
                    ) . '&id_order=' . (int)$order->id . '&vieworder&hipay_err_capture=' . $hipay_redirect_status . '#hipay'
                );
                die('');
            }

            if (!$transactionReference) {
                $hipay_redirect_status = $this->module->l(
                    'No transaction reference link to this order',
                    'capture'
                );
                Tools::redirectAdmin(
                    $context->link->getAdminLink(
                        'AdminOrders'
                    ) . '&id_order=' . (int)$order->id . '&vieworder&hipay_err_capture=' . $hipay_redirect_status . '#hipay'
                );
                die('');
            }

            if ($capture_type == 'complete') {
                $params["amount"] = $stillToCapture;
                $params["order"] = $order->id;
                $params["transaction_reference"] = $transactionReference;
                $this->apiHandler->handleCapture($params);
            } elseif ($capture_type == 'partial') {
                $params["amount"] = $capture_amount;
                $params["order"] = $order->id;
                $params["transaction_reference"] = $transactionReference;
                $this->apiHandler->handleCapture($params);
            }
        } elseif ((Tools::isSubmit('hipay_capture_basket_submit'))) {
            //capture with basket
            if (Tools::getValue('hipay_capture_type') == "partial") {
                $refundItems = (!Tools::getValue('hipaycapture')) ? array() : Tools::getValue('hipaycapture');
                //check if no items has been sent
                if (array_sum($refundItems) == 0 && Tools::getValue('hipay_capture_fee')
                    !== "on"
                ) {
                    $hipay_redirect_status = $this->module->l(
                        'Select at least one item to capture',
                        'capture'
                    );
                    Tools::redirectAdmin(
                        $context->link->getAdminLink(
                            'AdminOrders'
                        ) . '&id_order=' . (int)$order->id . '&vieworder&hipay_err_capture=' . $hipay_redirect_status . '#hipay'
                    );
                    die('');
                }

                $params["refundItems"] = $refundItems;
                $params["order"] = $order->id;
                $params["transaction_reference"] = $transactionReference;
                $params["capture_refund_fee"] = Tools::getValue('hipay_capture_fee');
            } else {
                $params["refundItems"] = $refundItems;
                $params["order"] = $order->id;
                $params["transaction_reference"] = $transactionReference;
            }
            $this->apiHandler->handleCapture($params);
        }

        Tools::redirectAdmin(
            $context->link->getAdminLink(
                'AdminOrders'
            ) . '&id_order=' . (int)$order->id . '&vieworder&hipay_err_capture=ok#hipay'
        );
    }
}
