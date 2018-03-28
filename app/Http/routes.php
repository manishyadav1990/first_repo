<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
   dd("nymdx_admin");
});
Route::group(array('prefix' => 'api/v1'), function() {

    Route::post('oauth/access_token', 'OAuth2Controller@accessToken');
    Route::post('change_system_user_password','AdminConsole\AdminConsoleController@change_system_user_password');


});

Route::group(array('prefix' => 'api/v1','middleware' => ['oauth']), function() {
    //admin console
     Route::get('getcountrylist','AdminConsole\AdminConsoleController@getcountrylist');
     Route::get('get_regionwise_countrylist','AdminConsole\AdminConsoleController@get_regionwise_countrylist');
     Route::post('setcountryname','AdminConsole\AdminConsoleController@setcountrylist');
     Route::get('users/{userid}/notifications','AdminConsole\AdminConsoleController@usernotification');
     Route::get('welldata/reports','AdminConsole\AdminConsoleController@welldatareport');
     Route::get('get/color/code/data/{input}','AdminConsoleController@colorcodedata');
     Route::get('admin/get_userdetails','AdminConsole\AdminConsoleController@get_userdetails');
});
    //admin console
    Route::group(array('prefix' => 'api/v1','middleware' => ['oauth','subdomain','setcookie']), function() {
    Route::resource('admin/transaction','AdminConsole\AdminConsoleController',['except' => ['create', 'edit']]);
    Route::post('admin/transaction/update/{id}','AdminConsole\AdminConsoleController@update');
    Route::post('admin/getuserdetails','AdminConsole\AdminConsoleController@getuserdetails');
    Route::post('admin/getusercount','AdminConsole\AdminConsoleController@getuser_count');
    Route::get('admin/gettransactiondetails_byid/{userid}/{usertype}/{duration}/{month}','AdminConsole\AdminConsoleController@gettransactiondetails_byid');
    Route::get('get_countrylist','AdminConsole\AdminConsoleController@getcountrylist');
    Route::post('admin/change_user_status','AdminConsole\AdminConsoleController@change_user_status');
    Route::post('admin/change_user_password/{id}','AdminConsole\AdminConsoleController@change_user_password');

    Route::post('admin/update/transaction/log/status','AdminConsole\AdminConsoleController@update_transaction_log_status');
    Route::delete('user/delete/{user_id}/{userType}','AdminConsole\AdminConsoleController@user_delete');
    Route::put('change/user/type/{doctor_id}','AdminConsole\AdminConsoleController@change_user_type');
    Route::delete('clinic/delete/{organization_id}','AdminConsole\AdminConsoleController@organization_delete');
    Route::get('specific/organization/get/all/doctor/{organization_id}','AdminConsole\AdminConsoleController@get_all_doctor');


});




