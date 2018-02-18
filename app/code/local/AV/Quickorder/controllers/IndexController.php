<?php

class AV_Quickorder_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $config = Mage::getStoreConfig('av_quickorder/general/enable');

        if ($config) {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirect("/");
        }
    }

    public function orderAction() {
        $product_ids = array();
        $order = Mage::app()->getRequest()->getParam('order');
        $cart = Mage::getSingleton('checkout/cart');

        foreach ($order as $orderdetails) {
            if ($orderdetails['sku'] != '' && $orderdetails['quantity'] > 0) {
                $orders[] = array(
                    'product' => Mage::getModel('catalog/product')->getIdBySku($orderdetails['sku']),
                    'sku' => $orderdetails['sku'],
                    'qty' => (int) $orderdetails['quantity']
                );
            } else {
                $message = $this->__('The product %s is not found or incorrect quantity', $orderdetails['sku']);
                Mage::getSingleton('core/session')->addError($message);
            }
        }

        try {
            if ($orders) {
                foreach ($orders as $_order) {
                    $qty = $_order['qty'];
                    $product = $_order['product'];
                    $sku = $_order['sku'];
                    $cart->addProduct($product, $qty);
                    $cart->save();
                    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                    $message = $this->__('The %s have been added to your cart %s times.', $sku, $qty);
                    Mage::getSingleton('core/session')->addSuccess($message);
                    $this->_redirect('checkout/cart');
                }
            }
        } catch (Exception $ex) {
            $this->_redirectReferer();
            Mage::getSingleton('core/session')->addError($ex->getMessage() . '<br>' . 'Sku: ' . $sku);
        }
    }

    public function postAction() {
        $this->orderAction();
    }

}
