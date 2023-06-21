<?php

namespace App\Http\Controllers;

use App\Client;
use App\Deposit;
use App\Exchange;
use App\TransactionHistory;
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
        $exchange->amount=$req->total_coins;
        $exchange->save();
        // $transactionHistory=new TransactionHistory();
        // $transactionHistory->amount=$req->total_coins;
        // $transactionHistory->agent_id=session('user')->id;
        // $transactionHistory->exchange_id=$exchange->id;
        // $transactionHistory->type='deposit';
        // $transactionHistory->save();
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
        $id=$req->query('id')??null;
        $startDate = $req->query('from_date')??null;
        $endDate = $req->query('to_date')??null;
        $type=$req->query('type')??'null';
        $client_id=$req->query('client_id')??'null';
        if (!$startDate) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        }
        $transactions = TransactionHistory::where('transaction_histories.exchange_id', '=', $id)
                        ->leftJoin('clients', 'transaction_histories.client_id', '=', 'clients.id')
                        ->leftJoin('users', 'transaction_histories.agent_id', '=', 'users.id')
                        ->select('transaction_histories.*','clients.name as client_name','users.name as approved_by')
                        ->whereNotNull('transaction_histories.exchange_id')
                        ->when($type !== 'null', function ($query) use ($type) {
                            $query->where('transaction_histories.type', $type);
                        })
                        ->when($client_id!='null', function ($query, $client_id) {
                            $query->where(function ($query) use ($client_id) {
                                $query->Where('transaction_histories.client_id', '=', $client_id);
                            });
                        })
                        ->whereDate('transaction_histories.created_at', '>=', date('Y-m-d', strtotime($startDate)))
                        ->whereDate('transaction_histories.created_at', '<=', date('Y-m-d', strtotime($endDate)))
                        
                        ->get();
        $clients=Client::get();
        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();
        return view('Admin.Exchanges.viewDetails',compact('type','client_id','endDate','startDate','clients','transactions','id'));  
    }
    public function addMoneyForm(Request $req) {
        $id=$req->query('id');
        $exchange=Exchange::find($id);
        return view('Admin.Exchanges.addMoney',compact('exchange','id'));
    }
    public function addMoney(Request $req){
        $excahnge=Exchange::find($req->hiddenid);
        $transaction=new TransactionHistory();
        $transaction->type='deposit';
        $transaction->amount=$req->amount;
        $transaction->agent_id=session('user')->id;
        $transaction->exchange_id=$req->hiddenid;
        $transaction->opening_balance=$excahnge->amount;
        $transaction->save();
        $excahnge->amount=$excahnge->amount+$req->amount;
        $excahnge->save();
       
        return redirect()->back()->with(['msg-success' => 'Added successfully']);
        
    }
    public function withdrawMoneyForm(Request $req) {
        $id=$req->query('id');
        $exchange=Exchange::find($id);
        return view('Admin.Exchanges.withdraw-money',compact('exchange','id'));
    }
    public function withdrawMoney(Request $req) {
        $excahnge=Exchange::find($req->hiddenid);
        $transaction=new TransactionHistory();
        $transaction->type='withdraw';
        $transaction->amount=$req->amount;
        $transaction->agent_id=session('user')->id;
        $transaction->exchange_id=$req->hiddenid;
        $transaction->opening_balance=$excahnge->amount;
        $transaction->save();
        $excahnge->amount=$excahnge->amount-$req->amount;
        $excahnge->save();
        return redirect()->back()->with(['msg-success' => 'Withdraw successfully']);
    }
}
