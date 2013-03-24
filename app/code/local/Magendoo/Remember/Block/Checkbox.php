<?php
class Magendoo_Remember_Block_Checkbox extends Mage_Core_Block_Template
{
    /**
     * Prevent rendering if Persistent disabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $helper Magendoo_Remember_Helper_Data */
        $helper = Mage::helper('remember');
        return ($helper->isEnabled()) ? parent::_toHtml() : '';
    }

    /**
     * Is "Remember Me" checked
     *
     * @return bool
     */
    public function isRememberMeChecked()
    {
        /** @var  $helper Magendoo_Remember_Helper_Data */
        $helper = Mage::helper('remember');
        return $helper->isEnabled() && $helper->isRememberMeCheckedDefault();
    }


    /**
     * Show "Clear OLD session" checkbox
     *
     * @return bool
     */
    public function showClearOld()
    {
        /** @var  $helper Magendoo_Remember_Helper_Data */
        $helper = Mage::helper('remember');
        return $helper->isEnabled() && $helper->showClearOld();
    }
}
