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
use App\Models\SumbExpensesClients;
use App\Models\SumbExpenseDetails;
use App\Models\SumbExpenseParticulars;
use App\Models\SumbExpenseSettings;
use App\Models\SumbChartAccounts;
use App\Models\SumbInvoiceTaxRates;
use App\Models\Transactions;
use App\Models\TransactionCollections;
use App\Models\SumbChartAccountsTypeParticulars;

class ExpenseController extends Controller {

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
            'pagetitle' => 'Expenses'
        );
        $errors = array(
            1 => ['A new expense has been saved.', 'primary'],
            2 => ['The expense was deleted.', 'danger'],
            3 => ['The expense is now paid.', 'primary'],
            4=> ['The expense is now voided.', 'primary'],
            5 => ['The expense is now unpaid.', 'primary'],
            6 => ['The expense was updated.', 'primary'],
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
        $pagedata['myurl'] = route('expense');
        $pagedata['ourl'] = route('expense', $purl);
        $pagedata['npurl'] = http_build_query(['ipp'=>$itemsperpage]);

        $pagedata['search_number_email_amount'] = '';
        $pagedata['start_date'] = '';
        $pagedata['end_date'] = '';
        $pagedata['orderBy'] = '';
        $pagedata['direction'] = '';
        
        //==== get all tranasactions
        $ptype = 'all';
        if (!empty($request->input('type'))) {
            $expensedata = TransactionCollections::where('user_id', $userinfo[0])->where('transaction_type', 'expense')->where('is_active', 1)->paginate($itemsperpage);
            if(!empty($expensedata)){
                $expensedata = $expensedata->toArray();
            }
            
            $ptype = $request->input('type');
        } else {
                if($request->search_number_name_amount || $request->start_date || $request->end_date || $request->orderBy)
                {
                    if($request->start_date){
                        $start_date = Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d');
                    }
                    if($request->end_date){
                        $end_date = Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d');
                    }
                    // var_dump($request->start_date);die();
                    $total_amount = $request->search_number_name_amount;
                    $expense_number = $request->search_number_name_amount;

                    if($request->search_number_name_amount){
                        if(is_numeric(trim($request->search_number_name_amount))){
                            $total_amount = trim($request->search_number_name_amount);
                            $expense_number = $total_amount;
                        } 
                        else if(is_string(trim($request->search_number_name_amount))){
                            $expense_number = trim(strtolower($request->search_number_name_amount));                        
                        }
                    }
                        $userinfo = $request->get('userinfo');
                        $expensedata = TransactionCollections::
                                where('user_id', $userinfo[0])
                                ->where('transaction_type', 'expense')
                                ->where('is_active', 1);
                                if($request->search_number_name_amount && $request->start_date && $request->end_date){
                                    // $expensedata->where('expense_date','>=',$start_date);
                                    // $expensedata->where('expense_date','<=',$end_date);
                                    // $expensedata->where('expense_number', 'LIKE', "%{$expense_number}%");
                                    // $expensedata->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%");
                                    // $expensedata->orWhere('expense_total_amount', 'LIKE', "{$total_amount}");
                                    
                                    $expensedata->where('issue_date','>=',$start_date)
                                    ->where('issue_date','<=',$end_date)
                                    ->where(function($query) use ($expense_number, $total_amount, $request) {
                                        $query->where('transaction_number', 'LIKE', "%{$expense_number}%")
                                              ->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%")
                                              ->orWhere('total_amount', 'LIKE', "{$total_amount}");
                                    });
                                }
                                if($request->search_number_name_amount && !$request->start_date && !$request->end_date){
                                    $expensedata->where('transaction_number', 'LIKE', "%{$expense_number}%");
                                    $expensedata->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%");
                                    $expensedata->orWhere('total_amount', 'LIKE', "{$total_amount}");
                                }
                                if($request->start_date && $request->end_date && !($request->search_number_name_amount)){
                                    $expensedata->whereBetween('issue_date',array($start_date, $end_date));
                                }
                                if($request->start_date && !$request->search_number_name_amount && !$request->end_date){
                                    $expensedata->where('issue_date','>=',$start_date);
                                }
                                if($request->end_date && !$request->search_number_name_amount && !$request->start_date){
                                    $expensedata->where('issue_date','<=',$end_date);
                                }
                                if(!$request->start_date && $request->search_number_name_amount && $request->end_date){
                                    $expensedata->where('issue_date','<=',$end_date)
                                    ->where(function($query) use ($expense_number, $total_amount, $request) {
                                        $query->where('transaction_number', 'LIKE', "%{$expense_number}%")
                                              ->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%")
                                              ->orWhere('total_amount', 'LIKE', "{$total_amount}");
                                    });
                                }
                                if(!$request->end_date && $request->search_number_name_amount && $request->start_date){
                                    $expensedata->where('issue_date','>=',$start_date)
                                    ->where(function($query) use ($expense_number, $total_amount, $request) {
                                        $query->where('transaction_number', 'LIKE', "%{$expense_number}%")
                                              ->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%")
                                              ->orWhere('total_amount', 'LIKE', "{$total_amount}");
                                    });
                                }
                                if($request->orderBy){
                                    $expensedata->orderBy($request->orderBy, $request->direction);
                                }
                    $expensedata = $expensedata->paginate($itemsperpage)->toArray();

                    $pagedata['search_number_name_amount'] = $request->search_number_name_amount;
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
                
                }
                else
                {
                    $pagedata['orderBy'] = 'issue_date';
                    $pagedata['direction'] = 'ASC';
                    
                    $expensedata = TransactionCollections::where('user_id', $userinfo[0])
                        ->where('transaction_type', 'expense')
                        ->where('is_active', 1)
                        ->orderBy('issue_date', 'DESC')
                    ->paginate($itemsperpage)->toArray();
                }
        }
        
        $pagedata['expensedata'] = $expensedata;

        $allrequest = $request->all();
        $pfirst = $allrequest; $pfirst['page'] = 1;
        $pprev = $allrequest; $pprev['page'] = $expensedata['current_page']-1;
        $pnext = $allrequest; $pnext['page'] = $expensedata['current_page']+1;
        $plast = $allrequest; $plast['page'] = $expensedata['last_page'];
        $pagedata['paging'] = [
            'current' => url()->current().'?'.http_build_query($allrequest),
            'starpage' => url()->current().'?'.http_build_query($pfirst),
            'first' => ($expensedata['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pfirst),
            'prev' => ($expensedata['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pprev),
            'now' => 'Page '.$expensedata['current_page']." of ".$expensedata['last_page'],
            'next' => ($expensedata['current_page'] >= $expensedata['last_page']) ? '' : url()->current().'?'.http_build_query($pnext),
            'last' => ($expensedata['current_page'] >= $expensedata['last_page']) ? '' : url()->current().'?'.http_build_query($plast),
        ];

        return view('invoice.expenselist', $pagedata); 
    }
    
    //***********************************************
    //*
    //* Create Expenses Page
    //*
    //***********************************************
    public function create_expense(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Expenses'
        );
        $dtnow = Carbon::now();
        // $dtnow = Carbon::now();
        // if(!SumbExpenseSettings::where('user_id', $userinfo[0])->first()){
        //     SumbExpenseSettings::insert(['user_id'=>$userinfo[0], 'created_at'=>$dtnow, 'updated_at'=>$dtnow]);
        // }
       

        // $errors = array(
        //     1 => ['Values are required to process invoice or expenses, please fill in non-optional fields.', 'danger'],
        //     2 => ['Your amount is incorrect, it should be numeric only and no negative value. Please try again', 'danger'],
        //     3 => ['A new expenses has been saved.', 'primary'],
        // );
        // $pagedata['errors'] = $errors;
        // if (!empty($request->input('err'))) { $pagedata['err'] = $request->input('err'); }

        $chart_account = SumbChartAccountsTypeParticulars::where('user_id', $userinfo[0])
        ->whereIn('chart_accounts_particulars_code', ['400','404','408','412','420','425','429','433','437','441','445',
        '449','453','461','469','473','485','489','493','494','710','720'])->get();
        // $chart_account = SumbChartAccounts::with(['chartAccountsParticulars'])
        //             ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
        //                 $query->where('user_id', $userinfo[0])
        //                 ->whereIn('chart_accounts_particulars_code', ['400','404','408','412','420','425','429','433','437','441','445',
        //                                                 '449','453','461','469','473','485','489','493','494','710','720']);
        //             })
        //         ->get();
        if (!empty($chart_account)) {
            $pagedata['chart_account'] = $chart_account->toArray();
        }

        $tax_rates = SumbInvoiceTaxRates::get();
        if (!empty($tax_rates)) {
            $pagedata['tax_rates'] = $tax_rates->toArray();
        }

        $get_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first();
        if(!empty($get_settings)){
            $pagedata['data'] = $get_settings->toArray();
        }
        else{
            $user = SumbUsers::where('id', $userinfo[0])->first();
            if(!empty($user)){
               // echo $user['id'];die();
                SumbExpenseSettings::insert(['user_id'=>$user['id'], 'created_at'=>$dtnow, 'updated_at'=>$dtnow]);
                $get_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first();
                if(!empty($get_settings)){
                    $pagedata['data'] = $get_settings->toArray();
                }
            }
        }
        
       $get_expclients = SumbExpensesClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients->toArray();
        }
        $pagedata['type'] = 'create';
        return view('invoice.expensescreate', $pagedata);
    }
    
    //***********************************************
    //*
    //* Create Expenses Process
    //*
    //***********************************************
    public function save_expense(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Expense'
        );

        //validation
        $validator = $request->validate([
            'expense_number' => 'bail|required',
            'expense_date' => 'bail|required|date',
            'expense_due_date' => 'bail|required|date',
            'client_name' => 'bail|required|max:100',
            'expense_description.*' => 'bail|required',
            'item_quantity.*' => 'bail|required|numeric',
            'item_unit_price.*' => 'bail|required|numeric',
            'expense_tax.*' => 'bail|required|gt:-1',
            'expense_amount.*' => 'bail|required',
            'tax_type' => 'bail|required',
            'expense_total_amount.*' => 'bail|required|numeric',
            'total_gst.*' => 'bail|required|numeric',
            'total_amount.*' => 'bail|required|numeric',
            'file_upload' =>  'mimes:jpg,jpeg,png,pdf',
            'item_account.*' => 'bail|required',
        ]
        // ,
        // [
        //    'expense_number' => 'Expense Number is Required',
        //    'expense_date.required' => 'Date is Required',
        //    'expense_date.date' => 'Enter proper date format',
        //    'expense_due_date.required' => 'Due Date is Required',
        //    'expense_due_date.date' => 'Enter proper Due date format',
        //    'client_name.required' => 'Recepient Name is Required',
        //    'client_name.max' => 'Recepient Name must to exceed 100 characters',
        //    'expense_description.*.required' => 'Item Description is Required',
        //    'item_quantity.*.required' => 'Item Quantity is Required',
        //    //'item_quantity.*.gt' => 'Item Quantity must be greater than 0',
        //    'item_quantity.*.numeric' => 'Item Quantity must be a numeric value',
        //    'item_unit_price.*.required' => 'Item Unit Price is Required',
        //    //'item_unit_price.*.gt' => 'Item Unit Price must be greater than 0',
        //    'item_unit_price.*.numeric' => 'Item Unit Price must be a numeric value',
        //    'expense_tax.*.required' => 'Tax rate is Required',
        //    'expense_tax.*.gt' => 'Tax rate must be selected',
        //    'file_upload' =>  'Please insert image/pdf only'
        // ]
    );

        $expense_details = [];
        $dtnow = Carbon::now();

        $expense_date_exploded = explode("/", $request->expense_date);
        $expense_due_date_exploded = explode("/", $request->expense_due_date);
        $carbon_expense_date = Carbon::createFromDate($expense_date_exploded[2], $expense_date_exploded[0], $expense_date_exploded[1]);
        $carbon_expense_due_date = Carbon::createFromDate($expense_due_date_exploded[2], $expense_due_date_exploded[0], $expense_due_date_exploded[1]);

        $get_exp_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first()->toArray();

        if ($request->hasFile('file_upload')) {
            // Get the file instance
            $file = $request->file('file_upload');

            // Store the file in the public directory
            $path = $file->store('public');

            // Get the file URL
            $url = Storage::url($path);
        }

        $expense_details = array(
            "user_id" => $userinfo[0],
            "transaction_id" => $get_exp_settings['expenses_count'],
            "expense_number" => $request->expense_number,
            "client_name" => $request->client_name,
            "expense_date" => $carbon_expense_date,
            "expense_due_date" => $carbon_expense_due_date,
            "tax_type" => $request->tax_type,
            "expense_total_amount" => $request->expense_total_amount,
            "total_gst" => $request->total_gst,
            "total_amount" => $request->total_amount,
            "file_upload" => (isset($url) ? $url : ''),
            "file_upload_format" => (isset($file) ? $file->extension() : ''),
            "created_at" => $dtnow,
            "updated_at" => $dtnow,
            "status_paid" => (($request->total_amount != 0) ? 'unpaid' : 'paid')
        );

        $pagedata['expense_details'] = $expense_details;

        DB::beginTransaction();
        //if save reciepient is on
        if (!empty($request->savethisrep)) {
            $getexp_clients = SumbExpensesClients::where(DB::raw('UPPER(client_name)'), strtoupper($request->client_name))
                ->where('user_id',$userinfo[0])->first();
            print_r($getexp_clients);
            if (empty($getexp_clients)) {
                $dataprep_client = [
                    'user_id'               => $userinfo[0],
                    'client_name'           => $request->client_name,
                   // 'client_description'    => $request->invoice_details,
                    'created_at'            => $dtnow,
                    'updated_at'            => $dtnow,
                ];
                SumbExpensesClients::insert($dataprep_client);
            }
        }
        
        //saving data
        $expense_transaction_collection = TransactionCollections::create(
            [
                'user_id' => trim($userinfo[0]), 
                'client_name' => trim($request->client_name),
                'client_email' => trim('Test@gmail.com'),
                // 'client_phone' => trim($request->client_phone),
                'issue_date' => trim($carbon_expense_date),
                'due_date' => trim($carbon_expense_due_date),
                'transaction_number' => trim($get_exp_settings['expenses_count']),
                'default_tax' => trim($request->tax_type) == "0" ? 'tax_inclusive' : 'tax_exclusive',
                'sub_total' => trim($request->expense_total_amount),
                'total_gst' => trim($request->total_gst ? $request->total_gst : 0),
                'total_amount' => trim($request->total_amount),
                'logo' => (isset($url) ? $url : ''),
                'transaction_type' => 'expense',
            ]
        );        
       
        for ($i = 0; $i < count($request->item_quantity); $i++) {
            
            Transactions::create(
            [
                'user_id' => trim($userinfo[0]), 
                'transaction_collection_id' => $expense_transaction_collection->id,
                'parts_quantity' => trim($request->item_quantity[$i]),
                'parts_description' => trim($request->expense_description[$i]),
                'parts_unit_price' => trim($request->item_unit_price[$i]),
                'parts_amount' => trim($request->expense_amount[$i]),
                // 'parts_code' => (!empty($value['parts_code']) ? $value['parts_code'] : $value['parts_name']),
                // 'parts_name' => trim($value['parts_name']),
                // 'parts_tax_rate' => trim($value['invoice_parts_tax_rate']),
                'parts_chart_accounts_id' => trim($request->item_account[$i]),
                'parts_tax_rate_id' => trim($request->expense_tax[$i]),
                'parts_gst_amount' => 1,
            ]);
        }

        $updatethis = SumbExpenseSettings::where('user_id', $userinfo[0])->first();
        $updatethis->increment('expenses_count');

        DB::commit();
       
        return redirect()->route('expense', ['err'=>1]); die();
    }

    //***********************************************
    //*
    //* Edit Expense Page
    //*
    //***********************************************
    public function edit_expense(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $id = $request->id;
        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Edit Expense'
        );
        $expense_particulars = [];
       
        $pagedata['expense_details'] = TransactionCollections::with(['transactions', 'transactions.chartAccountsParticulars', 'transactions.invoiceTaxRates'])
                            ->whereHas('transactions', function($query) use($userinfo) {
                                $query->where('user_id', $userinfo[0]);
                            })
                            ->where('id', $id)
                            ->where('status','Unpaid')->where('is_active', 1)
                            ->where('transaction_type', 'expense')
                            ->where('user_id', $userinfo[0])->first();
            if (!empty($pagedata['expense_details'])) {
                $pagedata['expense_particulars'] = $pagedata['expense_details']->toArray()['transactions'];
            }
        
        $pagedata['data'] = $get_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first()->toArray();
       
        $chart_account = SumbChartAccountsTypeParticulars::where('user_id', $userinfo[0])
                ->whereIn('chart_accounts_particulars_code', ['400','404','408','412','420','425','429','433','437','441','445',
                '449','453','461','469','473','485','489','493','494','710','720'])->get();

        // $chart_account = SumbChartAccounts::with(['chartAccountsParticulars'])
        //         ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
        //             $query->where('user_id', $userinfo[0])
        //             ->whereIn('chart_accounts_particulars_code', ['400','404','408','412','420','425','429','433','437','441','445',
        //             '449','453','461','469','473','485','489','493','494','710','720']);
        //         })
        //     ->get();
        if (!empty($chart_account)) {
            $pagedata['chart_account'] = $chart_account->toArray();
        }

        $tax_rates = SumbInvoiceTaxRates::get();
        if (!empty($tax_rates)) {
            $pagedata['tax_rates'] = $tax_rates->toArray();
        }

        $get_expclients = SumbExpensesClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients->toArray();
        }
        $pagedata['type'] = 'edit';
        $pagedata['from'] = !empty($request->from) ? $request->from : '';
       
        return view('invoice.expensescreate', $pagedata);

        //  echo "<pre>"; var_dump($pagedata['expense_details']); echo "</pre>";
        //  die();
        
    }
    //***********************************************
    //*
    //* View Expense Page
    //*
    //***********************************************
    public function view_expense(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $id = $request->id;
        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Edit Expense'
        );

        $pagedata['expense_details'] = TransactionCollections::with(['transactions', 'transactions.chartAccountsParticulars'])
                                ->whereHas('transactions', function($query) use($userinfo) {
                                    $query->where('user_id', $userinfo[0]);
                                })
                                ->where('id', $id)
                                ->where('status', '!=', 'Unpaid')->where('is_active', 1)
                                ->where('transaction_type', 'expense')
                                ->where('user_id', $userinfo[0])->first();
            if (!empty($pagedata['expense_details'])) {
                $pagedata['expense_particulars'] = $pagedata['expense_details']->toArray()['transactions'];
            }
            $pagedata['data'] = $get_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first()->toArray();

            $chart_account = SumbChartAccountsTypeParticulars::where('user_id', $userinfo[0])
                    ->whereIn('chart_accounts_particulars_code', ['400','404','408','412','420','425','429','433','437','441','445',
                    '449','453','461','469','473','485','489','493','494','710','720'])->get();

            // $chart_account = SumbChartAccounts::with(['chartAccountsParticulars'])
            //     ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
            //         $query->where('user_id', $userinfo[0])
            //         ->whereIn('chart_accounts_particulars_code', ['400','404','408','412','420','425','429','433','437','441','445',
            //                                             '449','453','461','469','473','485','489','493','494','710','720']);
            //     })
            //     ->get();

            if (!empty($chart_account)) {
                $pagedata['chart_account'] = $chart_account->toArray();
            }

            $tax_rates = SumbInvoiceTaxRates::get();
            if (!empty($tax_rates)) {
                $pagedata['tax_rates'] = $tax_rates->toArray();
            }

            $get_expclients = SumbExpensesClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
            if (!empty($get_expclients)) {
                $pagedata['exp_clients'] = $get_expclients->toArray();
            }
            $pagedata['type'] = 'view';
        return view('invoice.expensescreate', $pagedata);
    }
//***********************************************
    //*
    //* Delete Expense 
    //*
    //***********************************************
    public function delete_expense(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $id = $request->id;
        $pagedata = array(
            'userinfo' => $userinfo,
            'pagetitle' => 'Delete Expense'
        );
       
        $expense_details = array("is_active" => 0);

        $updateExpenseDetails = TransactionCollections::where('user_id', $userinfo[0])->where('id', $id)->first();
        $updateExpenseDetails->update($expense_details);
        
        return redirect()->route('expense', ['err'=>2]); die();
    
    }

    //***********************************************
    //*
    //* Update Expense Details
    //*
    //***********************************************
    public function update_expense(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $id = $request->id;
        // $pagedata = array(
        //     'userinfo' => $userinfo,
        //     'pagetitle' => 'Update Expense'
        // );
        $expense_particulars = [];
        $expense_details = [];
        $updateExpenseParticulars = [];

        $validator = $request->validate([
            'expense_number' => 'bail|required',
            'expense_date' => 'bail|required|date',
            'expense_due_date' => 'bail|required|date',
            'client_name' => 'bail|required|max:100',
            'expense_description.*' => 'bail|required',
            'item_quantity.*' => 'bail|required|numeric|gt:0',
            'item_unit_price.*' => 'bail|required|numeric|gt:0',
            'expense_tax.*' => 'bail|required|gt:-1',
            'expense_amount.*' => 'bail|required|gt:0',
            'tax_type' => 'bail|required',
            'expense_total_amount.*' => 'bail|required|numeric|gt:0',
            'total_gst.*' => 'bail|required|numeric',
            'total_amount.*' => 'bail|required|numeric',
            'file_upload' =>  'mimes:jpg,jpeg,png,pdf'
        ]
        // ,
        // [
        //    'expense_number' => 'Expense Number is Required',
        //    'expense_date.required' => 'Date is Required',
        //    'expense_date.date' => 'Enter proper date format',
        //    'expense_due_date.required' => 'Due Date is Required',
        //    'expense_due_date.date' => 'Enter proper Due date format',
        //    'client_name.required' => 'Recepient Name is Required',
        //    'client_name.max' => 'Recepient Name must to exceed 100 characters',
        //    'expense_description.*.required' => 'Item Description is Required',
        //    'item_quantity.*.required' => 'Item Quantity is Required',
        //    'item_quantity.*.gt' => 'Item Quantity must be greater than 0',
        //    'item_quantity.*.numeric' => 'Item Quantity must be a numeric value',
        //    'item_unit_price.*.required' => 'Item Unit Price is Required',
        //    'item_unit_price.*.gt' => 'Item Unit Price must be greater than 0',
        //    'item_unit_price.*.numeric' => 'Item Unit Price must be a numeric value',
        //    'expense_tax.*.required' => 'Tax rate is Required',
        //    'expense_tax.*.gt' => 'Tax rate must be selected',
        //    'file_upload' =>  'Please insert image/pdf only'
        // ]
        );

        $dtnow = Carbon::now();
        
        $expense_date_exploded = explode("/", ($request->expense_date));
        $expense_due_date_exploded = explode("/", ($request->expense_due_date));
        
        $carbon_expense_date = Carbon::createFromDate($expense_date_exploded[2], $expense_date_exploded[0], $expense_date_exploded[1]);
        $carbon_expense_due_date = Carbon::createFromDate($expense_due_date_exploded[2], $expense_due_date_exploded[0], $expense_due_date_exploded[1]);

        // $get_exp_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first()->toArray();

        if ($request->hasFile('file_upload')) {
            // Get the file instance
            $file = $request->file('file_upload');

            // Store the file in the public directory
            $path = $file->store('public');

            // Get the file URL
            $url = Storage::url($path);
        }

        DB::beginTransaction();
        $expense_details = array(
            "user_id" => $userinfo[0],
            "expense_number" => $request->expense_number,
            "client_name" => $request->client_name,
            "expense_date" => $carbon_expense_date,
            "expense_due_date" => $carbon_expense_due_date,
            "tax_type" => $request->tax_type,
            "expense_total_amount" => $request->expense_total_amount,
            "total_gst" => $request->total_gst,
            "total_amount" => $request->total_amount,
            "file_upload" => (isset($url) ? $url : ''),
            "file_upload_format" => (isset($file) ? $file->extension() : ''),
            "updated_at" => $dtnow,
           // "status_paid" => 'paid'
        );
        
        $ids = [];
        $updateExpenseDetails = TransactionCollections::where('user_id', trim($userinfo[0]))
            ->where('id', $id)->where('is_active', 1)
            ->update(
                [
                    'user_id' => trim($userinfo[0]), 
                    'client_name' => trim($request->client_name),
                    'client_email' => trim('test@gmail.com'),
                    // 'client_phone' => trim($request->client_phone),
                    'issue_date' => trim($carbon_expense_date),
                    'due_date' => trim($carbon_expense_due_date),
                    'transaction_number' => trim($request->expense_number),
                    'default_tax' => trim($request->tax_type) == "0" ? 'tax_inclusive' : 'tax_exclusive',
                    'sub_total' => trim($request->expense_total_amount),
                    'total_gst' => trim($request->total_gst ? $request->total_gst : 0),
                    'total_amount' => trim($request->total_amount),
                    'logo' => (isset($url) ? $url : ''),
                    'transaction_type' => 'expense',
                ]
            );
      
        if($updateExpenseDetails){
            for ($i = 0; $i < count($request->item_quantity); $i++) {
                $newParticulars = Transactions::create(
                    [
                        'user_id' => trim($userinfo[0]), 
                        'transaction_collection_id' => $id,
                        'parts_quantity' => trim($request->item_quantity[$i]),
                        'parts_description' => trim($request->expense_description[$i]),
                        'parts_unit_price' => trim($request->item_unit_price[$i]),
                        'parts_amount' => trim($request->expense_amount[$i]),
                        // 'parts_code' => (!empty($value['parts_code']) ? $value['parts_code'] : $value['parts_name']),
                        // 'parts_name' => trim($value['parts_name']),
                        'parts_tax_rate_id' => trim($request->expense_tax[$i]),
                        'parts_chart_accounts_id' => trim($request->item_account[$i]),
                        // 'parts_tax_rate_id' => trim($value['parts_tax_rate_id']),
                        'parts_gst_amount' => 1,
                    ]);
                array_push($ids,  $newParticulars->id);
            };
           
            if(!empty($ids)){
                Transactions::whereNotIn('id', $ids)
                                ->where('transaction_collection_id', $id)
                                ->where('user_id', trim($userinfo[0]))
                                ->delete();
            }
            DB::commit();
        }
        return redirect()->route('expense', ['err'=>6]); die();
    }
   
    //***********************************************
    //*
    //* Invoice VOID PROCESS
    //*
    //***********************************************
    public function expense_void(Request $request) {
        $userinfo =$request->get('userinfo');
        $id = $request->id;
        $type = $request->type;
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Void Expense'
        );
       
        $chk_inv = TransactionCollections::where('user_id', $userinfo[0])->where('id', $id)->where('is_active', 1)->update(['status'=>$type]);
        if ($chk_inv) {
            return redirect()->route('expense', ['err'=>4]); die();
        }
    }
    //***********************************************
    //*
    //* Transaction status change PROCESS
    //*
    //***********************************************
    public function status_change(Request $request) {
        $userinfo =$request->get('userinfo');
        $id = $request->id;
        $type = $request->type;
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Status Change'
        );
      
        $chk_inv = TransactionCollections::where('user_id', $userinfo[0])->where('id', $id)->where('status','!=','Voided')->where('is_active', 1)->update(['status' => $type]);
        
        if($type == 'paid'){
            return redirect()->route('expense', ['err'=>3]); die();
        }else{
            return redirect()->route('expense', ['err'=>5]); die();
        }
    }

    public function expenseTaxRatesById(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $tax_rates = SumbInvoiceTaxRates::select('tax_rates')->where('id', $request->id)
                ->orderBy('id')
                ->get();

            if($tax_rates)
            {
                $response = [
                    'status' => 'success',
                    'err' => '',
                    'data' => $tax_rates
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

    public function expenseTaxratesList(Request $request){
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $tax_rates = SumbInvoiceTaxRates::orderBy('id')
                ->get();

            if($tax_rates)
            {
                $response = [
                    'status' => 'success',
                    'err' => '',
                    'data' => $tax_rates
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

    public function expenseChartAccounts(Request $request)
    {
        if ($request->ajax())
        { 
            $userinfo = $request->get('userinfo');

            $chart_account_parts = SumbChartAccountsTypeParticulars::where('user_id', $userinfo[0])
                    ->whereIn('chart_accounts_particulars_code', ['400','404','408','412','420','425','429','433','437','441','445',
                    '449','453','461','469','473','485','489','493','494','710','720'])->get();
            
            // $chart_account_parts = SumbChartAccounts::with(['chartAccountsParticulars'])
            //             ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
            //                 $query->whereIn('chart_accounts_particulars_code', ['400','404','408','412','420','425','429','433','437','441','445',
            //                                             '449','453','461','469','473','485','489','493','494','710','720'])
            //                 ->where('user_id', $userinfo[0]);
            //             })
            //             ->get();
            if($chart_account_parts){
                $response = [
                    'status' => 'success',
                    'err' => '',
                    'data' => $chart_account_parts
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
}
