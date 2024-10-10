<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CBHook extends Controller {

	/*
	| --------------------------------------
	| Please note that you should re-login to see the session work
	| --------------------------------------
	|
	*/
	public function afterLogin() {
		$users = DB::table(config('crudbooster.USER_TABLE'))->where("email", request('email'))->first();

        if (Hash::check(request('password'), $users->password)){
            if($users->status == "INACTIVE"){
                Session::flush();
                return redirect()->route('getLogin')->with('message', "Users doesn\'t exists!");
            }
            Session::put('admin_channel_id', $users->channels_id);
            Session::put('admin_store_id', $users->stores_id);
        }

        $today = Carbon::now()->format('Y-m-d H:i:s');
        $lastChangePass = Carbon::parse($users->last_password_updated_at);
        $needsPasswordChange = Hash::check('qwerty', $users->password) || $lastChangePass->diffInMonths($today) >= 3;
        $defaultPass = Hash::check('qwerty', $users->password);

        if($needsPasswordChange){
            Log::debug("message: {$needsPasswordChange}");
            return redirect()->route('show-change-password')->send();
        }
	}
}
