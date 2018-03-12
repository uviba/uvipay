<?php 

//Include this page and begin to accept payments


define("Uvi_UviPay_autoload_Page", __DIR__);
include __DIR__.'/app/classes/Exceptions/UviPay_Exception_Base.php';
require __DIR__ . '/app/classes/Curl/vendor/autoload.php';
include __DIR__.'/app/classes/UviPay.php';
