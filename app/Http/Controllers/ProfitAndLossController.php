<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\SumbChartAccountsTypeParticulars;
use App\Models\SumbChartAccountsType;
use App\Models\SumbChartAccounts;
use App\Models\SumbInvoiceTaxRates;
use App\Models\SumbInvoiceParticulars;
use App\Models\SumbInvoiceDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Arr;

class ProfitAndLossController extends Controller
{
    public function __construct() {

    }

    public function index(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Profit and Loss'
        );

        $today = Carbon::now(); //Current Date and Time 
        $todays = Carbon::parse($today)->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $todays)->format('m/d/Y'); 
        $last_date_of_month = Carbon::parse($today)->endOfMonth()->toDateString(); 
        $end_date = Carbon::createFromFormat('Y-m-d', $last_date_of_month)->format('m/d/Y');

        // $query = SumbChartAccountsTypeParticulars::with('particulars', 'particulars.invoice');
        // $query = $query->whereHas('particulars', function (Builder $query) use ($userinfo) {
        //     $query = $query->where('user_id', $userinfo[0]);
        // });
        // $query = $query->whereHas( 'particulars.invoice', function (Builder $query) use ($userinfo) {
        //     $query = $query->where('user_id', $userinfo[0]);
        //     $query = $query->where('invoice_issue_date','>=','2023-03-07');
        // });
        // $query = $query->get();


        $data = SumbChartAccountsTypeParticulars::with(['particulars', 'particulars.invoice'])
            ->whereHas('particulars', function($query) use($userinfo) {
                $query->where('user_id', $userinfo[0]);
            })
            ->whereHas('particulars.invoice', function($query) use($userinfo) {
                $query->where('user_id', $userinfo[0]);
                // $query->where('invoice_issue_date','>=','2023-03-04');
            })
            ->groupBy('id')
            ->where('user_id', $userinfo[0])->get();


            // $datas = SumbChartAccountsTypeParticulars::selectRaw('sumb_chart_accounts_particulars.id, invoice_chart_accounts_parts_id, sumb_invoice_particulars.id, sumb_chart_accounts_particulars.*, sumb_invoice_particulars.*')
            //     ->leftJoin('sumb_invoice_particulars', 'sumb_invoice_particulars.invoice_chart_accounts_parts_id', '=', 'sumb_chart_accounts_particulars.id')
            //     ->leftJoin('sumb_invoice_details', 'sumb_invoice_details.id', '=', 'sumb_invoice_particulars.invoice_id')
            //     ->where('sumb_invoice_details.user_id', $userinfo[0])
            //     ->groupBy('sumb_invoice_particulars.invoice_chart_accounts_parts_id', 'sumb_invoice_particulars.id')
            //     ->where('invoice_issue_date','>=','2023-03-07')
            //     ->get();

            // echo "<pre>"; var_dump($data->toArray()); echo "</pre>";die();

        $pagedata['start_date'] = $start_date;
        $pagedata['end_date'] = $end_date;
        $pagedata['invoice_list'] = !empty($data) ? $data->toArray() : '';
        return view('reports.profitandloss', $pagedata); 
    }

    public function reports(Request $request){
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Transactions'
        );
        $today = Carbon::now()->firstOfMonth(); //Current Date and Time 
        $todays = Carbon::parse($today)->toDateString();
        $start_date = Carbon::createFromFormat('Y-m-d', $todays); 
        $last_date_of_month = Carbon::parse($today)->endOfMonth()->toDateString(); 
        $end_date = Carbon::createFromFormat('Y-m-d', $last_date_of_month);

        $request->report_start_date = !empty($request->report_start_date) ? Carbon::createFromFormat('m/d/Y', $request->report_start_date)->format('Y-m-d') : ($start_date)->format('Y-m-d');
        $request->report_end_date = !empty($request->report_end_date) ?Carbon::createFromFormat('m/d/Y', $request->report_end_date)->format('Y-m-d') : ($end_date)->format('Y-m-d');

        $account_parts = SumbChartAccountsTypeParticulars::get();

        // $invoice_ids = SumbInvoiceDetails::select('id')->whereBetween('invoice_issue_date', ['2023-03-09', '2023-03-10'])->get();
        
        // $invoice_ids = $invoice_ids->map(function($invoice_id){
        //     return $invoice_id['id'];
        // }); 
            $accounts_ids = [];
        // if(!empty($request->report_chart_accounts_ids)){
        //     foreach($request->report_chart_accounts_ids as $ids){
        //         $accounts_ids[] = intval($ids);
        //     }
        // }

        // echo "<pre>"; var_dump($request->report_start_date); echo "</pre>";die();
            $data = SumbChartAccountsTypeParticulars::selectRaw('sumb_chart_accounts_particulars.id as account_id, sumb_chart_accounts_particulars.*, sumb_invoice_particulars.id as particulars_id, sumb_invoice_particulars.*, sumb_invoice_details.id as primary_invoice_id, sumb_invoice_details.*, sumb_invoice_tax_rates.id as tax_rate_id, sumb_invoice_tax_rates.*')->leftJoin('sumb_invoice_particulars', 'sumb_invoice_particulars.invoice_chart_accounts_parts_id', '=', 'sumb_chart_accounts_particulars.id')
                    ->leftJoin('sumb_invoice_details', 'sumb_invoice_details.id', '=', 'sumb_invoice_particulars.invoice_id')
                    ->leftJoin('sumb_invoice_tax_rates', 'sumb_invoice_tax_rates.id', '=', 'sumb_invoice_particulars.invoice_parts_tax_rate_id')
                    ->where('sumb_invoice_details.user_id', $userinfo[0]);
                    // ->whereIn('sumb_invoice_particulars.invoice_chart_accounts_parts_id', [93,94])
                    if(!empty($request->report_chart_accounts_ids)){
                        $data->whereIn('sumb_invoice_particulars.invoice_chart_accounts_parts_id', $request->report_chart_accounts_ids);
                    }
                    
                    $data->whereBetween('sumb_invoice_details.invoice_issue_date', [$request->report_start_date, $request->report_end_date]);
                    $data =  $data->get();


        // $data = SumbChartAccountsTypeParticulars::with(['particulars'])
        //     ->whereHas('particulars', function($query) use($userinfo) {
        //         $query->where('user_id', $userinfo[0]);
        //         $query->whereIn('invoice_chart_accounts_parts_id', [93,94]);
        //         $query->whereIn('invoice_id', [33]);
        //     })
        //     ->get();
            $data = $data->toArray();
            // echo "<pre>"; var_dump($invoice_ids); echo "</pre>";die();
$invoice_details = [];         
foreach($data as $key => $val){
    if(!isset($invoice_details[$val['account_id']])){
        $invoice_details[$val['account_id']] = [
            'account_id' => $val['account_id'],
            'user_id' => $val['user_id'],
            'chart_accounts_particulars_code' => $val['chart_accounts_particulars_code'],
            'chart_accounts_particulars_name' => $val['chart_accounts_particulars_name'],
            'chart_accounts_particulars_description' => $val['chart_accounts_particulars_description'],
            'total_credits_amount' => 0,
            'total_parts_amount' => 0,
            'total_tax_amount' => 0,
            'particulars' => []
        ];
    }
    
    if(isset($invoice_details[$val['account_id']])){
        if(!in_array($val['particulars_id'], array_column($invoice_details[$val['account_id']]['particulars'], 'particulars_id'))){
            
            $gst_amount = 0;$total_gross = 0;
            if($val['invoice_default_tax'] == 'tax_inclusive'){
                $gst_amount = ($val['invoice_parts_amount'] - $val['invoice_parts_amount'] / (1 + $val['tax_rates']/100));
                $invoice_credit_amount = $val['invoice_parts_amount'];
            }
            else if($val['invoice_default_tax'] == 'tax_exclusive'){
                $gst_amount = ($val['invoice_parts_amount'] * $val['tax_rates']/100);
                $invoice_credit_amount = $val['invoice_parts_amount'] - $gst_amount;
            }else if($val['invoice_default_tax'] == 'no_tax'){
                $gst_amount = 0;
                $invoice_credit_amount = $val['invoice_parts_amount'];
            }
            $invoice_details[$val['account_id']]['particulars'][] = [
                'particulars_id' => $val['particulars_id'],
                'invoice_id' => $val['invoice_id'],
                'invoice_parts_amount' => $val['invoice_parts_amount'],
                'invoice_parts_credit_amount' => $invoice_credit_amount,
                'invoice_parts_description' => $val['invoice_parts_description'],
                'invoice_default_tax' => $val['invoice_default_tax'],
                'invoice_gst' => $gst_amount,
                'invoice_tax_rates' => [],
                'invoice' => [],
            ];

            $invoice_details[$val['account_id']]['total_parts_amount'] += $val['invoice_parts_amount'];
            $invoice_details[$val['account_id']]['total_credits_amount'] += $invoice_credit_amount;
            $invoice_details[$val['account_id']]['total_tax_amount'] += $gst_amount;
        }

        if(in_array($val['particulars_id'], array_column($invoice_details[$val['account_id']]['particulars'], 'particulars_id'))){
            $particular_invoice_id_index = array_search($val['particulars_id'], array_column($invoice_details[$val['account_id']]['particulars'], 'particulars_id'));
            if($particular_invoice_id_index !== false && isset($invoice_details[$val['account_id']]['particulars'][$particular_invoice_id_index]['invoice'])){
                $invoice_details[$val['account_id']]['particulars'][$particular_invoice_id_index]['invoice'] = [
                    'primary_invoice_id' => $val['primary_invoice_id'],
                    'invoice_issue_date' => $val['invoice_issue_date'],
                    'invoice_due_date' => $val['invoice_due_date'],
                    'invoice_sub_total' => $val['invoice_sub_total'],
                    'invoice_total_gst' => $val['invoice_total_gst'],
                    'invoice_default_tax' => $val['invoice_default_tax'],
                ];
            }
        }

        if(in_array($val['particulars_id'], array_column($invoice_details[$val['account_id']]['particulars'], 'particulars_id'))){
            $particular_id_index = array_search($val['particulars_id'], array_column($invoice_details[$val['account_id']]['particulars'], 'particulars_id'));
            if($particular_id_index !== false && isset($invoice_details[$val['account_id']]['particulars'][$particular_id_index]['invoice_tax_rates'])){
                $invoice_details[$val['account_id']]['particulars'][$particular_id_index]['invoice_tax_rates'] = [
                    'tax_rate_id' => $val['tax_rate_id'],
                    'tax_rates' => $val['tax_rates'],
                    'tax_rates_name' => $val['tax_rates_name'],
                ];
            }
        }
    }
}

    sort($invoice_details);
    $pagedata['transaction_details'] = !empty($invoice_details) ? $invoice_details : '';
    $pagedata['account_parts_code'] = $request->report_chart_accounts_ids ? $request->report_chart_accounts_ids : [];
    $pagedata['report_start_date'] = $request->report_start_date ? Carbon::createFromFormat('Y-m-d', $request->report_start_date)->format('m/d/Y') : '';
    $pagedata['report_end_date'] = $request->report_end_date ? Carbon::createFromFormat('Y-m-d', $request->report_end_date)->format('m/d/Y') : '';
    $pagedata['account_parts'] = $account_parts ? $account_parts->toArray() : '';

    return view('reports.reportslist', $pagedata);
}

    public function multiSelect(Request $request){
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Transactions'
        );
        return view('reports.test', $pagedata);
    }
}

?>