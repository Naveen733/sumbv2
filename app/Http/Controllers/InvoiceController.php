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
use App\Models\SumbInvoiceSettings;
use App\Models\SumbTransactions;
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
            if($request->search_number_email_amount || $request->start_date || $request->end_date || $request->orderBy){
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
                $invoicedata = SumbInvoiceDetails::
                                where('user_id', $userinfo[0])->where('is_active', 1);
                                if($request->search_number_email_amount){
                                    $invoicedata->where('invoice_number', 'LIKE', "%{$invoice_number}%");
                                    $invoicedata->orWhere('client_email', 'LIKE', "%{$request->search_number_email_amount}%");
                                    $invoicedata->orWhere('invoice_total_amount', $total_amount);
                                }
                                if($request->start_date && $request->end_date){
                                    $invoicedata->whereBetween('invoice_issue_date',array($start_date, $end_date));
                                }
                                if($request->orderBy){
                                    $invoicedata->orderBy($request->orderBy, $request->direction);
                                }
                                $invoicedata = $invoicedata->paginate($itemsperpage)->toArray();

                $pagedata['search_number_email_amount'] = $request->search_number_email_amount;
                $pagedata['start_date'] = $request->start_date;
                $pagedata['end_date'] = $request->end_date;
                $pagedata['orderBy'] = $request->orderBy;
                if($request->direction == 'ASC')
                {
                    $pagedata['direction'] = 'DESC';
                }
                if($request->direction == 'DESC')
                {
                    $pagedata['direction'] = 'ASC';
                }
                
            }else{
                $pagedata['orderBy'] = 'invoice_issue_date';
                $pagedata['direction'] = 'ASC';

                $invoicedata = SumbInvoiceDetails::
                // with(['particulars'])
                // ->whereHas('particulars', function($query) use($userinfo) {
                //     $query->where('user_id', $userinfo[0]);
                // })
                // ->
                where('user_id', $userinfo[0])->where('is_active', 1)
                ->orderBy('invoice_issue_date', 'DESC')
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
        // echo "<pre>"; var_dump($pagedata); echo "</pre>";die();
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
            $invoice_details = SumbInvoiceDetails::with(['particulars', 'particulars.invoiceChartAccountsParticulars'])
                                ->whereHas('particulars', function($query) use($userinfo) {
                                    $query->where('user_id', $userinfo[0]);
                                })
                                ->where('id', $request->id)
                                ->where('user_id', $userinfo[0])->first()->toArray();
            if (!empty($invoice_details)) {
                $invoice_details['parts'] = $invoice_details['particulars'];
                $invoice_details['invoice_part_total_count'] = "[]";
                unset($invoice_details['particulars']);
                $pagedata['invoice_details'] = $invoice_details;

                // echo "<pre>"; var_dump($invoice_details['parts']); echo "</pre>";die();
            }
            
        }else{
            $invoice_details = SumbInvoiceDetails::where('user_id', $userinfo[0])->orderBy('invoice_number', 'desc')->first();
            if (!empty($invoice_details)) {
                $pagedata['invoice_number'] = 000001 + $invoice_details->toArray()['invoice_number'];
            }else{
                $pagedata['invoice_number'] = 000001;
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

        $chart_accounts_types = SumbChartAccounts::with(['chartAccountsTypes'])
                    ->where('user_id', $userinfo[0])->get();
        if (!empty($chart_accounts_types)) {
            $pagedata['chart_accounts_types'] = $chart_accounts_types->toArray();
        }

        $chart_account = SumbChartAccounts::with(['chartAccountsParticulars', 'chartAccountsTypes'])
                        ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
                            $query->where('user_id', $userinfo[0]);
                        })
                        ->whereHas('chartAccountsTypes', function($query) use($userinfo) {
                            $query->where('user_id', $userinfo[0]);
                        })
                        ->where('user_id', $userinfo[0])->get();
        if (!empty($chart_account)) {
            $pagedata['chart_account'] = $chart_account->toArray();
        }

        $tax_rates = SumbInvoiceTaxRates::get();
        if (!empty($tax_rates)) {
            $pagedata['tax_rates'] = $tax_rates->toArray();
        }

        // $pagedata['invoice_details']['image'] = env('APP_PUBLIC_DIRECTORY') . 'a71ed73925a75dae44b71bc161131adb.png';
        // $pagedata['form_data'] = $pagedata;
        // echo "<pre>"; var_dump($pagedata['tax_rates']); echo "</pre>";die();

        return $pagedata;
        // return view('invoice.invoicecreate', $pagedata);
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
                // 'invoice_parts_unit_price_.*' => 'bail|required|max:255',
                // 'invoice_parts_description_.' => 'bail|required',
                // 'invoice_parts_amount_.*' => 'bail|required',
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
                        'id' => trim($request->input('invoice_parts_id_'.$id)),
                        'invoice_parts_quantity' => trim($request->input('invoice_parts_quantity_'.$id)),
                        'invoice_parts_unit_price' => trim($request->input('invoice_parts_unit_price_'.$id)),
                        'invoice_parts_description' => trim($request->input('invoice_parts_description_'.$id)),
                        'invoice_parts_amount' => trim($request->input('invoice_parts_amount_'.$id)),
                        'invoice_parts_tax_rate' => trim($request->input('invoice_parts_tax_rate_'.$id)),
                        'invoice_parts_code' => $request->input('invoice_parts_code_'.$id),
                        'invoice_parts_name' => $request->input('invoice_parts_name_'.$id),
                        'invoice_parts_name_code' => $request->input('invoice_parts_name_code_'.$id),
                        'invoice_chart_accounts_parts_id' => $request->input('invoice_parts_chart_accounts_parts_id_'.$id),
                        'invoice_parts_chart_accounts' => trim($request->input('invoice_parts_chart_accounts_'.$id)),
                        'invoice_parts_tax_rate_id' => trim($request->input('invoice_parts_tax_rate_id_'.$id)),
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
            // echo "<pre>"; var_dump($invoice_details['parts']); echo "</pre>";die();
            $invoice_details['invoice_part_total_count'] = trim($request->input('invoice_part_total_count'));
            $invoice_details['invoice_status'] = $request->invoice_status;
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
                    $query->where('user_id', $userinfo[0]);
                })
                ->where('user_id', $userinfo[0])->get();
                if (!empty($chart_account)) {
                    $pagedata['chart_account'] = $chart_account->toArray();
                }

                $chart_accounts_types = SumbChartAccounts::with(['chartAccountsTypes'])
                ->where('user_id', $userinfo[0])->get();
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
            // 
            $invoice_details['invoice_issue_date'] =  Carbon::createFromFormat('m/d/Y', $request->invoice_issue_date)->format('Y-m-d');
            $invoice_details['invoice_due_date'] =  Carbon::createFromFormat('m/d/Y', $request->invoice_due_date)->format('Y-m-d');
            
           
            $particlars = $invoice_details['parts'];
            
            unset($invoice_details['parts']);
            unset($invoice_details['invoice_part_total_count']);
            $ids = [];
            if($request->invoice_id && $request->type=='edit'){
                $invoice_update = SumbInvoiceDetails::where('user_id', trim($userinfo[0]))
                                ->where('id', $request->invoice_id)
                                ->update(
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
                                    ]
                                );
                if($invoice_update){
                    foreach($particlars as $key=>$value){
                        $newParticulars = SumbInvoiceParticulars::create(
                            [
                                'user_id' => trim($userinfo[0]), 
                                'invoice_id' => $request->invoice_id,
                                'invoice_parts_quantity' => trim($value['invoice_parts_quantity']),
                                'invoice_parts_description' => trim($value['invoice_parts_description']),
                                'invoice_parts_unit_price' => trim($value['invoice_parts_unit_price']),
                                'invoice_parts_amount' => trim($value['invoice_parts_amount']),
                                'invoice_parts_code' => (!empty($value['invoice_parts_code']) ? $value['invoice_parts_code'] : $value['invoice_parts_name_code']),
                                'invoice_parts_name' => trim($value['invoice_parts_name']),
                                'invoice_parts_tax_rate' => trim($value['invoice_parts_tax_rate']),
                                'invoice_chart_accounts_parts_id' => trim($value['invoice_chart_accounts_parts_id']),
                                'invoice_parts_tax_rate_id' => trim($value['invoice_parts_tax_rate_id'])
                            ]);
                        array_push($ids,  $newParticulars->id);
                    }
                    if(!empty($ids)){
                        SumbInvoiceParticulars::whereNotIn('id', $ids)
                                        ->where('invoice_id', $request->invoice_id)
                                        ->where('user_id', trim($userinfo[0]))
                                        ->delete();
                    }
                    DB::commit();
                }
            }else{
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
                    ]
                );
                if($invoice->id){
                    foreach($particlars as $key=>$value){
                        SumbInvoiceParticulars::create(
                        [
                            'user_id' => trim($userinfo[0]), 
                            'invoice_id' => $invoice->id,
                            'invoice_parts_quantity' => trim($value['invoice_parts_quantity']),
                            'invoice_parts_description' => trim($value['invoice_parts_description']),
                            'invoice_parts_unit_price' => trim($value['invoice_parts_unit_price']),
                            'invoice_parts_amount' => trim($value['invoice_parts_amount']),
                            'invoice_parts_code' => (!empty($value['invoice_parts_code']) ? $value['invoice_parts_code'] : $value['invoice_parts_name_code']),
                            'invoice_parts_name' => trim($value['invoice_parts_name']),
                            'invoice_parts_tax_rate' => trim($value['invoice_parts_tax_rate']),
                            'invoice_chart_accounts_parts_id' =>trim($value['invoice_chart_accounts_parts_id']),
                            'invoice_parts_tax_rate_id' => trim($value['invoice_parts_tax_rate_id'])
                        ]);
                    }
                }
                DB::commit();
            }
        }
        return redirect()->route('invoice');
    }

    public function sendInvoice(Request $request)
    {
        $userinfo = $request->get('userinfo');
        if($request->invoice_id){
            $invoice_settings = SumbInvoiceSettings::where('user_id', $userinfo[0])->first();

            // $invoice_exists = SumbInvoiceDetails::find($request->invoice_id);
            $invoice_detail = SumbInvoiceDetails::with(['particulars'])
                                ->whereHas('particulars', function($query) use($userinfo) {
                                    $query->where('user_id', $userinfo[0]);
                                })
                                ->where('id', $request->invoice_id)
                                ->where('user_id', $userinfo[0])->first()->toArray();
            if (!empty($invoice_detail)) {
                $request->invoice_format = !empty($invoice_settings) ? $invoice_settings['business_invoice_format'] : 'format001';
                $logoimg = base64_encode(file_get_contents('uploads/a71ed73925a75dae44b71bc161131adb.png'));
                $invpdf['inv'] = [
                    'logo' => 'a71ed73925a75dae44b71bc161131adb.png',
                    'invoice_number' => $invoice_detail['invoice_number'],
                    'client_name' => $invoice_detail['client_name'],
                    'client_email' => $invoice_detail['client_email'],
                    'client_address' => 'test',
                    'client_phone' => $invoice_detail['client_phone'],
                    'invoice_sub_total' => $invoice_detail['invoice_sub_total'],
                    'invoice_total_gst' => $invoice_detail['invoice_total_gst'],
                    'invoice_total_amount' => $invoice_detail['invoice_total_amount'],
                    'invoice_name' => !empty($invoice_settings) ? $invoice_settings['business_name'] : $userinfo[1], 
                    'invoice_email' => !empty($invoice_settings) ? $invoice_settings['business_email'] : $userinfo[2],
                    'invoice_phone' => !empty($invoice_settings) ? $invoice_settings['business_phone'] : '',
                    'invoice_address' => !empty($invoice_settings) ? $invoice_settings['business_address'] : '',
                    'invoice_abn' => !empty($invoice_settings) ? $invoice_settings['business_abn'] : '',
                    'invoice_terms' => !empty($invoice_settings) ? $invoice_settings['business_terms_conditions'] : '',
                    'invoice_format' => $request->invoice_format,
                    'invoice_date' => $invoice_detail['invoice_issue_date'],
                    'invoice_due_date' => $invoice_detail['invoice_due_date'],
                    'inv_parts' => $invoice_detail['particulars']
                ];
                $invpdf['inv']['logoimgdet'] = getimagesize('uploads/a71ed73925a75dae44b71bc161131adb.png');
                $invpdf['inv']['logobase64'] = 'data:'.$invpdf['inv']['logoimgdet']['mime'].';charset=utf-8;base64,' . $logoimg;
                $inv_filename = 'inv'.date('YmdHis')."-".$invoice_detail['invoice_number']."-".md5(date('YmdHis')).".pdf";
                
                //$get_invoice_parts = SumbInvoiceParticularsTemp::where('user_id', $userinfo[0])->where('invoice_number', $get_settings['invoice_count'])->get();
                $invpdf['inv']['image'] = public_path().env('APP_PUBLIC_DIRECTORY') . 'a71ed73925a75dae44b71bc161131adb.png';
                
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

                SumbInvoiceReports::create([
                    'user_id' =>  $userinfo[0],
                    'invoice_id' => $invoice_detail['id'],
                    'invoice_report_file' => $inv_filename
                ]);

                SumbInvoiceDetails::where('id', $invoice_detail['id'])
                                ->where('user_id', $userinfo[0])
                                ->update(['invoice_sent' => 1]);

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
                $invoice_item = SumbInvoiceItems::with(['taxRates'])->where('user_id', $userinfo[0])
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

    public function statusUpdate(Request $request)
    {
        $userinfo = $request->get('userinfo');
        if($request->invoice_id && $request->status){
            SumbInvoiceDetails::where('id', $request->invoice_id)
                    ->where('user_id', $userinfo[0])
                    ->update(['invoice_status' => $request->status]);
            return redirect()->route('invoice')->with('success', 'Invoice status updated successfully');;
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
        $data = SumbInvoiceDetails::
        where('user_id', $userinfo[0]);
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
        $deleted = SumbInvoiceDetails::where('user_id', $userinfo[0])->where('id', $request->id)->update(['is_active'=> 0]);
        if($deleted){
            return redirect()->route('invoice')->with('success', 'Invoice deleted successfully');
        }
        return redirect()->route('invoice');
    }

    public function invoiceTaxRates(Request $request)
    {
        if ($request->ajax())
        {
                $invoice_tax_rates = SumbInvoiceTaxRates::get();
                if($invoice_tax_rates){
                    $response = [
                        'status' => 'success',
                        'err' => '',
                        'data' => $invoice_tax_rates
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
