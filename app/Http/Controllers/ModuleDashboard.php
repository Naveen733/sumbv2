<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\SignupMail;
use Carbon\Carbon;

//Models
use App\Models\SumbUsers;

class ModuleDashboard extends Controller {

    public $userinfo;
    
    public function __construct() {
        //$this->userinfo = ;
    }
    
    //***********************************************
    //*
    //*  Dashboard Page
    //*
    //***********************************************
    public function index(Request $request) {
        $pagedata = array('userinfo'=>$request->get('userinfo'));
        //echo "<pre>loggedin!";
        //$value = $request->session()->get('keysumb');
        //print_r($request->get('userinfo'));
        return view('dashboard', $pagedata); 
    }
}
