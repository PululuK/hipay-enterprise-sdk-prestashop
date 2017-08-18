<?php
/**
 * HiPay Enterprise SDK Prestashop
 *
 * 2017 HiPay
 *
 * NOTICE OF LICENSE
 *
 * @author    HiPay <support.tpp@hipay.com>
 * @copyright 2017 HiPay
 * @license   https://github.com/hipay/hipay-enterprise-sdk-prestashop/blob/master/LICENSE.md
 */

require_once(dirname(__FILE__) . '/../../lib/vendor/autoload.php');

use HiPay\Fullservice\Enum\Transaction\TransactionStatus;

/**
 * Handle order message
 *
 * @author      HiPay <support.tpp@hipay.com>
 * @copyright   Copyright (c) 2017 - HiPay
 * @license     https://github.com/hipay/hipay-enterprise-sdk-prestashop/blob/master/LICENSE.md
 * @link    https://github.com/hipay/hipay-enterprise-sdk-prestashop
 */
class HipayOrderMessage
{
    /**
     * write notification order message
     * @param type $orderId
     * @param type $transaction
     */
    public static function orderMessage($orderId, $customerId, $transaction)
    {
        $data = HipayOrderMessage::formatOrderData($transaction);
        HipayOrderMessage::addMessage($orderId, $customerId, $data);
    }

    /**
     * format notification data for order message
     * @param type $transaction
     * @return type
     */
    public static function formatOrderData($transaction)
    {
        $message = "HiPay Notification " . $transaction->getState() . "\n";
        switch ($transaction->getStatus()) {
            case TransactionStatus::CAPTURED: //118
            case TransactionStatus::CAPTURE_REQUESTED: //117
                $message .= "Registered notification from HiPay about captured amount of " .
                    $transaction->getCapturedAmount() .
                    "\n";
                break;
            case TransactionStatus::REFUND_REQUESTED: //124
            case TransactionStatus::REFUNDED: //125
                $message .= "Registered notification from HiPay about refunded amount of " .
                    $transaction->getRefundedAmount() .
                    "\n";
                break;
        }

        $message .= "Order total amount :" . $transaction->getAuthorizedAmount() . "\n";
        $message .= "\n";
        $message .= "Transaction ID: " . $transaction->getTransactionReference() . "\n";
        $message .= "HiPay status: " . $transaction->getStatus() . "\n";
        if (Validate::isCleanHtml($message)) {
            return $message;
        }

        return "Something went wrong";
    }

    /**
     * generic function to add prestashop order message
     * @param type $orderId
     * @param type $data
     */
    private static function addMessage($orderId, $customerId, $data)
    {
        if (_PS_VERSION_ < '1.7.1') {
            $message = new Message();
            $message->message = $data;
            $message->id_order = (int)$orderId;
            $message->private = 1;

            $message->add();
        } else {
            $customer = new Customer($customerId);
            $order = new Order($orderId);
            $shop = new Shop($order->id_shop);
            $context = Context::getContext();
            Shop::setContext(Shop::CONTEXT_SHOP, $order->id_shop);
            //Force context shop otherwise we get duplicate customer thread
            $context->shop = $shop;
            //check if a thread already exist
            $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($customer->email, $orderId);
            if (!$id_customer_thread) {
                $customer_thread = new CustomerThread();
                $customer_thread->id_contact = 0;
                $customer_thread->id_lang = 1;
                $customer_thread->id_customer = (int)$customerId;
                $customer_thread->id_order = (int)$orderId;
                $customer_thread->status = 'open';
                $customer_thread->token = Tools::passwdGen(12);
                $customer_thread->id_shop = (int)$context->shop->id;
                $customer_thread->id_lang = (int)$context->language->id;
                $customer_thread->email = $customer->email;

                $customer_thread->add();
            } else {
                $customer_thread = new CustomerThread((int)$id_customer_thread);
            }

            $customer_message = new CustomerMessage();
            $customer_message->id_customer_thread = $customer_thread->id;
            $customer_message->message = $data;
            $customer_message->private = 1;
            $customer_message->add();
        }
    }
}
