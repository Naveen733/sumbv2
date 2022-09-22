<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SignupMail;

use App\Models\SumbUsers;
use App\Models\SumbInvoiceSettings;


class MainController extends Controller {
    public function __construct() {
        //none at this time
    }
    
    //***********************************************
    //*
    //*  Index Page - Login Form
    //*
    //***********************************************
    public function index(Request $request) {
        $pagedata = array(
            'request'   => $request->all(),
            'pagetitle' => 'Welcome TO Set Up My Business Australia'
        );
        //print_r($request->all());
        $errors = array(
            1 => ['Sorry your form is incomplete, please fill out the information correctly.', 'danger'],
            2 => ['Verification details does not exist, its either its already verified or expired, please log in and try it again.', 'danger'],
            3 => ['Both password forms are not the same, Please try again.', 'danger'],
            4 => ['Your email has been verified.', 'primary'],
            5 => ['You are now registered, please check your email for verification.', 'primary'],
            6 => ['Username and Password does not exists or wrong credentials, Please try again.', 'danger'],
            7 => ['You are offline, please log in again.', 'warning'],
            8 => ['You are now logged out.', 'primary'],
        );
        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        return view('index', $pagedata); 
    }
    
    //***********************************************
    //*
    //*  Login Process
    //*
    //***********************************************
    public function login(Request $request) {
        echo "<pre>";
        print_r($request->all());
        $pagedata = array('request'=>$request->all());
        $upass = md5($request->password);
        $userdata = SumbUsers::where('email', $request->input('email'))
            ->where('password', $upass)->where('active',1)
            ->first();
        print_r($userdata);
        if (empty($userdata)) {
            $oriform['err'] = 6;
            return redirect()->route('index', $oriform); die();
        }
        print_r($userdata->email_verified_at);
        $verified_user = !empty($userdata->email_verified_at) ? 'verified' : 'unverified';
        $profile_pic = !empty($userdata->profilepic) ? $userdata->profilepic : 'blankpic.png';
        $userkey = [$userdata->id, $userdata->fullname, $userdata->email, $userdata->accountype, $verified_user, $profile_pic, date('ymdHis')];
        $user_id = encrypt(implode($userkey, ","));
        print_r($user_id); echo "\n\n";
        $decrypted = decrypt($user_id);
        print_r($decrypted);
        //$request->session()->put('keysumb', $user_id);
        session(['keysumb' => $user_id]);
        return redirect()->route('dashboard'); die();
    }
    
    //***********************************************
    //*
    //*  Signup Page
    //*
    //***********************************************
    public function signup(Request $request) {
        $pagedata = array('pagetitle' => 'Sign up now for free');        
        //$pagedata = SumbUsers::all();
        //print_r($request->all());
        $errors = array(
            1 => ['Verification cannot be completed, requirements are not complete.', 'danger'],
            2 => ['Your email you registered is already in our system, Please try to login.', 'danger'],
            3 => ['Both password forms are not the same, Please try again.', 'danger'],
            4 => ['You are now registered, please check your email for verification.', 'primary'],
        );
        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        if (!empty($request->input('accountant'))) { $pagedata['form_accountant'] = $request->input('accountant'); }
        if (!empty($request->input('email'))) { $pagedata['form_email'] = $request->input('email'); }
        if (!empty($request->input('fullname'))) { $pagedata['form_fullname'] = $request->input('fullname'); }
        return view('signup', $pagedata); 
    }
    
    
    //***********************************************
    //*
    //*  User Registration Process
    //*
    //***********************************************
    public function register(Request $request) {
        //echo "<pre>";
        //print_r($request->all());
        //print_r(SumbUsers::all());
        //print_r($request->input('email'));
        
        $predata = array();
        $oriform = array('email'=>$request->input('email'), 'fullname'=>$request->input('fullname'), 'accountant' => empty($request->input('accountant')) ? 0 : 1);
        //die();
        
        //===== Data checks
        if (empty($request->input('accountant'))) { $predata['accountype'] = 'user'; } else { $predata['accountype'] = 'accountant'; }
        
        if (empty($request->input('fullname')) || 
            empty($request->input('password1')) || 
            empty($request->input('password2'))) {
            $oriform['err'] = 1;
            return redirect()->route('signup', $oriform); die();
        }
        $predata['fullname'] = $request->input('fullname');
        if ($request->input('password1') != $request->input('password2')) {
            $oriform['err'] = 3;
            return redirect()->route('signup', $oriform); die();
        } else {
            $predata['password'] = md5($request->input('password2'));
        }
        if (empty($request->input('email'))) { 
            $oriform['err'] = 1;
            return redirect()->route('signup', $oriform); die();
        } else {
            $emaildata = SumbUsers::where('email', $request->input('email'))->first();
            //print_r($emaildata);
            if (!empty($emaildata)) {
                $oriform['err'] = 2;
                return redirect()->route('signup', $oriform); die();
            } else {
                $predata['email'] = $request->input('email');
            }
        }
        
        //===== saving data
        $dtnow = Carbon::now();
        $predata['created_at'] = $dtnow;
        $predata['updated_at'] = $dtnow;
        $predata['remember_token'] = md5($dtnow);
        $userid = SumbUsers::insertGetId($predata);
        
        //===== creating invoice data
        SumbInvoiceSettings::insert(['user_id'=>$userid, 'created_at'=>$dtnow, 'updated_at'=>$dtnow]);
        
        //===== sending emails
        $predata['URL'] = env('APP_URL');
        Mail::to($predata['email'])->send(new SignupMail($predata));
        
        $oriform['err'] = 5;
        return redirect()->route('index', $oriform); die();
    }
    
    //***********************************************
    //*
    //*  User Verification Process
    //*
    //***********************************************
    public function verify(Request $request) {
        //echo "<pre>";
        //print_r($request->all());
        //print_r($request->input('email'));
        
        //====  checking blanks
        if (empty($request->input('email')) || empty($request->input('token'))) {
            $oriform['err'] = 1;
            return redirect()->route('index', $oriform); die();
        }
        //==== checking data if correct
        $emaildata = SumbUsers::where('email', $request->input('email'))
            ->where('remember_token',$request->input('token'))
            ->whereNull('email_verified_at')
            ->first();//->first();toSql
        //print_r($emaildata);
        if (empty($emaildata)) {
            $oriform['err'] = 2;
            return redirect()->route('index', $oriform); die();
        }
        
        //no saving yet
        
        //print_r($emaildata->id);
        $oriform['err'] = 4;
        return redirect()->route('index', $oriform); die();
    }
    
    
    //***********************************************
    //*
    //*  Logout Process
    //*
    //***********************************************
    public function logout(Request $request) {
        $request->session()->flush();
        $oriform['err'] = 8;
        return redirect()->route('index', $oriform); die();
    }
}
