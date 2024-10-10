<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Session;

class Helper {
    public static function myStore() {
        return Session::get('admin_store_id');
    }

    public static function myChannel() {
        return Session::get('admin_channel_id');
    }
}
