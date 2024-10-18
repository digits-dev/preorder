<?php

namespace App\Http\Middleware;

use App\Models\OrderSchedule;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class CheckOrderSchedule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $currentTime = Carbon::now();

        $activeSchedule = OrderSchedule::where('status','ACTIVE')->orderBy('start_date','asc')->first();

        if ($currentTime->lt(Carbon::parse($activeSchedule->start_date)) || $currentTime->gt(Carbon::parse($activeSchedule->end_date))) {
            return redirect()->route('show-order-restriction')->send();
        }

        return $next($request);
    }
}
