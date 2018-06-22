<?php
class Fourtek_Bajaj_PaymentController extends Mage_Core_Controller_Front_Action 
{
 

public function succesorderAction(){

   $param=$this->getRequest()->getPost();
   if($param['ResponseCode']==0){
    $order = Mage::getModel('sales/order')->loadByIncrementId($param['OrderNo']);
    $order->setState(Mage_Sales_Model_Order::STATE_NEW, true);
    $order->save();
    $response['message'] = 'sucessfull';
	Mage_Core_Controller_Varien_Action::_redirect('bajaj/payment/response', array('_secure' => false, '_query'=> $arr_querystring));	
    }else{
    $order = Mage::getModel('sales/order')->loadByIncrementId($param['OrderNo']);
    $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
    $order->save();
    $response['message'] = 'Order Canceled ';
                              
    }
      
   echo json_encode($response);
   exit;
 
  }

 public function gatewayAction() 
  {
    if ($this->getRequest()->get("orderId"))
    {
      $arr_querystring = array(
        'flag' => 1, 
        'orderId' => $this->getRequest()->get("orderId")
      );
       
      Mage_Core_Controller_Varien_Action::_redirect('bajaj/payment/response', array('_secure' => false, '_query'=> $arr_querystring));
    }
  }
   
  public function redirectAction() 
  {
    $this->loadLayout();
    $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','bajaj',array('template' => 'bajaj/redirect.phtml'));
    $this->getLayout()->getBlock('content')->append($block);
    $this->renderLayout();
  }
 
  public function responseAction() 
  {
    $param=$this->getRequest()->getPost();
    //print_r($param); die;
    if($param['ResponseCode']==0){
    $order = Mage::getModel('sales/order')->loadByIncrementId($param['OrderNo']);
    $order->setState(Mage_Sales_Model_Order::STATE_NEW, true);
    $order->save();
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$write->insert("bajaj_payment",array("ResponseCode" => $param['ResponseCode'], "ResponseDesc" => $param['ResponseDesc'],"OrderNo"=>$param['OrderNo'],"RequestID"=>$param['RequestID'],"DealID"=>$param['DealID'])
);
      Mage::getSingleton('checkout/session')->unsQuoteId();
      Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=> false));
    }
    else
    {
     $order = Mage::getModel('sales/order')->loadByIncrementId($param['RequestID']);
     $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
     $order->save();
	 $write = Mage::getSingleton("core/resource")->getConnection("core_write");
$write->insert("bajaj_payment",array("ResponseCode" => $param['ResponseCode'], "ResponseDesc" => $param['ResponseDesc'],"OrderNo"=>$param['OrderNo'],"RequestID"=>$param['RequestID'],"DealID"=>$param['DealID'])
);
     Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=> false));
    }
  }
}