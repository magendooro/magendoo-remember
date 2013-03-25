<?php
class Magendoo_Remember_Model_Observer
{


    private $_initialized = false;
    private $_session     = null;




    protected function isInitialized() {
        return $this->_initialized;
    }

    protected function setInitialized($bool=true) {
        $this->_initialized = $bool;
    }



    public function onLogin(Varien_Event_Observer $observer)
    {

            if(!Mage::helper('remember')->isEnabled()) {
                return $this;
            }


            //Mage::dispatchEvent('customer_login', array('customer'=>$customer));
            $customer= $observer->getEvent()->getCustomer();
            if(!$customer || !$customer->getId()) {
                return $this;
            }

            $data = Mage::app()->getRequest()->getPost('remember');
            if(!$data) {
                return $this;
            }


            try {

                $remember = Mage::getModel('remember/remember');

                if(!empty($data['clear'])) {
                    $remember->deleteAllTokens($customer->getId());
                }

                if(Mage::helper('remember')->hasRememberCookie()) {
                    $remember->deleteByRememberCookie(Mage::helper('remember')->getRememberCookie());
                }


                if(!empty($data['me'])) {
                    $remember->setCustomerId($customer->getId());
                    $remember->setToken($this->_generateToken($customer));
                    $remember->setNonce($this->_generateToken($customer,'NONCE'));

                    $remember->save();
                    Mage::helper('remember')->setRememberCookie($remember);

                } else {
                    Mage::helper('remember')->deleteRememberCookie();
                }
            } catch(Exception $e) {
                Mage::logException($e);
            }


            return $this;


    }

        public function onLogout(Varien_Event_Observer $observer)
        {
            if(!Mage::helper('remember')->isEnabled()) {
                return $this;
            }

            //Mage::dispatchEvent('customer_logout', array('customer' => $this->getCustomer()) );
            $customer= $observer->getEvent()->getCustomer();
            if(Mage::helper('remember')->hasRememberCookie()) {
                Mage::getModel('remember/remember')->deleteByRememberCookie(Mage::helper('remember')->getRememberCookie());
                Mage::helper('remember')->deleteRememberCookie();
            }
        }


   /*

    called from 'controller_action_predispatch' event
    Mage::dispatchEvent('controller_action_predispatch', array('controller_action' => $this));

    must to be SINGLETON in config.xml

    */

    public function autoLogin(Varien_Event_Observer $observer) {


        $session = Mage::getSingleton('customer/session');
        $action  =  $observer->getEvent()->getControllerAction();




        if(!$action || $this->isInitialized() || !Mage::helper('remember')->isEnabled()) {
            return $this;
        }
        $this->setInitialized(true);

        $fullActionName = $action->getFullActionName();
        $isAjax         = $action->getRequest()->isAjax();


        if($session->isLoggedIn() && $session->getIsRememberMeLogged()) {

            $protectActions = array(
                'customer_account_edit',
                'customer_address_new',
                'customer_address_edit',
                'downloadable_customer_products'
            );
            if(in_array($fullActionName,$protectActions)) {
                $session->setRedirectAfterConfirm(Mage::helper('core/url')->getCurrentUrl());
                $action->getResponse()->setRedirect(Mage::getUrl('remember/account/confirm'));
                return $this;
            }
        }


        if($session->isLoggedIn() || !Mage::helper('remember')->hasRememberCookie()) {
            return $this;
        }


        $skipActions = array(
            'remember_account_confirm',
            'customer_account_logout',
            'customer_account_logoutSuccess',
            'cms_index_noRoute',
            'customer_account_loginPost',
        );

        if(in_array($fullActionName,$skipActions)) {
            return $this;
        }


        try {

            $rememberModel = Mage::getModel('remember/remember');
            $rememberCookie = Mage::helper('remember')->getRememberCookie();
            $remember = $rememberModel->getByRememberCookie($rememberCookie);
            if(!$remember->getId()) {
                //expired token?
                Mage::helper('remember')->deleteRememberCookie();
                return $this;
            }

            $customer = Mage::getModel('customer/customer')->load($remember->getCustomerId());
            if(!$customer->getId()) {
                //customer not (more) exists
                Mage::helper('remember')->deleteRememberCookie();
                return $this;
            }

            if($rememberCookie->getNonce() != $remember->getNonce()) {
                //somebody stollen cookie?
                Mage::getSingleton('core/session')->addError(Mage::helper('remember')->__('Someone else has used your login information to acccess this page! For your security ALL "Remember Me" tokens were removed. Please <a href="%s">LOG IN with your credentials</a> and check your data.',Mage::helper('customer')->getLoginUrl()));

                Mage::getSingleton('core/session')->addNotice(Mage::helper('remember')->__('Your "Remember Me" token was used on %s from %s, browser %s',
                        $remember->getUpdated(). " (GMT time)",
                        $remember->getLastip(),
                        $remember->getUseragent()
                ));

                $rememberModel->deleteAllTokens($customer->getId());
                Mage::helper('remember')->deleteRememberCookie();
                return $this;
            }


            //regenerate another "nonce" for curent token and log in user
            $remember->setNonce($this->_generateToken($customer,'NONCE'));
            $remember->save();

            $session->setCustomerAsLoggedIn($customer);
            $session->setIsRememberMeLogged(true);




            Mage::helper('remember')->setRememberCookie($remember);


        } catch(Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }


    protected $_suffix = null;
    protected function _generateToken($customer,$prefix="TOKEN") {

        if(!$this->_suffix) {
            $this->_suffix = ':'.$customer->getId().':'.Mage::getSingleton('customer/session')->getSessionId().':'.md5(serialize($_SERVER));
        }
        return sha1(uniqid($prefix,true).$this->_suffix);
    }


}
