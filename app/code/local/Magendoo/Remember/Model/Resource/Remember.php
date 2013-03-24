<?php
class Magendoo_Remember_Model_Resource_Remember extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("remember/remember", "entity_id");
    }
}