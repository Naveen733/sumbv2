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

        $data = SumbChartAccountsTypeParticulars::with(['particulars', 'particulars.invoice'])
            ->whereHas('particulars', function($query) use($userinfo) {
                $query->where('user_id', $userinfo[0]);
            })
            ->whereHas('particulars.invoice', function($query) use($userinfo) {
                $query->where('user_id', $userinfo[0]);
            })
            ->groupBy('id')
            ->where('user_id', $userinfo[0])->get();
        if($data){
            // echo "<pre>"; var_dump($users->toArray()); echo "</pre>";die();

            $pagedata['invoice_list'] = !empty($data) ? $data->toArray() : '';
        }

        return view('reports.profitandloss', $pagedata); 
    }
}

?>