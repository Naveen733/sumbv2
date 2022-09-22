<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SignupMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

use App\Models\SumbUsers;
use App\Models\SumbInvoiceSettings;
use App\Models\SumbTransactions;
use App\Models\SumbClients;
use App\Models\SumbExpensesClients;
use App\Models\SumbInvoiceParticulars;
use App\Models\SumbInvoiceParticularsTemp;

class InvoiceController extends Controller {

    public function __construct() {
        //$this->userinfo = ;
    }
    
    //***********************************************
    //*
    //* Invoice Page
    //*
    //***********************************************
    public function index(Request $request) {
        $errors = array(
            1 => ['A new expenses has been saved.', 'primary'],
        );
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Invoice & Expenses'
        );
        //==== preparing error message
        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        
        //==== get all tranasactions
        //no paging yet
        $invoicedata = SumbTransactions::where('user_id', $userinfo[0])->get()->toArray();
        $pagedata['invoicedata'] = $invoicedata;
        
        //echo "<pre>"; print_r(empty($invoicedata)); echo "</pre>"; die();
        
        //echo "<pre>loggedin!";
        //$value = $request->session()->get('keysumb');
        //print_r($request->get('userinfo'));
        return view('invoice.invoicelist', $pagedata); 
    }
    
    //***********************************************
    //*
    //* Create Expenses Page
    //*
    //***********************************************
    public function create_expenses(Request $request) {
        $errors = array(
            1 => ['Values are required to process invoice or expenses, please fill in non-optional fields.', 'danger'],
            2 => ['Your amount is incorrect, it should be numeric only and no negative value. Please try again', 'danger'],
            3 => ['A new expenses has been saved.', 'primary'],
        );
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Expenses'
        );
        $pagedata['data'] = $get_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first()->toArray();
        $get_expclients = SumbExpensesClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients->toArray();
        }
        return view('invoice.expensescreate', $pagedata);
    }
    
    //***********************************************
    //*
    //* Create Expenses Process
    //*
    //***********************************************
    public function create_expenses_new(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array('userinfo'=>$userinfo);
        echo "<pre>";
        print_r($request->all());
        
        //check form data
        if (empty($request->invoice_date) || empty($request->client_name) || empty($request->amount)) {
            $oriform['err'] = 1;
            return redirect()->route('expenses-create', $oriform); die();
        }
        
        $oriform = ['err'=>0, 'invoice_date'=>$request->invoice_date, 'client_name'=>$request->client_name, 'invoice_details'=>$request->invoice_details, 'amount'=>$request->amount];
        
        if (!empty($request->savethisrep)) {
            $oriform['savethisrep'] = $request->savethisrep;
        } else {
            $oriform['savethisrep'] = 0;
        }
        
        //print_r(is_numeric($request->amount));
        if (!is_numeric($request->amount)) {
            $oriform['err'] = 2;
            return redirect()->route('expenses-create', $oriform); die();
        }
        
        //prepare saving data
        $get_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first()->toArray();
        //print_r($get_settings);
        $dateexploded = explode("/", $request->invoice_date);
        //print_r($dateexploded);
        $carbon_invdate = Carbon::createFromDate($dateexploded[2], $dateexploded[0], $dateexploded[1]);
        //print_r($carbon_invdate);
        $dtnow = Carbon::now();
        
        $dataprep = array(
            'user_id'           => $userinfo[0],
            'transaction_type'  => 'expenses',
            'transaction_id'    => $get_settings['expenses_count'],
            'amount'            => $request->amount,
            'client_name'       => $request->client_name,
            'invoice_details'   => $request->invoice_details,
            'invoice_date'      => $carbon_invdate,
            'status_paid'       => 'paid',
            'created_at'        => $dtnow,
            'updated_at'        => $dtnow,
        );
        //print_r($dataprep);
        
        //if save reciepient is on
        if (!empty($oriform['savethisrep'])) {
            $getexp_clients = SumbExpensesClients::where('client_name', strtoupper($request->client_name))
                ->where('user_id',$userinfo[0])->first();
            print_r($getexp_clients);
            if (empty($getexp_clients)) {
                $dataprep_client = [
                    'user_id'               => $userinfo[0],
                    'client_name'           => strtoupper($request->client_name),
                    'client_description'    => $request->invoice_details,
                    'created_at'            => $dtnow,
                    'updated_at'            => $dtnow,
                ];
                SumbExpensesClients::insert($dataprep_client);
            }
        }
        
        //saving data
        $transaction_id = SumbTransactions::insertGetId($dataprep);
        $updatethis = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();
        $updatethis->increment('expenses_count');
        
        return redirect()->route('invoice', ['err'=>1]); die();
    }
    
    //***********************************************
    //*
    //* Create Invoice Page
    //*
    //***********************************************
    public function create_invoice(Request $request) {
        $errors = array(
            //1 => ['A new expenses has been saved.', 'primary'],
        );
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $pagedata['data'] = $get_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->orderBy('id')->first()->toArray();
        $get_expclients = SumbClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients->toArray();
        }
        //print_r($get_settings); die();
        $get_current_particulars = SumbInvoiceParticularsTemp::where('user_id', $userinfo[0])->where('invoice_number', $get_settings['invoice_count'])->get();
        if (!empty($get_current_particulars)) {
            $pagedata['particulars'] = $get_current_particulars->toArray();
        } else {
            $pagedata['particulars'] = array();
        }
        $pagedata['gtotal'] = $gtotal = SumbInvoiceParticularsTemp::where('invoice_number', $get_settings['invoice_count'])->sum('amount');
        return view('invoice.invoicecreate', $pagedata);
    }
    
    //***********************************************
    //*
    //* Create Invoice Process
    //*
    //***********************************************
    public function create_invoice_new(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        //return view('invoice.invoicecreate', $pagedata);
    }
    
    //***********************************************
    //*
    //* Invoice Particulars Add
    //*
    //***********************************************
    public function invoice_particulars_add(Request $request) {
        $userinfo =$request->get('userinfo');
        $get_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first()->toArray();
        $pagedata = array('userinfo'=>$userinfo);

        //==== checking blanks
        if (empty($request->process) || empty($request->partype) || empty($request->part_desc) || empty($request->part_amount)) {
            return 'error'; die();
        }
        
        //==== Default Values
        $dtnow = Carbon::now();
        $response['chk'] = 'error';
        
        //==== add
        if ($request->process == 'a') {
            //==== Preparation Data
            $predata = [
                'user_id' => $userinfo[0],
                'invoice_number' => $get_settings['invoice_count'],
                'quantity' => $request->partype=='services' ? 0 : $request->part_qty,
                'part_type' => $request->partype,
                'description' => $request->part_desc,
                'unit_price' => $request->partype=='services' ? 0 : $request->part_uprice,
                'amount' => $request->part_amount,
                'created_at' => $dtnow,
                'updated_at' => $dtnow,
            ];
            
            //==== Save and get total
            $partid = SumbInvoiceParticularsTemp::insertGetId($predata);
            $gtotal = SumbInvoiceParticularsTemp::where('invoice_number', $get_settings['invoice_count'])->sum('amount');
            
            //==== Preparation Json Response
            $response['id'] = $partid;
            $response['part_type'] = $predata['part_type'];
            $response['qty'] = $predata['quantity']<1 ? '-' : $predata['quantity'];
            $response['desc'] = nl2br($predata['description']);
            $response['uprice'] = $predata['unit_price']<1 ? '-' : '$'.number_format($predata['unit_price'], 2, ".", ",");
            $response['upriceno'] = $predata['unit_price'];
            $response['amount'] = '$'.number_format($predata['amount'], 2, ".", ",");
            $response['amountno'] = $predata['amount'];
            $response['chk'] = 'success';
            $response['grand_total'] = number_format($gtotal, 2, ".", ",");
            
            //==== sen responce
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
            die();
        } elseif ($request->process == 'e') {
            //$display = $request->all();
            //print_r($display);
            
            if (empty($request->partid)) {
                return 'error'; die();
            }
            $partid = $request->partid;
            
            //==== Preparation Data
            $predata = [
                'quantity' => $request->partype=='services' ? 0 : $request->part_qty,
                'part_type' => $request->partype,
                'description' => $request->part_desc,
                'unit_price' => $request->partype=='services' ? 0 : $request->part_uprice,
                'amount' => $request->part_amount,
                'updated_at' => $dtnow,
            ];
            
            SumbInvoiceParticularsTemp::where('id',$partid)->update($predata);
            $gtotal = SumbInvoiceParticularsTemp::where('invoice_number', $get_settings['invoice_count'])->sum('amount');
            
            //==== Preparation Json Response
            $response['id'] = $partid;
            $response['part_type'] = $predata['part_type'];
            $response['qty'] = $predata['quantity']<1 ? '-' : $predata['quantity'];
            $response['desc'] = nl2br($predata['description']);
            $response['uprice'] = $predata['unit_price']<1 ? '-' : '$'.number_format($predata['unit_price'], 2, ".", ",");
            $response['upriceno'] = $predata['unit_price'];
            $response['amount'] = '$'.number_format($predata['amount'], 2, ".", ",");
            $response['amountno'] = $predata['amount'];
            $response['chk'] = 'success';
            $response['grand_total'] = number_format($gtotal, 2, ".", ",");
            
            //==== sen responce
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
            die();
        }
    }
    
    //***********************************************
    //*
    //* Invoice Particulars Delete
    //*
    //***********************************************
    public function invoice_particulars_delete(Request $request) {
        $userinfo = $request->get('userinfo');
        $get_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first()->toArray();
        $pagedata = array('userinfo'=>$userinfo);
        $display = $request->all();
        //print_r($display);
        //==== Default Values
        $dtnow = Carbon::now();
        $response['chk'] = 'error';
        
        if(empty($request->partid)) {
            echo "error"; die();
        }
        
        //check data is correct
        $getdata = SumbInvoiceParticularsTemp::where('id',$request->partid)
            ->where('user_id', $userinfo[0])
            ->where('invoice_number', $get_settings['invoice_count'])->first();
        if(empty($getdata)) {
            echo "error"; die();
        } else {
            $getdata = $getdata->toArray();
            //print_r($getdata);
            SumbInvoiceParticularsTemp::where('id',$getdata['id'])->delete();
            $gtotal = SumbInvoiceParticularsTemp::where('invoice_number', $get_settings['invoice_count'])->sum('amount');
            
            $response = [
                'chk' => 'success',
                'partid' => $getdata['id'],
                'gtotal' => number_format($gtotal, 2, ".", ",")
            ];
            
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
            die();
        }
        
        
        
        
    }
    
    //***********************************************
    //*
    //* Invoice Particulars Clear All - not yet implemented
    //*
    //***********************************************
    public function invoice_particular_clear(Request $request) {
        $userinfo =$request->get('userinfo');
        $get_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first()->toArray();
        $pagedata = array('userinfo'=>$userinfo);
        //==== Default Values
        $dtnow = Carbon::now();
        $response['chk'] = 'error';
        //not working at this time
    }
    
    //***********************************************
    //*
    //* Invoice Logo Upload
    //*
    //***********************************************
    public function invoice_logo_upload(Request $request) {
        $userinfo =$request->get('userinfo');
        $get_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first()->toArray();
        $pagedata = array('userinfo'=>$userinfo);
        //==== Default Values
        $dtnow = Carbon::now();
        $response['chk'] = 'error';
        //echo "hello world!";
        return view('invoice.invoicelogo');
    }
    
    //***********************************************
    //*
    //* Invoice Logo Upload PROCESS
    //*
    //***********************************************
    public function invoice_logo_process(Request $request) {
        $userinfo =$request->get('userinfo');
        $get_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first()->toArray();
        $pagedata = array('userinfo'=>$userinfo);
        //==== Default Values
        $dtnow = Carbon::now();
        $response['chk'] = 'error';
        $display = $request->file('logo_file')->store('logos');
        echo "hello world!";
        print_r($display);
        echo "<img src='/$display'>";
    }
    
    public function testpdf(Request $request) {
        $pagedata = array('userinfo'=>$request->get('userinfo'));
        $pdf = Pdf::loadView('pdf.invoicelayout1', $pagedata);
        return $pdf->save(env('APP_PDF_DIRECTORY').'my_stored_file.pdf')->download('invoice.pdf');
    }
    
    public function testformat(Request $request) {
        return view('pdf.invoicelayout1');
    }
    public function testing(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $display=$request->all();
        //print_r($userinfo);
        return view('invoice.particulars', $pagedata);
    }
    
}
