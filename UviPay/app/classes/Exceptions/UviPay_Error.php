<?php

class UviPay_Error extends UviPay_Exception_Base
{
	
    // Redefine the exception so message isn't optional
    public function __construct($message,$code = 0, Exception $previous = null) {
        // some code
    	//$this->error_body = $error_body;
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);

    }

    public function customFunction() {
        // A custom function for this type of exception\n
    }

    
}