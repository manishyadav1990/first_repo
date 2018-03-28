<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Request;

class SubdomainSetup
{

    public function handle($request, Closure $next)
    {
        $test = $request->hasCookie('countrynamefordbselection ');
        $value = $request->cookie('countrynamefordbselection ');
        $countryfordbselection = $request->headers->get('countrynamefordbselection');


        if($countryfordbselection == '142' || $countryfordbselection == '')
            DB::setDefaultConnection('db_in');
        if($countryfordbselection == '002'||$countryfordbselection == '150' )
            DB::setDefaultConnection('db_mx');
        if($countryfordbselection == '009'||$countryfordbselection == '019')
            DB::setDefaultConnection('db_us');
        return $next($request);
    }
}
