<?php

namespace App\Http\Controllers;

use App\Client;
use App\Deposit;
use App\Exchange;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    public function list() {
        $exchanges=Exchange::where('is_active','=','Yes')->get();
        return view('Admin.Exchanges.list',compact('exchanges'));
    }
    public function addForm() {
        return view('Admin.Exchanges.add');
    }
    public function add(Request $req) {
        $req->validate([
            'name'=>'required',
            'total_coins'=>'required'
        ]);
        $exchange=new  Exchange();
        $exchange->name=$req->name;
        $exchange->coin=$req->total_coins;
        $exchange->save();
        return redirect('/exchanges')->with(['msg-success' => 'Exchange has been added']);
    }
    public function delete(Request $req) {
        $exchange=Exchange::find($req->deleteId);
        $exchange->is_active="No";
        $result=$exchange->update();
        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Deleted successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function viewDetails(Request $req){
        $id=$req->query('id');
        $startDate = $req->query('from_date');
        $endDate = $req->query('to_date');
        $type=$req->query('type');
        $client_id=$req->query('client_id');
        if (!$startDate) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        }
        $transactions = Deposit::where('exchange_id', '=', $id)
                        ->leftJoin('clients', 'deposits.client_id', '=', 'clients.id')
                        ->leftJoin('users', 'deposits.agent_id', '=', 'users.id')
                        ->select('deposits.*','clients.name as client_name','users.name as approved_by')
                        ->when($type, function ($query, $type) {
                            $query->where(function ($query) use ($type) {
                                $query->Where('deposits.type', '=', $type);
                            });
                        })
                        ->when($client_id, function ($query, $client_id) {
                            $query->where(function ($query) use ($client_id) {
                                $query->Where('deposits.client_id', '=', $client_id);
                            });
                        })
                        ->whereDate('deposits.created_at', '>=', date('Y-m-d', strtotime($startDate)))
                        ->whereDate('deposits.created_at', '<=', date('Y-m-d', strtotime($endDate)))
                        ->get();
        $clients=Client::get();
        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();
        return view('Admin.Exchanges.viewDetails',compact('type','client_id','endDate','startDate','clients','transactions','id'));  
    }
}
