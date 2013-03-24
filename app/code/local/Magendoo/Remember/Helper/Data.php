<?php
class Magendoo_Remember_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_ENABLED = 'remember/options/enabled';
    const XML_PATH_LIFETIME = 'remember/options/lifetime';
    const XML_PATH_REMEMBER_CLEAR_OLD = 'remember/options/remember_clear_old';
    const XML_PATH_REMEMBER_DEFAULT = 'remember/options/remember_default';


    const XML_MAGE_PERSISTENT = 'persistent';


    const LOGGED_IN_LAYOUT_HANDLE = 'customer_logged_in_remember_handle';
    const LOGGED_OUT_LAYOUT_HANDLE = 'customer_logged_out_remember_handle';


    const REMEMBER_COOKIE = 'remember';

    protected $_rememberCookie = null;


    /**
     * Checks whether Remember Me Functionality is enabled
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isEnabled($store = null)
    {

        return !$this->isPersistenceEnabled() && Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }


    /**
     * Checks whether Mage Persistence Functionality is enabled
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isPersistenceEnabled($store = null)

    {
        return Mage::helper('core')->isModuleEnabled('Mage_Persistent') && Mage::helper(self::XML_MAGE_PERSISTENT)->isEnabled();
    }

    /**
     * Is "Remember Me" checked by default
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isRememberMeCheckedDefault($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_DEFAULT, $store);
    }


    /**
     * Is "Remember Me" checked by default
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    public function showClearOld($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_CLEAR_OLD, $store);
    }

    protected function _getCookie() {
        return Mage::app()->getCookie();
    }


    public function hasRememberCookie() {
        return $this->getRememberCookie()->getCustomerId() != 0;
    }

    public function getRememberCookie() {

        if(!$this->_rememberCookie) {
            $this->_rememberCookie = new Varien_Object();
            try {

                if(!($cookie = $this->_getCookie())) {
                    Mage::throwException($this->__('Cookie is not enabled'));
                }
                $remember = $this->_getCookie()->get(self::REMEMBER_COOKIE);

                if($remember) {
                    $rememberCookie = json_decode(Mage::helper('core')->decrypt($remember),true);
                    if(!$this->_checkRememberCookie($rememberCookie)) {
                        Mage::throwException($this->__('Invalid cookie received %s, decoded as: ',$remember,var_export($rememberCookie,true)));
                    }

                    $this->_rememberCookie->setCustomerId($rememberCookie[0]);
                    $this->_rememberCookie->setToken($rememberCookie[1]);
                    $this->_rememberCookie->setNonce($rememberCookie[2]);
                }
            } catch(Exception $e) {
                    //remove invalid cooke
                    $this->deleteRememberCookie();
                    Mage::logException($e);
            }
        }
        return $this->_rememberCookie;
    }


    public function setRememberCookie($object) {

            if(!($cookie = $this->_getCookie())) {
                return $this;
            }
            if(!$object || !$object->getCustomerId()) {
                return $this;
            }

            $this->_rememberCookie = new Varien_Object(array(
                'customer_id'=>$object->getCustomerId(),
                'token'=>$object->getToken(),
                'nonce'=>$object->getNonce()
            ));
            $cookieValue = Mage::helper('core')->encrypt(json_encode(array_values($this->_rememberCookie->getData())));
            //public function set($name, $value, $period = null, $path = null, $domain = null, $secure = null, $httponly = null)

            $lifetime    = max($cookie->getLifetime(),(int)Mage::getStoreConfig(self::XML_PATH_LIFETIME));

            $cookie->set(self::REMEMBER_COOKIE,$cookieValue,$lifetime,null,null,null,$httponly=true);

            return $this;
    }


    public function deleteRememberCookie() {

            if(!($cookie = $this->_getCookie())) {
                return $this;
            }
            $rememberCookie = $cookie->get(self::REMEMBER_COOKIE);
            if($rememberCookie !== false) {
                //public function delete($name, $path = null, $domain = null, $secure = null, $httponly = null)
                $cookie->delete(self::REMEMBER_COOKIE,null,null,null,$httponly=true);
            }
            return $this;
    }

    protected  function _checkRememberCookie($rememberCookie) {
        return (
            $rememberCookie &&
            is_array($rememberCookie) &&
            count($rememberCookie) == 3 &&
            !empty($rememberCookie[0]) &&
            is_numeric($rememberCookie[0]) &&
            !empty($rememberCookie[1]) &&
            is_string($rememberCookie[1]) &&
            strlen($rememberCookie[1]) == 40 &&
            !empty($rememberCookie[2]) &&
            is_string($rememberCookie[2]) &&
            strlen($rememberCookie[2]) == 40
        );
    }

}
