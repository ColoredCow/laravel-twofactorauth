<?php

namespace ColoredCow\TwoFactorAuth;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use ColoredCow\Twilio\Facades\Sms;

trait TwoFactorAuthentication {

	public function twoFactorAuth(){

		$user = Auth::user();

		if ($user == null) {
			return redirect('/');
		}

		try {
			if(!$user->hasOTP()){
				$message_status = $user->sendOTP();
			}

		} catch (Exception $e) {
			Log::error($e);
			throw $e;
		}

		return view('auth.twofactorauth');
	}

	public function resendOTP(){

		$user = Auth::user();

		if ($user == null) {
			return redirect('/');
		}

		$user->sendOTP();

		return redirect('/auth/twofactorauth');
	}

	public function verifyOTP(Request $request){
		
		$user = Auth::user();

		if ($user == null) {
			return redirect('/');
		}

		if ($user->verifyOTP($request->otp)) {
			session(['two-factor-auth' => true]);
			return redirect('/');
		} else {
			session()->flash('error', 'OTP Verification Failed');
			return redirect('/');
		}

	}

}