<?php

class UviPay_CardError extends UviPay_Exception_Base
{
	
    // Redefine the exception so message isn't optional
    public function __construct($message,$code = 0, Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);

    }


     public function customFunction() {
        // A custom function for this type of exception\n
    }

    
}