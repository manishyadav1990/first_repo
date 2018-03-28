<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
class SetCookieInResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $resp = $next($request);
        $result = json_decode($resp->getContent());
        $countryfordbselection = $request->headers->get('countrynamefordbselection');
        if($countryfordbselection == "")
        {
            if(!empty($result))
            {
                if($result->collection->data =="Invalid Country Name")
                {
                    $cookie = Cookie::make('countrynamefordbselection',"142",'120',"","","","","false");
                    $response = $next($request)->withCookie($cookie);
                    $response1 = $response->header('countrynamefordbselection',"142");

                }
                else
                {
                    $cookie = Cookie::make('countrynamefordbselection',$result->collection->data[0]->region_code,'120',"","","","","false");
                    $response = $next($request)->withCookie($cookie);
                    $response1 = $response->header('countrynamefordbselection',$result->collection->data[0]->region_code);

                }
            }
            else
            {
                $response = $resp->header('countrynamefordbselection',$countryfordbselection);
                $cookie = Cookie::make('countrynamefordbselection',"142",'120',"","","","","false");
                $response1 = $response->withCookie($cookie);
            }
        }
        else
        {
            $response = $resp->header('countrynamefordbselection',$countryfordbselection);
            $cookie = Cookie::make('countrynamefordbselection',"142",'120',"","","","","false");
            $response1 = $response->withCookie($cookie);

        }
        //dd($response1);
        return $response1;
    }
}
