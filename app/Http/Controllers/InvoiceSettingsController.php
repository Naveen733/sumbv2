<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use DB;
use URL;

use App\Models\SumbUsers;
use App\Models\SumbTransactions;
use App\Models\SumbClients;
use App\Models\SumbExpensesClients;
use App\Models\SumbInvoiceParticulars;
use App\Models\SumbInvoiceParticularsTemp;
use App\Models\SumbInvoiceDetails;
use App\Models\SumbInvoiceItems;
use App\Models\SumbInvoiceReports;
use Illuminate\Support\Facades\Validator;
use App\Models\SumbInvoiceSettings;

class InvoiceSettingsController extends Controller {

    public function __construct() {

    }
    
    //***********************************************
    //*
    //* Invoice Settings View Page
    //*
    //***********************************************
    
    public function invoiceSettingsForm(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $pagedata['type'] = 'add';
        $pagedata['invoice_settings'] = '';
        $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->orderBy('id')->first();
        if($invoice_settings){
            $pagedata['invoice_settings'] = $invoice_settings->toArray();
            $pagedata['type'] = 'edit';
            $pagedata['settings_id'] = $pagedata['invoice_settings']['id'];
        }
        return view('invoice.settings', $pagedata);
    }

    public function store(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $validated = $request->validate([
            'business_name' => 'required|max:255',
            'business_email' => 'required|max:255',
            'business_phone' => 'required|max:14',
            'business_address' => 'required|max:255',
            'business_abn' => 'required|digits:11'
        ]);
        $invoice_settings = [
            'user_id' => $userinfo[0],
            'business_name' => $request->business_name,
            'business_email' => $request->business_email,
            'business_phone' => $request->business_phone,
            'business_address' => $request->business_address,
            'business_logo' => $request->logo_path ? $request->logo_path : '', 
            'business_abn' => $request->business_abn,
            'business_terms_conditions' => $request->business_terms_conditions,
            'business_invoice_format' => $request->business_invoice_format
        ];

            DB::beginTransaction();
            $setting = SumbInvoiceSettings::create($invoice_settings);
            if($setting->id){
                DB::commit();
            }
        return redirect()->route('/invoice/settings')->with('success', 'Invoice settings added');
    }

    public function update(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $validated = $request->validate([
            'business_name' => 'required|max:255',
            'business_email' => 'required|max:255',
            'business_phone' => 'required|max:14',
            'business_address' => 'required|max:255',
            'business_abn' => 'required|max:11'
        ]);

        $invoice_settings = [
            'user_id' => $userinfo[0],
            'business_name' => $request->business_name,
            'business_email' => $request->business_email,
            'business_phone' => $request->business_phone,
            'business_address' => $request->business_address,
            'business_logo' => $request->logo_path ? $request->logo_path : '', 
            'business_abn' => $request->business_abn,
            'business_terms_conditions' => $request->business_terms_conditions,
            'business_invoice_format' => $request->business_invoice_format
        ];
        // var_dump($request->logo_path);die();
        if($request->invoice_settings_id)
        {
            DB::beginTransaction();
            $setting = SumbInvoiceSettings::where('id', $request->invoice_settings_id)->where('user_id', $userinfo[0])->update($invoice_settings);
            if($setting){
                DB::commit();
            }
        }
        return redirect()->route('/invoice/settings')->with('success', 'Invoice settings updated');
    }

    public function logoUpload(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            if($request->file('fileInput'))
            {
                $file = $request->file('fileInput');
                $filetypeallowed = ['image/jpg', 'image/png', 'image/jpeg', 'image/gif'];
                if (in_array($file->getMimeType(), $filetypeallowed)) 
                {
                    $destinationPath = 'uploads/'.$userinfo[0];
                    $ofile = $file->getClientOriginalName();
                    $nfile = md5($ofile) . "." . $file->getClientOriginalExtension();
                    $file->move($destinationPath,$ofile);
                    rename(public_path('/'.$destinationPath.'/'.$ofile), public_path('/'.$destinationPath.'/'.$nfile));
                    
                    $fileurl = '/'.$destinationPath.'/'.$nfile;
                    
                    if($fileurl){
                        $response = [
                            'status' => 'success',
                            'err' => '',
                            'logo' => $nfile
                        ];
                        echo json_encode($response);
                    }
                    else{
                        $response = [
                            'status' => 'error',
                            'err' => 'Error in file upload',
                            'data' => ''
                        ];
                        echo json_encode($response);
                    }
                }
            }
            else
            {

            }
        }
        else
        {
            $response = [
                'status' => 'error',
                'err' => 'Something went wrong',
                'data' => ''
            ];
            echo json_encode($response);
        }
    }
}
