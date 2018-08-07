<?php 


use \Curl\Curl;


class UviPay{

private static $private_key='';

	public static function bring_affiliates($method='cookie'){
		if($method=='cookie'){
			if(isset($_GET['uviba_params'])){
				setcookie('uviba_params', json_encode($_GET['uviba_params']), time()+86400,'/',false,false);
			}
		}
	}

	public static function setApiPrivateKey($u_private_key=''){
		return self::$private_key=$u_private_key;
	}
	public static function setApiKey($u_private_key){
		return self::setApiPrivateKey($u_private_key);
	}
	public static function checkErrors(){
		//general errors, for example private key
		if(trim(self::$private_key)==''){
			// UviPay_CodeError means some error happened in code
			// UviPay_ResultError means errors that our server returned
			throw new UviPay_Exception_Base("UviPay_CodeError",array(
				'message'=>'Please define private key with UviPay::setApiPrivateKey function.',
			),"Please define private key with UviPay::setApiPrivateKey function.", 0);
			exit;
		}
	}

	public static function charge($payment_info=array()){
		self::checkErrors();

		if(empty($payment_info)){
			throw new UviPay_Exception_Base("UviPay_CodeError",array(
				'message'=>'Payment Info is not defined in code. Please define it in UviPay::charge function.',
			),"Payment Info is not defined in code. Please define it in UviPay::charge function.", 0);
			exit;
			
		}else if(!isset($payment_info['amount'])){
			$mes = 'Please provide how much money needed';
			throw new UviPay_Exception_Base("UviPay_CodeError",array(
				'message'=>$mes,
			),$mes, 1);
			exit;
		}
		if(!isset($payment_info['token'])){
 
			if(isset($_GET['UvibaToken'])){
				$payment_info['token']=$_GET['UvibaToken'];
			}else if(isset($_POST['UvibaToken'])){
				$payment_info['token']=$_POST['UvibaToken'];
			}else{
				//not defined
					throw new UviPay_Exception_Base("UviPay_CodeError",array(
						'message'=>'Please provide token attribute.',
					),"Please provide token attribute.", 0);
					exit;
			}
		}
		 $ch = new Curl();;
		$ch->post('https://api.uviba.com/pay/charge',array(
			'private_key'=>self::$private_key,
			'amount'=>$payment_info['amount'],
			'UvibaToken'=>$payment_info['token'],
			));
 
		try{
 
			$json_data = json_decode($ch->response);
 
		}catch(Exception $e){
			throw new UviPay_Exception_Base("UviPay_CodeError",array(
						'message'=>'Sorry some errors happened.',
					),"Sorry some errors happened.", 0);
					exit;
		}



if(is_null($json_data)){
	throw new UviPay_Exception_Base("UviPay_ResultError",array(
						'message'=>'Sorry some errors happened.',
					),"Sorry some errors happened.", 0);
					exit;
}
if(isset($json_data->error_data,$json_data->error)){


	if($json_data->error===true){
		if(!empty($json_data->error_data)){
			throw new UviPay_Exception_Base("UviPay_ResultError",array(
							'message'=>'Sorry some errors happened.',
							'error'=>$json_data->error_data,
						),"Sorry some errors happened.", 0);
						exit;
		}
	}
	
	
}


return $json_data->success_data;


//End of function
	}

	


	// End of Class
}
 


UviPay::bring_affiliates('cookie'); //should be called and included at every page
