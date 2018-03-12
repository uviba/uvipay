<?php 

//missed params, or other code error on your server.

class UviPay_ResultError extends Exception
{
	private $error_body;


    // Redefine the exception so message isn't optional
    public function __construct($message,$error_body = array(),$code = 0, Exception $previous = null) {
        // make sure everything is assigned properly
        $this->error_body = $error_body;
        parent::__construct($message, $code, $previous);

    }


     public function customFunction() {
        // A custom function for this type of exception\n
    }

    public function getJsonBody()
    {
        return $this->error_body;
    }

    
}