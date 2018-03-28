<?php
namespace App\Http\Controllers\AdminConsole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Input;
use App\Models\Transaction\TransactionCharges;
use Illuminate\Support\Facades\Mail;
use App\Models\Transaction\TransactionDetails;

/**
 * Created by PhpStorm.
 * User: Deepika
 * Date: 3/29/2017
 * Time: 5:03 PM
 */

class AdminConsoleController extends Controller
{
    public function index(Request $request)
    {
        $countryfordbselection = $request->headers->get('countrynamefordbselection');
        if($countryfordbselection == "all")
        {
            $result = array();
            DB::setDefaultConnection('db_in');
            $result_in = DB::table('transaction_charges')
                ->select('transaction_charges.id',
                    'transaction_charges.transactionsourceid',
                    'transaction_source.transactionfor',
                    'transaction_charges.name',
                    'transaction_charges.description',
                    'transaction_charges.transactionno_rangefrom',
                    'transaction_charges.transactionno_rangeto',
                    'transaction_charges.min_monthly_amt',
                    'transaction_charges.transactioncharge',
                    'transaction_charges.country',
                    'transaction_charges.country_code',
                    'transaction_charges.currency',
                    'countries.region_code',
                    'transaction_charges.created_at', 'transaction_charges.updated_at')
                ->join('transaction_source', 'transaction_source.id', '=', 'transaction_charges.transactionsourceid')
                ->join('countries', 'countries.name', '=', 'transaction_charges.country')
                ->orderby('transaction_charges.country', 'asc')
                ->get();
            $count = count($result_in);
            for($i=0;$i<$count;$i++)
                    $result_in[$i]->region_code = '142';

            $result = array_merge($result,$result_in);
            DB::setDefaultConnection('db_us');
            $result_us = DB::table('transaction_charges')
                ->select('transaction_charges.id',
                    'transaction_charges.transactionsourceid',
                    'transaction_source.transactionfor',
                    'transaction_charges.name',
                    'transaction_charges.description',
                    'transaction_charges.transactionno_rangefrom',
                    'transaction_charges.transactionno_rangeto',
                    'transaction_charges.min_monthly_amt',
                    'transaction_charges.transactioncharge',
                    'transaction_charges.country',
                    'transaction_charges.country_code',
                    'transaction_charges.currency',
                    'countries.region_code',
                    'transaction_charges.created_at', 'transaction_charges.updated_at')
                ->join('transaction_source', 'transaction_source.id', '=', 'transaction_charges.transactionsourceid')
                ->join('countries', 'countries.name', '=', 'transaction_charges.country')
                ->orderby('transaction_charges.country', 'asc')
                ->get();
            $count = count($result_us);
            for($i=0;$i<$count;$i++)
                  $result_us[$i]->region_code = '019';

            $result = array_merge($result,$result_us);
            DB::setDefaultConnection('db_mx');
            $result_mx = DB::table('transaction_charges')
                ->select('transaction_charges.id',
                    'transaction_charges.transactionsourceid',
                    'transaction_charges.name',
                    'transaction_charges.description',
                    'transaction_source.transactionfor',
                    'transaction_charges.transactionno_rangefrom',
                    'transaction_charges.transactionno_rangeto',
                    'transaction_charges.min_monthly_amt',
                    'transaction_charges.transactioncharge',
                    'transaction_charges.country',
                    'transaction_charges.country_code',
                    'transaction_charges.currency',
                    'countries.region_code',
                    'transaction_charges.created_at', 'transaction_charges.updated_at')
                ->join('countries', 'countries.name', '=', 'transaction_charges.country')
                ->join('transaction_source', 'transaction_source.id', '=', 'transaction_charges.transactionsourceid')
                ->orderby('transaction_charges.country', 'asc')
                ->get();
            $count = count($result_mx);
            for($i=0;$i<$count;$i++)
                    $result_mx[$i]->region_code = '150';
            $result = array_merge($result,$result_mx);

        }
        else
        {
            $result = DB::table('transaction_charges')
                ->select('transaction_charges.id',
                    'transaction_charges.transactionsourceid',
                    'transaction_charges.name',
                    'transaction_charges.description',
                    'transaction_source.transactionfor',
                    'transaction_charges.transactionno_rangefrom',
                    'transaction_charges.transactionno_rangeto',
                    'transaction_charges.min_monthly_amt',
                    'transaction_charges.transactioncharge',
                    'transaction_charges.country',
                    'transaction_charges.country_code',
                    'transaction_charges.currency',
                    'transaction_charges.created_at', 'transaction_charges.updated_at')
                ->join('transaction_source', 'transaction_source.id', '=', 'transaction_charges.transactionsourceid')
                ->orderby('transaction_charges.country', 'asc')
                ->get();

        }
        return Response::json([
            'collection' => [
                'version' => '1.0',
                'href' => $request->url(),
                'data' => $result
            ]
        ], 200);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        //dd("in store");
        $rules = array(
            'transactioncharge' => 'required',
            'transactionsource' => 'required',
            'transactionno_rangefrom' => 'required',
            'transactionno_rangeto' => 'required',
            'min_monthly_amt' => 'required',
            'country' => 'required'
        );
        $input = \Input::all();
        $v = Validator::make($input, $rules);
        if ($v->passes()) {
            //id, transactioncharge, transactionsourceid, transactionno_rangefrom, transactionno_rangeto, min_monthly_amt, country
            if(Input::get('transactionno_rangefrom')>=Input::get('transactionno_rangeto'))
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "Range is not defined properly!!"
                    ]
                ], 400);
            else
            {
                $result = DB::table('transaction_charges')
                    ->select('transaction_charges.id',
                        'transaction_charges.transactionsourceid',
                        'transaction_charges.transactionno_rangefrom',
                        'transaction_charges.transactionno_rangeto',
                        'transaction_charges.min_monthly_amt',
                        'transaction_charges.country',
                        'transaction_charges.created_at', 'transaction_charges.updated_at')
                    ->join('transaction_source', 'transaction_source.id', '=', 'transaction_charges.transactionsourceid')
                    ->Where('transaction_source.transactionfor', '=', Input::get('transactionsource'))
                    ->Where('transaction_charges.transactionno_rangefrom', '=', Input::get('transactionno_rangefrom'))
                    ->Where('transaction_charges.transactionno_rangeto', '=', Input::get('transactionno_rangeto'))
                   // ->Where('transaction_charges.country', '=', Input::get('country'))
                    ->get();
                if (!empty($result)) {
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "Transaction already exists! Want to edit details!!"
                        ]
                    ], 400);

                } else {
                    $resultdata = DB::table('transaction_charges')
                        ->select('transaction_charges.id',
                            'transaction_charges.transactionsourceid',
                            'transaction_charges.transactionno_rangefrom',
                            'transaction_charges.transactionno_rangeto',
                            'transaction_charges.min_monthly_amt',
                            'transaction_charges.country',
                            'transaction_charges.created_at', 'transaction_charges.updated_at')
                        ->join('transaction_source', 'transaction_source.id', '=', 'transaction_charges.transactionsourceid')
                        ->Where('transaction_source.transactionfor', '=', Input::get('transactionsource'))
                        //->Where('transaction_charges.transactionno_rangefrom', '=', Input::get('transactionno_rangefrom'))
                        ->Where('transaction_charges.transactionno_rangeto', '>', Input::get('transactionno_rangefrom'))
                        //->Where('transaction_charges.country', '=', Input::get('country'))
                        ->get();
                    if(!empty($resultdata))
                    {
                        return Response::json([
                            'collection' => [
                                'version' => '1.0',
                                'href' => $request->url(),
                                'data' => "Range is not defined properly!!"
                            ]
                        ], 400);


                    }
                    //dd($result);

                    $transactionsource_id = DB::table('transaction_source')
                        ->select('transactionfor', 'id')
                        ->Where('transaction_source.transactionfor', '=', Input::get('transactionsource'))
                        ->get();
                    if (empty($transactionsource_id)) {
                        return Response::json([
                            'collection' => [
                                'version' => '1.0',
                                'href' => $request->url(),
                                'data' => "Invalid User Type"
                            ]
                        ], 400);

                    } else {
                        $country_details = DB::table('countries')
                            ->select('name', 'currency_code', 'iso_3166_2','region_code')
                            ->Where('countries.region_code', '=', Input::get('country'))
                            ->get();
                        //dd($country_details);
                        if (!empty($country_details)) {
                            //id, transactioncharge, transactionsourceid, transactionno_rangefrom,
                            // transactionno_rangeto, min_monthly_amt, country, country_code,
                            // currency, created_at, updated_at

                            $transactiondetails = TransactionCharges::create([
                                'name' => Input::get('name'),
                                'description' =>Input::get('description'),
                                'transactioncharge' => Input::get('transactioncharge'),
                                'transactionsourceid' => $transactionsource_id[0]->id,
                                'transactionno_rangefrom' => Input::get('transactionno_rangefrom'),
                                'transactionno_rangeto' => Input::get('transactionno_rangeto'),
                                'min_monthly_amt' => Input::get('min_monthly_amt'),
                                'country' => $country_details[0]->name,
                                'country_code' => $country_details[0]->region_code,
                                'currency' => $country_details[0]->currency_code,
                                'created_at' => date("Y-m-d H:i:s"),
                            ]);

                            return Response::json([
                                'collection' => [
                                    'version' => '1.0',
                                    'href' => $request->url(),
                                    'data' => 'Transaction details added Successfully',
                                    'transactiondetails' => $transactiondetails,
                                ]
                            ], 200);
                        } else {
                            return Response::json([
                                'collection' => [
                                    'version' => '1.0',
                                    'href' => $request->url(),
                                    'data' => "Invalid Country name"
                                ]
                            ], 400);
                        }
                    }
                }

            }


        } else {
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => $v->messages()->all()
                ]
            ], 400);
        }
    }
    public function show(Request $request, $id)
    {
        $transactiondetails = DB::table('transaction_charges')
            ->select('transaction_charges.id',
                'transaction_charges.transactionsourceid',
                'transaction_charges.name',
                'transaction_charges.description',
                'transaction_source.transactionfor',
                'transaction_charges.transactionno_rangefrom',
                'transaction_charges.transactionno_rangeto',
                'transaction_charges.min_monthly_amt',
                'transaction_charges.country',
                'transaction_charges.country_code',
                'transaction_charges.currency',
                'transaction_charges.created_at', 'transaction_charges.updated_at')
            ->join('transaction_source', 'transaction_source.id', '=', 'transaction_charges.transactionsourceid')
            ->Where('transaction_charges.id', '=', $id)
            ->get();
        if (!empty($transactiondetails)) {
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => $transactiondetails
                ]
            ], 200);
        } else {
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => "No record Exists"
                ]
            ], 400);
        }
    }
    public function edit($id)
    {
        //
    }
    public function update(Request $request, $id)
    {
        //dd($id);
        //id, transactioncharge, transactionsourceid, transactionno_rangefrom, transactionno_rangeto, min_monthly_amt, country
        $input = Input::all();
        //dd(Input::all());
        $result = DB::table('transaction_charges')
            ->select('transaction_charges.id',
                'transaction_charges.transactionsourceid',
                'transaction_charges.transactionno_rangefrom',
                'transaction_charges.transactionno_rangeto',
                'transaction_charges.min_monthly_amt',
                'transaction_charges.country',
                'transaction_charges.created_at', 'transaction_charges.updated_at')
            ->join('transaction_source', 'transaction_source.id', '=', 'transaction_charges.transactionsourceid')
            ->Where('transaction_source.transactionfor', '=', Input::get('transactionsource'))
            ->Where('transaction_charges.transactionno_rangefrom', '=', Input::get('transactionno_rangefrom'))
            ->Where('transaction_charges.transactionno_rangeto', '=', Input::get('transactionno_rangeto'))
            //->Where('transaction_charges.country_code', '=', Input::get('country'))
            ->get();
        //dd($result);
        if (empty($result)) {
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => "Transaction does not exists!!!"
                ]
            ], 400);
        } else {
            //dd($result);
            $transactionsource_id = DB::table('transaction_source')
                ->select('transactionfor', 'id')
                ->Where('transaction_source.transactionfor', '=', Input::get('transactionsource'))
                ->get();
            //dd($transactionsource_id);
            if (empty($transactionsource_id)) {
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "Invalid User Type"
                    ]
                ], 400);

            } else {
//                $country_details = DB::table('countries')
//                    ->select('name', 'currency_code', 'iso_3166_2')
//                    ->Where('countries.name', '=', Input::get('country'))
//                    ->orWhere('countries.iso_3166_3', '=', Input::get('country'))
//                    ->orWhere('countries.iso_3166_2', '=', Input::get('country'))
//                    ->get();
                //dd($country_details);
//                if (!empty($country_details)) {
                    //id, transactioncharge, transactionsourceid, transactionno_rangefrom,
                    // transactionno_rangeto, min_monthly_amt, country, country_code,
                    // currency, created_at, updated_at
                    $transactioncharge = Input::get('transactioncharge');
                    $min_monthly_amt = Input::get('min_monthly_amt');
                    $country =  Input::get('country');
                    $name = Input::get('name');
                    $description = Input::get('description');
                    if(!empty($transactioncharge))
                        DB::table('transaction_charges')->where('id', '=', [$id])->update(array('transactioncharge' => $transactioncharge));
                    if(!empty($min_monthly_amt))
                    {
                        DB::table('transaction_charges')->where('id', '=', [$id])->update(array('min_monthly_amt' => $min_monthly_amt));
                    }
                   if(!empty($name))
                    DB::table('transaction_charges')->where('id', '=', [$id])->update(array('name' => $name));
                    if(!empty($description))
                    {
                    DB::table('transaction_charges')->where('id', '=', [$id])->update(array('description' => $description));
                    }
                    if(!empty($country))
                    {
                        //DB::table('transaction_charges')->where('id', '=', [$id])->update(array('country' => $country_details[0]->name));
                        DB::table('transaction_charges')->where('id', '=', [$id])->update(array('country_code' => $country));
                       // DB::table('transaction_charges')->where('id', '=', [$id])->update(array('currency' => $country_details[0]->currency_code));
                    }
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => 'Transaction details Updated Successfully',

                        ]
                    ], 200);
//                } else {
//                    return Response::json([
//                        'collection' => [
//                            'version' => '1.0',
//                            'href' => $request->url(),
//                            'data' => "Invalid Country name"
//                        ]
//                    ], 400);
//                }
            }
        }
    }
    public function destroy($id)
    {
      //$data = TransactionCharges::destroy($id);
      DB::delete('delete from transaction_charges where id = ?',[$id]);

    }
    public function getuserdetails(Request $request)
    {
                $usertype = Input::get('usertype');

                if(empty($usertype))
                {
                    $result = array();
                    $userdetails_doctor = DB::table('users')
                        ->select('doctors.id', 'doctors.login_id','doctors.organization_id','users.name', 'users.email', 'users.type', 'users.status',
                            'doctors.address1','doctors.city','doctors.state','doctors.country',
                            'doctors.phone_number','users.created_at as RegisteredOn')
                        ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
                        ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
                        ->where('doctors.IsDeleted','=','0')
                        ->where('organization.IsDeleted','=','0')
                        ->where(function($query){
                            return $query
                                ->where('users.type', '=', 'doctor')
                                ->orwhere('users.type', '=', 'admin');
                        })

//                        ->where('doctors.country', '=', $country)
//                        ->orwhere('doctors.country', '=', $country_code1)
//                        ->orwhere('doctors.country', '=', $country_code2)
                        ->get();

                    if (!empty($userdetails_doctor)) {
                        $result = array_merge($result, $userdetails_doctor);
                    }

                    //id, login_id, lab_name, street, city, state, postal_code, contact1, contact2, contact_email, lab_type, staff_count, rank_out_of_10, status, created_at, updated_at
                    $userdetails_lab = DB::table('users')
                        ->select('laboratory.id','laboratory.login_id','lab_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status',
                            'laboratory.street as address1','laboratory.city','laboratory.state','laboratory.state as country',
                            'laboratory.contact1 as phone_number','users.created_at as RegisteredOn')
                        ->leftjoin('laboratory', 'laboratory.login_id', '=', 'users.id')
                        ->leftjoin('lab_organization','lab_organization.laboratory_id','=','laboratory.id')
                        ->where('users.type', '=', 'laboratorist')
                        ->where('laboratory.IsDeleted','=','0')
//                        ->where('laboratory.state', '=', $country)
//                        ->orwhere('laboratory.state', '=', $country_code1)
//                        ->orwhere('laboratory.state', '=', $country_code2)
                        ->get();

                    if (!empty($userdetails_lab)) {
                        $result = array_merge($result, $userdetails_lab);
                    }
                    //id, login_id, name, email, locality, street, city, state, country, postal_code, website, contact1, contact2, services_offered, fax, created_at, updated_at
                    $userdetails_imgcenter = DB::table('users')
                        ->select('imaging_centers.id','imaging_centers.login_id', 'imaging_center_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','imaging_centers.street as address1',
                            'imaging_centers.city','imaging_centers.state','imaging_centers.country','imaging_centers.contact1 as phone_number ','users.created_at as RegisteredOn')
                        ->leftjoin('imaging_centers', 'imaging_centers.login_id', '=', 'users.id')
                        ->leftjoin('imaging_center_organization','imaging_center_organization.imaging_centers_id','=','imaging_centers.id')
                        ->where('users.type', '=', 'imaging_center')
                        ->where('imaging_centers.IsDeleted','=','0')
//                        ->where('imaging_centers.country', '=', $country)
//                        ->orwhere('imaging_centers.country', '=', $country_code1)
//                        ->orwhere('imaging_centers.country', '=', $country_code2)
                        ->get();
                    if (!empty($userdetails_imgcenter)) {
                        $result = array_merge($result, $userdetails_imgcenter);
                    }
                    $userdetails_dentist = DB::table('users')
                        ->select('doctors.id','doctors.login_id','doctors.organization_id','users.name', 'users.email', 'users.type', 'users.status',
                            'doctors.address1','doctors.city','doctors.state','doctors.country',
                            'doctors.phone_number','users.created_at as RegisteredOn')
                        ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
                        ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
                        ->where('users.type', '=', 'dentist')
                        ->where('doctors.IsDeleted','=','0')
                        ->where('organization.IsDeleted','=','0')
//                        ->where('doctors.country', '=', $country)
//                        ->orwhere('doctors.country', '=', $country_code1)
//                        ->orwhere('doctors.country', '=', $country_code2)
                        ->get();
                    if (!empty($userdetails_dentist)) {
                        $result = array_merge($result, $userdetails_dentist);
                    }
                    //id, login_id, pharmacy_name, display_name, phone_number, fax, address, city, state, country, email, zip_code, 24_hour_service, retail_service, mail_order_service, Status, created_at, updated_at
                    $userdetails_pharmacist = DB::table('users')
                        ->select('pharmacy.id','pharmacy.login_id','pharmacy_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','pharmacy.address as address1',
                            'pharmacy.city','pharmacy.state','pharmacy.country','pharmacy.phone_number as phone_number','users.created_at as RegisteredOn')
                        ->leftjoin('pharmacy', 'pharmacy.login_id', '=', 'users.id')
                        ->leftjoin('pharmacy_organization', 'pharmacy_organization.pharmacy_id', '=', 'pharmacy.id')
                        ->where('users.type', '=', 'pharmacist')
                        ->where('pharmacy.IsDeleted','=','0')
//                        ->where('pharmacy.country', '=', $country)
//                        ->orwhere('pharmacy.country', '=', $country_code1)
//                        ->orwhere('pharmacy.country', '=', $country_code2)
                        ->get();
                    if (!empty($userdetails_pharmacist)) {
                        $result = array_merge($result, $userdetails_pharmacist);
                    }
                    $userdetails_patients = DB::table('users')
                        ->select('patients.id','patients.login_id','organization_patient.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','patients.street as address1',
                            'patients.city','patients.state','patients.country','patients.phone_cell as phone_number','users.created_at as RegisteredOn')
                        ->leftjoin('patients', 'patients.login_id', '=', 'users.id')
                        ->leftjoin('organization_patient', 'organization_patient.patient_id', '=', 'patients.id')
                        ->where('users.type', '=', 'patient')
                        ->where('patients.IsDeleted','=','0')
//                        ->where('patients.country', '=', $country)
//                        ->orwhere('patients.country', '=', $country_code1)
//                        ->orwhere('patients.country', '=', $country_code2)
                        ->get();
                    if (!empty($userdetails_patients)) {
                        $result = array_merge($result, $userdetails_patients);
                    }
                }
                else
                {
//                    $country = $result[0]->name;
//                    $country_code1 = $result[0]->iso_3166_2;
//                    $country_code2 = $result[0]->iso_3166_3;
                    $count = count($usertype);
                    //dd($count);
                    $result = array();
                    for ($i = 0; $i < $count; $i++) {
                        dd($usertype[$i]);
                       if ($usertype[$i] == 'doctor') {
                            $userdetails_doctor = DB::table('users')
                                ->select('doctors.id','doctors.login_id','doctors.organization_id','users.name', 'users.email', 'users.type', 'users.status',
                                    'doctors.address1','doctors.city','doctors.state','doctors.country',
                                    'doctors.phone_number','users.created_at as RegisteredOn')
                                ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
                                ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
                                ->where('doctors.IsDeleted','=','0')
                                ->where('users.type', '=',$usertype[$i])
                                ->orwhere('users.type', '=', 'admin')
                                ->where('organization.IsDeleted','=','0')
//                                ->where('doctors.country', '=', $country)
//                                ->orwhere('doctors.country', '=', $country_code1)
//                                ->orwhere('doctors.country', '=', $country_code2)
                                ->get();

                            if (!empty($userdetails_doctor)) {
                                $result = array_merge($result, $userdetails_doctor);
                            }


                        }
                        if ($usertype[$i] == 'laboratorist') {
                            $userdetails_lab = DB::table('users')
                                ->select('laboratory.id','laboratory.login_id','lab_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status',
                                    'laboratory.street as address1','laboratory.city','laboratory.state','laboratory.state as country',
                                    'laboratory.contact1 as phone_number','users.created_at as RegisteredOn')
                                ->leftjoin('laboratory', 'laboratory.login_id', '=', 'users.id')
                                ->leftjoin('lab_organization','lab_organization.laboratory_id','=','laboratory.id')
                                ->where('users.type', '=', $usertype[$i])
                                ->where('laboratory.IsDeleted','=','0')
//                                ->where('laboratory.state', '=', $country)
//                                ->orwhere('laboratory.state', '=', $country_code1)
//                                ->orwhere('laboratory.state', '=', $country_code2)
                                ->get();

                            if (!empty($userdetails_lab)) {
                                $result = array_merge($result, $userdetails_lab);
                            }
                        }
                        if ($usertype[$i] == 'imaging_center') {
                            $userdetails_imgcenter = DB::table('users')
                                ->select('imaging_centers.id','imaging_centers.login_id','imaging_center_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','imaging_centers.street as address1',
                                    'imaging_centers.city','imaging_centers.state','imaging_centers.country','imaging_centers.contact1 as phone_number','users.created_at as RegisteredOn')
                                ->leftjoin('imaging_centers', 'imaging_centers.login_id', '=', 'users.id')
                                ->leftjoin('imaging_center_organization','imaging_center_organization.imaging_centers_id','=','imaging_centers.id')
                                ->where('users.type', '=', $usertype[$i])
                                ->where('imaging_centers.IsDeleted','=','0')
//                                ->where('imaging_centers.country', '=', $country)
//                                ->orwhere('imaging_centers.country', '=', $country_code1)
//                                ->orwhere('imaging_centers.country', '=', $country_code2)
                                ->get();
                            if (!empty($userdetails_imgcenter)) {
                                $result = array_merge($result, $userdetails_imgcenter);
                            }

                        }
                        if ($usertype[$i] == 'dentist') {
                            $userdetails_dentist = DB::table('users')
                                ->select('doctors.id','doctors.login_id','doctors.organization_id','users.name', 'users.email', 'users.type', 'users.status',
                                    'doctors.address1','doctors.city','doctors.state','doctors.country',
                                    'doctors.phone_number','users.created_at as RegisteredOn')
                                ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
                                ->where('users.type', '=', $usertype[$i])
                                ->where('doctors.IsDeleted','=','0')
//                                ->where('doctors.country', '=', $country)
//                                ->orwhere('doctors.country', '=', $country_code1)
//                                ->orwhere('doctors.country', '=', $country_code2)
                                ->get();
                            if (!empty($userdetails_dentist)) {
                                $result = array_merge($result, $userdetails_dentist);
                            }

                        }
                        if ($usertype[$i] == 'pharmacist') {
                            $userdetails_pharmacist = DB::table('users')
                                ->select('pharmacy.id','pharmacy.login_id','pharmacy_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','pharmacy.address as address1',
                                    'pharmacy.city','pharmacy.state','pharmacy.country','pharmacy.phone_number as phone_number','users.created_at as RegisteredOn')
                                ->leftjoin('pharmacy', 'pharmacy.login_id', '=', 'users.id')
                                ->leftjoin('pharmacy_organization', 'pharmacy_organization.pharmacy_id', '=', 'pharmacy.id')
                                ->where('users.type', '=', $usertype[$i])
                                ->where('pharmacy.IsDeleted','=','0')
//                                ->where('pharmacy.country', '=', $country)
//                                ->orwhere('pharmacy.country', '=', $country_code1)
//                                ->orwhere('pharmacy.country', '=', $country_code2)
                                ->get();
                            if (!empty($userdetails_pharmacist)) {
                                $result = array_merge($result, $userdetails_pharmacist);
                            }

                        }
                        if ($usertype[$i] == 'patient') {
                            $userdetails_patients = DB::table('users')
                                ->select('patients.id','patients.login_id','organization_patient.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','patients.street as address1',
                                    'patients.city','patients.state','patients.country','patients.phone_cell as phone_number','users.created_at as RegisteredOn')
                                ->leftjoin('patients', 'patients.login_id', '=', 'users.id')
                                ->leftjoin('organization_patient', 'organization_patient.patient_id', '=', 'patients.id')
                                ->where('users.type', '=', 'patient')
                                ->where('patients.IsDeleted','=','0')
//                                ->where('patients.country', '=', $country)
//                                ->orwhere('patients.country', '=', $country_code1)
//                                ->orwhere('patients.country', '=', $country_code2)
                                ->get();
                            if (!empty($userdetails_patients)) {
                                $result = array_merge($result, $userdetails_patients);
                            }

                        }
                    }


               }

                if(empty($userdetails_doctor))
                    $doctor_count = 0;
                else
                    $doctor_count = count($userdetails_doctor);
                if(empty($userdetails_lab))
                    $lab_count = 0;
                else
                    $lab_count = count($userdetails_lab);
                if(empty($userdetails_imgcenter))
                    $img_center_count = 0;
                else
                    $img_center_count = count($userdetails_imgcenter);
                if(empty($userdetails_pharmacist))
                    $pharmacy_count = 0;
                else
                    $pharmacy_count = count($userdetails_pharmacist);
                if(empty($userdetails_dentist))
                    $dentist_count = 0;
                else
                    $dentist_count = count($userdetails_dentist);
                if(empty($userdetails_patients))
                    $patient_count = 0;
                else
                    $patient_count = count($userdetails_patients);

                $result['count']['doctors_count'] = $doctor_count;
                $result['count']['lab_count'] = $lab_count;
                $result['count']['pharmacy_count'] = $pharmacy_count;
                $result['count']['img_center_count'] = $img_center_count;
                $result['count']['dentist_count'] = $dentist_count;
                $result['count']['patient_count'] = $patient_count;
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => $result
                    ]
                ], 200);
            }
    public function get_userdetails(Request $request)
    {
        $result = array();
        DB::setDefaultConnection('db_in');
        $userdetails_doctor_in = DB::table('users')
            ->select('doctors.id', 'doctors.login_id', 'doctors.organization_id', 'users.name', 'users.email', 'users.type', 'users.status',
                'doctors.address1', 'doctors.city', 'doctors.state', 'doctors.country',
                'doctors.phone_number', 'users.created_at as RegisteredOn')
            ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
            ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
            ->where('doctors.IsDeleted', '=', '0')
            ->where('organization.IsDeleted', '=', '0')
            ->where(function ($query) {
                return $query
                    ->where('users.type', '=', 'doctor')
                    ->orwhere('users.type', '=', 'admin');
            })
            ->get();

        if (!empty($userdetails_doctor_in)) {
            $count = count($userdetails_doctor_in);
            for ($i = 0; $i < $count; $i++){
                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('doctors','organization.id','=','doctors.organization_id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_doctor_in[$i]->organization_id)
                    ->where('source_id','=',$userdetails_doctor_in[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_doctor_in[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_doctor_in[$i]->ispaid = null;
                }

            }

        }

        if (!empty($userdetails_doctor_in)) {
            $result = array_merge($result, $userdetails_doctor_in);
        }
            //id, login_id, lab_name, street, city, state, postal_code, contact1, contact2, contact_email, lab_type, staff_count, rank_out_of_10, status, created_at, updated_at
            $userdetails_lab_in = DB::table('users')
                ->select('laboratory.id','laboratory.login_id','lab_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status',
                    'laboratory.street as address1','laboratory.city','laboratory.state','laboratory.state as country',
                    'laboratory.contact1 as phone_number','users.created_at as RegisteredOn')
                ->leftjoin('laboratory', 'laboratory.login_id', '=', 'users.id')
                ->leftjoin('lab_organization','lab_organization.laboratory_id','=','laboratory.id')
                ->where('users.type', '=', 'laboratorist')
                ->where('laboratory.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_lab_in)) {
            $count = count($userdetails_lab_in);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('lab_organization','lab_organization.organization_id','=','organization.id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_lab_in[$i]->organization_id)
                    ->where('source_id','=',$userdetails_lab_in[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_lab_in[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_lab_in[$i]->ispaid = null;
                }


            }

        }

            if (!empty($userdetails_lab_in)) {
                $result = array_merge($result, $userdetails_lab_in);
            }
            //id, login_id, name, email, locality, street, city, state, country, postal_code, website, contact1, contact2, services_offered, fax, created_at, updated_at
            $userdetails_imgcenter_in = DB::table('users')
                ->select('imaging_centers.id','imaging_centers.login_id','imaging_center_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','imaging_centers.street as address1',
                    'imaging_centers.city','imaging_centers.state','imaging_centers.country','imaging_centers.contact1 as phone_number ','users.created_at as RegisteredOn')
                ->leftjoin('imaging_centers', 'imaging_centers.login_id', '=', 'users.id')
                ->leftjoin('imaging_center_organization','imaging_center_organization.imaging_centers_id','=','imaging_centers.id')
                ->where('users.type', '=', 'imaging_center')
                ->where('imaging_centers.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_imgcenter_in)) {
            $count = count($userdetails_imgcenter_in);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('imaging_center_organization','imaging_center_organization.organization_id','=','imaging_center_organization.organization_id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_imgcenter_in[$i]->organization_id)
                    ->where('source_id','=',$userdetails_imgcenter_in[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_imgcenter_in[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_imgcenter_in[$i]->ispaid = null;
                }

            }

        }

            if (!empty($userdetails_imgcenter_in)) {
                $result = array_merge($result, $userdetails_imgcenter_in);
            }
            $userdetails_dentist_in = DB::table('users')
                ->select('doctors.id','doctors.login_id','doctors.organization_id', 'users.name', 'users.email', 'users.type', 'users.status',
                    'doctors.address1','doctors.city','doctors.state','doctors.country',
                    'doctors.phone_number','users.created_at as RegisteredOn')
                ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
                ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
                ->where('users.type', '=', 'dentist')
                ->where('organization.IsDeleted','=','0')
                ->where('doctors.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_dentist_in)) {
            $count = count($userdetails_dentist_in);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('doctors','organization.id','=','doctors.organization_id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_dentist_in[$i]->organization_id)
                    ->where('source_id','=',$userdetails_dentist_in[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_dentist_in[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_dentist_in[$i]->ispaid = null;
                }

            }

        }

            if (!empty($userdetails_dentist_in)) {
                $result = array_merge($result, $userdetails_dentist_in);
            }
            //id, login_id, pharmacy_name, display_name, phone_number, fax, address, city, state, country, email, zip_code, 24_hour_service, retail_service, mail_order_service, Status, created_at, updated_at
            $userdetails_pharmacist_in = DB::table('users')
                ->select('pharmacy.id','pharmacy.login_id','pharmacy_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','pharmacy.address as address1',
                    'pharmacy.city','pharmacy.state','pharmacy.country','pharmacy.phone_number as phone_number','users.created_at as RegisteredOn')
                ->leftjoin('pharmacy', 'pharmacy.login_id', '=', 'users.id')
                ->leftjoin('pharmacy_organization', 'pharmacy_organization.pharmacy_id', '=', 'pharmacy.id')
                ->where('users.type', '=', 'pharmacist')
                ->where('pharmacy.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_pharmacist_in)) {
            $count = count($userdetails_pharmacist_in);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('pharmacy_organization', 'pharmacy_organization.organization_id', '=', 'organization.id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_pharmacist_in[$i]->organization_id)
                    ->where('source_id','=',$userdetails_pharmacist_in[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_pharmacist_in[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_pharmacist_in[$i]->ispaid = null;
                }

            }

        }

            if (!empty($userdetails_pharmacist_in)) {
                $result = array_merge($result, $userdetails_pharmacist_in);
            }
            $userdetails_patients_in = DB::table('users')
                ->select('patients.id','patients.login_id','organization_patient.organization_id as organization_id' ,'users.name', 'users.email', 'users.type', 'users.status','patients.street as address1',
                    'patients.city','patients.state','patients.country','patients.phone_cell as phone_number','users.created_at as RegisteredOn')
                ->leftjoin('patients', 'patients.login_id', '=', 'users.id')
                ->leftjoin('organization_patient', 'organization_patient.patient_id', '=', 'patients.id')
                ->where('users.type', '=', 'patient')
                ->where('patients.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_patients_in)) {
            $count = count($userdetails_patients_in);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('organization_patient', 'organization_patient.organization_id', '=', 'organization.id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_patients_in[$i]->organization_id)
                    ->where('source_id','=',$userdetails_patients_in[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_patients_in[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_patients_in[$i]->ispaid = null;
                }

            }

        }

            if (!empty($userdetails_patients_in)) {
                $result = array_merge($result, $userdetails_patients_in);
            }
            $result_count = count($result);
            for($i=0;$i<$result_count;$i++)
            {
                $result[$i]->region_code ='142';
            }
            DB::setDefaultConnection('db_us');
            $userdetails_doctor_us = DB::table('users')
                ->select('doctors.id','doctors.login_id','doctors.organization_id', 'users.name', 'users.email', 'users.type', 'users.status',
                    'doctors.address1','doctors.city','doctors.state','doctors.country',
                    'doctors.phone_number','users.created_at as RegisteredOn')
                ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
                ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
                ->where('doctors.IsDeleted','=','0')
                ->where('organization.IsDeleted','=','0')
                ->where(function($query){
                    return $query
                        ->where('users.type', '=', 'doctor')
                        ->orwhere('users.type', '=', 'admin');
                })
                ->get();

        if (!empty($userdetails_doctor_us)) {
            $count = count($userdetails_doctor_us);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('doctors','organization.id','=','doctors.organization_id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_doctor_us[$i]->organization_id)
                    ->where('source_id','=',$userdetails_doctor_us[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_doctor_us[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_doctor_us[$i]->ispaid = null;
                }

            }

        }

        if (!empty($userdetails_doctor_us)) {
            $result_count = count($userdetails_doctor_us);

            for($i=0;$i<$result_count;$i++)
            {
                $userdetails_doctor_us[$i]->region_code ='019';
            }
            $result = array_merge($result, $userdetails_doctor_us);
        }

            //id, login_id, lab_name, street, city, state, postal_code, contact1, contact2, contact_email, lab_type, staff_count, rank_out_of_10, status, created_at, updated_at
            $userdetails_lab_us = DB::table('users')
                ->select('laboratory.id','laboratory.login_id','lab_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status',
                    'laboratory.street as address1','laboratory.city','laboratory.state','laboratory.state as country',
                    'laboratory.contact1 as phone_number','users.created_at as RegisteredOn')
                ->leftjoin('laboratory', 'laboratory.login_id', '=', 'users.id')
                ->leftjoin('lab_organization','lab_organization.laboratory_id','=','laboratory.id')
                ->where('users.type', '=', 'laboratorist')
                ->where('laboratory.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_lab_us)) {
            $count = count($userdetails_lab_us);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('lab_organization','lab_organization.organization_id','=','organization.id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_lab_us[$i]->organization_id)
                    ->where('source_id','=',$userdetails_lab_us[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_lab_us[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_lab_us[$i]->ispaid = null;
                }


            }

        }

            if (!empty($userdetails_lab_us)) {
                $result_count = count($userdetails_lab_us);
                for($i=0;$i<$result_count;$i++)
                {
                    $userdetails_lab_us[$i]->region_code ='019';
                }
                $result = array_merge($result, $userdetails_lab_us);
            }
            //id, login_id, name, email, locality, street, city, state, country, postal_code, website, contact1, contact2, services_offered, fax, created_at, updated_at
            $userdetails_imgcenter_us = DB::table('users')
                ->select('imaging_centers.id','imaging_centers.login_id','imaging_center_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','imaging_centers.street as address1',
                    'imaging_centers.city','imaging_centers.state','imaging_centers.country','imaging_centers.contact1 as phone_number','users.created_at as RegisteredOn')
                ->leftjoin('imaging_centers', 'imaging_centers.login_id', '=', 'users.id')
                ->leftjoin('imaging_center_organization','imaging_center_organization.imaging_centers_id','=','imaging_centers.id')
                ->where('users.type', '=', 'imaging_center')
                ->where('imaging_centers.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_imgcenter_us)) {
            $count = count($userdetails_imgcenter_us);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('imaging_center_organization','imaging_center_organization.organization_id','=','imaging_center_organization.organization_id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_imgcenter_us[$i]->organization_id)
                    ->where('source_id','=',$userdetails_imgcenter_us[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_imgcenter_us[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_imgcenter_us[$i]->ispaid = null;
                }

            }

        }
            if (!empty($userdetails_imgcenter_us)) {
                $result_count = count($userdetails_imgcenter_us);
                for($i=0;$i<$result_count;$i++)
                {
                    $userdetails_imgcenter_us[$i]->region_code ='019';
                }

                $result = array_merge($result, $userdetails_imgcenter_us);
            }
            $userdetails_dentist_us = DB::table('users')
                ->select('doctors.id','doctors.login_id','doctors.organization_id','users.name', 'users.email', 'users.type', 'users.status',
                    'doctors.address1','doctors.city','doctors.state','doctors.country',
                    'doctors.phone_number','users.created_at as RegisteredOn')
                ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
                ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
                ->where('users.type', '=', 'dentist')
                ->where('doctors.IsDeleted','=','0')
                ->where('organization.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_dentist_us)) {
            $count = count($userdetails_dentist_us);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('doctors','organization.id','=','doctors.organization_id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_dentist_us[$i]->organization_id)
                    ->where('source_id','=',$userdetails_dentist_us[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_dentist_us[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_dentist_us[$i]->ispaid = null;
                }

            }

        }

            if (!empty($userdetails_dentist_us)) {
                $result_count = count($userdetails_dentist_us);
                for($i=0;$i<$result_count;$i++)
                {
                    $userdetails_dentist_us[$i]->region_code ='019';
                }
                $result = array_merge($result, $userdetails_dentist_us);
            }
            //id, login_id, pharmacy_name, display_name, phone_number, fax, address, city, state, country, email, zip_code, 24_hour_service, retail_service, mail_order_service, Status, created_at, updated_at
            $userdetails_pharmacist_us = DB::table('users')
                ->select('pharmacy.id','pharmacy.login_id','pharmacy_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','pharmacy.address as address1',
                    'pharmacy.city','pharmacy.state','pharmacy.country','pharmacy.phone_number as phone_number','users.created_at as RegisteredOn')
                ->leftjoin('pharmacy', 'pharmacy.login_id', '=', 'users.id')
                ->leftjoin('pharmacy_organization', 'pharmacy_organization.pharmacy_id', '=', 'pharmacy.id')
                ->where('users.type', '=', 'pharmacist')
                ->where('pharmacy.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_pharmacist_us)) {
            $count = count($userdetails_pharmacist_us);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('pharmacy_organization', 'pharmacy_organization.organization_id', '=', 'organization.id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_pharmacist_us[$i]->organization_id)
                    ->where('source_id','=',$userdetails_pharmacist_us[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_pharmacist_us[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_pharmacist_us[$i]->ispaid = null;
                }

            }

        }

            if (!empty($userdetails_pharmacist_us)){
                $result_count = count($userdetails_pharmacist_us);
                for($i=0;$i<$result_count;$i++)
                {
                    $userdetails_pharmacist_us[$i]->region_code ='019';
                }
                $result = array_merge($result, $userdetails_pharmacist_us);
            }
            $userdetails_patients_us = DB::table('users')
                ->select('patients.id','patients.login_id','organization_patient.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','patients.street as address1',
                    'patients.city','patients.state','patients.country','patients.phone_cell as phone_number','users.created_at as RegisteredOn')
                ->leftjoin('patients', 'patients.login_id', '=', 'users.id')
                ->leftjoin('organization_patient', 'organization_patient.patient_id', '=', 'patients.id')
                ->where('users.type', '=', 'patient')
                ->where('patients.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_patients_us)) {
            $count = count($userdetails_patients_us);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('organization_patient', 'organization_patient.organization_id', '=', 'organization.id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_patients_us[$i]->organization_id)
                    ->where('source_id','=',$userdetails_patients_us[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_patients_us[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_patients_us[$i]->ispaid = null;
                }

            }

        }
            if (!empty($userdetails_patients_us)) {
                $result_count = count($userdetails_patients_us);
                for($i=0;$i<$result_count;$i++)
                {
                    $userdetails_patients_us[$i]->region_code ='019';
                }
                $result = array_merge($result, $userdetails_patients_us);
            }
            DB::setDefaultConnection('db_mx');
            $userdetails_doctor_mx = DB::table('users')
                ->select('doctors.id','doctors.login_id','doctors.organization_id','users.name', 'users.email', 'users.type', 'users.status',
                    'doctors.address1','doctors.city','doctors.state','doctors.country',
                    'doctors.phone_number','users.created_at as RegisteredOn')
                ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
                ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
                ->where('doctors.IsDeleted','=','0')
                ->where('organization.IsDeleted','=','0')
                ->where(function($query){
                    return $query
                        ->where('users.type', '=', 'doctor')
                        ->orwhere('users.type', '=', 'admin');
                })
                ->get();

        if (!empty($userdetails_doctor_mx)) {
            $count = count($userdetails_doctor_mx);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('doctors','organization.id','=','doctors.organization_id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_doctor_mx[$i]->organization_id)
                    ->where('source_id','=',$userdetails_doctor_mx[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_doctor_mx[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_doctor_mx[$i]->ispaid = null;
                }

            }

        }

            //dd($userdetails_doctor);
            if (!empty($userdetails_doctor_mx)) {
                $result_count = count($userdetails_doctor_mx);
                //dd($result_count);
                for($i=0;$i<$result_count;$i++)
                {
                    $userdetails_doctor_mx[$i]->region_code ='002';
                }
                $result = array_merge($result, $userdetails_doctor_mx);
            }


            //id, login_id, lab_name, street, city, state, postal_code, contact1, contact2, contact_email, lab_type, staff_count, rank_out_of_10, status, created_at, updated_at
            $userdetails_lab_mx = DB::table('users')
                ->select('laboratory.id','laboratory.login_id','lab_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status',
                    'laboratory.street as address1','laboratory.city','laboratory.state','laboratory.state as country',
                    'laboratory.contact1 as phone_number','users.created_at as RegisteredOn')
                ->leftjoin('laboratory', 'laboratory.login_id', '=', 'users.id')
                ->leftjoin('lab_organization','lab_organization.laboratory_id','=','laboratory.id')
                ->where('users.type', '=', 'laboratorist')
                ->where('laboratory.IsDeleted','=','0')
                ->get();


        if (!empty($userdetails_lab_mx)) {
            $count = count($userdetails_lab_mx);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('lab_organization','lab_organization.organization_id','=','organization.id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_lab_mx[$i]->organization_id)
                    ->where('source_id','=',$userdetails_lab_mx[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_lab_mx[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_lab_mx[$i]->ispaid = null;
                }


            }

        }

            if (!empty($userdetails_lab_mx)) {
                $result_count = count($userdetails_lab_mx);
                for($i=0;$i<$result_count;$i++)
                {
                    $userdetails_lab_mx[$i]->region_code ='002';
                }
                $result = array_merge($result, $userdetails_lab_mx);
            }
            //id, login_id, name, email, locality, street, city, state, country, postal_code, website, contact1, contact2, services_offered, fax, created_at, updated_at
            $userdetails_imgcenter_mx = DB::table('users')
                ->select('imaging_centers.id','imaging_centers.login_id','imaging_center_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','imaging_centers.street as address1',
                    'imaging_centers.city','imaging_centers.state','imaging_centers.country','imaging_centers.contact1 as phone_number','users.created_at as RegisteredOn')
                ->leftjoin('imaging_centers', 'imaging_centers.login_id', '=', 'users.id')
                ->leftjoin('imaging_center_organization','imaging_center_organization.imaging_centers_id','=','imaging_centers.id')
                ->where('users.type', '=', 'imaging_center')
                ->where('imaging_centers.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_imgcenter_mx)) {
            $count = count($userdetails_imgcenter_mx);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('imaging_center_organization','imaging_center_organization.organization_id','=','imaging_center_organization.organization_id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_imgcenter_mx[$i]->organization_id)
                    ->where('source_id','=',$userdetails_imgcenter_mx[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_imgcenter_mx[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_imgcenter_mx[$i]->ispaid = null;
                }

            }

        }

            if (!empty($userdetails_imgcenter_mx)) {
                $result_count = count($userdetails_imgcenter_mx);
                for($i=0;$i<$result_count;$i++)
                {
                    $userdetails_imgcenter_mx[$i]->region_code ='002';
                }
                $result = array_merge($result, $userdetails_imgcenter_mx);
            }
            $userdetails_dentist_mx = DB::table('users')
                ->select('doctors.id','doctors.login_id','doctors.organization_id','users.name', 'users.email', 'users.type', 'users.status',
                    'doctors.address1','doctors.city','doctors.state','doctors.country',
                    'doctors.phone_number','users.created_at as RegisteredOn')
                ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
                ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
                ->where('users.type', '=', 'dentist')
                ->where('doctors.IsDeleted','=','0')
                ->where('organization.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_dentist_mx)) {
            $count = count($userdetails_dentist_mx);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('doctors','organization.id','=','doctors.organization_id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_dentist_mx[$i]->organization_id)
                    ->where('source_id','=',$userdetails_dentist_mx[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_dentist_mx[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_dentist_mx[$i]->ispaid = null;
                }

            }

        }

            if (!empty($userdetails_dentist_mx)) {
                $result_count = count($userdetails_dentist_mx);
                for($i=0;$i<$result_count;$i++)
                {
                    $userdetails_dentist_mx[$i]->region_code ='002';
                }
                $result = array_merge($result, $userdetails_dentist_mx);
            }
            //id, login_id, pharmacy_name, display_name, phone_number, fax, address, city, state, country, email, zip_code, 24_hour_service, retail_service, mail_order_service, Status, created_at, updated_at
            $userdetails_pharmacist_mx = DB::table('users')
                ->select('pharmacy.id','pharmacy.login_id','pharmacy_organization.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','pharmacy.address as address1',
                    'pharmacy.city','pharmacy.state','pharmacy.country','pharmacy.phone_number as phone_number','users.created_at as RegisteredOn')
                ->leftjoin('pharmacy', 'pharmacy.login_id', '=', 'users.id')
                ->leftjoin('pharmacy_organization', 'pharmacy_organization.pharmacy_id', '=', 'pharmacy.id')
                ->where('users.type', '=', 'pharmacist')
                ->where('pharmacy.IsDeleted','=','0')
                ->get();

        if (!empty($userdetails_pharmacist_mx)) {
            $count = count($userdetails_pharmacist_mx);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('pharmacy_organization', 'pharmacy_organization.organization_id', '=', 'organization.id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_pharmacist_mx[$i]->organization_id)
                    ->where('source_id','=',$userdetails_pharmacist_mx[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_pharmacist_mx[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_pharmacist_mx[$i]->ispaid = null;
                }

            }

        }

            if (!empty($userdetails_pharmacist_mx)) {
                $result_count = count($userdetails_pharmacist_mx);
                for($i=0;$i<$result_count;$i++)
                {
                    $userdetails_pharmacist_mx[$i]->region_code ='002';
                }
                $result = array_merge($result, $userdetails_pharmacist_mx);
            }
            $userdetails_patients_mx = DB::table('users')
                ->select('patients.id','patients.login_id','organization_patient.organization_id as organization_id','users.name', 'users.email', 'users.type', 'users.status','patients.street as address1',
                    'patients.city','patients.state','patients.country','patients.phone_cell as phone_number','users.created_at as RegisteredOn')
                ->leftjoin('patients', 'patients.login_id', '=', 'users.id')
                ->leftjoin('organization_patient', 'organization_patient.patient_id', '=', 'patients.id')
                ->where('users.type', '=', 'patient')
                ->where('patients.IsDeleted','=','0')
                ->get();


        if (!empty($userdetails_patients_mx)) {
            $count = count($userdetails_patients_mx);
            for ($i = 0; $i < $count; $i++){

                $organization = DB::table('organization')
                    ->join('transaction_log_monthly','transaction_log_monthly.org_id','=','organization.id')
                    ->join('organization_patient', 'organization_patient.organization_id', '=', 'organization.id')
                    ->where('transaction_log_monthly.transactionmonth','<','MONTH(CURRENT_DATE)')
                    ->where('org_id','=',$userdetails_patients_mx[$i]->organization_id)
                    ->where('source_id','=',$userdetails_patients_mx[$i]->id)
                    ->OrderBy('transaction_log_monthly.created_at','desc')
                    ->select('transaction_log_monthly.ispaid')
                    ->get();

                if(!empty($organization))
                {
                    $IsPaid = $organization[0]->ispaid;
                    $userdetails_patients_mx[$i]->ispaid = $IsPaid;
                }
                else{
                    $userdetails_patients_mx[$i]->ispaid = null;
                }

            }

        }
            if (!empty($userdetails_patients_mx)) {
                $result_count = count($userdetails_patients_mx);
                for($i=0;$i<$result_count;$i++)
                {
                    $userdetails_patients_mx[$i]->region_code ='002';
                }

                $result = array_merge($result, $userdetails_patients_mx);
            }
            if(empty($userdetails_doctor_in) && empty($userdetails_doctor_us) && empty($userdetails_doctor_mx) )
                $doctor_count = 0;
            else
                $doctor_count = count($userdetails_doctor_in)+count($userdetails_doctor_us)+count($userdetails_doctor_mx);

            if(empty($userdetails_lab_in)&& empty($userdetails_lab_us) && empty($userdetails_lab_mx))
               $lab_count = 0;
            else
                $lab_count = count($userdetails_lab_in)+count($userdetails_lab_us)+count($userdetails_lab_mx);

            if(empty($userdetails_imgcenter_in)&& empty($userdetails_imgcenter_us) && empty($userdetails_imgcenter_mx))
                $img_center_count = 0;
            else
                $img_center_count = count($userdetails_imgcenter_in)+count($userdetails_imgcenter_us)+count($userdetails_imgcenter_mx);

            if(empty($userdetails_pharmacist_in)&& empty($userdetails_pharmacist_us) && empty($userdetails_pharmacist_mx))
                $pharmacy_count = 0;
            else
                $pharmacy_count = count($userdetails_pharmacist_in)+count($userdetails_pharmacist_us)+count($userdetails_pharmacist_mx);

            if(empty($userdetails_dentist_in)&& empty($userdetails_dentist_us) && empty($userdetails_dentist_mx))
                $dentist_count = 0;
            else
               $dentist_count = count($userdetails_dentist_in)+count($userdetails_dentist_us)+count($userdetails_dentist_mx);

            if(empty($userdetails_patients_in)&& empty($userdetails_patients_us) && empty($userdetails_patients_mx))
                $patient_count = 0;
           else
                $patient_count = count($userdetails_patients_in)+count($userdetails_patients_us)+count($userdetails_patients_mx);


            $result['count']['doctors_count'] = $doctor_count;
            $result['count']['lab_count'] = $lab_count;
            $result['count']['pharmacy_count'] = $pharmacy_count;
            $result['count']['img_center_count'] = $img_center_count;
            $result['count']['dentist_count'] = $dentist_count;
            $result['count']['patient_count'] = $patient_count;
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => $result
                ]
            ], 200);



    }
    public function getuser_count(Request $request)
    {
                $usertype = Input::get('usertype');
                if(empty($usertype))
                {
                    $result = array();
                    $userdetails_doctor = DB::table('users')
                        ->select('doctors.id', 'users.name', 'users.email', 'users.type', 'users.status',
                            'doctors.address1','doctors.city','doctors.state','doctors.country',
                            'doctors.phone_number')
                        ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
                        ->where('doctors.IsDeleted','=','0')
                        ->where(function($query){
                            return $query
                                ->where('users.type', '=', 'doctor')
                                ->orwhere('users.type', '=', 'admin');
                        })


//                        ->where('doctors.country', '=', $country)
//                        ->orwhere('doctors.country', '=', $country_code1)
//                        ->orwhere('doctors.country', '=', $country_code2)
                        ->get();
                    //dd($userdetails_doctor);
                    if (!empty($userdetails_doctor))
                        $response['doctors_count'] = count($userdetails_doctor);
                    else
                        $response['doctors_count'] = 0;

                    //id, login_id, lab_name, street, city, state, postal_code, contact1, contact2, contact_email, lab_type, staff_count, rank_out_of_10, status, created_at, updated_at
                    $userdetails_lab = DB::table('users')
                        ->select('laboratory.id', 'users.name', 'users.email', 'users.type', 'users.status',
                            'laboratory.street as address1','laboratory.city','laboratory.state','laboratory.state as country',
                            'laboratory.contact1 as phone_number')
                        ->leftjoin('laboratory', 'laboratory.login_id', '=', 'users.id')
                        ->where('laboratory.IsDeleted','=','0')
                        ->where('users.type', '=', 'laboratorist')
//                        ->where('laboratory.state', '=', $country)
//                        ->orwhere('laboratory.state', '=', $country_code1)
//                        ->orwhere('laboratory.state', '=', $country_code2)
                        ->get();


                    if (!empty($userdetails_lab))
                        $response['lab_count'] = count($userdetails_lab);
                    else
                        $response['lab_count'] = 0;

                    $userdetails_imgcenter = DB::table('users')
                        ->select('imaging_centers.id', 'users.name', 'users.email', 'users.type', 'users.status','imaging_centers.street as address1',
                            'imaging_centers.city','imaging_centers.state','imaging_centers.country','imaging_centers.contact1 as phone_number ')
                        ->leftjoin('imaging_centers', 'imaging_centers.login_id', '=', 'users.id')
                        ->where('users.type', '=', 'imaging_center')
                        ->where('imaging_centers.IsDeleted','=','0')
//                        ->where('imaging_centers.country', '=', $country)
//                        ->orwhere('imaging_centers.country', '=', $country_code1)
//                        ->orwhere('imaging_centers.country', '=', $country_code2)
                        ->get();

                   // dd($userdetails_imgcenter);
                    if (!empty($userdetails_imgcenter))
                        $response['imgcenter_count'] =count($userdetails_imgcenter);
                    else
                        $response['imgcenter_count'] = 0;
                    $userdetails_dentist = DB::table('users')
                        ->select('doctors.id', 'users.name', 'users.email', 'users.type', 'users.status',
                            'doctors.address1','doctors.city','doctors.state','doctors.country',
                            'doctors.phone_number')
                        ->leftjoin('doctors', 'doctors.login_id', '=', 'users.id')
                        ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
                        ->where('users.type', '=', 'dentist')
                        ->where('doctors.IsDeleted','=','0')
                        ->where('organization.IsDeleted','=','0')

//                        ->where('doctors.country', '=', $country)
//                        ->orwhere('doctors.country', '=', $country_code1)
//                        ->orwhere('doctors.country', '=', $country_code2)
                        ->get();
                    if (!empty($userdetails_dentist))
                        $response['dentist_count'] = count($userdetails_dentist);
                    else
                        $response['dentist_count']=0;
                    //id, login_id, pharmacy_name, display_name, phone_number, fax, address, city, state, country, email, zip_code, 24_hour_service, retail_service, mail_order_service, Status, created_at, updated_at
                    $userdetails_pharmacist = DB::table('users')
                        ->select('pharmacy.id', 'users.name', 'users.email', 'users.type', 'users.status','pharmacy.address as address1',
                            'pharmacy.city','pharmacy.state','pharmacy.country','pharmacy.phone_number as phone_number')
                        ->leftjoin('pharmacy', 'pharmacy.login_id', '=', 'users.id')
                        ->where('users.type', '=', 'pharmacist')
                        ->where('pharmacy.IsDeleted','=','0')
//                        ->where('pharmacy.country', '=', $country)
//                        ->orwhere('pharmacy.country', '=', $country_code1)
//                        ->orwhere('pharmacy.country', '=', $country_code2)
                        ->get();
                    if (!empty($userdetails_pharmacist))
                        $response['pharmacy_count'] = count($userdetails_pharmacist);
                    else
                        $response['pharmacy_count'] = 0;

                    $userdetails_patients = DB::table('users')
                        ->select('patients.id', 'users.name', 'users.email', 'users.type', 'users.status','patients.street as address1',
                            'patients.city','patients.state','patients.country','patients.phone_cell as phone_number')
                        ->leftjoin('patients', 'patients.login_id', '=', 'users.id')
                        ->where('patients.IsDeleted','=','0')
                        ->where('users.type', '=', 'patient')

//                        ->where('patients.country', '=', $country)
//                        ->orwhere('patients.country', '=', $country_code1)
//                        ->orwhere('patients.country', '=', $country_code2)
                        ->get();

                    if (!empty($userdetails_patients))
                        $response['patient_count'] = count($userdetails_patients);
                    else
                        $response['patient_count'] = 0;
                }

                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => $response
                    ]
                ], 200);
            }
    public function gettransactiondetails_byid(Request $request, $userid, $usertype, $duration,$month)
    {
        if($duration == 'monthly')
        {
            $results =   DB::table('transaction_log_monthly')
                ->select('transaction_log_monthly.id', 'transaction_log_monthly.pertransactioncharge', 'transaction_log_monthly.transactionmonth',
                    'transaction_log_monthly.transactionyear', 'transaction_log_monthly.transactionsourceid', 'transaction_log_monthly.amount',
                    'transaction_log_monthly.source_id', 'transaction_log_monthly.no_of_transaction', 'transaction_log_monthly.ispaid',
                    'transaction_log_monthly.created_at', 'transaction_log_monthly.updated_at')
                ->join('transaction_source', 'transaction_source.id', '=', 'transaction_log_monthly.transactionsourceid')
                ->Where('transaction_source.transactionfor', '=', $usertype)
                ->Where('transaction_log_monthly.source_id', '=', $userid)
                ->get();
        }
        else if($duration == 'daily')
        {
            if($month == 'all')
            {
                $results =  DB::table('transaction_details')
                    ->select('transaction_details.doctor_id','transaction_details.id as transactionid','doctors.first_name',
                        'doctors.last_name','transaction_details.patient_id',
                        'patients.fname','patients.lname','transaction_details.visit_id',
                        'visits.visit_category', 'visits.consultation_brief_description',
                        'transaction_charges.transactioncharge','transaction_charges.min_monthly_amt',
                        'transaction_charges.transactionno_rangeto',
                        'transaction_charges.transactionno_rangefrom',
                        'transaction_details.is_paid',
                        'transaction_details.created_at')
                    ->join('doctors','doctors.id','=','transaction_details.doctor_id')
                    ->join('patients','patients.id','=','transaction_details.patient_id')
                    ->join('visits','visits.id','=','transaction_details.visit_id')
                    ->join('transaction_source','transaction_source.id','=','transaction_details.transactionsourceid')
                    ->join('transaction_charges','transaction_charges.id','=','transaction_details.transactionchargesid')
                    ->Where('transaction_source.transactionfor','=',$usertype)
                    ->Where('transaction_details.source_id','=',$userid)
                    //->where(DB::raw('DATE_FORMAT(transaction_details.created_at, "%b")'), '=', $month)
                    ->get();
            }
            else
            {
                $results =  DB::table('transaction_details')
                    ->select('transaction_details.doctor_id','transaction_details.id as transactionid','doctors.first_name',
                        'doctors.last_name','transaction_details.patient_id',
                        'patients.fname','patients.lname','transaction_details.visit_id',
                        'visits.visit_category', 'visits.consultation_brief_description',
                        'transaction_charges.transactioncharge','transaction_charges.min_monthly_amt',
                        'transaction_charges.transactionno_rangeto',
                        'transaction_charges.transactionno_rangefrom',
                        'transaction_details.is_paid',
                        'transaction_details.created_at')
                    ->join('doctors','doctors.id','=','transaction_details.doctor_id')
                    ->join('patients','patients.id','=','transaction_details.patient_id')
                    ->join('visits','visits.id','=','transaction_details.visit_id')
                    ->join('transaction_source','transaction_source.id','=','transaction_details.transactionsourceid')
                    ->join('transaction_charges','transaction_charges.id','=','transaction_details.transactionchargesid')
                    ->Where('transaction_source.transactionfor','=',$usertype)
                    ->Where('transaction_details.source_id','=',$userid)
                    ->where(DB::raw('DATE_FORMAT(transaction_details.created_at, "%b")'), '=', $month)
                    ->get();
            }
        }
        if (!empty($results))
        {
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => $results
                ]
            ], 200);
        }
    }
    public function getcountrylist(Request $request)
    {
        $countriesdetails = DB::table('countries')
            ->select('countries.id',
                'countries.name',
                'countries.iso_3166_2',
                'countries.iso_3166_3','region_code')
            ->get();
        if (!empty($countriesdetails)) {
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => $countriesdetails
                ]
            ], 200);
        } else {
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => "No record Exists"
                ]
            ], 400);
        }
    }
    public function get_regionwise_countrylist(Request $request,$id)
    {

        $countriesdetails = "";
        if($id == '002' ||$id == '150')
        {
            $countriesdetails = DB::table('countries')
                    ->select('countries.id',
                        'countries.name',
                        'countries.iso_3166_2',
                        'countries.iso_3166_3','region_code' )
                    ->where('region_code','=','002')
                -orWhere('region_code','=','150')
                    ->get();

        }
        if($id == '009' ||$id == '019') {
            $countriesdetails = DB::table('countries')
                ->select('countries.id',
                    'countries.name',
                    'countries.iso_3166_2',
                    'countries.iso_3166_3', 'region_code')
                ->where('region_code', '=', '009')
                ->orWhere('region_code', '=', '019')
                ->get();
        }
        if($id == '142' ||$id == '') {
            $countriesdetails = DB::table('countries')
                ->select('countries.id',
                    'countries.name',
                    'countries.iso_3166_2',
                    'countries.iso_3166_3', 'region_code')
                ->where('region_code', '=', '142')
                ->orWhere('region_code', '=', '')
                ->get();
        }
        if (!empty($countriesdetails)) {
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'data' => $countriesdetails
                ]
            ], 200);
        } else {
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => "No record Exists"
                ]
            ], 400);
        }
    }
    public function setcountrylist(Request $request)
    {
        $countryname = Input::get('country');
        $countriesdetails = DB::table('countries')
            ->select('countries.id',
                'countries.name',
                'countries.iso_3166_2',
                'countries.iso_3166_3','region_code' )
             ->where('name','=',[$countryname])
            ->orWhere('iso_3166_2','=',[$countryname])
            ->orWhere('iso_3166_3','=',[$countryname])
            ->orWhere('region_code','=',[$countryname])
            ->limit(1)
            ->get();
       if (!empty($countriesdetails)) {
           if($countriesdetails[0]->region_code == '142' || $countriesdetails[0]->region_code == '' )
               $countriesdetails[0]->database = 'Database of Asia is selected';
           if($countriesdetails[0]->region_code == '009' || $countriesdetails[0]->region_code == '019' )
               $countriesdetails[0]->database = 'Database of USA is selected';
           if($countriesdetails[0]->region_code == '002' || $countriesdetails[0]->region_code == '150' )
               $countriesdetails[0]->database = 'Database of EMEA is selected';

           $request->session()->put('countrynamefordbselection', $countriesdetails[0]->name);
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => $countriesdetails
                ]
            ], 200);
        } else {
            return Response::json([
               'collection' => [
                   'version' => '1.0',
                   'href' => $request->url(),
                   'data' => "Invalid Country Name"
               ]
           ], 400);
        }
    }
    public function  change_user_status(Request $request)
    {
        $rules = array(
            'userid' => 'required',
            'status' => 'required',
            'usertype'=>'required'
        );
        $input = \Input::all();
        $v = Validator::make($input, $rules);
        if ($v->passes())
        {
            $status = Input::get('status');
            $userid = Input::get('userid');
            $usertype = Input::get('usertype');
            if(($usertype=='doctor')||($usertype=='dentist')||($usertype=='admin'))
                $login_id= Db::table('doctors')->where('id',$userid)->pluck('login_id');
            if($usertype=='laboratorist')
                $login_id= Db::table('laboratory')->where('id',$userid)->pluck('login_id');
            if($usertype=='pharmacist')
                $login_id= Db::table('pharmacy')->where('id',$userid)->pluck('login_id');
            if($usertype=='imaging_center')
                $login_id= Db::table('laboratory')->where('id',$userid)->pluck('login_id');
            if($usertype=='patient')
                $login_id= Db::table('patients')->where('id',$userid)->pluck('login_id');

            if(trim(strtolower($status)) == 'active')
            {
                DB::table('users')->where('id', '=', [$login_id])->update(array('status' => 'Active'));
                //DB::table('users')->where('id', '=', [$userid])->update(array('password' => bcrypt('password')));
                $message = "User account is  Activated";
            }
            elseif(trim(strtolower($status)) == 'paused')
            {
                DB::table('users')->where('id', '=', [$login_id])->update(array('status' => 'Paused'));
                $message = " User account is Suspended";
            }
            elseif(trim(strtolower($status)) == 'inactive')
            {
                DB::table('users')->where('id', '=', [$login_id])->update(array('status' => 'Inactive'));
                $message = "User account is  Inactive";
            }

            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => $message,
                ]
            ], 200);
        }
        else
        {
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => $v->messages()->all()
                ]
            ], 400);
        }
    }
    public function usernotification(Request $request)
    {


            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => ""
                ]
            ], 200);

    }
    public function welldatareport(Request $request)
    {


        return Response::json([
            'collection' => [
                'version' => '1.0',
                'href' => $request->url(),
                'data' => ""
            ]
        ], 200);

    }
    public function colorcodedata(Request $request)
    {


        return Response::json([
            'collection' => [
                'version' => '1.0',
                'href' => $request->url(),
                'data' => ""
            ]
        ], 200);

    }
    public function  change_user_password(Request $request,$logged_user)
    {
        $input = \Input::all();
        $input_new_password=trim($input['new_password']);
        $input_confirm_password=trim($input['confirm_password']);
        if ($input_new_password != $input_confirm_password) {
            throw new NewPasswordMismatchException;
        }
        $usersdetails = DB::table('users')
            ->select('*' )
            ->where('id','=',$logged_user)
            ->get();
        if(!empty($usersdetails))
        {
            DB::table('users')->where('id', '=', [$logged_user])->update(array('password' => bcrypt($input_confirm_password)));
            $name = $usersdetails[0]->name;
            $username = $usersdetails[0]->email;
            $var = "<h1>Hi, " . $name . "!</h1>" .
                   "<p>Your password is changed successfully. Your new credentials are:- </p>
                             <p>Username: " . $username . "</p>
                             <p>Password: " .$input_confirm_password. "</p>
                             <p>Thank you for registering.</p>
                             <p>Regards,</p>
                             <p>Team Nymdx</p>";
            $emails_table_input = array(
                'Message' => $var,
                'EmailID' =>$username,
                'Name' =>$name,
                'Subject'=>"Password Changed Successfully",
            );

            Mail::send('emails_template', ['html_code' => $emails_table_input['Message']], function ($message) use ($emails_table_input) {
                $message->to($emails_table_input['EmailID'], $emails_table_input['Name'])
                    //->cc('mitesh.jain@nitaipartners.com', 'manish.yadav@nitaipartners.com', 'aditya@nitaipartners.com')
                    ->subject($emails_table_input['Subject']);
            });
        }
        return "Password Changed Successfully";
    }

    public function  change_system_user_password(Request $request)
    {
        $input = \Input::all();
        $input_email = $input['email'];
        $input_new_password=trim($input['new_password']);
        $input_confirm_password=trim($input['confirm_password']);
        if ($input_new_password != $input_confirm_password) {
            throw new NewPasswordMismatchException;
        }
        $usersdetails = DB::table('users')
            ->select('*' )
            ->where('email','=',$input_email)
            ->get();
        if(!empty($usersdetails))
        {
            DB::table('users')->where('email', '=', [$input_email])->update(array('password' => bcrypt($input_confirm_password)));
            $name = $usersdetails[0]->name;
            $username = $usersdetails[0]->email;
            $var = "<h1>Hi, " . $name . "!</h1>" .
                "<p>Your password for Nymdx admin is changed successfully. Your new credentials are:- </p>
                             <p>Username: " . $username . "</p>
                             <p>Password: " .$input_confirm_password. "</p>
                             <p>Thank you for registering.</p>
                             <p>Regards,</p>
                             <p>Team Nymdx</p>";
            $emails_table_input = array(
                'Message' => $var,
                'EmailID' =>$username,
                'Name' =>$name,
                'Subject'=>"Password Changed Successfully",
            );

            Mail::send('emails_template', ['html_code' => $emails_table_input['Message']], function ($message) use ($emails_table_input) {
                $message->to($emails_table_input['EmailID'], $emails_table_input['Name'])
                    //->cc('mitesh.jain@nitaipartners.com', 'manish.yadav@nitaipartners.com', 'aditya@nitaipartners.com')
                    ->subject($emails_table_input['Subject']);
            });
        }
        return "Password Changed Successfully";
    }

    public function update_transaction_log_status(Request $request)
    {
        $rules = array(
            'user_id' => 'required',
            'organization_id' => 'required',
            'status' => 'required'
        );
        $input = \Input::all();
        $v = Validator::make($input, $rules);
        if ($v->passes()) {
            $user_id = Input::get('user_id');
            $organization_id = Input::get('organization_id');
            $status = Input::get('status');

            $transaction_log_monthly = DB::table('transaction_log_monthly')
                ->where('transaction_log_monthly.source_id', '=', $user_id)
                ->where('transaction_log_monthly.org_id', '=', $organization_id)
                ->where('transaction_log_monthly.transactionmonth', '<', 'MONTH(CURRENT_DATE)')
                ->where('transaction_log_monthly.transactionyear', '<=', 'YEAR(CURRENT_DATE)')
                ->where('transaction_log_monthly.ispaid', '!=', 1)
                ->get();

            $current_date_time = date('Y-m-d H:i:s');
            $transaction_details = TransactionDetails::whereYear('created_at', '<=', $current_date_time)
                ->whereMonth('created_at', '<', $current_date_time)
                ->where('source_id', '=', $user_id)
                ->where('transaction_details.is_paid', '!=', 1)
                ->get();

            if (!empty($transaction_log_monthly) && !empty($transaction_details)) {
                $Update_transaction_log = DB::table('transaction_log_monthly')
                    ->update(['transaction_log_monthly.ispaid' => 1]);

                $Update_transaction_details = DB::table('transaction_details')
                    ->update(['is_paid' => 1]);

                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "Status updated Successfully"
                    ]
                ], 200);
            }
            else {
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "Transaction does not exists!!!"
                    ]
                ], 400);


            }
        }
        else{

            return Response::json([
                'collection' => [
                   'version' => '1.0',
                   'href' => $request->url(),
                   'data' => $v->messages()->all()
               ]
           ], 400);


        }
    }




    public function user_delete(Request $request,$user_id,$userType)
    {

        if($userType == 'patient') {
            $patients = DB::table('patients')
                ->select('patients.*')
                ->where('patients.id', '=', $user_id)
                ->get();

            if (!empty($patients)) {

                $result = DB::table('patients')->where('id', '=', $user_id)->update(array('IsDeleted' => '1'));
               // $patients=DB::update('update patients set IsDeleted = 1 where id = ?',[$user_id]);

                if($result==1)
                {
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "patients Details deleted successfully."
                        ]
                    ], 200);
                }
                else{
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "patients Details Already deleted."
                        ]
                    ], 400);

                }

            } else {

                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "doctors Details does not exists."
                    ]
                ], 400);
            }
        }
        elseif($userType == 'doctor' || $userType == 'dentist')
        {
            $doctors = DB::table('doctors')
                ->select('doctors.*')
                ->where('doctors.id', '=', $user_id)
                ->get();
            if(!empty($doctors))
            {
                $result=DB::table('doctors')->where('id', '=', $user_id)->update(array('IsDeleted' => '1'));

                if($result==1)
                {
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "doctors Details deleted successfully."
                        ]
                    ], 200);
                }
                else{
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "doctors Details Already deleted."
                        ]
                    ], 400);

                }

            }
            else{
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "doctors Details does not exists."
                    ]
                ], 400);

            }

        }
        elseif($userType == 'admin')
        {
            $doctors = DB::table('doctors')
                ->select('doctors.*')
                ->where('doctors.id', '=', $user_id)
                ->get();
            if(!empty($doctors))
            {
                $result=DB::table('doctors')->where('id', '=', $user_id)->update(array('IsDeleted' => '1'));
                $results = DB::table('users')
                    ->leftjoin('doctors', 'users.id', '=', 'doctors.login_id')
                    ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
                    ->where('doctors.id', '=', $user_id)
                    ->update(array('users.type' => 'doctor'));

                if($result==1)
                {
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "doctors Details deleted successfully."
                        ]
                    ], 200);
                }
                else{
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "doctors Details Already deleted."
                        ]
                    ], 400);

                }

            }
            else{
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "doctors Details does not exists."
                    ]
                ], 400);

            }

        }

        elseif($userType == 'pharmacist')
        {
            $pharmacy = DB::table('pharmacy')
                ->select('pharmacy.*')
                ->where('pharmacy.id', '=', $user_id)
                ->get();

            if(!empty($pharmacy))
            {
                $result=DB::table('pharmacy')->where('id', '=', $user_id)->update(array('IsDeleted' => '1'));

                if($result==1)
                {
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "pharmacy Details deleted successfully."
                        ]
                    ], 200);
                }
                else{
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "pharmacy Details Already deleted."
                        ]
                    ], 400);

                }

            }
            else{
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "pharmacy Details does not exists."
                    ]
                ], 400);

            }

        }
        elseif($userType == 'laboratorist')
        {
            $laboratory = DB::table('laboratory')
                ->select('laboratory.*')
                ->where('laboratory.id', '=', $user_id)
                ->get();

            if(!empty($laboratory))
            {
                $result=DB::table('laboratory')->where('id', '=', $user_id)->update(array('IsDeleted' => '1'));

                if($result==1)
                {
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "laboratory Details deleted successfully."
                        ]
                    ], 200);
                }
                else{
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "laboratory Details Already deleted."
                        ]
                    ], 400);

                }

            }
            else{
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "laboratory Details does not exists."
                    ]
                ], 400);

            }

        }
        elseif($userType == 'imaging_center'){

            $imaging_centers = DB::table('imaging_centers')
                ->select('imaging_centers.*')
                ->where('imaging_centers.id', '=', $user_id)
                ->get();

            if(!empty($imaging_centers))
            {
                $result=DB::table('imaging_centers')->where('id', '=', $user_id)->update(array('IsDeleted' => '1'));

                if($result==1)
                {
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "imaging_centers Details deleted successfully."
                        ]
                    ], 200);
                }
                else{
                    return Response::json([
                        'collection' => [
                            'version' => '1.0',
                            'href' => $request->url(),
                            'data' => "imaging_centers Details Already deleted."
                        ]
                    ], 400);

                }

            }
            else{
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "imaging_centers Details does not exists."
                    ]
                ], 400);

            }


        }

    }

    public function change_user_type(Request $request,$doctor_id)
    {

        $doctor = DB::table('doctors')
            ->select('doctors.*')
            ->where('doctors.id', '=', $doctor_id)
            ->get();

        if (!empty($doctor)) {

            $result = DB::table('users')
                ->leftjoin('doctors', 'users.id', '=', 'doctors.login_id')
                ->leftjoin('organization', 'organization.id', '=', 'doctors.organization_id')
                ->where('doctors.id', '=', $doctor_id)
                ->update(array('users.type' => 'admin'));

            if($result==1)
            {
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "User Type Updated successfully."
                    ]
                ], 200);
            }
            else{
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "User Type Already Updated."
                    ]
                ], 400);

            }


        }
        else{

            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => "doctors Details does not exists"
                ]
            ], 400);

        }
    }


    public function organization_delete(Request $request,$organization_id)
    {
        $organization = DB::table('organization')
            ->select('organization.*')
            ->where('organization.id', '=', $organization_id)
            ->get();

        if(!empty($organization))
        {
            $org=DB::update('update organization set IsDeleted = 1 where id = ?',[$organization_id]);

            if($org==1)
            {
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "organization Details deleted successfully."
                    ]
                ], 200);

            }
            else{
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "organization Details Already deleted."
                    ]
                ], 400);

            }

        }
        else{
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => "organization Details does not exists."
                ]
            ], 400);

        }

    }


    public function get_all_doctor(Request $request,$organization_id)
    {
        $organization = DB::table('organization')
            ->select('organization.*')
            ->where('organization.id', '=', $organization_id)
            ->get();

        if(!empty($organization))
        {
            $doctor = DB::table('organization')
                ->select('doctors.*')
                ->leftjoin('doctors', 'doctors.organization_id', '=', 'organization.id')
                ->where('doctors.IsDeleted','=','0')
                ->where('organization.id', '=', $organization_id)
                ->where(function($query){
                    return $query
                        ->where('organization.IsDeleted', '=', '0');

                })

                ->get();

            if(!empty($doctor))
            {
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => $doctor
                    ]
                ], 200);

            }
            else{
                return Response::json([
                    'collection' => [
                        'version' => '1.0',
                        'href' => $request->url(),
                        'data' => "organization Related Doctor Details does not exists."
                    ]
                ], 400);

            }

        }
        else{
            return Response::json([
                'collection' => [
                    'version' => '1.0',
                    'href' => $request->url(),
                    'data' => "organization Details does not exists."
                ]
            ], 400);

        }

    }


}