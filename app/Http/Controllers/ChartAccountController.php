<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\SumbChartAccountsTypeParticulars;
use App\Models\SumbChartAccountsType;
use App\Models\SumbChartAccounts;
use App\Models\SumbInvoiceTaxRates;

class ChartAccountController extends Controller
{
    public function __construct() {

    }

    public function invoiceChartAccountForm(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $chart_account_exists = SumbChartAccountsTypeParticulars::where('user_id', $userinfo[0])
                                            ->where('chart_accounts_particulars_code', $request->invoice_chart_accounts_code)
                                            ->first();
            if(!empty($chart_account_exists)){
                $response = [
                    'status' => 'error',
                    'err' => 'Code already exists',
                    'data' => ''
                ];
                echo json_encode($response);
            }else{
                DB::beginTransaction();
                $chart_account_particulars = SumbChartAccountsTypeParticulars::create(
                    [
                        'user_id' => trim($userinfo[0]), 
                        'chart_accounts_id' => $request->invoice_chart_accounts_id,
                        'chart_accounts_type_id' => trim($request->invoice_chart_accounts_type_id),
                        'chart_accounts_particulars_code' => trim($request->invoice_chart_accounts_code),
                        'chart_accounts_particulars_name' => trim($request->invoice_chart_accounts_name),
                        'chart_accounts_particulars_description' => trim($request->invoice_chart_accounts_description),
                        'chart_accounts_particulars_tax' => trim($request->invoice_chart_accounts_tax_rate),
                        'accounts_tax_rate_id' => trim($request->invoice_chart_accounts_tax_rate_id)
                    ]);
                if($chart_account_particulars->id){
                    DB::commit();
                    $chart_account = SumbChartAccounts::with(['chartAccountsParticulars', 'chartAccountsTypes'])
                    ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
                        $query->where('user_id', $userinfo[0]);
                    })
                    ->get();
                    if($chart_account){
                        $response = [
                            'status' => 'success',
                            'err' => '',
                            'data' => $chart_account,
                            'id' => $chart_account_particulars->id
                        ];
                        echo json_encode($response);
                    }
                } 
            }
        }
    }

    public function chartAccountsPartsById(Request $request)
    {
        if ($request->ajax())
        {
            $userinfo = $request->get('userinfo');
            $chart_account_exists = SumbChartAccountsTypeParticulars::with(['invoiceTaxRates'])->where('user_id', $userinfo[0])
                                        ->where('id', $request->id)
                                        ->first();
            if(!empty($chart_account_exists)){
                $response = [
                    'status' => 'success',
                    'err' => '',
                    'data' => $chart_account_exists
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

    public function chartAccountsPartsList(Request $request)
    {
        if ($request->ajax())
        { 
            $userinfo = $request->get('userinfo');
            $chart_account_parts = SumbChartAccounts::with(['chartAccountsParticulars', 'chartAccountsTypes'])
                        ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
                            $query->where('user_id', $userinfo[0]);
                        })
                        // ->whereHas('chartAccountsTypes', function($query) use($userinfo) {
                        //     // $query->where('user_id', $userinfo[0]);
                        // })
                        ->get();
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

    public function index(Request $request) {
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        
        if($request->orderBy || $request->search_code_name_desc){
            $pagedata['orderBy'] = $request->orderBy;
                if($request->direction == 'ASC')
                {
                    $pagedata['direction'] = 'DESC';
                }
                if($request->direction == 'DESC')
                {
                    $pagedata['direction'] = 'ASC';
                }
                $pagedata['search_code_name_desc'] = $request->search_code_name_desc;
        }else{
            $pagedata['orderBy'] = 'chart_accounts_particulars_code';
            $pagedata['direction'] = 'ASC';
            $request->orderBy = 'chart_accounts_particulars_code';
            $request->direction = 'DESC';
        }

        $tax_rates = SumbInvoiceTaxRates::get();
        if (!empty($tax_rates)) {
            $pagedata['tax_rates'] = $tax_rates->toArray();
        }

        $chart_account = SumbChartAccounts::with(['chartAccountsTypes'])->where('user_id', $userinfo[0])->get();
        $chart_account_particulars = SumbChartAccountsTypeParticulars::with(['chartAccounts', 'chartAccountsTypes', 'invoiceTaxRates'])
                    ->where('user_id', $userinfo[0])
                    ->where(function ($query) use ($request) {
                        $query->where('chart_accounts_particulars_code', 'LIKE', "%{$request->search_code_name_desc}%")
                            ->orWhere('chart_accounts_particulars_name', 'LIKE', "%{$request->search_code_name_desc}%")
                            ->orWhere('chart_accounts_particulars_description', 'LIKE', "%{$request->search_code_name_desc}%");
                    })->where(function ($query) use ($request) {
                        if($request->id){
                            $query->where('chart_accounts_id', $request->id);
                        }
                    });
                    if($request->orderBy){
                        $chart_account_particulars->orderBy($request->orderBy, $request->direction);
                    }
                $chart_account_particulars = $chart_account_particulars->get();

        $pagedata['chart_accounts_types'] = !empty($chart_account) ? $chart_account->toArray() : '';
        $pagedata['tab'] = $request->tab ? $request->tab : 'all_accounts';
        $pagedata['accounts_id'] = $request->id ? $request->id : '';
        $pagedata['chart_account'] = !empty($chart_account) ? $chart_account->toArray() : '';
        $pagedata['chart_account_particulars'] = !empty($chart_account_particulars) ? $chart_account_particulars->toArray() : '';

        return view('invoice.chartaccounts', $pagedata);
    }

    public function update(Request $request){
        $userinfo = $request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Create Invoice'
        );
        if ($request->ajax())
        {
        // $validated = $request->validate([
        //     'chart_accounts_id' => 'bail|required|max:255',
        //     'chart_accounts_type_id' => 'bail|required|max:255',
        //     'chart_accounts_parts_code' => 'bail|required',
        //     'chart_accounts_parts_name' => 'bail|required',
        //     'chart_accounts_description' => 'bail|required|max:255',
        //     'chart_accounts_tax_rate' => 'bail|required|max:255',
        // ]);
            $type_id = $request->chart_accounts_type_id;
            
            $userinfo = $request->get('userinfo');
            $chart_account_exists = SumbChartAccountsTypeParticulars::where('user_id', $userinfo[0])
                                            ->where('chart_accounts_particulars_code', $request->chart_accounts_parts_code)
                                            ->where('id', '!=' ,$request->chart_accounts_part_id)
                                            ->first();

            if(!empty($chart_account_exists)){
                $response = [
                    'status' => 'error',
                    'err' => 'Code already exists',
                    'data' => ''
                ];
                echo json_encode($response);
            }else{
                DB::beginTransaction();
                $chart_account_particulars = SumbChartAccountsTypeParticulars::where('user_id', $userinfo[0])->where('id', $request->chart_accounts_part_id)
                ->update(
                    [
                        'user_id' => trim($userinfo[0]), 
                        'chart_accounts_id' => $request->chart_accounts_id,
                        'chart_accounts_type_id' => trim($request->chart_accounts_type_id),
                        'chart_accounts_particulars_code' => trim($request->chart_accounts_parts_code),
                        'chart_accounts_particulars_name' => trim($request->chart_accounts_parts_name),
                        'chart_accounts_particulars_description' => trim($request->chart_accounts_description),
                        'chart_accounts_particulars_tax' => trim($request->chart_accounts_tax_rate),
                        'accounts_tax_rate_id' => trim($request->chart_accounts_tax_rate)
                    ]);
                if($chart_account_particulars)
                {
                    DB::commit();
                    $response = [
                        'status' => 'success',
                        'err' => '',
                        'data' => '',
                        'id' => ''
                    ];
                    echo json_encode($response);
                }
            }
        }
    }
}
