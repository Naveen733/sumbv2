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
class ChartAccountController extends Controller
{
    public function __construct() {

    }

    public function InvoiceChartAccountForm(Request $request)
    {
        if ($request->ajax())
        {
            // var_dump($request->invoice_chart_accounts_type_id, $request->invoice_chart_accounts_id);die();
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
                        'chart_accounts_particulars_tax_rate_id' => trim($request->invoice_chart_accounts_tax_rate_id)
                    ]);
                if($chart_account_particulars->id){
                    DB::commit();
                    $chart_account = SumbChartAccounts::with(['chartAccountsParticulars', 'chartAccountsTypes'])
                    ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
                        $query->where('user_id', $userinfo[0]);
                    })
                    ->whereHas('chartAccountsTypes', function($query) use($userinfo) {
                        $query->where('user_id', $userinfo[0]);
                    })
                    ->where('user_id', $userinfo[0])->get();
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
            $chart_account_parts = $chart_account = SumbChartAccounts::with(['chartAccountsParticulars', 'chartAccountsTypes'])
                        ->whereHas('chartAccountsParticulars', function($query) use($userinfo) {
                            $query->where('user_id', $userinfo[0]);
                        })
                        ->whereHas('chartAccountsTypes', function($query) use($userinfo) {
                            $query->where('user_id', $userinfo[0]);
                        })
                        ->where('user_id', $userinfo[0])->get();
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
