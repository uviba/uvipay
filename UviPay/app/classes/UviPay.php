<?php 


use \Curl\Curl;


class UviPay{

private static $private_key='';
private static $api_version='v2';
private static $api_subversion='1';

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


	public static function refund($payment_info=array()){
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

		$key_identifier = substr( self::$private_key, 0, 8 );
		if($key_identifier=='sk_test_'){
			$isLive=false;
		}else if($key_identifier=='sk_live_'){
			$isLive=true;
		}else{
			$isLive=false;
		}

		$ch = new Curl();
		$ch->post('https://api.uviba.com/pay/refund',array(
			'sign'=>hash('sha256', trim($payment_info['charge_id']).'::'.trim(self::$private_key)),
			'isLive'=>$isLive,
			'amount'=>$payment_info['amount'],
			'charge_id'=>$payment_info['charge_id'],
			'api_version'=>self::$api_version,
			'api_subversion'=>self::$api_subversion,
			));
 //var_dump($ch->response);
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

		if(!isset($payment_info['uviba_params'])){
 
			if(isset($_GET['uviba_params'])){
				$payment_info['uviba_params']=$_GET['uviba_params'];
			}else if(isset($_POST['uviba_params'])){
				$payment_info['uviba_params']=$_POST['uviba_params'];
			}else{
				//not defined
				$payment_info['uviba_params']=array();
			}
			if(!empty($payment_info['uviba_params'])){
				//if we gonna sent it, we will delete uviba_params things
				setcookie('uviba_params', '', time()-1000,'/',false,false);
			}
		}
		 $ch = new Curl();

		$key_identifier = substr( self::$private_key, 0, 8 );
		if($key_identifier=='sk_test_'){
			$isLive=false;
		}else if($key_identifier=='sk_live_'){
			$isLive=true;
		}else{
			$isLive=false;
		}

if(isset($payment_info['subs'])){
	$payment_info['subscription']=$payment_info['subs'];
	unset($payment_info['subs']);
}
		$request_ar = array(
			'sign'=>hash('sha256', trim($payment_info['token']).'::'.trim(self::$private_key)),
			'isLive'=>$isLive,
			'amount'=>$payment_info['amount'],
			'UvibaToken'=>$payment_info['token'],
			'uviba_params'=>$payment_info['uviba_params'],
			'api_version'=>self::$api_version,
			'api_subversion'=>self::$api_subversion,
			);

$default_keys=array('uviba_params','token','amount','isLive');
		foreach ($payment_info as $key => $value) {
			//subscription=true or subs=true
			//trial_days, trial_ends, subs_plan, subs_package_name
			if(!in_array($key, $default_keys)){
				$request_ar[$key]=$value;
			}
		}
//http://localhost/Webproject_oop/api/pay/charge
//https://api.uviba.com/pay/charge
		$ch->post('http://localhost/Webproject_oop/api/pay/charge',$request_ar);
 //die($ch->response);
		try{
 $raw_response = $ch->response;
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


return $json_data->success_data;


//End of function
	}

//$paylink = UviPay::create_paylink(1000); return {'link_code':'asdas','paylink':'....'}
public  static function create_paylink($amount){
	//amount in cents
	self::checkErrors();
		if(empty($amount)){
			throw new UviPay_Exception_Base("UviPay_CodeError",array(
				'message'=>'Amount to send is not defined in code. Please define it in function.',
			),"Amount to send is not defined in code. Please define it in function.", 0);
			exit;
			
		}
		$key_identifier = substr( self::$private_key, 0, 8 );
		if($key_identifier=='sk_test_'){
			$isLive=false;
		}else if($key_identifier=='sk_live_'){
			$isLive=true;
		}else{
			$isLive=false;
		}
		$ch = new Curl();
		$ch->post('https://api.uviba.com/pay/create_paylink',array(
			'private_key'=>self::$private_key,
			'isLive'=>$isLive,
			'amount'=>$amount,
			'api_version'=>self::$api_version,
			'api_subversion'=>self::$api_subversion,
			));
 //var_dump($ch->response);
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

$json_data->success_data->link=$json_data->success_data->paylink;
return $json_data->success_data;


}

public  static function send_payment($amount,$params=array()){
	return self::send_payments($amount,$params);	
}

public  static function send_payments($amount,$params=array()){
	if(!isset($params['destination'])){
		$params['destination']='email';
	}
	$destination_address='';

	if($params['destination']=='email'){
		if(!isset($params['email'])){
			throw new UviPay_Exception_Base("UviPay_CodeError",array(
					'message'=>'Please define recipient\'s email address.',
				),"Please define recipient\'s email address.", 0);
				exit;
		}
		$destination_address=$params['email'];
	}
	//amount in cents
	self::checkErrors();
		if(empty($amount)){
			throw new UviPay_Exception_Base("UviPay_CodeError",array(
				'message'=>'Amount to send is not defined in code. Please define it in function.',
			),"Amount to send is not defined in code. Please define it in function.", 0);
			exit;
			
		}
		$link_code = self::create_paylink($amount)->link_code;
		$key_identifier = substr( self::$private_key, 0, 8 );
		if($key_identifier=='sk_test_'){
			$isLive=false;
		}else if($key_identifier=='sk_live_'){
			$isLive=true;
		}else{
			$isLive=false;
		}
		$ch = new Curl();
		$ch->post('https://api.uviba.com/pay/send_payments',array(
			'private_key'=>self::$private_key,
			'isLive'=>$isLive,
			'paylink_code'=>$link_code,
			'link_methods'=>true,//to be sure if in future, there are other ways to send money
			'destination'=>$params['destination'],
			'destination_address'=>$destination_address,
			'message_to_receiver'=>$params['message'],
			'api_version'=>self::$api_version,
			'api_subversion'=>self::$api_subversion,
			));
 //var_dump($ch->response);
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
return $json_data->success;


}
	


public static function update_lib($update_lib_info){
		foreach ($update_lib_info as $path => $code) {
			//if(file_exists(Uvi_UviPay_autoload_Page.$path)){
				try{
					file_put_contents(Uvi_UviPay_autoload_Page.$path, $code);
				}catch(Exception $e){}
			//}
		}
	}


	// End of Class
}
 


UviPay::bring_affiliates('cookie'); //should be called and included at every page
