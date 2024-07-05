<?php
namespace App\Http\Controllers;

use DB;
use Illuminate\Support\Facades\Hash;
use Session;
use Request;

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
        }
	}
}
