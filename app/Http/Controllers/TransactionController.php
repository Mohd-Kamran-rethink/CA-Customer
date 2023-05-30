<?php

namespace App\Http\Controllers;

use App\BankDetail;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function dashboard(Request $req)
    {
        
        $search=null;
        $status=null;
        if(isset($req->table_search))
        {
            $search=$req->table_search;
        }
        if(isset($req->status_name))
        {
            $status=$req->status_name;
        }
        // getting user details
        $sesstionId = session('user')->id;
        $user = User::find($sesstionId);
        // conditional data rendereing
        if($user->role == 'customer_care_manager')
        {
            return view('Admin.Dashboard.index');
        }
        else if ($user->role == 'deposit_banker') {
            $transactions = DB::table('transactions')->where('type', '=', 'Deposit')
                ->join('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
                ->when($status != null, function ($query) use ($status) {
                    $query->where('transactions.status', '=', $status);
                })
                ->when($search != null, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('transactions.utr_no', 'like', '%' . $search . '%');
                    });
                })
                ->select('transactions.*', 'bank_details.holder_name as holder_name')
                ->orderBy('id','desc')
                ->paginate(30);
        }
        else if($user->role == 'depositer')
        {
            $transactions = DB::table('transactions')->where('type', '=', 'Deposit')
            ->join('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
            ->select('transactions.*', 'bank_details.holder_name as holder_name')
            ->when($status != 'null', function ($query) use ($status) {
                $query->where('transactions.status', '=', $status);
            })
            ->when($search != null, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('transactions.utr_no', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id','desc')
            ->paginate(30);
        }
        return view('Admin.Dashboard.index', compact('transactions','status','search'));
    }
    public function addForm()
    {
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        $banks = BankDetail::get();
        return view('Admin.Transactions.add', compact('todaysdate', 'currentDateTime', 'banks'));
    }
    public function add(Request $req)
    {
        $req->validate([
            'date' => 'required',
            'amount' => 'required',
            'bonus' => 'required',
            'utr' => 'required',
            'total' => 'required',
            'bank_account' => 'required|not_in:0',
        ]);
        $deposit_banker = session('user');
        $transaction = new Transaction();
        $transaction->date = $req->date;
        $transaction->amount = $req->amount;
        $transaction->bonus = $req->bonus;
        $transaction->utr_no = $req->utr;
        $transaction->total = $req->total;
        $transaction->bank_account = $req->bank_account;
        $transaction->deposit_banker_id = $deposit_banker->id;
        $transaction->type = 'Deposit';
        $transaction->status = 'Pending';
        $result = $transaction->save();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Transaction added successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function delete(Request $req)
    {
        $transaction = Transaction::find($req->deleteId);
        $result = $transaction->delete();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Transaction deleted successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function editForm($id)
    {
        $transaction=Transaction::find($id);
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        $banks = BankDetail::get();
        return view('Admin.Transactions.add', compact('transaction','todaysdate', 'currentDateTime', 'banks'));
    }
    public function edit(Request $req)
    {
        $req->validate([
            'date' => 'required',
            'amount' => 'required',
            'bonus' => 'required',
            'utr' => 'required',
            'total' => 'required',
            'bank_account' => 'required|not_in:0',
        ]);
        $deposit_banker = session('user');
        $transaction =  Transaction::find($req->hiddenid);
        $transaction->date = $req->date;
        $transaction->amount = $req->amount;
        $transaction->bonus = $req->bonus;
        $transaction->utr_no = $req->utr;
        $transaction->total = $req->total;
        $transaction->bank_account = $req->bank_account;
        $transaction->deposit_banker_id = $deposit_banker->id;
        $transaction->type = 'Deposit';
        $transaction->status = 'Pending';
        $result = $transaction->save();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Transaction updated successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function acceptPendingDepositForm($id)
    {
        $transaction=Transaction::find($id);
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        $banks = BankDetail::get();
        return view('Admin.Transactions.acceptPendingDeposit', compact('transaction','todaysdate', 'currentDateTime', 'banks'));
    }
    public function changeStatus(Request $req)
    {
        $req->validate([
            'date' => 'required',
            'amount' => 'required',
            'bonus' => 'required',
            'utr' => 'required',
            'total' => 'required',
            'bank_account' => 'required|not_in:0',
        ]);
        $depositer = session('user');
        $transaction =  Transaction::find($req->hiddenid);
        $transaction->date = $req->date;
        $transaction->amount = $req->amount;
        $transaction->bonus = $req->bonus;
        $transaction->utr_no = $req->utr;
        $transaction->total = $req->total;
        $transaction->bank_account = $req->bank_account;
        $transaction->deposit_banker_id = $depositer->id;
        $transaction->type = 'Deposit';
        $transaction->status = 'Approve';
        $result = $transaction->save();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Transaction approved successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function rejectApproal(Request $req)
    {
        
        $transaction =  Transaction::find($req->hiddenId);
        $transaction->status = 'Cancel';
        $transaction->cancel_note =$req->cancel_note;
        $result = $transaction->save();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Transaction rejected successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }
  
}
