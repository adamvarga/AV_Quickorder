<?php

class AV_Quickorder_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getQuickorderUrl() {
        return $this->_getUrl('quickorder/index/post');
    }

}
