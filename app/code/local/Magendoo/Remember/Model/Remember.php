<?php

class Magendoo_Remember_Model_Remember extends Mage_Core_Model_Abstract
{
    protected function _construct(){

       $this->_init("remember/remember");

    }


   /**
     * before save remember
     * @access protected
     * @return Magendoo_Remember_Model_Remember
     *
     */
    protected function _beforeSave(){

        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()){
            $this->setCreated($now);
        }
        $this->setUpdated($now);
        $this->setLastip(Mage::helper('core/http')->getRemoteAddr());
        $this->setUseragent(Mage::helper('core/http')->getHttpUserAgent());
    }



    public function getByRememberCookie($rememberCookie) {

        $collection = $this->getResourceCollection();

        $collection->addFieldToFilter('customer_id',$rememberCookie->getCustomerId());
        $collection->addFieldToFilter('token',$rememberCookie->getToken());

        return $collection->getFirstItem();

    }



    public function deleteAllTokens($customerID) {

        $collection = $this->getResourceCollection();

        $collection->addFieldToFilter('customer_id',$customerID);
        foreach($collection as $item) {
            $item->delete();
        }
        return $this;

    }

    public function deleteByRememberCookie($rememberCookie) {

        $collection = $this->getResourceCollection();

        $collection->addFieldToFilter('customer_id',$rememberCookie->getCustomerId());
        $collection->addFieldToFilter('token',$rememberCookie->getToken());
        foreach($collection as $item) {
            $item->delete();
        }
        return $this;

    }



}
