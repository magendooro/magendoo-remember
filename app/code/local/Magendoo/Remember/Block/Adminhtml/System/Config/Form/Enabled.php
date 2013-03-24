<?php

class Magendoo_Remember_Block_Adminhtml_System_Config_Form_Enabled
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {

        $helper = Mage::helper('remember');

        if($helper->isPersistenceEnabled()) {
            $element->setComment(Mage::helper('remember')->__('This cannot works with <a href="%s">Persistent Shopping Cart</a> <strong>ENABLED</strong>. Please disable it, in order to enable Remember Me.',$this->getUrl('*/*/*',array('section'=>'persistent'))));
            $element->setValue(0);
            $element->setDisabled(true);

        } else {
            $element->setComment(Mage::helper('remember')->__('<a href="%s">Persistent Shopping Cart</a> is disabled. If you enable  later, this will automatically disabled',$this->getUrl('*/*/*',array('section'=>'persistent'))));
        }
        return parent::_getElementHtml($element);

    }

}
