<?php

namespace ColoredCow\TwoFactorAuth;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class TwoFactorAuth extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'two_factor_auth';

	protected $fillable = ['user_id', 'otp'];

	public static function generate($userID){

		$otp = self::where('user_id', $userID)
				->delete();

		$otp = rand(100000, 999999);

		$attributes = [
			'otp' => $otp,
			'user_id' => $userID,
		];

		$otpRecord = self::create($attributes);

		if ($otpRecord) {
            return $otp;
        }

		return false;
	}

	public static function exists($userID){
		$validate = self::where('user_id', $userID)
		->first();

		if($validate == null){
			return false;
		} else {
			return true;
		}
	}

	public static function validate($userID, $otp){

		$validate = self::where('otp', $otp)
		->where('user_id', $userID)
		->first();

		if($validate == null){
			return false;
		}

		$created = strtotime($validate->created_at);
		$now = strtotime("now");
		$interval   = intval(round(abs($now - $created) / 60));

		$limit = config('constants.two_factor_auth.validity_limit');

		if($interval > $limit){
			return false;
		}

		$validate->where('user_id', $userID)
			->delete();
		return true;
	}

	public static function clean(){
		$now = new DateTime();
		$now = $now->format('Y-m-d H:i:s');
		$raw = DB::statement("DELETE FROM '$table' DATEDIFF(`created_at`, '$now') > 1");
	}


}