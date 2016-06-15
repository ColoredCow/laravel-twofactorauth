<?php

namespace ColoredCow\TwoFactorAuth;

use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use ColoredCow\Twilio\Facades\Sms;
use ColoredCow\TwoFactorAuth\TwoFactorAuth;

trait TwoFactorAuthenticatable {

	public function sendOTP(){
		
		$to = $this->cellphone;

		if($to == null || $to == ''){
			$this->failedTwoFactorAuthRequest('102', $this->name);
		}

		try {
			$otp = $this->generateOTP();
		} catch (Exception $e) {
			$this->failedTwoFactorAuthRequest('104', $e);
			return;
		}
		
		if($otp == false){
			$this->failedTwoFactorAuthRequest('104', $e);
			return;
		}

		$message = "Your OTP for Ocuhub login is $otp OTP is valid for 5 minutes.";

		try {
			$messageID = Sms::send($to, $message);
		} catch (Exception $e) {
			$this->failedTwoFactorAuthRequest('101', $e);
		}

		return;
	}

	public function generateOTP(){
		return TwoFactorAuth::generate($this->id);
	}

	public function verifyOTP($otp){
		try {
			return TwoFactorAuth::validate($this->id, $otp);
		} catch (Exception $e) {
			Log::error($e);
		}
		
		return false;
	}

	public function hasOTP(){
		return TwoFactorAuth::exists($this->id);
	}

	public function failedTwoFactorAuthRequest($code, $e = null){
		
		if ($e != null) {
			Log::error($e);
		}

	}

}