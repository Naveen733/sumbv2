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
use Illuminate\Support\Facades\Validator;
use DB;
use URL;
use Illuminate\Support\Facades\Redirect;

use App\Models\SumbUsers;
use App\Models\SumbInvoiceSettings;
use App\Models\SumbTransactions;
use App\Models\SumbClients;
use App\Models\SumbExpensesClients;
use App\Models\SumbInvoiceParticulars;
use App\Models\SumbInvoiceParticularsTemp;
use App\Models\SumbInvoiceDetails;
use App\Models\SumbExpenseDetails;
use App\Models\SumbExpenseParticulars;
use App\Models\SumbExpenseSettings;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\VarDumper\VarDumper;

use function PHPUnit\Framework\isNull;

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
            $expensedata = SumbExpenseDetails::where('user_id', $userinfo[0])->where('transaction_type', $request->input('type'))->paginate($itemsperpage)->toArray();
            $ptype = $request->input('type');
        } else {
            if($request->search_number_name_amount || $request->start_date || $request->end_date || $request->orderBy){
                
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
                $expensedata = SumbExpenseDetails::
                                where('user_id', $userinfo[0]);
                                if($request->search_number_name_amount && $request->start_date && $request->end_date){
                                    $expensedata->where('expense_date','>=',$start_date);
                                    $expensedata->where('expense_date','<=',$end_date);
                                    $expensedata->where('expense_number', 'LIKE', "%{$expense_number}%");
                                    $expensedata->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%");
                                    $expensedata->orWhere('expense_total_amount', 'LIKE', "{$total_amount}");
                                }
                                if($request->search_number_name_amount && !$request->start_date && !$request->end_date){
                                    $expensedata->where('expense_number', 'LIKE', "%{$expense_number}%");
                                    $expensedata->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%");
                                    $expensedata->orWhere('expense_total_amount', 'LIKE', "{$total_amount}");
                                }
                                if($request->start_date && $request->end_date && !($request->search_number_name_amount)){
                                    $expensedata->whereBetween('expense_date',array($start_date, $end_date));
                                }
                                if($request->start_date && !$request->search_number_name_amount && !$request->end_date){
                                    $expensedata->where('expense_date','>=',$start_date);
                                }
                                if($request->end_date && !$request->search_number_name_amount && !$request->start_date){
                                    $expensedata->where('expense_date','<=',$end_date);
                                }
                                if(!$request->start_date && $request->search_number_name_amount && $request->end_date){
                                    $expensedata->where('expense_date','<=',$end_date);
                                    $expensedata->where('expense_number', 'LIKE', "%{$expense_number}%");
                                    $expensedata->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%");
                                    $expensedata->orWhere('expense_total_amount', 'LIKE', "{$total_amount}");
                                }
                                if(!$request->end_date && $request->search_number_name_amount && $request->start_date){
                                    $expensedata->where('expense_date','>=',$start_date);
                                    $expensedata->where('expense_number', 'LIKE', "%{$expense_number}%");
                                    $expensedata->orWhere('client_name', 'LIKE', "%{$request->search_number_name_amount}%");
                                    $expensedata->orWhere('expense_total_amount', 'LIKE', "{$total_amount}");
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
                
            }else{
                $pagedata['orderBy'] = 'expense_date';
                $pagedata['direction'] = 'ASC';

                $expensedata = SumbExpenseDetails::
                // with(['particulars'])
                // ->whereHas('particulars', function($query) use($userinfo) {
                //     $query->where('user_id', $userinfo[0]);
                // })
                // ->
                where('user_id', $userinfo[0])
                ->orderBy('expense_date', 'DESC')
                ->paginate($itemsperpage)->toArray();
            }
        }
        $pagedata['expensedata'] = $expensedata;
        
        //echo '<pre>';
        //print_r($expensedata);
        //paginghandler
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
        //print_r($pagedata['paging']);
        //die();
        //echo "<pre>"; print_r($expensedata); die();
        
        
        //echo "<pre>"; print_r(empty($expensedata)); echo "</pre>"; die();
        
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
    public function create_expense(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Expenses'
        );
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
        $pagedata['data'] = $get_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first()->toArray();
       
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
        // if ($validator->fails()) {
        //     // return response()->json([
        //     //     'status' => false,
        //     //     'message' => 'validation error',
        //     //     'errors' => $validator->errors()
        //     // ], 401);
        //     // echo "sds";
        //     // die();
        //    // return redirect()->route( 'expenses-create' )->withErrors($validator)->with('form_data',$pagedata);
        //    //return Redirect::back()->withErrors($validator);
        //    return view('invoice.expensescreate')->withErrors($validator)->with($pagedata);
        // }

        //echo "<pre>"; var_dump( $expense_details); echo "</pre>";
       // die();
      
        // echo "<pre>";
        // print_r($request->all());
        
        //check form data
        // if (empty($request->invoice_date) || empty($request->client_name) || empty($request->amount)) {
        //     $oriform['err'] = 1;
        //     return redirect()->route('expenses-create', $oriform); die();
        // }
        
        // $oriform = ['err'=>0, 'invoice_date'=>$request->invoice_date, 'client_name'=>$request->client_name, 'invoice_details'=>$request->invoice_details, 'amount'=>$request->amount];
        
        // if (!empty($request->savethisrep)) {
        //     $oriform['savethisrep'] = $request->savethisrep;
        // } else {
        //     $oriform['savethisrep'] = 0;
        // }
        
        // //print_r(is_numeric($request->amount));
        // if (!is_numeric($request->amount)) {
        //     $oriform['err'] = 2;
        //     return redirect()->route('expenses-create', $oriform); die();
        // }
        

        
        
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
        $transaction_id = SumbExpenseDetails::insertGetId($expense_details);
        
        for ($i = 0; $i < count($request->item_quantity); $i++) {
            $expense_details = array(
                "user_id" => $userinfo[0],
                "expense_description" => $request->expense_description[$i],
                "item_quantity" => $request->item_quantity[$i],
                "item_unit_price" => $request->item_unit_price[$i],
                "expense_tax" => $request->expense_tax[$i],
                "expense_amount" => $request->expense_amount[$i],
                "expense_id" => $transaction_id,
                "expense_number" => $get_exp_settings['expenses_count'],
                'created_at'            => $dtnow,
                'updated_at'            => $dtnow
            );
            SumbExpenseParticulars::insert($expense_details);
        };

        $updatethis = SumbExpenseSettings::where('user_id', $userinfo[0])->first();
        $updatethis->increment('expenses_count');
        
        return redirect()->route('invoice', ['err'=>1]); die();
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
       
        $pagedata['expense_details'] = SumbExpenseDetails::where('user_id', $userinfo[0])->where('transaction_id', $id)->first();
        $expense_particulars = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->orderBy('id')->get();
        $pagedata['data'] = $get_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first()->toArray();
       
        if(!empty($expense_particulars)){
            $pagedata['expense_particulars'] = $expense_particulars->toArray();
        }

        $get_expclients = SumbExpensesClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients->toArray();
        }
        $pagedata['type'] = 'edit';
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
        $expense_particulars = [];
       
        $pagedata['expense_details'] = SumbExpenseDetails::where('user_id', $userinfo[0])->where('transaction_id', $id)->first();
        $expense_particulars = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->orderBy('id')->get();
        $pagedata['data'] = $get_settings = SumbExpenseSettings::where('user_id', $userinfo[0])->first()->toArray();
       
        if(!empty($expense_particulars)){
            $pagedata['expense_particulars'] = $expense_particulars->toArray();
        }

        $get_expclients = SumbExpensesClients::where('user_id', $userinfo[0])->orderBy('client_name')->get();
        if (!empty($get_expclients)) {
            $pagedata['exp_clients'] = $get_expclients->toArray();
        }
        $pagedata['type'] = 'view';
          return view('invoice.expensescreate', $pagedata);

        //  echo "<pre>"; var_dump($pagedata['expense_details']); echo "</pre>";
        //  die();
        
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
        // $expense_particulars = [];
        // $deleteExpenseParticulars = [];

        // $expense_particulars = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->orderBy('id')->get();
        // if(!empty($expense_particulars)){
        //     $deleteExpenseParticulars = $expense_particulars->toArray();
        // }
        
        // SumbExpenseDetails::where('user_id', $userinfo[0])->where('transaction_id', $id)->first()->delete();
        
        // for($i = 0; $i < count($deleteExpenseParticulars); $i++){
        //     SumbExpenseParticulars::where('user_id', $userinfo[0])->where('id', $deleteExpenseParticulars[$i]['id'])->delete();
        // }
        $expense_details = array("inactive_status" => 1);

        $updateExpenseDetails = SumbExpenseDetails::where('user_id', $userinfo[0])->where('transaction_id', $id)->first();
        $updateExpenseDetails->update($expense_details);
        
        //die();
        return redirect()->route('invoice', ['err'=>2]); die();
    
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
        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'validation error',
        //         'errors' => $validator->errors()
        //     ], 401);
        //     // echo "sds";
        //     // die();
        //    // return redirect()->route( 'expenses-create' )->withErrors($validator)->with('form_data',$pagedata);
        // }

        $updateExpenseDetails = SumbExpenseDetails::where('user_id', $userinfo[0])->where('transaction_id', $id)->first();
        $updateExpenseDetails->update($expense_details);

        $expense_particulars = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->orderBy('id')->get();
        if(!empty($expense_particulars)){
            $updateExpenseParticulars = $expense_particulars->toArray();
        }
        //echo count($request->item_quantity);
        //echo count($updateExpenseParticulars);

        if(count($request->item_quantity) == count($updateExpenseParticulars)){
            for ($i = 0; $i < count($request->item_quantity); $i++) {
            
                $expense_particular_item = array(
                    "user_id" => $userinfo[0],
                    "expense_description" => $request->expense_description[$i],
                    "item_quantity" => $request->item_quantity[$i],
                    "item_unit_price" => $request->item_unit_price[$i],
                    "expense_tax" => $request->expense_tax[$i],
                    "expense_amount" => $request->expense_amount[$i],
                    "updated_at" => $dtnow,
                );
                $expense_item = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->where('id',$updateExpenseParticulars[$i]['id'])->first();
                $expense_item->update($expense_particular_item);
            };
        }else if(count($request->item_quantity) < count($updateExpenseParticulars)){
            for ($i = 0; $i < count($updateExpenseParticulars); $i++) {
                if($i < count($request->item_quantity)){
                    $expense_particular_item = array(
                        "user_id" => $userinfo[0],
                        "expense_description" => $request->expense_description[$i],
                        "item_quantity" => $request->item_quantity[$i],
                        "item_unit_price" => $request->item_unit_price[$i],
                        "expense_tax" => $request->expense_tax[$i],
                        "expense_amount" => $request->expense_amount[$i],
                        "updated_at" => $dtnow,
                    );
                    $expense_item = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->where('id',$updateExpenseParticulars[$i]['id'])->first();
                   $expense_item->update($expense_particular_item);
                //     echo "update";
                //   echo "<pre>"; var_dump($expense_particular_item); echo "</pre>";
                }else{
                    $expense_del_old_extra_item = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->where('id',$updateExpenseParticulars[$i]['id'])->first();
                   $expense_del_old_extra_item->delete();
                //   echo "delete";
                //   echo "<pre>"; var_dump($expense_del_old_extra_item); echo "</pre>";
                }
                
            };
        }else{
            for ($i = 0; $i < count($request->item_quantity); $i++) {
                if($i < count($updateExpenseParticulars)){
                    $expense_particular_item = array(
                        "user_id" => $userinfo[0],
                        "expense_description" => $request->expense_description[$i],
                        "item_quantity" => $request->item_quantity[$i],
                        "item_unit_price" => $request->item_unit_price[$i],
                        "expense_tax" => $request->expense_tax[$i],
                        "expense_amount" => $request->expense_amount[$i],
                        "updated_at" => $dtnow,
                    );
                    $expense_item = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->where('id',$updateExpenseParticulars[$i]['id'])->first();
                    $expense_item->update($expense_particular_item);
                //     echo "update";
                //   echo "<pre>"; var_dump($expense_particular_item); echo "</pre>";
                }else{
                    $expense_item = SumbExpenseParticulars::where('user_id', $userinfo[0])->where('expense_id', $id)->first();
                    
                    $expense_particular_new_item = array(
                        "user_id" => $userinfo[0],
                        "expense_description" => $request->expense_description[$i],
                        "item_quantity" => $request->item_quantity[$i],
                        "item_unit_price" => $request->item_unit_price[$i],
                        "expense_tax" => $request->expense_tax[$i],
                        "expense_amount" => $request->expense_amount[$i],
                        "expense_id" => $id,
                        "expense_number" => $expense_item['expense_number'],
                        'created_at'            => $dtnow,
                        'updated_at'            => $dtnow,
                    );
                    SumbExpenseParticulars::insert($expense_particular_new_item);
                //    echo "insert";
                //    echo "<pre>"; var_dump($expense_particular_new_item); echo "</pre>";
                }
                
            };
        }
        
         return redirect()->route('invoice', ['err'=>6]); die();

        //  echo "<pre>"; var_dump($updateExpenseParticulars); echo "</pre>";
        //  die();
        
    }

    //***********************************************
    //*
    //* Create Invoice Page
    //*
    //***********************************************
    public function create_invoice(Request $request) {
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
    public function create_invoice_new(Request $request) {
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
    public function expense_void(Request $request) {
        $userinfo =$request->get('userinfo');
        $id = $request->id;
        $type = $request->type;
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Void Invoice'
        );
        //echo "<pre>"; print_r($request->all()); //echo "</pre>";
        // $pagedata['oriform'] = $request->all();
        // echo "<pre>"; print_r($pagedata);
        // if (empty($pagedata['oriform']['invno'])) {
        //     return redirect()->route('invoice', ['err'=>3]); die();
        // }

        $chk_inv = SumbExpenseDetails::where('user_id', $userinfo[0])->where('transaction_id', $id)->first();
        if ($chk_inv->exists) {
            $chk_inv = $chk_inv->toArray();
        }
        //print_r($chk_inv);
        //die();
        SumbExpenseDetails::where('id',$chk_inv['id'])->update(['status_paid'=>$type]);
        //echo "<pre>"; print_r($chk_inv); //echo "</pre>";
        //die();
        return redirect()->route('invoice', ['err'=>4]); die();
        //return redirect()->route('invoice'); die();
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
       // $pagedata['oriform'] = $request->all();
        //echo "<pre>"; print_r($pagedata);
        
        // if (empty($pagedata['oriform']['tno']) || empty($pagedata['oriform']['type']) || empty($pagedata['oriform']['to']) ) {
        //     return redirect()->route('invoice', ['err'=>3]); die();
        // }
        // if ($pagedata['oriform']['type'] != 'expenses' && $pagedata['oriform']['type'] != 'invoice' && $pagedata['oriform']['type'] != 'adjustment') {
        //     return redirect()->route('invoice', ['err'=>3]); die();
        // }
        // if ($pagedata['oriform']['to'] != 'paid' && $pagedata['oriform']['to'] != 'unpaid' && $pagedata['oriform']['to'] != 'void') {
        //     return redirect()->route('invoice', ['err'=>3]); die();
        // }
        
        
        $chk_inv = SumbExpenseDetails::where('user_id', $userinfo[0])->where('transaction_id', $id)->first();
        if ($chk_inv->exists) {
            $chk_inv = $chk_inv->toArray();
        }
        //print_r($chk_inv);
        //die();
        SumbExpenseDetails::where('id',$chk_inv['id'])->update(['status_paid'=>$type]);
        //echo "<pre>"; print_r($chk_inv); //echo "</pre>";
        //die();
        if($type == 'paid'){
            return redirect()->route('invoice', ['err'=>3]); die();
        }else{
            return redirect()->route('invoice', ['err'=>5]); die();
        }
        
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
    
}
