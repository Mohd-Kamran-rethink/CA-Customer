<?php

namespace App\Http\Controllers;

use App\BankDetail;
use App\Client;
use App\DepositHistory;
use App\Lead;
use App\LeadStatus;
use App\LeadStatusOption;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function dashboard(Request $req)
    {

        $search = null;
        $status = 'Pending';
        $amount_search=null;
        $start_date = $req->start_date;
        $end_date = $req->end_date;
        // Default values if start date is not available
        if (empty($start_date)) {
            $start_date = now()->toDateString();
        }
        // Default value if end date is not available
        if (empty($end_date)) {
            $end_date = now()->toDateString();
        }
        if (isset($req->table_search)) {
            $search = $req->table_search;
        }
        if (isset($req->status_name)) {
            $status = $req->status_name;
        }
        if (isset($req->amount_search)) {
            
            $amount_search = $req->amount_search;
        }
        // getting user details
        $sesstionId = session('user')->id;
        $user = User::find($sesstionId);
        // conditional data rendereing
        if ($user->role == 'customer_care_manager') {
            $depositers=User::where('role','=','deposit_banker')->get()->count();
            $depositBanker =User::where('role','=','depositer')->get()->count();
            $withdraweres=User::where('role','=','withdrawrer')->get()->count();
            $withdrawrerBanker =User::where('role','=','withdrawal_banker')->get()->count();
            // todays
            $today = now()->format('Y-m-d');
            $ApproveDepoistTranToday = Transaction::where('type', 'Deposit')->where('status', 'Approve')->whereDate('created_at', $today)->get();
            $ApprovedDepoistToday= $ApproveDepoistTranToday->sum('amount');
            $ApprovewithTranToday = Transaction::where('type', 'Withdraw')->where('status', 'Approve')->whereDate('created_at', $today)->get();
            $ApprovedWithdrawToday= $ApprovewithTranToday->sum('amount');
            // total
            $ApproveDepoistTranTotal = Transaction::where('type', 'Deposit')->where('status', 'Approve')->get();
            $ApprovedDepoistTotal= $ApproveDepoistTranTotal->sum('amount');
            $ApprovewithTranTotal = Transaction::where('type', 'Withdraw')->where('status', 'Approve')->get();
            $ApprovedWithdrawTotal= $ApprovewithTranTotal->sum('amount');

            // todays bonu
            $todaysBonus=$ApproveDepoistTranToday->sum('bonus');
           $totalBonus= $ApproveDepoistTranTotal->sum('bonus');
            return view('Admin.Dashboard.index',compact('totalBonus','todaysBonus','ApprovedWithdrawTotal','ApprovedDepoistTotal','ApprovedWithdrawToday','ApprovedDepoistToday','depositers','depositBanker','withdraweres','withdrawrerBanker'));
            // ends
        } else if ($user->role == 'deposit_banker'||$user->role == 'depositer') {
            $transactions = DB::table('transactions')->where('type', '=', 'Deposit')
                ->join('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
                ->when($status !== 'null', function ($query) use ($status) {
                    return $query->where('transactions.status', '=', $status);
                })
                ->when($search != null, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('transactions.utr_no', 'like', '%' . $search . '%');
                     });
                })
                ->when($amount_search, function ($query) use ($amount_search) {
                    $query->where(function ($query) use ($amount_search) {
                        $query->where('transactions.total', $amount_search);
                    });
                })
                ->select('transactions.*', 'bank_details.holder_name as holder_name')
                ->when($start_date != null, function ($query) use ($start_date, $end_date) {
                    $query->whereDate('transactions.date', '>=', $start_date)
                        ->whereDate('transactions.date', '<=', $end_date);
                })
                ->orderBy('id', 'desc')
                ->paginate(30);
        } 
         else if ($user->role == 'withdrawrer' ||$user->role == 'withdrawal_banker') {
            $transactions = DB::table('transactions')->where('type', '=', 'Withdraw')
                ->leftjoin('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
                ->select('transactions.*', 'bank_details.holder_name as holder_name')
                ->when($status !== 'null', function ($query) use ($status) {
                    return $query->where('transactions.status', '=', $status);
                })
                ->when($search != null, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('transactions.utr_no', 'like', '%' . $search . '%');
                     });
                })
                ->when($amount_search, function ($query) use ($amount_search) {
                    $query->where(function ($query) use ($amount_search) {
                        $query->where('transactions.total', $amount_search);
                    });
                })
                ->when($start_date != null, function ($query) use ($start_date, $end_date) {
                    $query->whereDate('transactions.date', '>=', $start_date)
                        ->whereDate('transactions.date', '<=', $end_date);
                })
                ->orderBy('id', 'desc')
                ->paginate(30);
        
        }
        return view('Admin.Dashboard.index', compact('amount_search','transactions', 'status', 'search','start_date','end_date'));
    }
    // deposit work functions
    public function addForm()
    {
       if(session('user')->role=='deposit_banker')
       {

           $todaysdate = Carbon::now()->startOfDay()->toDateString();
           $currentDateTime = Carbon::now()->startOfDay();
           $banks = BankDetail::whereNull('customer_id')->get();
           return view('Admin.Transactions.add', compact('todaysdate', 'currentDateTime', 'banks'));
        }
        else return redirect()->back();
       
    }
    public function add(Request $req)
    {
        $req->validate([
            'date' => 'required',
            'amount' => 'required',
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
        $transaction = Transaction::find($id);
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        $banks = BankDetail::get();
        return view('Admin.Transactions.add', compact('transaction', 'todaysdate', 'currentDateTime', 'banks'));
    }
    public function edit(Request $req)
    {
        $req->validate([
            'date' => 'required',
            'amount' => 'required',
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
      
        $clients = Client::where('isDeleted', '=', 'No')->get();
        $transaction = Transaction::find($id);
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        $banks = BankDetail::get();
        return view('Admin.Transactions.acceptPendingDeposit', compact('clients', 'transaction', 'todaysdate', 'currentDateTime', 'banks'));
       
    }
    // change status for deposit
    public function changeStatus(Request $req)
    {
        $req->validate([
            'date' => 'required',
            'amount' => 'required',
            'utr' => 'required',
            'total' => 'required',
            'bank_account' => 'required|not_in:0',
            'client' => 'required|not_in:0',
        ]);
        
        $depositer = session('user');
        $transaction =  Transaction::find($req->hiddenid);
        $transaction->date = $req->date;
        $transaction->amount = $req->amount;
        $transaction->bonus = $req->bonus;
        $transaction->utr_no = $req->utr;
        $transaction->total = $req->total;
        $transaction->bank_account = $req->bank_account;
        $transaction->depositer_id = $depositer->id;
        $transaction->client_id = $req->client;
        $transaction->type = 'Deposit';
        $transaction->status = 'Approve';
        // before save update lead status
        $client=Client::find($req->client);
        $status=LeadStatusOption::where('name','=','Deposited')->first();
        $client_lead=Lead::where('number','=',$client->number)->latest()->first();
        if($client_lead)
        {
            $client->agent_id=$client_lead->agent_id;
            $client->deposit_amount=$req->total;
            $client_lead->status_id=$status->id;
            $client_lead->current_status=$status->name;
            $client_lead->update();
            $client->update();
           
            $leadStatus=new LeadStatus();
           
            $leadStatus->status_id=$status->id;
            $leadStatus->lead_id=$client_lead->id;
            $leadStatus->lead_id=$client_lead->id;
            $leadStatus->save();

        }
        $depositHistory=new DepositHistory();
        $depositHistory->type="Deposit";
        $depositHistory->client_id=$client->id;
        $depositHistory->amount=$req->total;
        $depositHistory->save();
            
        $result = $transaction->save();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Transaction approved successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }
    // withdraw work functions
    public function withdrawAddForm()
    {
        if(session('user')->role=='withdrawrer')
       {
        $clients = Client::where('isDeleted', '=', 'No')->get();
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        $banks = BankDetail::get();
        return view('Admin.Transactions.addWithdrawRequest', compact('clients', 'todaysdate', 'currentDateTime', 'banks'));
       }
       else return redirect()->back();
    }
    public function withdrawAdd(Request $req)
    {
        $req->validate([
            'client' => 'required|not_in:0',
            'amount' => 'required',
            'total' => 'required',
            'client_bank_account' => 'required|not_in:0',
        ]);
        $withdrawrer = session('user');
        $transaction = new Transaction();
        $transaction->date = $req->date;
        $transaction->amount = $req->amount;
        $transaction->bonus = $req->bonus;
        $transaction->client_id = $req->client;
        $transaction->total = $req->total;
        $transaction->customer_bank_id = $req->client_bank_account;
        $transaction->withdrawrer_id = $withdrawrer->id;
        $transaction->type = 'Withdraw';
        $transaction->status = 'Pending';
        $result = $transaction->save();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Withdraw Request added successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function withdrawEditForm($id)
    {
        $transaction = Transaction::leftjoin('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
        ->select('transactions.*', 'bank_details.holder_name as holder_name')->find($id);
        $clients = Client::where('isDeleted', '=', 'No')->get();
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        
        $banks = BankDetail::where('customer_id','=',$transaction->client_id)->get();
        return view('Admin.Transactions.addWithdrawRequest', compact('transaction', 'clients', 'todaysdate', 'currentDateTime', 'banks'));
    }
    public function withdrawEdit(Request $req)
    {
        $req->validate([
            'amount' => 'required',
            'total' => 'required',
            'client_bank_account' => 'required|not_in:0',
        ]);
        $withdrawrer = session('user');
        $transaction = Transaction::find($req->hiddenid);
        $transaction->date = $req->date;
        $transaction->amount = $req->amount;
        $transaction->bonus = $req->bonus;
        $transaction->total = $req->total;
        $transaction->customer_bank_id = $req->client_bank_account;
        $transaction->withdrawrer_id = $withdrawrer->id;
        $transaction->type = 'Withdraw';
        $transaction->status = 'Pending';
        $result = $transaction->update();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Withdraw Request added successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function acceptPendingWithdrawForm($id)
    {
        $transaction = Transaction::leftjoin('bank_details', 'transactions.customer_bank_id', '=', 'bank_details.id')
        ->select('transactions.*', 'bank_details.holder_name as holder_name','bank_details.bank_name as customer_bank_name','bank_details.account_number as customer_account_number','bank_details.ifsc as customer_ifsc','bank_details.phone as customer_phone')->find($id);
        $clients = Client::where('isDeleted', '=', 'No')->get();
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        $banks = BankDetail::whereNull('customer_id')->get();
        return view('Admin.Transactions.acceptPendingWithdraw', compact('transaction', 'clients', 'todaysdate', 'currentDateTime', 'banks'));
    }
    // chaneg status for withdraw
    public function changeWithdrawStatus(Request $req)
    {
        $req->validate([
            'date' => 'required',
            'amount' => 'required',
            'utr' => 'required',
            'total' => 'required',
            'bank_account' => 'required|not_in:0',
        ]);
        $withdrawal_banker = session('user');
        $transaction =  Transaction::find($req->hiddenid);
        $transaction->date = $req->date;
        $transaction->amount = $req->amount;
        $transaction->bonus = $req->bonus;
        $transaction->utr_no = $req->utr;
        $transaction->total = $req->total;
        $transaction->bank_account = $req->bank_account;
        $transaction->withdrawal_banker_id = $withdrawal_banker->id;
        $transaction->type = 'Withdraw';
        $transaction->status = 'Approve';
        $result = $transaction->save();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Withdraw approved successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function rejectApproal(Request $req)
    {
        $session_id = session('user')->id;
        $withdrawal_banker_id = session('user')->role == 'withdrawal_banker' ? $session_id : null;
        $depositer_id = session('user')->role == 'depositer' ? $session_id : null;
        $transaction =  Transaction::find($req->hiddenId);
        $transaction->status = 'Cancel';
        $transaction->depositer_id = $depositer_id;
        $transaction->withdrawal_banker_id = $withdrawal_banker_id;
        $transaction->cancel_note = $req->cancel_note;
        $result = $transaction->save();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Transaction rejected successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }
}
