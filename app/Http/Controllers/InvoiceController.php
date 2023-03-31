<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use App\Mail\RecallInvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use DB;
use URL;

use App\Models\SumbUsers;
use App\Models\SumbInvoiceSettings;
use App\Models\Transactions;
use App\Models\TransactionCollections;
use App\Models\SumbClients;
use App\Models\SumbExpensesClients;
use App\Models\SumbInvoiceParticulars;
use App\Models\SumbInvoiceParticularsTemp;
use App\Models\SumbInvoiceDetails;
use App\Models\SumbInvoiceItems;
use App\Models\SumbInvoiceReports;
use App\Models\SumbChartAccounts;
use App\Models\SumbChartAccountsType;
use App\Models\SumbChartAccountsTypeParticulars;
use Illuminate\Support\Facades\Validator;
use App\Models\SumbInvoiceTaxRates;
use App\Models\InvoiceReports;
use App\Models\InvoiceHistory;

class InvoiceController extends Controller {

    public function __construct() {

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
        
        $pagedata['search_number_email_amount'] = '';
        $pagedata['start_date'] = '';
        $pagedata['end_date'] = '';
        $pagedata['orderBy'] = '';
        $pagedata['direction'] = '';
        
        //==== get all tranasactions
        $ptype = 'all';
        if (!empty($request->input('type'))) {
            $invoicedata = SumbInvoiceDetails::where('user_id', $userinfo[0])->where('is_active', 1)->paginate($itemsperpage)->toArray();  
            $ptype = $request->input('type');
        } else {
            
            if($request->search_number_email_amount || $request->start_date || $request->end_date || $request->orderBy || $request->filterBy){
                if($request->start_date){
                    $start_date = Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d');
                }
                if($request->end_date){
                    $end_date = Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d');
                }
                // var_dump($request->start_date);die();
                $total_amount = $request->search_number_email_amount;
                $invoice_number = $request->search_number_email_amount;

                if($request->search_number_email_amount){
                    if(is_numeric(trim($request->search_number_email_amount))){
                        $total_amount = ltrim($request->search_number_email_amount, '0');
                        $invoice_number = $total_amount;
                    }
                    else if(is_string(trim($request->search_number_email_amount))){
                        $invoice_number = str_replace('inv-00000', '', trim(strtolower($request->search_number_email_amount)));                        
                    }
                }
                $userinfo = $request->get('userinfo');
                $invoicedata = TransactionCollections::where('user_id', $userinfo[0])->where('is_active', 1);
                                if($request->search_number_email_amount){
                                    $invoicedata->where(function($query) use($invoice_number, $request, $total_amount){
                                        $query->where('transaction_number', 'LIKE', "%{$invoice_number}%")
                                       ->orWhere('client_email', 'LIKE', "%{$request->search_number_email_amount}%")
                                       ->orWhere('total_amount', 'LIKE', "%{$total_amount}%");
                                    });
                                }
                                if($request->start_date && $request->end_date){
                                    $invoicedata->whereBetween('issue_date', [$start_date, $end_date]);
                                }
                                if($request->orderBy){
                                    $invoicedata->orderBy($request->orderBy, $request->direction);
                                }
                                if($request->filterBy){
                                    $invoicedata->where('status', $request->filterBy);
                                }
                                $invoicedata = $invoicedata->paginate($itemsperpage)->toArray();

                $pagedata['search_number_email_amount'] = $request->search_number_email_amount;
                $pagedata['start_date'] = $request->start_date;
                $pagedata['end_date'] = $request->end_date;
                $pagedata['orderBy'] = $request->orderBy;
                $pagedata['filterBy'] = $request->filterBy;
                if($request->direction == 'ASC')
                {
                    $pagedata['direction'] = 'DESC';
                }
                if($request->direction == 'DESC')
                {
                    $pagedata['direction'] = 'ASC';
                }
            }
            else
            {
                $pagedata['orderBy'] = 'issue_date';
                $pagedata['direction'] = 'ASC';
                $pagedata['filterBy'] = '';
                $invoicedata = TransactionCollections::where('user_id', $userinfo[0])->where('is_active', 1)
                        ->orderBy('issue_date', 'DESC')
                        // ->orderBy('issue_date', 'DESC')
                        ->paginate($itemsperpage)->toArray();
            }
        }
        $pagedata['invoicedata'] = $invoicedata;
       
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
        return view('invoice.invoicelist', $pagedata); 
    }

    public function store(Request $request) {
        $request->type = 'create';
        $request->invoice_id = '';

        $pagedata = $this->invoiceForm($request);
       
        return view('invoice.invoicecreate', $pagedata);
    }

    public function update(Request $request) {
        $request->type = 'edit';
        $pagedata = $this->invoiceForm($request);
        return view('invoice.invoicecreate', $pagedata);
    }

    public function invoiceForm($request){
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $pagedata['invoice_details'] = $request->post();
        // $invoice_details = [];
        $pagedata['invoice_id'] = $request->id ? $request->id : '';
        $pagedata['type'] = $request->type;
        if($request->type == 'edit' && $request->id){
            
            $invoice_details = TransactionCollections::with(['transactions', 'transactions.chartAccountsParticulars'])
                                ->whereHas('transactions', function($query) use($userinfo) {
                                    $query->where('user_id', $userinfo[0]);
                                })
                                ->where('id', $request->id)
                                ->where('user_id', $userinfo[0])->first()->toArray();
            if (!empty($invoice_details)) {
                $invoice_details['parts'] = $invoice_details['transactions'];
                $invoice_details['invoice_part_total_count'] = "[]";
                unset($invoice_details['transactions']);
                $pagedata['invoice_details'] = $invoice_details;    
                
            }

            $invoice_history = InvoiceHistory::where('user_id', $userinfo[0])->where('invoice_id', $request->id)->get();
            if (!empty($invoice_history)) {
                $pagedata['invoice_history'] = $invoice_history->toArray();
            }

        }else{ 
                $invoice_details = TransactionCollections::where('user_id', $userinfo[0])->orderBy('transaction_number', 'desc')->first();
                if (!empty($invoice_details)) {
                    $pagedata['transaction_number'] = 000001 + $invoice_details->toArray()['transaction_number'];
                }else{
                    $pagedata['transaction_number'] = 000001;
                }
        }
        $get_clients = SumbClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_clients)) {
            $pagedata['clients'] = $get_clients = $get_clients->toArray();
        }
        
        $get_items = SumbInvoiceItems::where('user_id', $userinfo[0])->orderBy('invoice_item_name')->get();
        if (!empty($get_items)) {
            $pagedata['invoice_items'] = $get_items->toArray();
        }

        $chart_accounts_types = SumbChartAccounts::with(['chartAccountsTypes'])->get();
        if (!empty($chart_accounts_types)) {
            $pagedata['chart_accounts_types'] = $chart_accounts_types->toArray();
        }

        $chart_account = SumbChartAccounts::with(['chartAccountsParticulars', 'chartAccountsTypes'])
                        ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
                            $query->where('user_id', $userinfo[0]);
                        })
                        ->whereHas('chartAccountsTypes', function($query) use($userinfo) {
                        })
                    ->get();
        if (!empty($chart_account)) {
            $pagedata['chart_account'] = $chart_account->toArray();
        }

        $tax_rates = SumbInvoiceTaxRates::get();
        if (!empty($tax_rates)) {
            $pagedata['tax_rates'] = $tax_rates->toArray();
        }

        $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();
        if (!empty($invoice_settings)) {
            $pagedata['invoice_settings'] = $invoice_settings->toArray();
        }
        return $pagedata;
    }

    public function create_invoice_new(Request $request) {
        $userinfo = $request->get('userinfo');
            $pagedata = array(
                'userinfo'=>$userinfo,
                'pagetitle' => 'Create Invoice'
            );
        if($request->save_invoice == 'Save Invoice'){
            
            $validator = Validator::make($request->all(),[
                'client_name' => 'bail|required|max:255',
                'client_email' => 'bail|required|max:255',
                'invoice_issue_date' => 'bail|required',
                'invoice_due_date' => 'bail|required',
                'invoice_number' => 'bail|required|max:255',
            ]);
            
            $pagedata['invoice_id'] = $request->invoice_id;
            $pagedata['type'] = $request->type;
            $invoice_details = [];
            $parts = [];
            $invoice_details = array(
                "user_id" => $userinfo[0],
                "client_name" => $request->client_name,
                "client_email" => $request->client_email,
                "client_phone" => $request->client_phone,
                "due_date" => $request->invoice_due_date,
                "issue_date" => $request->invoice_issue_date,
                "transaction_number" => $request->invoice_number,
                "default_tax" => $request->invoice_default_tax,
                "sub_total" => $request->invoice_sub_total,
                "total_gst" => $request->invoice_total_gst,
                "total_amount" => $request->invoice_total_amount,
                "transaction_type" => 'invoice',
                "invoice_ref_number" => trim($request->invoice_ref_number) ? : 0,
            );
            
            if(count(json_decode(trim($request->invoice_part_total_count), true)) >= 0){
                $ids = json_decode(trim($request->invoice_part_total_count), true);
                foreach($ids as $id){
                    $parts[] = array(
                        'id' => trim($request->input('invoice_parts_id_'.$id)),
                        'parts_quantity' => trim($request->input('invoice_parts_quantity_'.$id)),
                        'parts_unit_price' => trim($request->input('invoice_parts_unit_price_'.$id)),
                        'parts_description' => trim($request->input('invoice_parts_description_'.$id)),
                        'parts_amount' => trim($request->input('invoice_parts_amount_'.$id)),
                        'parts_tax_rate' => trim($request->input('invoice_parts_tax_rate_'.$id)),
                        'parts_code' => $request->input('invoice_parts_code_'.$id),
                        'parts_name' => $request->input('invoice_parts_name_'.$id),
                        'parts_name_code' => $request->input('invoice_parts_name_code_'.$id),
                        'parts_chart_accounts_id' => $request->input('invoice_parts_chart_accounts_parts_id_'.$id),
                        'parts_chart_accounts' => trim($request->input('invoice_parts_chart_accounts_'.$id)),
                        'parts_tax_rate_id' => trim($request->input('invoice_parts_tax_rate_id_'.$id)),
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
            $invoice_details['status'] = $request->invoice_status;
            $pagedata['invoice_details'] = $invoice_details;
            
            if ($validator->fails()) {
                $get_items = SumbInvoiceItems::where('user_id', $userinfo[0])->orderBy('invoice_item_name')->get();
                if (!empty($get_items)) {
                    $pagedata['invoice_items'] = $get_items->toArray();
                }

                $get_clients = SumbClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
                if (!empty($get_clients)) {
                    $pagedata['clients'] = $get_clients = $get_clients->toArray();
                }
                
                $chart_account = SumbChartAccounts::with(['chartAccountsParticulars', 'chartAccountsTypes'])
                ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
                    $query->where('user_id', $userinfo[0]);
                })
                ->whereHas('chartAccountsTypes', function($query) use($userinfo) {
                    // $query->where('user_id', $userinfo[0]);
                })
                ->where('user_id', $userinfo[0])->get();
                if (!empty($chart_account)) {
                    $pagedata['chart_account'] = $chart_account->toArray();
                }

                $chart_accounts_types = SumbChartAccounts::with(['chartAccountsTypes'])->get();
                if (!empty($chart_accounts_types)) {
                    $pagedata['chart_accounts_types'] = $chart_accounts_types->toArray();
                }

                $tax_rates = SumbInvoiceTaxRates::get();
                if (!empty($tax_rates)) {
                    $pagedata['tax_rates'] = $tax_rates->toArray();
                }
                return view('invoice.invoicecreate')->withErrors($validator)->with($pagedata);
            }

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
             
            $invoice_details['issue_date'] =  Carbon::createFromFormat('m/d/Y', $request->invoice_issue_date)->format('Y-m-d');
            $invoice_details['due_date'] =  Carbon::createFromFormat('m/d/Y', $request->invoice_due_date)->format('Y-m-d');
           
            $particlars = $invoice_details['parts'];
            
            unset($invoice_details['parts']);
            unset($invoice_details['invoice_part_total_count']);
            $ids = [];

            // var_dump($request->invoice_ref_number);die();
            // 
            if($request->invoice_id && $request->type=='edit'){
                $invoice_update = TransactionCollections::where('user_id', trim($userinfo[0]))
                                ->where('id', $request->invoice_id)
                                ->update(
                                    [
                                        'user_id' => trim($userinfo[0]), 
                                        'client_name' => trim($request->client_name),
                                        'client_email' => trim($request->client_email),
                                        'client_phone' => trim($request->client_phone),
                                        'issue_date' => trim($invoice_details['issue_date']),
                                        'due_date' => trim($invoice_details['due_date']),
                                        'transaction_number' => trim($invoice_details['transaction_number']),
                                        'default_tax' => trim($invoice_details['default_tax']),
                                        'sub_total' => trim($request->invoice_sub_total),
                                        'total_gst' => trim($request->invoice_total_gst),
                                        'total_amount' => trim($request->invoice_total_amount),
                                        'transaction_type' => 'invoice',
                                        // 'invoice_ref_number' => trim($request->invoice_ref_number) ? : '',
                                    ]
                                );
                if($invoice_update){
                    foreach($particlars as $key=>$value){
                        $newParticulars = Transactions::create(
                            [
                                'user_id' => trim($userinfo[0]), 
                                'transaction_collection_id' => $request->invoice_id,
                                'parts_quantity' => trim($value['parts_quantity']),
                                'parts_description' => trim($value['parts_description']),
                                'parts_unit_price' => trim($value['parts_unit_price']),
                                'parts_amount' => trim($value['parts_amount']),
                                'parts_code' => (!empty($value['parts_code']) ? $value['parts_code'] : $value['parts_name']),
                                'parts_name' => trim($value['parts_name']),
                                // 'parts_tax_rate' => trim($value['invoice_parts_tax_rate']),
                                'parts_chart_accounts_id' => trim($value['parts_chart_accounts_id']),
                                'parts_tax_rate_id' => trim($value['parts_tax_rate_id']),
                                'parts_gst_amount' => 1,
                            ]);
                        array_push($ids,  $newParticulars->id);
                    }
                    if(!empty($ids)){
                        Transactions::whereNotIn('id', $ids)
                                        ->where('transaction_collection_id', $request->invoice_id)
                                        ->where('user_id', trim($userinfo[0]))
                                        ->delete();
                    }
                    $date = Carbon::now()->toDateString();
                    $time = Carbon::now()->toTimeString();

                    $invoice_history = array(
                        "invoice_id" => trim($request->invoice_id),
                        "invoice_number" => trim($invoice_details['transaction_number']),
                        "user_id" => trim($userinfo[0]),
                        "user_name" => trim($userinfo[1]),
                        "action" => "Edited",
                        "description" => "INV-".str_pad($invoice_details['transaction_number'], 6, '0', STR_PAD_LEFT).' to '.trim(ucfirst($request->client_name)).' for $ '.trim($request->invoice_total_amount),
                        "date" => $date,
                        "time" => $time
                    );
                    $this->createInvoiceHistory($invoice_history);
                    DB::commit();
                }
            }else{
                $invoice = TransactionCollections::create(
                    [
                        'user_id' => trim($userinfo[0]), 
                        'client_name' => trim($request->client_name),
                        'client_email' => trim($request->client_email),
                        'client_phone' => trim($request->client_phone),
                        'issue_date' => trim($invoice_details['issue_date']),
                        'due_date' => trim($invoice_details['due_date']),
                        'transaction_number' => trim($invoice_details['transaction_number']),
                        'default_tax' => trim($invoice_details['default_tax']),
                        'sub_total' => trim($request->invoice_sub_total),
                        'total_gst' => trim($request->invoice_total_gst),
                        'total_amount' => trim($request->invoice_total_amount),
                        'transaction_type' => 'invoice',
                        'invoice_ref_number' => trim($request->invoice_ref_number) ? : 0
                    ]
                );
                if($invoice->id){
                    foreach($particlars as $key=>$value){
                        Transactions::create(
                        [
                            'user_id' => trim($userinfo[0]), 
                            'transaction_collection_id' => $invoice->id,
                            'parts_quantity' => trim($value['parts_quantity']),
                            'parts_description' => trim($value['parts_description']),
                            'parts_unit_price' => trim($value['parts_unit_price']),
                            'parts_amount' => trim($value['parts_amount']),
                            'parts_code' => (!empty($value['parts_code']) ? $value['parts_code'] : $value['parts_name']),
                            'parts_name' => trim($value['parts_name']),
                            // 'parts_tax_rate' => trim($value['invoice_parts_tax_rate']),
                            'parts_chart_accounts_id' => trim($value['parts_chart_accounts_id']),
                            'parts_tax_rate_id' => trim($value['parts_tax_rate_id']),
                            'parts_gst_amount' => 1,
                        ]);
                    }
                }
                
                $date = Carbon::now()->toDateString();
                $time = Carbon::now()->toTimeString();

                $invoice_history = array(
                    "invoice_id" => trim($invoice->id),
                    "invoice_number" => trim($invoice_details['transaction_number']),
                    "user_id" => trim($userinfo[0]),
                    "user_name" => trim($userinfo[1]),
                    "action" => !empty($request->invoice_ref_number) ? "Cloned" : "Created",
                    "description" => "INV-".str_pad($invoice_details['transaction_number'], 6, '0', STR_PAD_LEFT).' to '.trim(ucfirst($request->client_name)).' for $ '.trim($request->invoice_total_amount),
                    "date" => $date,
                    "time" => $time
                );

                DB::commit();
                $this->createInvoiceHistory($invoice_history);
            }
        }
        // return view('invoice.invoicecreate', $pagedata);
        return redirect()->route('invoice');
        
    }

    public function sendInvoice(Request $request)
    {
        $userinfo = $request->get('userinfo');
        if($request->invoice_id){
            $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();

            // $invoice_exists = SumbInvoiceDetails::find($request->invoice_id);
            $invoice_detail = TransactionCollections::with(['transactions', 'transactions.invoiceTaxRates'])
                                ->whereHas('transactions', function($query) use($userinfo) {
                                    $query->where('user_id', $userinfo[0]);
                                })
                                ->where('id', $request->invoice_id)
                                ->where('user_id', $userinfo[0])->first();
            if (!empty($invoice_detail)) {
                $invoice_detail = $invoice_detail->toArray();

                $request->invoice_format = !empty($invoice_settings) && $invoice_settings['business_invoice_format'] ? $invoice_settings['business_invoice_format'] : 'format002';
                $logoimg = base64_encode(file_get_contents('uploads/a71ed73925a75dae44b71bc161131adb.png'));
                $invpdf['inv'] = [
                    'logo' => 'a71ed73925a75dae44b71bc161131adb.png',
                    'invoice_number' => $invoice_detail['transaction_number'],
                    'client_name' => $invoice_detail['client_name'],
                    'client_email' => $invoice_detail['client_email'],
                    'client_address' => 'test',
                    'client_phone' => $invoice_detail['client_phone'],
                    'invoice_sub_total' => $invoice_detail['sub_total'],
                    'invoice_total_gst' => $invoice_detail['total_gst'],
                    'invoice_total_amount' => $invoice_detail['total_amount'],
                    'invoice_name' => !empty($invoice_settings) ? $invoice_settings['business_name'] : $userinfo[1], 
                    'invoice_email' => !empty($invoice_settings) ? $invoice_settings['business_email'] : $userinfo[2],
                    'invoice_phone' => !empty($invoice_settings) ? $invoice_settings['business_phone'] : '',
                    'invoice_address' => !empty($invoice_settings) ? $invoice_settings['business_address'] : '',
                    'invoice_abn' => !empty($invoice_settings) ? $invoice_settings['business_abn'] : '',
                    'invoice_terms' => !empty($invoice_settings) ? $invoice_settings['business_terms_conditions'] : '',
                    'invoice_format' => $request->invoice_format,
                    'invoice_date' => $invoice_detail['issue_date'],
                    'invoice_due_date' => $invoice_detail['due_date'],
                    'inv_parts' => $invoice_detail['transactions']
                ];
                $invpdf['inv']['logoimgdet'] = getimagesize('uploads/a71ed73925a75dae44b71bc161131adb.png');
                $invpdf['inv']['logobase64'] = 'data:'.$invpdf['inv']['logoimgdet']['mime'].';charset=utf-8;base64,' . $logoimg;
                $inv_filename = 'inv'.date('YmdHis')."-".$invoice_detail['transaction_number']."-".md5(date('YmdHis')).".pdf";
                
                //$get_invoice_parts = SumbInvoiceParticularsTemp::where('user_id', $userinfo[0])->where('invoice_number', $get_settings['invoice_count'])->get();
                $invpdf['inv']['image'] = env('APP_PUBLIC_DIRECTORY') . 'a71ed73925a75dae44b71bc161131adb.png';
               
                $pdf = Pdf::loadView('pdf.'.$request->invoice_format, $invpdf);
                $pdf->save(env('APP_PDF_DIRECTORY').$inv_filename);
                $transactiondata['invoice_pdf'] = $inv_filename;
                $invpdf['inv']['file_name'] = $inv_filename;

                $emails = explode(",", $request->send_invoice_to_emails);

                $invpdf['inv']['from'] = $userinfo[1];
                $invpdf['inv']['subject'] = $request->send_invoice_subject;
                $invpdf['inv']['message'] = $request->send_invoice_message;

                // $mesg = explode("<br>", $request->send_invoice_message);

                Mail::to($emails)->send(new InvoiceMail($pdf, $invpdf['inv']));

                InvoiceReports::create([
                    'user_id' =>  $userinfo[0],
                    'transaction_collection_id' => $invoice_detail['id'],
                    'invoice_report_file' => $inv_filename
                ]);

                if(!$invoice_detail['invoice_sent'] && $invoice_detail['status'] == 'Recalled'){
                    TransactionCollections::where('id', $invoice_detail['id'])
                    ->where('user_id', $userinfo[0])
                    ->where('invoice_sent', 0)
                    ->where('status', 'Recalled')
                    ->update(['invoice_sent' => 1, 'status' => 'Unpaid']);
                }else{
                    TransactionCollections::where('id', $invoice_detail['id'])
                    ->where('user_id', $userinfo[0])
                    ->update(['invoice_sent' => 1]);
                }


                $date = Carbon::now()->toDateString();
                $time = Carbon::now()->toTimeString();
    
                $invoice_history = array(
                    "invoice_id" => trim($request->invoice_id),
                    "invoice_number" => trim($invoice_detail['transaction_number']),
                    "user_id" => trim($userinfo[0]),
                    "user_name" => trim($userinfo[1]),
                    "action" => "Invoice sent",
                    "description" => "This invoice has been sent to ".$request->send_invoice_to_emails,
                    "date" => $date,
                    "time" => $time
                );
                $this->createInvoiceHistory($invoice_history);

                return redirect()->route('invoice')->with('success', 'Invoice sent successfully');
            }
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

    public function invoiceItemForm(Request $request)
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
                        'invoice_item_tax_rate_id' => trim($request->invoice_item_tax_rate_id),
                        'invoice_item_description' => trim($request->invoice_item_description),
                        'invoice_item_chart_accounts_parts_id' => trim($request->invoice_item_chart_accounts_parts_id)
                    ]);
                if($item->id){
                    DB::commit();
                    $invoice_items = SumbInvoiceItems::with(['taxRates'])->where('user_id', $userinfo[0])->get();
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

    public function invoiceItemFormList(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $invoice_item_name = trim($request->invoice_item_name);
            $invoice_items = SumbInvoiceItems::where('user_id', $userinfo[0])
            ->orderBy('invoice_item_name')
            ->get();
            if($invoice_items)
            {
                $response = [
                    'status' => 'success',
                    'err' => '',
                    'data' => $invoice_items
                ];
                echo json_encode($response);
            }
            else
            {
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

    public function invoiceItemFormListById(Request $request, $id)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $invoice_item = SumbInvoiceItems::with(['taxRates', 'chartAccountsParts'])->where('user_id', $userinfo[0])
                            ->where('id', $id)
                            ->first();
            if($invoice_item)
            {
                $response = [
                    'status' => 'success',
                    'err' => '',
                    'data' => $invoice_item
                ];
                echo json_encode($response);
            }
            else
            {
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

    public function statusUpdate(Request $request)
    {
        $userinfo = $request->get('userinfo');
        if($request->invoice_id && $request->status){
            $status_updated = TransactionCollections::where('id', $request->invoice_id)
                    ->where('user_id', $userinfo[0])
                    ->update(['status' => $request->status]);
            if($status_updated){
                $updated_invoice = TransactionCollections::where('id', $request->invoice_id)->where('user_id', $userinfo[0])->first()->toArray();

                $date = Carbon::now()->toDateString();
                $time = Carbon::now()->toTimeString();
                
                if($request->status == 'Voided'){
                    $description = '';
                }else if($request->status == 'Paid'){
                    $description = 'Payment received on ' .$date. ' for '.trim($updated_invoice['total_amount']);
                }
                else if($request->status == 'Unpaid'){
                    $description = "INV-".str_pad($updated_invoice['transaction_number'], 6, '0', STR_PAD_LEFT).' to '.trim(ucfirst($updated_invoice['client_name'])).' for $ '.trim($updated_invoice['total_amount']);
                }

                $invoice_history = array(
                    "invoice_id" => trim($request->invoice_id),
                    "invoice_number" => trim($updated_invoice['transaction_number']),
                    "user_id" => trim($userinfo[0]),
                    "user_name" => trim($userinfo[1]),
                    "action" => $request->status,
                    "description" => $description,
                    "date" => $date,
                    "time" => $time
                );
                $this->createInvoiceHistory($invoice_history);
    
                return redirect()->route('invoice')->with('success', 'Invoice status updated successfully');
            }
        }
    }
    public function invoiceSearch(Request $request)
    {
        if($request->start_date){
            $request->start_date = Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d');
        }
        if($request->end_date){
            $request->end_date = Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d');
        }
        
        $userinfo = $request->get('userinfo');
        $data = SumbInvoiceDetails::where('user_id', $userinfo[0]);
        if($request->search_number_email_amount){
            $data->where('invoice_number', 'LIKE', "%{$request->search_number_email_amount}%");
            $data->orWhere('client_email', 'LIKE', "%{$request->search_number_email_amount}%");
            $data->orWhere('invoice_total_amount', $request->search_number_email_amount);
        }
        if($request->start_date){
            $data->whereBetween('invoice_issue_date',array($request->start_date, $request->end_date));
            // $data->where('invoice_issue_date', $request->start_date);
        }
        $data = $data->get();
        if($data){
            echo "<pre>";  var_dump($data->toArray()); echo "</pre>";
        }
    }

    public function delete(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $deleted = TransactionCollections::where('user_id', $userinfo[0])->where('id', $request->id)->whereIn('status', ['Unpaid', 'Voided'])->update(['is_active'=> 0]);
        if($deleted){
            $deleted_invoice = TransactionCollections::where('user_id', $userinfo[0])->where('id', $request->id)->first()->toArray();
            
            $date = Carbon::now()->toDateString();
            $time = Carbon::now()->toTimeString();

            $invoice_history = array(
                "invoice_id" => trim($request->id),
                "invoice_number" => trim($deleted_invoice['transaction_number']),
                "user_id" => trim($userinfo[0]),
                "user_name" => trim($userinfo[1]),
                "action" => 'Deleted',
                "description" => '',
                "date" => $date,
                "time" => $time
            );

            $this->createInvoiceHistory($invoice_history);

            return redirect()->route('invoice')->with('success', 'Invoice deleted successfully');
        }
        return redirect()->route('invoice');
    }

    public function invoiceTaxRates(Request $request)
    {
        if ($request->ajax())
        {
            $invoice_tax_rates = SumbInvoiceTaxRates::get();
            if($invoice_tax_rates)
            {
                $response = [
                    'status' => 'success',
                    'err' => '',
                    'data' => $invoice_tax_rates
                ];
                echo json_encode($response);
            }
            else
            {
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
    public function cloneInvoice(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        $pagedata['invoice_details'] = $request->post();
        $pagedata['invoice_id'] = '';
        $pagedata['type'] = 'create';

        $transaction_number = TransactionCollections::select('transaction_number')->where('user_id', $userinfo[0])->orderBy('transaction_number', 'desc')->first();

        $invoice_details = TransactionCollections::with(['transactions', 'transactions.chartAccountsParticulars'])
                                ->whereHas('transactions', function($query) use($userinfo) {
                                    $query->where('user_id', $userinfo[0]);
                                })
                                ->where('id', $request->invoice_id)
                                ->where('status', 'Voided')
                                ->where('is_active', 1)
                                ->where('user_id', $userinfo[0])->first();
                
            if (!empty($invoice_details)) {
                $invoice_details = $invoice_details->toArray();
                $invoice_details['invoice_ref_number'] = $invoice_details['transaction_number'];
                $invoice_details['invoice_clone'] = true;
                $invoice_details['status'] = 'Unpaid';
                $invoice_details['transaction_number'] = 000001 + $transaction_number['transaction_number'];
                $invoice_details['parts'] = $invoice_details['transactions'];
                $invoice_details['invoice_part_total_count'] = "[]";
                unset($invoice_details['transactions']);

                $pagedata['invoice_details'] = $invoice_details;    
            }
            $get_clients = SumbClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_clients)) {
            $pagedata['clients'] = $get_clients = $get_clients->toArray();
        }
        
        $get_items = SumbInvoiceItems::where('user_id', $userinfo[0])->orderBy('invoice_item_name')->get();
        if (!empty($get_items)) {
            $pagedata['invoice_items'] = $get_items->toArray();
        }

        $chart_accounts_types = SumbChartAccounts::with(['chartAccountsTypes'])->get();
        if (!empty($chart_accounts_types)) {
            $pagedata['chart_accounts_types'] = $chart_accounts_types->toArray();
        }

        $chart_account = SumbChartAccounts::with(['chartAccountsParticulars', 'chartAccountsTypes'])
                        ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
                            $query->where('user_id', $userinfo[0]);
                        })
                        ->whereHas('chartAccountsTypes', function($query) use($userinfo) {
                        })
                    ->get();
        if (!empty($chart_account)) {
            $pagedata['chart_account'] = $chart_account->toArray();
        }

        $tax_rates = SumbInvoiceTaxRates::get();
        if (!empty($tax_rates)) {
            $pagedata['tax_rates'] = $tax_rates->toArray();
        }

        $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();
        if (!empty($invoice_settings)) {
            $pagedata['invoice_settings'] = $invoice_settings->toArray();
        }

        $date = Carbon::now()->toDateString();
        $time = Carbon::now()->toTimeString();

        $invoice_history = array(
            "invoice_number" => trim($invoice_details['transaction_number']),
            "user_id" => trim($userinfo[0]),
            "user_name" => trim($userinfo[1]),
            "action" => "Cloned invoice from ". $invoice_details['invoice_ref_number'],
            "date" => $date,
            "time" => $time
        );
        $this->createInvoiceHistory($invoice_history);

        return view('invoice.invoicecreate', $pagedata);
    }

    public function createInvoiceHistory($invoice_history){

        InvoiceHistory::create($invoice_history);
    }

    public function recallInvoice(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $invoice_detail = TransactionCollections::where('id', $request->id)
                                ->where('invoice_sent', 1)
                                ->where('status', 'Unpaid')
                                ->where('user_id', $userinfo[0])->first();
            if (!empty($invoice_detail)) {
                $invoice_detail = $invoice_detail->toArray();

                // $request->invoice_format = !empty($invoice_settings) && $invoice_settings['business_invoice_format'] ? $invoice_settings['business_invoice_format'] : 'format002';
                // $logoimg = base64_encode(file_get_contents('uploads/a71ed73925a75dae44b71bc161131adb.png'));
                $invpdf['inv'] = [];

                //$get_invoice_parts = SumbInvoiceParticularsTemp::where('user_id', $userinfo[0])->where('invoice_number', $get_settings['invoice_count'])->get();
               
                // $pdf = Pdf::loadView('pdf.'.$request->invoice_format, $invpdf);
                // $pdf->save(env('APP_PDF_DIRECTORY').$inv_filename);
                // $transactiondata['invoice_pdf'] = $inv_filename;
                // $invpdf['inv']['file_name'] = $inv_filename;

                $email = $invoice_detail['client_email'];

                // $invpdf['inv']['from'] = $userinfo[1];
                $invpdf['inv']['subject'] = 'Invoice INV-'.str_pad($invoice_detail['transaction_number'], 6, '0', STR_PAD_LEFT).' has been recalled.';
                $invpdf['inv']['message'] = 'The invoice INV-'.str_pad($invoice_detail['transaction_number'], 6, '0', STR_PAD_LEFT).' sent to you on '.$invoice_detail['issue_date']. ' has been recalled. 
                                            
                                            A new invoice will be sent to you. 
                                            
                                            If you have paid the invoice, please reply to this message.';

                // $mesg = explode("<br>", $request->send_invoice_message);

                Mail::to($email)->send(new RecallInvoiceMail($invpdf['inv']));

                $updated = TransactionCollections::where('id', $request->id)
                            ->where('invoice_sent', 1)
                            ->where('status', 'Unpaid')
                            ->where('user_id', $userinfo[0])
                            ->update(['invoice_sent' => 0, 'status' => 'Recalled']);
                if($updated)
                {
                    return redirect("/invoice/$request->id/edit");
                }
            }
        // return redirect()->route('invoice')->with(['email-sent'=> true, 'invoice_id' => 28]);
    }
}
