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
use DB;
use URL;

use App\Models\SumbUsers;
use App\Models\SumbInvoiceSettings;
use App\Models\SumbTransactions;
use App\Models\SumbClients;
use App\Models\SumbExpensesClients;
use App\Models\SumbInvoiceParticulars;
use App\Models\SumbInvoiceParticularsTemp;
use App\Models\SumbInvoiceDetails;
use App\Models\SumbInvoiceItems;
use Illuminate\Support\Facades\Validator;

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
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Invoice & Expenses'
        );
        $errors = array(
            1 => ['A new expenses has been saved.', 'primary'],
            2 => ['A new invoice has been saved.', 'primary'],
            3 => ['Invoice does not exists to void or requirements are not complete to do this process, please try again.', 'danger'],
            4 => ['the invoice is now voided.', 'primary'],
            5 => ['the expenses is now voided.', 'primary'],
        );
        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        
        $itemsperpage = 10;
        if (!empty($request->input('ipp'))) { $itemsperpage = $request->input('ipp'); }
        $pagedata['ipp'] = $itemsperpage;
        
        //==== preparing error message
        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        
        $purl = $oriform = $request->all();
        unset($purl['ipp']);
        $pagedata['myurl'] = route('invoice');
        $pagedata['ourl'] = route('invoice', $purl);
        $pagedata['npurl'] = http_build_query(['ipp'=>$itemsperpage]);
        
        
        
        //==== get all tranasactions
        $ptype = 'all';
        if (!empty($request->input('type'))) {
            $invoicedata = SumbTransactions::where('user_id', $userinfo[0])->where('transaction_type', $request->input('type'))->paginate($itemsperpage)->toArray();
            $ptype = $request->input('type');
        } else {
            $invoicedata = SumbTransactions::where('user_id', $userinfo[0])->paginate($itemsperpage)->toArray();
        }
        $pagedata['invoicedata'] = $invoicedata;
        
        //echo '<pre>';
        //print_r($invoicedata);
        //paginghandler
        $allrequest = $request->all();
        $pfirst = $allrequest; $pfirst['page'] = 1;
        $pprev = $allrequest; $pprev['page'] = $invoicedata['current_page']-1;
        $pnext = $allrequest; $pnext['page'] = $invoicedata['current_page']+1;
        $plast = $allrequest; $plast['page'] = $invoicedata['last_page'];
        $pagedata['paging'] = [
            'current' => url()->current().'?'.http_build_query($allrequest),
            'starpage' => url()->current().'?'.http_build_query($pfirst),
            'first' => ($invoicedata['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pfirst),
            'prev' => ($invoicedata['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pprev),
            'now' => 'Page '.$invoicedata['current_page']." of ".$invoicedata['last_page'],
            'next' => ($invoicedata['current_page'] >= $invoicedata['last_page']) ? '' : url()->current().'?'.http_build_query($pnext),
            'last' => ($invoicedata['current_page'] >= $invoicedata['last_page']) ? '' : url()->current().'?'.http_build_query($plast),
        ];
        //print_r($pagedata['paging']);
        //die();
        //echo "<pre>"; print_r($invoicedata); die();
        
        
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
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Expenses'
        );
        $errors = array(
            1 => ['Values are required to process invoice or expenses, please fill in non-optional fields.', 'danger'],
            2 => ['Your amount is incorrect, it should be numeric only and no negative value. Please try again', 'danger'],
            3 => ['A new expenses has been saved.', 'primary'],
        );
        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
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
            $getexp_clients = SumbExpensesClients::where(DB::raw('UPPER(client_name)'), strtoupper($request->client_name))
                ->where('user_id',$userinfo[0])->first();
            print_r($getexp_clients);
            if (empty($getexp_clients)) {
                $dataprep_client = [
                    'user_id'               => $userinfo[0],
                    'client_name'           => $request->client_name,
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
    public function create_invoice_copy(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $errors = array(
            1 => ['Required fields should be filled out.', 'danger'],
            2 => ['Required emails is not a proper email', 'danger'],
            3 => ['Invoice Particular Details is required at least one.', 'danger'],
        );
        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        $get_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->orderBy('id')->first();
        if(!empty($get_settings)) {
            $pagedata['data'] = $get_settings = $get_settings->toArray();
        } else {
            $pagedata['data'] = $get_settings = array();
        }
        
        $pagedata['form'] = $request->all();
        $get_expclients = SumbClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients = $get_expclients->toArray();
        }
        //print_r($get_settings); die();
        $get_current_particulars = SumbInvoiceParticularsTemp::where('user_id', $userinfo[0])->where('invoice_number', $get_settings['invoice_count'])->get();
        if (!empty($get_current_particulars)) {
            $pagedata['particulars'] = $get_current_particulars->toArray();
        } else {
            $pagedata['particulars'] = array();
        }
        $pagedata['gtotal'] = $gtotal = SumbInvoiceParticularsTemp::where('invoice_number', $get_settings['invoice_count'])->sum('amount');
        
        //invoice info
        $pagedata['invdet_list'] = $invdet_list = SumbInvoiceDetails::where('user_id', $userinfo[0])->get()->toArray();
        
        return view('invoice.invoicecreate', $pagedata);
    }
    
    //***********************************************
    //*
    //* Create Invoice Process
    //*
    //***********************************************
    public function create_invoice_new_old(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        //echo "<pre>"; print_r($request->all()); //echo "</pre>";
        $oriform = $request->all();
        //check data
        if (empty($request->invoice_date) || empty($request->client_name) || empty($request->client_email) || empty($request->invoice_name) || empty($request->invoice_email) || empty($request->invoice_format)) {
            $oriform['err'] = 1;
            return redirect()->route('invoice-create', $oriform); die();
        }
        $pagedata['inv_settings'] = $get_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->orderBy('id')->first()->toArray();
        $get_invoice_parts = SumbInvoiceParticularsTemp::where('user_id', $userinfo[0])->where('invoice_number', $get_settings['invoice_count'])->get();
        //print_r($get_invoice_parts);
        if (empty($get_invoice_parts->toArray())) {
            $oriform['err'] = 3;
            return redirect()->route('invoice-create', $oriform); die();
        } else {
            $get_invoice_parts = $get_invoice_parts->toArray();
        }
        //if (empty(filter_var($request->client_name, FILTER_VALIDATE_EMAIL)) || empty(filter_var($request->invoice_email, FILTER_VALIDATE_EMAIL))) {
        //    $oriform['err'] = 2;
        //    return redirect()->route('invoice-create', $oriform); die();
        //}
        
        //SumbInvoiceParticulars::insert($get_invoice_parts);
        DB::statement('INSERT INTO sumb_invoice_particulars (`user_id`,`invoice_number`,`quantity`,`part_type`,`description`,`unit_price`,`amount`,`created_at`,`updated_at`) ( SELECT `user_id`,`invoice_number`,`quantity`,`part_type`,`description`,`unit_price`,`amount`,`created_at`,`updated_at`  FROM  sumb_invoice_particulars_temp WHERE user_id = ' . $userinfo[0] . ' AND invoice_number = ' . $get_settings['invoice_count'] . ')');
        SumbInvoiceParticularsTemp::where('user_id', $userinfo[0])->where('invoice_number', $get_settings['invoice_count'])->delete();
        //print_r($get_invoice_parts);
        
        $pagedata['inv_parts'] = $get_invoice_parts;
        //die();
        
        
        //==== setup data
        $invdate = explode("/", $request->invoice_date);
        $carbon_invdate = Carbon::createFromDate($invdate[2], $invdate[0], $invdate[1]);
        $dtnow = Carbon::now();
        $transactiondata = [
            'user_id' => $userinfo[0],
            'transaction_type' => 'invoice',
            'transaction_id' => $get_settings['invoice_count'],
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_address' => $request->client_address,
            'client_phone' => $request->client_phone,
            'invoice_details' => $request->invoice_details,
            'amount' => (int)$request->gtotal,
            'invoice_name' => $request->invoice_name, 
            'invoice_email' => $request->invoice_email,
            'invoice_phone' => $request->invoice_phone,
            'invoice_terms' => $request->invoice_terms,
            'logo' => $request->invoice_logo, 
            'invoice_format' => $request->invoice_format,
            'invoice_date' => $carbon_invdate,
            'created_at' => $dtnow,
            'updated_at' => $dtnow
        ];
        if (!empty($request->invoice_duedate)) {
            $invduedate = explode("/", $request->invoice_duedate);
            $carbon_invduedate = Carbon::createFromDate($invduedate[2], $invduedate[0], $invduedate[1]);
            $transactiondata['invoice_duedate'] = $carbon_invduedate;
        }
        //print_r($transactiondata);
        $nextinvoice = (int)$get_settings['invoice_count']+1;
        //die();
        //==== saving client details
        if(!empty($request->save_client)) {
            if ($request->save_client == 'yes') {
                //search if exists
                $chkdb_client = SumbClients::where(DB::raw('UPPER(client_name)'), strtoupper(trim($request->client_name)))->where('user_id', $userinfo[0])->first();
                //print_r($chkdb_client);
                if (empty($chkdb_client)) {
                    //echo 'no data!';
                    $clientsdata = [
                        'user_id' => $userinfo[0],
                        'client_name' => $request->client_name,
                        'client_email' => $request->client_email,
                        'client_phone' => $request->client_phone,
                        'client_address' => $request->client_address,
                        'client_details' => $request->invoice_details,
                        'created_at' => $dtnow,
                        'updated_at' => $dtnow
                    ];
                    SumbClients::insert($clientsdata);
                } else {
                    //echo 'have data!';
                    $chkdb_client = $chkdb_client->toArray();
                    //print_r($chkdb_client);
                    $clientsdata = [
                        'client_name' => $request->client_name,
                        'client_email' => $request->client_email,
                        'client_phone' => $request->client_phone,
                        'client_address' => $request->client_address,
                        'client_details' => $request->invoice_details,
                        'updated_at' => $dtnow
                    ];
                    SumbClients::where('id', $chkdb_client['id'])->update($clientsdata);
                }
            }
        }
        
        //==== saving invoice details
        if(!empty($request->save_invdet)) {
            if ($request->save_invdet == 'yes') {
                //search if exists
                $chkdb_invoicedet = SumbInvoiceDetails::where(DB::raw('UPPER(invoice_name)'), strtoupper(trim($request->invoice_name)))->where('user_id', $userinfo[0])->first();
                //print_r($chkdb_client);
                if (empty($chkdb_invoicedet)) {
                    //echo 'no data!';
                    $invoicedetdata = [
                        'user_id' => $userinfo[0],
                        'invoice_name' => $request->invoice_name,
                        'invoice_email' => $request->invoice_email,
                        'invoice_phone' => $request->invoice_phone,
                        'invoice_desc' => $request->invoice_terms,
                        'invoice_logo' => $request->invoice_logo,
                        'invoice_format' => $request->invoice_format,
                        'created_at' => $dtnow,
                        'updated_at' => $dtnow
                    ];
                    SumbInvoiceDetails::insert($invoicedetdata);
                } else {
                    //echo 'have data!';
                    $chkdb_invoicedet = $chkdb_invoicedet->toArray();
                    //print_r($chkdb_invoicedet);
                    $invoicedetdata = [
                        'invoice_name' => $request->invoice_name,
                        'invoice_email' => $request->invoice_email,
                        'invoice_phone' => $request->invoice_phone,
                        'invoice_desc' => $request->invoice_terms,
                        'invoice_logo' => $request->invoice_logo,
                        'invoice_format' => $request->invoice_format,
                        'updated_at' => $dtnow
                    ];
                    SumbInvoiceDetails::where('id', $chkdb_invoicedet['id'])->update($invoicedetdata);
                }
            }
        }
        
        
        
        SumbInvoiceSettings::where('user_id', $userinfo[0])->update(['invoice_count'=>$nextinvoice]);
        
        //==== Prepare PDF
        $logoimagetype = '';
        
        $logoimg = base64_encode(file_get_contents(env('APP_PUBLIC_DIRECTORY') . $request->invoice_logo));
        $invpdf['inv'] = [
            "logo" => $request->invoice_logo,
            'transaction_id' => $get_settings['invoice_count'],
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_address' => $request->client_address,
            'client_phone' => $request->client_phone,
            'invoice_details' => $request->invoice_details,
            'amount' => '$'.number_format((int)$request->gtotal, 2, ".", ","),
            'invoice_name' => $request->invoice_name, 
            'invoice_email' => $request->invoice_email,
            'invoice_phone' => $request->invoice_phone,
            'invoice_terms' => $request->invoice_terms,
            'invoice_format' => $request->invoice_format,
            'invoice_date' => $carbon_invdate,
            'inv_parts' => $get_invoice_parts
        ];
        $invpdf['inv']['logoimgdet'] = getimagesize(env('APP_PUBLIC_DIRECTORY') . $request->invoice_logo);
        $invpdf['inv']['logobase64'] = 'data:'.$invpdf['inv']['logoimgdet']['mime'].';charset=utf-8;base64,' . $logoimg;
        $inv_filename = 'inv'.date('YmdHis')."-".$get_settings['invoice_count']."-".md5(date('YmdHis')).".pdf";
        
        //$get_invoice_parts = SumbInvoiceParticularsTemp::where('user_id', $userinfo[0])->where('invoice_number', $get_settings['invoice_count'])->get();
        
        $pdf = Pdf::loadView('pdf.'.$request->invoice_format, $invpdf);
        $pdf->save(env('APP_PDF_DIRECTORY').$inv_filename);
        $transactiondata['invoice_pdf'] = $inv_filename;
        
        SumbTransactions::insert($transactiondata);
        
        return redirect()->route('invoice', ['err'=>2]); die();
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
        $errors = array(
            //1 => ['A new expenses has been saved.', 'primary'],
        );
        //==== preparing error message
        $pagedata['errors'] = $errors;
        if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }
        
        $userinfo = $request->get('userinfo');
        $get_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first()->toArray();
        $pagedata = array('userinfo'=>$userinfo);
        //==== Default Values
        $dtnow = Carbon::now();
        $response['chk'] = 'error';
        if (!empty($request->file)) {
            $pagedata['file'] = $request->file;
        }
        //echo "hello world!";
        return view('invoice.invoicelogo', $pagedata);
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
        
        $file = $request->file('logo_file');
        
        $filetypeallowed = ['image/png', 'imge/jpeg', 'image/jpg', 'image/gif'];
        if (in_array($file->getMimeType(), $filetypeallowed)) {
            $destinationPath = 'uploads';
            $ofile = $file->getClientOriginalName();
            $nfile = md5($ofile) . "." . $file->getClientOriginalExtension();
            $file->move($destinationPath,$ofile);
            rename(public_path('/'.$destinationPath.'/'.$ofile), public_path('/'.$destinationPath.'/'.$nfile));
            //echo ($nfile);
            $fileurl = '/'.$destinationPath.'/'.$nfile;
            //echo "<img src='".$fileurl."'>";
            return redirect()->route('invoice-logo-upload', ['err'=>1, 'file'=>$fileurl]); die();
        }
        
    }
    
    //***********************************************
    //*
    //* Invoice VOID PROCESS
    //*
    //***********************************************
    public function invoice_void(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Void Invoice'
        );
        //echo "<pre>"; print_r($request->all()); //echo "</pre>";
        $pagedata['oriform'] = $request->all();
        echo "<pre>"; print_r($pagedata);
        if (empty($pagedata['oriform']['invno'])) {
            return redirect()->route('invoice', ['err'=>3]); die();
        }
        $chk_inv = SumbTransactions::where('user_id', $userinfo[0])->where('transaction_type','invoice')->where('transaction_id', $pagedata['oriform']['invno'])->first();
        if ($chk_inv->exists) {
            $chk_inv = $chk_inv->toArray();
        }
        SumbTransactions::where('id',$chk_inv['id'])->update(['status_paid'=>'void']);
        echo "<pre>"; print_r($chk_inv); //echo "</pre>";
        return redirect()->route('invoice', ['err'=>4]); die();
    }
    
    //***********************************************
    //*
    //* Expenses VOID PROCESS
    //*
    //***********************************************
    public function expenses_void(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Void Expenses'
        );
        //echo "<pre>"; print_r($request->all()); //echo "</pre>";
        $pagedata['oriform'] = $request->all();
        echo "<pre>"; print_r($pagedata);
        if (empty($pagedata['oriform']['invno'])) {
            return redirect()->route('invoice', ['err'=>3]); die();
        }
        $chk_inv = SumbTransactions::where('user_id', $userinfo[0])->where('transaction_type','expenses')->where('transaction_id', $pagedata['oriform']['invno'])->first();
        if ($chk_inv->exists) {
            $chk_inv = $chk_inv->toArray();
        }
        SumbTransactions::where('id',$chk_inv['id'])->update(['status_paid'=>'void']);
        echo "<pre>"; print_r($chk_inv); //echo "</pre>";
        return redirect()->route('invoice', ['err'=> 5]); die();
    }
    
    //***********************************************
    //*
    //* Transaction status change PROCESS
    //*
    //***********************************************
    public function status_change(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Status Change'
        );
        //echo "<pre>"; print_r($request->all()); //echo "</pre>";
        $pagedata['oriform'] = $request->all();
        //echo "<pre>"; print_r($pagedata);
        
        if (empty($pagedata['oriform']['tno']) || empty($pagedata['oriform']['type']) || empty($pagedata['oriform']['to']) ) {
            return redirect()->route('invoice', ['err'=>3]); die();
        }
        if ($pagedata['oriform']['type'] != 'expenses' && $pagedata['oriform']['type'] != 'invoice' && $pagedata['oriform']['type'] != 'adjustment') {
            return redirect()->route('invoice', ['err'=>3]); die();
        }
        if ($pagedata['oriform']['to'] != 'paid' && $pagedata['oriform']['to'] != 'unpaid' && $pagedata['oriform']['to'] != 'void') {
            return redirect()->route('invoice', ['err'=>3]); die();
        }
        
        
        $chk_inv = SumbTransactions::where('user_id', $userinfo[0])->where('transaction_type',$pagedata['oriform']['type'])->where('transaction_id', $pagedata['oriform']['tno'])->first();
        if ($chk_inv->exists) {
            $chk_inv = $chk_inv->toArray();
        }
        //print_r($chk_inv);
        //die();
        SumbTransactions::where('id',$chk_inv['id'])->update(['status_paid'=>$pagedata['oriform']['to']]);
        //echo "<pre>"; print_r($chk_inv); //echo "</pre>";
        //die();
        return redirect()->route('invoice', ['err'=> 5]); die();
    }
    
    public function testpdf(Request $request) {
        $pagedata = array('userinfo'=>$request->get('userinfo'));
        $pdf = Pdf::loadView('pdf.format001', $pagedata);
        return $pdf->save(env('APP_PDF_DIRECTORY').'my_stored_file.pdf')->download('invoice.pdf');
    }
    
    
    public function testformat(Request $request) {
        $id = 1; 
        if (isset($request->id)) { $id = $request->id; }
        $format = 'format001';
        if (isset($request->format)) { $format = $request->format; }
        $pdf = 1;
        if (isset($request->pdf)) { $pdf = $request->pdf; }
        
        $getdata = SumbTransactions::where('id', $id)->first();
        if (empty($getdata)) { die("no data"); }
        $pagedata['inv'] = $getdata->toArray();
        $pagedata['inv']['logoimgdet'] = getimagesize(url($pagedata['inv']['logo']));
        $pagedata['inv']['logobase64'] = 'data:'.$pagedata['inv']['logoimgdet']['mime'].';charset=utf-8;base64,' . base64_encode(file_get_contents(url($pagedata['inv']['logo'])));
        
        $pagedata['inv']['inv_parts'] = SumbInvoiceParticulars::where('user_id', $pagedata['inv']['user_id'])->where('invoice_number', $pagedata['inv']['transaction_id'])->get()->toArray();
        //echo "<pre>" ;print_r($pagedata);die();
        
        if ($pdf == 1) {
            //$pagedata['inv']['pdfpreview'] = 0;
            $inv_filename = 'invtest'.date('YmdHis')."-".$pagedata['inv']['transaction_id']."-".md5(date('YmdHis')).".pdf";
            $pdf = Pdf::loadView('pdf.'.$format, $pagedata);
            $pdf->save(env('APP_PDF_DIRECTORY').$inv_filename);
            $pagedata['inv']['invoice_pdf'] = $inv_filename;
        }
        $pagedata['inv']['pdfpreview'] = 1;
        return view('pdf.'.$format, $pagedata);
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

//---------Invoice new functions starts from here---------------------//

    public function create_invoice(Request $request) {

        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $pagedata['invoice_details'] = $request->post();
    
        $get_clients = SumbClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_clients)) {
            $pagedata['clients'] = $get_clients = $get_clients->toArray();
        }
        $invoice_details = SumbInvoiceDetails::where('user_id', $userinfo[0])->orderBy('invoice_number', 'desc')->first();
        if (!empty($invoice_details)) {
            $pagedata['invoice_number'] = $invoice_details->toArray()['invoice_number'];
            // echo "<pre>"; var_dump($pagedata['invoice_number']); echo "</pre>";die();
        }
        $get_items = SumbInvoiceItems::where('user_id', $userinfo[0])->orderBy('invoice_item_name')->get();
        if (!empty($get_items)) {
            $pagedata['invoice_items'] = $get_items->toArray();
            // echo "<pre>"; var_dump($pagedata['invoice_items']); echo "</pre>";die();
        }
        
        return view('invoice.invoicecreate', $pagedata);
    }

    public function create_invoice_new(Request $request) {
        $validator = Validator::make($request->all(),[
            'client_name' => 'bail|required|max:255',
            'client_email' => 'bail|required|max:255',
            'invoice_issue_date' => 'bail|required',
            'invoice_due_date' => 'bail|required',
            'invoice_number' => 'bail|required|max:255',
            // 'invoice_parts_unit_price_.*' => 'bail|required|max:255',
            // 'invoice_parts_description_.' => 'bail|required',
            // 'invoice_parts_amount_.*' => 'bail|required',
        ]);
        
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $invoice_details = [];
        $parts = [];
        $invoice_details = array(
            "user_id" => $userinfo[0],
            "client_name" => $request->client_name,
            "client_email" => $request->client_email,
            "client_phone" => $request->client_phone,
            "invoice_due_date" => $request->invoice_due_date,
            "invoice_issue_date" => $request->invoice_issue_date,
            "invoice_number" => $request->invoice_number,
            "invoice_default_tax" => $request->invoice_default_tax,
            "invoice_sub_total" => $request->invoice_sub_total,
            "invoice_total_gst" => $request->invoice_total_gst,
            "invoice_total_amount" => $request->invoice_total_amount,
        );
        if(count(json_decode(trim($request->invoice_part_total_count), true)) >= 0){
            $ids = json_decode(trim($request->invoice_part_total_count), true);
            foreach($ids as $id){
                $parts[] = array(
                    'invoice_parts_quantity' => trim($request->input('invoice_parts_quantity_'.$id)),
                    'invoice_parts_unit_price' => trim($request->input('invoice_parts_unit_price_'.$id)),
                    'invoice_parts_description' => trim($request->input('invoice_parts_description_'.$id)),
                    'invoice_parts_amount' => trim($request->input('invoice_parts_amount_'.$id)),
                    'invoice_parts_tax_rate' => trim($request->input('invoice_parts_tax_rate_'.$id)),
                    'invoice_parts_code' => $request->input('invoice_parts_code_'.$id),
                    'invoice_parts_name' => $request->input('invoice_parts_name_'.$id),
                    'invoice_parts_name_code' => $request->input('invoice_parts_name_code_'.$id),
                    'invoice_parts_id' => $id
                );
                $invoice_details['parts'] = $parts;
                // $validator = Validator::make($request->all(),[
                //     // 'client_name' => 'bail|required|max:255',
                //     // 'client_email' => 'bail|required|max:255',
                //     // 'invoice_issue_date' => 'bail|required',
                //     // 'invoice_due_date' => 'bail|required',
                //     'invoice_parts_quantity_'.$id => 'bail|required|max:255',
                //     'invoice_parts_unit_price_'.$id => 'bail|required|max:255',
                //     'invoice_parts_description_'.$id => 'bail|required',
                //     'invoice_parts_amount_'.$id => 'bail|required',
                // ]);
            }
        }
        
        $invoice_details['invoice_part_total_count'] = trim($request->input('invoice_part_total_count'));
        $pagedata['invoice_details'] = $invoice_details;
        if ($validator->fails()) {
            return redirect()->route( 'invoice-create' )->withErrors($validator)->with('form_data',$pagedata);
        }
        // 
        DB::beginTransaction();
        $client_exists = SumbClients::where('user_id', $userinfo[0])
                                    ->where('client_name', $request->client_name)
                                    ->get();
        if(empty($client_exists->toArray())){
            SumbClients::create([
                'user_id' => $userinfo[0],
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
            ]);
        }else{
            SumbClients::where('user_id', $userinfo[0])
                ->where('client_name', $request->client_name)
                ->update([
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
            ]);
        }

        $invoice_details['invoice_issue_date'] =  Carbon::createFromFormat('m/d/Y', $request->invoice_issue_date)->format('Y-m-d');
        $invoice_details['invoice_due_date'] =  Carbon::createFromFormat('m/d/Y', $request->invoice_due_date)->format('Y-m-d');
        

        $particlars = $invoice_details['parts'];
        // echo "<pre>"; var_dump($particlars); echo "</pre>";die();
        unset($invoice_details['parts']);
        unset($invoice_details['invoice_part_total_count']);

        // var_dump($request->invoice_number);die();
        $invoice = SumbInvoiceDetails::create(
            [
                'user_id' => trim($userinfo[0]), 
                'client_name' => trim($request->client_name),
                'client_email' => trim($request->client_email),
                'client_phone' => trim($request->client_phone),
                'invoice_issue_date' => trim($invoice_details['invoice_issue_date']),
                'invoice_due_date' => trim($invoice_details['invoice_due_date']),
                'invoice_number' => trim($invoice_details['invoice_number']),
                'invoice_default_tax' => trim($invoice_details['invoice_default_tax']),
                'invoice_sub_total' => trim($request->invoice_sub_total),
                'invoice_total_gst' => trim($request->invoice_total_gst),
                'invoice_total_amount' => trim($request->invoice_total_amount),
            ]);
        if($invoice->id){
            foreach($particlars as $key=>$value){
                SumbInvoiceParticulars::create(
                [
                    'user_id' => trim($userinfo[0]), 
                    'invoice_id' => $invoice->id,
                    'quantity' => trim($value['invoice_parts_quantity']),
                    'description' => trim($value['invoice_parts_description']),
                    'unit_price' => trim($value['invoice_parts_unit_price']),
                    'amount' => trim($value['invoice_parts_amount']),
                    'invoice_part_code' => (!empty($value['invoice_parts_code']) ? $value['invoice_parts_code'] : $value['invoice_parts_name_code']),
                    'invoice_part_name' => trim($value['invoice_parts_name']),
                    'invoice_part_tax_rate' => trim($value['invoice_parts_tax_rate']),
                ]);
            }
            
            DB::commit();
        }

        return redirect()->route('invoice');
    }

    public function searchInvoiceItem(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $invoice_item_name = trim($request->invoice_item_name);
                $invoice_items = SumbInvoiceItems::where('user_id', $userinfo[0])
                ->where('invoice_item_name', 'like', '%' . $request->invoice_item_name . '%')
                ->orderBy('invoice_item_name')
                ->get();

            echo json_encode($invoice_items);
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

    public function InvoiceItemForm(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $invoice_item_exists = SumbInvoiceItems::where('user_id', $userinfo[0])
                                            ->where('invoice_item_code', $request->invoice_item_code)
                                            ->first();
            if(!empty($invoice_item_exists)){
                $response = [
                    'status' => 'error',
                    'err' => 'Item code already exists',
                    'data' => ''
                ];
                echo json_encode($response);
            }else{
                DB::beginTransaction();
                $item = SumbInvoiceItems::create(
                    [
                        'user_id' => trim($userinfo[0]), 
                        'invoice_item_code' => $request->invoice_item_code,
                        'invoice_item_name' => trim($request->invoice_item_name),
                        'invoice_item_unit_price' => trim($request->invoice_item_unit_price),
                        'invoice_item_tax_rate' => trim($request->invoice_item_tax_rate),
                        'invoice_item_description' => trim($request->invoice_item_description),
                    ]);
                if($item->id){
                    DB::commit();
                    $invoice_items = SumbInvoiceItems::where('user_id', $userinfo[0])->get();
                    if($invoice_items){
                        $response = [
                            'status' => 'success',
                            'err' => '',
                            'data' => $invoice_items
                        ];

                        echo json_encode($response);
                    }
                } 
            }
        }
    }

    public function InvoiceItemFormList(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $invoice_item_name = trim($request->invoice_item_name);
                $invoice_items = SumbInvoiceItems::where('user_id', $userinfo[0])
                ->orderBy('invoice_item_name')
                ->get();
                if($invoice_items){
                    $response = [
                        'status' => 'success',
                        'err' => '',
                        'data' => $invoice_items
                    ];
                    echo json_encode($response);
                }
                else{
                    $response = [
                        'status' => 'error',
                        'err' => 'No items found',
                        'data' => ''
                    ];
                    echo json_encode($response);
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

    public function InvoiceItemFormListById(Request $request, $id)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
                $invoice_item = SumbInvoiceItems::where('user_id', $userinfo[0])
                                ->where('id', $id)
                                ->first();
                if($invoice_item){
                    $response = [
                        'status' => 'success',
                        'err' => '',
                        'data' => $invoice_item
                    ];
                    echo json_encode($response);
                }
                else{
                    $response = [
                        'status' => 'error',
                        'err' => 'No item found',
                        'data' => ''
                    ];
                    echo json_encode($response);
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
