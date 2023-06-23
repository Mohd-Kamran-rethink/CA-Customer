<?php

namespace App\Http\Controllers;

use App\BankDetail;
use App\Client;
use App\TransactionHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BannkController extends Controller
{

    public function list()
    {
        $type=null;
        if(session('user')->role=="deposit_banker")
        {
            $type='deposit';
        }
        else if(session('user')->role=="withdrawal_banker")
        {
            $type='withdraw';
        }

        $banks = BankDetail::leftjoin('users', 'bank_details.provider_id', '=', 'users.id')
                        ->select('bank_details.*','users.name as provider_name')
                        ->whereNull('customer_id')
                        ->when($type !== null, function ($query) use ($type) {
                            $query->where('bank_details.type', $type);
                        })
                        ->paginate(30);
        return view('Admin.BAccountData.list', compact('banks'));
    }
    public function addForm($id=null)
    {
        
        $creditors=User::get();
        if (isset($id)) {
            $bank = BankDetail::find($id);
            return view('Admin.BAccountData.add', compact('bank','creditors'));
        }
        return view('Admin.BAccountData.add',compact('creditors'));
       
    }
    public function add(Request $req)
    {
        $req->validate([
            'account_number' => 'required|unique:bank_details,account_number',
            'bank_name' => 'required',
            'name' => 'required',
            'ifcs_code' => 'required',
            'phone' => 'required',
            'amount'=>'required',
            'provider'=>'required|not_in:0',
            'bank_type'=>'required|not_in:0',

        ]);

        $bank = new BankDetail();
        $bank->holder_name = $req->name;
        $bank->bank_name = $req->bank_name;
        $bank->account_number = $req->account_number;
        $bank->ifsc = $req->ifcs_code;
        $bank->phone = $req->phone;
        $bank->email = $req->email;
        $bank->amount = $req->amount;
        $bank->address = $req->address;
        $bank->provider_id = $req->provider;
        $bank->type = $req->bank_type;
        $bankresult = $bank->save();
        if($bankresult)
        {
            $transHistory=new TransactionHistory();
            $transHistory->agent_id=session('user')->id;
            $transHistory->bank_id=$bank->id;
            $transHistory->amount=$req->amount;
            $transHistory->opening_balance=0;
            $transHistory->type='Intital Deposit';
            $result= $transHistory->save();
        }

        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Added successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function edit(Request $req)
    {
        
        $req->validate([
            'account_number' => 'required',
            'bank_name' => 'required',
            'name' => 'required',
            'ifcs_code' => 'required',
            'phone' => 'required',
            'amount'=>'required',
            'provider'=>'required|not_in:0',
            'bank_type'=>'required|not_in:0',
        ]);

        $bank =  BankDetail::find($req->hiddenid);
        $bank->holder_name = $req->name;
        $bank->bank_name = $req->bank_name;
        $bank->account_number = $req->account_number;
        $bank->ifsc = $req->ifcs_code;
        $bank->phone = $req->phone;
        $bank->email = $req->email;
        $bank->amount = $req->amount;
        $bank->address = $req->address;
        $bank->provider_id = $req->provider;
        $bank->type = $req->bank_type;
        $result = $bank->save();
        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Added successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function delete(Request $req)
    {
        $BankDetail = BankDetail::find($req->deleteId);
        $BankDetail->is_active='No';
        $result = $BankDetail->update();

        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Deleted successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function reactivebaNK(Request $req) {
        $BankDetail = BankDetail::find($req->deleteId);
        $BankDetail->is_active='Yes';
        $result = $BankDetail->update();

        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Bank activated successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Something went wsrong']);
        }
    }
    public function bankAccouAddAjax(Request $req)
    {
        $req->validate([
            'account_number' => 'required',
            'bank_name' => 'required',
            'name' => 'required',
            'ifcs_code' => 'required',
        ]);

        $bank = new BankDetail();
        $bank->holder_name = $req->name;
        $bank->customer_id = $req->customer_id;
        $bank->bank_name = $req->bank_name;
        $bank->account_number = $req->account_number;
        $bank->ifsc = $req->ifcs_code;
        $bank->phone = $req->phone;
        $bank->email = $req->email;
        $bank->address = $req->address;
        $result = $bank->save();
        if ($result) {
            $banks = BankDetail::where('customer_id','=',$bank->customer_id)->get();
            $html = '
            <option value="0">--Choose--</option>
            
            ';

            foreach ($banks as $item) {
                $html .= '<option value=' . $item->id . '>' . $item->holder_name . '-(' . $item->account_number . ')</option>';
            };
            return $html;
        }
    }
    public function renderClientAccounts(Request $req)
    {
        if ($req->ajax()) {
            $client_id = $req->client_id;
            $banks = BankDetail::where('customer_id', '=', $client_id)->get();
            $html = '<label class="form-label" for="form3Example1n1">Client Bank Account <span style="color:red"> *</span></label>
            <select tabindex="2" name="client_bank_account"  class="form-control searchOptions">
            <option value="0">--Choose--</option>
            
            ';

            foreach ($banks as $item) {
                $html .= '<option value=' . $item->id . '>' . $item->holder_name . '-(' . $item->account_number . ')</option>';
            }
            '</select>';
            return $html;
        }
    }

    // transactions in banks
    public function adddepositForm($id) {
        return view('Admin.BAccountData.AddDeposit',compact('id'));
    }
    public function addDeposit(Request $req) {
        $bank=BankDetail::find( $req->hiddenid);
        $transHistory=new TransactionHistory();
        $transHistory->agent_id=session('user')->id;
        $transHistory->bank_id= $req->hiddenid;
        $transHistory->amount=$req->amount;
        $transHistory->opening_balance=$bank->amount??0;
        $transHistory->save();
        $bank->amount=$bank->amount+$req->amount;
        $result=$bank->update();
        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Added successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function addWithdrawForm($id) {
        return view('Admin.BAccountData.addWithdraw',compact('id'));
    }
    public function addWithdraw(Request $req) {
        $bank=BankDetail::find( $req->hiddenid);
        $transHistory=new TransactionHistory();
        $transHistory->agent_id=session('user')->id;
        $transHistory->bank_id= $req->hiddenid;
        $transHistory->amount=$req->amount;
        $transHistory->opening_balance=$bank->amount??0;
        $transHistory->save();
        $bank->amount=$bank->amount-$req->amount;
        $result=$bank->update();
        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Withdrawal successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function viewDetails(Request $req) {
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
        $transactions = TransactionHistory::where('bank_id', '=', $id)
                        ->leftJoin('clients', 'transaction_histories.client_id', '=', 'clients.id')
                        ->leftJoin('users', 'transaction_histories.agent_id', '=', 'users.id')
                        ->select('transaction_histories.*','clients.name as client_name','users.name as approved_by')
                        ->whereNotNull('bank_id')
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
                        ->orderBy('id','desc')
                        ->get();
        $clients=Client::get();
        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();
        return view('Admin.BAccountData.viewDetails',compact('clients','client_id','type','endDate','startDate','transactions','id'));
    }
}
