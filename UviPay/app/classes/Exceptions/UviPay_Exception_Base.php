<?php 

class UviPay_Exception_Base
{
    public $var;
    private $error_body;

    function __construct($exception_name,$error_body = array(), $message="error happened", $code = 0, Exception $previous = null) {
    	if(empty($error_body)){
    		$error_body['message']=$message;
    		
    	}
    	if(!isset($error_body['error'])){
    		$error_body['error']=array('code'=>$code,'message'=>$message,'type'=>'invalid_request_error');
    	}

    	$this->error_body = $error_body;
    	require_once Uvi_UviPay_autoload_Page.'/app/classes/Exceptions/'.$exception_name.'.php';
        throw new $exception_name($message,$error_body,$code,$previous);
    }

    public function getJsonBody()
    {
        return $this->error_body;
    }
}
