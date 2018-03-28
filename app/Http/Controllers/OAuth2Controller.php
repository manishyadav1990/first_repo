<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Input;
use Authorizer;
use Validator;
use Config;
use League\OAuth2\Server\Exception\InvalidCredentialsException;
use League\OAuth2\Server\Exception\InvalidRefreshException;
use App\User;
use DB;
use Illuminate\Support\Facades\Session;

class OAuth2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accessToken(Request $request)
    {
        Input::merge([
            'client_id' => env('OAUTH2_CLIENT_ID'),
            'client_secret' => env('OAUTH2_CLIENT_SECRET'),
        ]);

        try {
            $accessToken = Authorizer::issueAccessToken();

            $request->headers->set('Authorization','Bearer '.$accessToken['access_token']);
            Authorizer::validateAccessToken();

            $userId = Authorizer::getResourceOwnerId();
            $userType=User::find($userId)->type;
            //$userType=User::find($userId);
//            if ($userType == "administrator"){
//                $id=Db::table('administrator')->where('login_id',$userId)->pluck('id');
//            }
            $date = new \DateTime('now', new \DateTimeZone('Asia/Kolkata'));
            $user=User::find($userId);
            $userType = DB::table('userrole')
                        ->select('userrole.role_name')
                ->join('system_users_role', 'system_users_role.role_id', '=', 'userrole.id')
                ->Where('system_users_role.login_id', '=', $userId)
                ->get();
           // $user->last_seen_at=$date->format('Y-m-d H:i:s');
            //$user->save();
            //$country = Input::get('country');
            //session_start();
            //$_SESSION['country'] = $country;
            //Session::set('country',$country);

            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => [
                        'token' => $accessToken,
                        'userType'=>$userType[0]->role_name,
                        'userId'=>$userId,
                        'name'=>$user->name,
                        'code' => 200,
                    ]
                ]
            ], 200);
        }
        catch(InvalidCredentialsException $e)
        {
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'error' => [
                        'title' => 'Invalid credentials.',
                        'code' => 401,
                        'message' => 'Invalid credentials.',
                    ]
                ]
            ], 401);
        }
    }


    public function logout(Request $request){

        return $request->url();


    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
