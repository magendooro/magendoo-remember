<?php


/**
 * Remember front controller
 *
 */
class Magendoo_Remember_AccountController extends Mage_Core_Controller_Front_Action
{



    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }


    public function confirmAction() {

      $session = $this->_getSession();
      $helper = Mage::helper('remember');

      if(!$session->isLoggedIn()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
            $this->_redirectUrl(Mage::helper('customer')->getLoginUrl());
            return;
      }

      if(!$session->getIsRememberMeLogged()) {
            $this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
      }

      if($this->getRequest()->getPost()) {
            $pass= $this->getRequest()->getPost('current_password');
            if(!empty($pass)) {
                $customer = $session->getCustomer();
                if($customer->validatePassword($pass)) {
                    $session->unsIsRememberMeLogged();
                    $session->addSuccess($helper->__("Your identity has confirmed, now you are fully logged IN"));
                    if($session->getRedirectAfterConfirm()) {
                        $this->_redirectUrl($session->getRedirectAfterConfirm());
                    } else {
                        $this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
                    }
                    $session->unsRedirectAfterConfirm();
                    return;
                } else {
                    $session->addError($helper->__("Invalid password"));
                }
            } else {
                $session->addError($helper->__("Please input yout password"));
            }
      } else {
            $session->addNotice($helper->__("You are automatically authenticated, you must to confirm your identity  in order to access some actions."));
      }


      $this->loadLayout();
      $this->_initLayoutMessages('customer/session');
      $this->_initLayoutMessages('catalog/session');
      $this->renderLayout();
    }
}
