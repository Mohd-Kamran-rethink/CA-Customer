<?php

namespace App\Http\Controllers;

use App\BankDetail;
use App\Client;
use App\Exchange;
use App\Exports\TransactionsExport;
use App\LadgerHistory;
use App\Lead;
use App\LeadStatus;
use App\LeadStatusOption;
use App\Ledger;
use App\Transaction;
use App\TransactionHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function dashboard(Request $req)
    {

        $search = null;
        $status = 'Pending';
        $amount_search = null;
        $start_date = $req->start_date;
        $end_date = $req->end_date;
        $sortamount = 'desc';
        $sortBy = 'id';

        // Default values if start date is not available
        if ($req->sortamount && $req->sortamount != 'null') {
            $sortamount = $req->sortamount;
            $sortBy = 'amount';
        }
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

        // for agent todays date
        $agentstartDate = Carbon::now()->startOfDay();
        $agentEndDate = Carbon::now()->endOfDay();
        $totalApprovedForAgent = [];
        $totalWithdrawRevert = 0;
        // conditional data rendereing
        if ($user->role == 'customer_care_manager') {
            $depositers = User::where('role', '=', 'deposit_banker')->get()->count();
            $depositBanker = User::where('role', '=', 'depositer')->get()->count();
            $withdraweres = User::where('role', '=', 'withdrawrer')->get()->count();
            $withdrawrerBanker = User::where('role', '=', 'withdrawal_banker')->get()->count();
            
            // todays
            $today = now()->format('Y-m-d');
            $ApproveDepoistTranToday = Transaction::where('type', 'Deposit')->where('status', 'Approve')->whereDate('created_at', $today)->get();
            $ApprovedDepoistToday = $ApproveDepoistTranToday->sum('amount')??0;
            $ApprovewithTranToday = Transaction::where('type', 'Withdraw')->where('status', 'Approve')->whereDate('created_at', $today)->get();
            $ApprovedWithdrawToday = $ApprovewithTranToday->sum('amount')??0;
            // total
            $ApproveDepoistTranTotal = Transaction::where('type', 'Deposit')->get();
            $ApprovedDepoistTotal = $ApproveDepoistTranTotal->sum('amount')??0;
            $ApprovewithTranTotal = Transaction::where('type', 'Withdraw')->where('status', 'Approve')->get();
            $ApprovedWithdrawTotal = $ApprovewithTranTotal->sum('amount')??0;

            // pending 
            $PendingDepoistTranTotal = Transaction::where('type', 'Deposit')->where('status', 'Pending')->get();
            $PendinhwithTranTotal = Transaction::where('type', 'Withdraw')->where('status', 'Pending')->get();

            // todays deposit bonus
            $todaysDepositBonus = $ApproveDepoistTranToday->sum('bonus')??0;
            $totalDepositBonus = $ApproveDepoistTranTotal->sum('bonus')??0;

            // total withdraw bonus
            $todaysWithdrawBonus = $ApprovewithTranTotal->sum('bonus')??0;
            $totalWithdrawBonus = $ApprovewithTranTotal->sum('bonus')??0;

            $todaysBonus = $todaysDepositBonus - $todaysWithdrawBonus??0;
            $totalBonus = $totalDepositBonus - $totalWithdrawBonus??0;

            $clients = Client::get()->count();
            return view('Admin.Dashboard.index', compact('clients', 'PendinhwithTranTotal', 'PendingDepoistTranTotal', 'totalBonus', 'todaysBonus', 'ApprovedWithdrawTotal', 'ApprovedDepoistTotal', 'ApprovedWithdrawToday', 'ApprovedDepoistToday', 'depositers', 'depositBanker', 'withdraweres', 'withdrawrerBanker'));
            // ends
        } else if ($user->role == 'deposit_banker' || $user->role == 'depositer') {
            $transactions = DB::table('transactions')->where('transactions.type', '=', 'Deposit')
                ->join('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
                ->leftjoin('clients', 'transactions.client_id', '=', 'clients.id')
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
                        $query->where('transactions.amount', $amount_search);
                    });
                })
                ->select('transactions.*', 'bank_details.holder_name as holder_name', 'clients.name as client_name', 'clients.ca_id as client_id')
                ->when($start_date != null, function ($query) use ($start_date, $end_date) {
                    $query->whereDate('transactions.date', '>=', $start_date)
                        ->whereDate('transactions.date', '<=', $end_date);
                })
                ->orderBy($sortBy, $sortamount)
                ->paginate(30);
        } else if ($user->role == 'withdrawrer' || $user->role == 'withdrawal_banker') {
            $totalWithdrawRevert = Transaction::where('status', '=', 'Revert')->get();
            $transactions = DB::table('transactions')->where('transactions.type', '=', 'Withdraw')
                ->leftjoin('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
                ->leftjoin('clients', 'transactions.client_id', '=', 'clients.id')
                ->select('transactions.*', 'bank_details.holder_name as holder_name', 'clients.name as client_name', 'clients.ca_id as client_id')
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
                        $query->where('transactions.amount', $amount_search);
                    });
                })
                ->when($start_date != null, function ($query) use ($start_date, $end_date) {
                    $query->whereDate('transactions.date', '>=', $start_date)
                        ->whereDate('transactions.date', '<=', $end_date);
                })
                ->orderBy('id', 'desc')
                ->paginate(30);
        }
        $todaysApproed = [];
        // dashboard counts work
        if (session('user')->role == 'deposit_banker') {
            $totalApprovedForAgent = DB::table('transactions')
                ->where('transactions.type', '=', 'Deposit')
                ->where('status', '=', 'Pending')
                ->whereDate('transactions.created_at', '>=', date('Y-m-d', strtotime($agentstartDate)))
                ->whereDate('transactions.created_at', '<=', date('Y-m-d', strtotime($agentEndDate)))
                ->get();
            $todaysApproed = DB::table('transactions')
                ->where('status', '=', 'Approve')
                ->where('transactions.type', '=', 'Deposit')
                ->whereDate('transactions.created_at', '>=', date('Y-m-d', strtotime($agentstartDate)))
                ->whereDate('transactions.created_at', '<=', date('Y-m-d', strtotime($agentEndDate)))
                ->get();
        } else if (session('user')->role == 'depositer') {
            $totalApprovedForAgent = DB::table('transactions')
                ->where('transactions.type', '=', 'Deposit')
                ->where('status', '=', 'Pending')
                ->whereDate('transactions.created_at', '>=', date('Y-m-d', strtotime($agentstartDate)))
                ->whereDate('transactions.created_at', '<=', date('Y-m-d', strtotime($agentEndDate)))
                ->get();
        } else if (session('user')->role == 'withdrawrer') {
            $totalApprovedForAgent = DB::table('transactions')->where('transactions.type', '=', 'Withdraw')
                ->where('status', '=', 'Pending')
                ->whereDate('transactions.created_at', '>=', date('Y-m-d', strtotime($agentstartDate)))
                ->whereDate('transactions.created_at', '<=', date('Y-m-d', strtotime($agentEndDate)))
                ->get();
        } else if (session('user')->role == 'withdrawal_banker') {
            $totalApprovedForAgent = DB::table('transactions')->where('transactions.type', '=', 'Withdraw')
                ->where('status', '=', 'Pending')
                ->whereDate('transactions.created_at', '>=', date('Y-m-d', strtotime($agentstartDate)))
                ->whereDate('transactions.created_at', '<=', date('Y-m-d', strtotime($agentEndDate)))
                ->get();
            $todaysApproed = DB::table('transactions')->where('transactions.type', '=', 'Withdraw')
                ->where('status', '=', 'Approve')
                ->whereDate('transactions.created_at', '>=', date('Y-m-d', strtotime($agentstartDate)))
                ->whereDate('transactions.created_at', '<=', date('Y-m-d', strtotime($agentEndDate)))
                ->get();
        }
        return view('Admin.Dashboard.index', compact('totalWithdrawRevert', 'todaysApproed', 'totalApprovedForAgent', 'amount_search', 'transactions', 'status', 'search', 'start_date', 'end_date'));
    }
    // deposit work functions
    public function addForm(Request $req)
    {

        if (session('user')->role == 'deposit_banker') {

            $todaysdate = Carbon::now()->startOfDay()->toDateString();
            $currentDateTime = Carbon::now()->startOfDay();
            // $todaysdate=$req->query('date');
            $banks = BankDetail::whereNull('customer_id')->where('is_active', '=', 'Yes')->get();
            return view('Admin.Transactions.add', compact('todaysdate', 'currentDateTime', 'banks'));
        } else return redirect()->back();
    }
    public function add(Request $req)
    {
        // new transaction create on submit add money to the bank
        $req->validate([
            'date' => 'required',
            'amount' => 'required',
            'utr' => 'required|unique:transactions,utr_no',
            'bank_account' => 'required|not_in:0',
        ]);
        $deposit_banker = session('user');
        $transaction = new Transaction();
        $transaction->date = $req->date;
        $transaction->amount = $req->amount;
        $transaction->utr_no = $req->utr;
        $transaction->bank_account = $req->bank_account;
        $transaction->deposit_banker_id = $deposit_banker->id;
        $transaction->type = 'Deposit';
        $transaction->status = 'Pending';
        $result = $transaction->save();
        // add money to banks and also show transaction history to the bank 
        $bank = BankDetail::find($req->bank_account);
        $depositHistory = new TransactionHistory();
        $depositHistory->type = "Deposit";
        $depositHistory->transaction_id = $transaction->id;
        $depositHistory->bank_id = $bank->id;
        $depositHistory->agent_id = session('user')->id;
        $depositHistory->amount = $req->amount;
        $depositHistory->opening_balance = $bank->amount;
        $depositHistory->current_balance = $bank->amount + $req->amount;
        $depositHistory->save();

        $bank->amount = $bank->amount + $req->amount;
        $bank->save();
        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Transaction added successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Something went wrong']);
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
        $banks = BankDetail::whereNull('customer_id')->where('is_active', '=', 'Yes')->get();
        return view('Admin.Transactions.add', compact('transaction', 'todaysdate', 'currentDateTime', 'banks'));
    }
    public function edit(Request $req)
    {

        $transaction = Transaction::find($req->hiddenid);
        $bank = BankDetail::find($transaction->bank_account);
        $bankoldAmount = $bank->amount - $transaction->amount;
        $bank->amount = ($bank->amount - $transaction->amount) + $req->amount;
        $bank->save();
        /* $req->validate([
            'date' => 'required',
            'amount' => 'required',
            'utr' => 'required',
            'total' => 'required',
        ]);*/
        $deposit_banker = session('user');
        $transaction->date = $req->date;
        $transaction->amount = $req->amount;
        $transaction->bonus = $req->bonus;
        $transaction->utr_no = $req->utr;
        $transaction->total = $req->total;
        //$transaction->bank_account = $req->bank_account;
        $transaction->deposit_banker_id = $deposit_banker->id;
        $transaction->type = 'Deposit';
        $transaction->status = 'Pending';
        $result = $transaction->save();
        $transHistory = TransactionHistory::where('transaction_id', '=', $transaction->id)->where('type', '=', 'deposit')->first();

        $transHistory->agent_id = session('user')->id;
        $transHistory->amount = $req->amount;
        $transHistory->opening_balance = $bankoldAmount;
        $transHistory->current_balance = $bankoldAmount + $req->amount;
        $transHistory->update();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Transaction updated successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }

    public function remove($id)
    {
        $transactiHistory = null;
        $transaction = Transaction::find($id);
        $transaction->status = 'Cancel';
        $transaction->save();
        if ($transaction->type == 'Deposit') {
            
            $bank = BankDetail::find($transaction->bank_account);
            $bank->amount = $bank->amount - $transaction->amount;
            $bank->update();
            $transactiHistory = TransactionHistory::where('transaction_id', '=', $transaction->id)->where('type', '=', 'Deposit')->first();
        }
        if ($transactiHistory) {
            $transactiHistory->delete();
        }
        return redirect('/dashboard')->with(['msg-success' => 'Transaction updated successfully']);
    }

    public function acceptPendingDepositForm($id)
    {

        $clients = Client::where('isDeleted', '=', 'No')->get();
        $transaction = Transaction::find($id);
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        $banks = BankDetail::get();
        $exchanges = Exchange::where('is_active', '=', 'Yes')->get();
        return view('Admin.Transactions.acceptPendingDeposit', compact('exchanges', 'clients', 'transaction', 'todaysdate', 'currentDateTime', 'banks'));
    }
    // change status for deposit
    public function changeStatus(Request $req)
    {
        $req->validate([
            'date' => 'required',
            'amount' => 'required',
            'utr' => 'required',
            'total' => 'required',
            'client' => 'required|not_in:0',
            'exchange_id' => 'required|not_in:0'
        ]);

        $depositer = session('user');
        $transaction =  Transaction::find($req->hiddenid);
        $transaction->date = $req->date;
        $transaction->bonus = $req->bonus;
        $transaction->utr_no = $req->utr;
        $transaction->total = $req->total;
        $transaction->depositer_id = $depositer->id;
        $transaction->client_id = $req->client;
        $transaction->exchange_id = $req->exchange_id;
        $transaction->status = 'Approve';
        // before save update lead status
        $client = Client::find($req->client);
        $status = LeadStatusOption::where('name', '=', 'Deposited')->first();
        $client_lead = Lead::where('number', '=', intval($client->number))->latest()->first();
        if ($client_lead) {
            $client->agent_id = $client_lead->agent_id;
            $client->deposit_amount = $req->total;
            $client_lead->status_id = $status->id;
            $client_lead->current_status = $status->name;
            $client_lead->update();
            $client->update();
            $leadStatus = new LeadStatus();
            $leadStatus->status_id = $status->id;
            $leadStatus->lead_id = $client_lead->id;
            $leadStatus->lead_id = $client_lead->id;
            $leadStatus->save();
        }
        $transaction->save();
        $exchange = Exchange::find($req->exchange_id);
        // for exchange tranasactin history
        $depositHistory = new TransactionHistory();
        $depositHistory->type = "Withdraw";
        $depositHistory->transaction_id = $transaction->id;
        $depositHistory->exchange_id = $req->exchange_id;
        $depositHistory->agent_id = session('user')->id;
        $depositHistory->client_id = $client->id;
        $depositHistory->amount = $req->amount;
        $depositHistory->opening_balance = $exchange->amount;
        $depositHistory->bonus = $req->bonus;
        $depositHistory->save();

        //increase exchnage total 
        $exchange->amount = $exchange->amount - $req->total;
        $result = $exchange->update();

        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Transaction approved successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }
    // withdraw work functions
    public function withdrawAddForm()
    {
        if (session('user')->role == 'withdrawrer') {
            $clients = Client::where('isDeleted', '=', 'No')->get();
            $todaysdate = Carbon::now()->startOfDay()->toDateString();
            $currentDateTime = Carbon::now()->startOfDay();
            $banks = BankDetail::whereNotNull('customer_id')->get();
            $exchanges = Exchange::orderby('id', 'desc')->get();
            return view('Admin.Transactions.addWithdrawRequest', compact('exchanges', 'clients', 'todaysdate', 'currentDateTime', 'banks'));
        } else return redirect()->back();
    }
    public function withdrawAdd(Request $req)
    {
        $req->validate([
            'client' => 'required|not_in:0',
            'amount' => 'required',
            'total' => 'required',
            'client_bank_account' => 'required|not_in:0',
            'exchange_id' => 'required|not_in:0',
        ]);
        $withdrawrer = session('user');
        $transaction = new Transaction();
        $transaction->client_id = $req->client;
        $transaction->customer_bank_id = $req->client_bank_account;
        $transaction->exchange_id = $req->exchange_id;
        $transaction->amount = $req->amount;
        $transaction->bonus = $req->bonus;
        $transaction->date = $req->date;
        $transaction->withdrawrer_id = $withdrawrer->id;
        $transaction->type = 'Withdraw';
        $transaction->status = 'Pending';
        $transaction->save();
        // add moeny to exchange
        $exchange = Exchange::orderBy('id', 'desc')->find($req->exchange_id);

        // add transaction history for this exhange transaction
        $transHistory = new TransactionHistory();
        $transHistory->agent_id = session('user')->id;
        $transHistory->exchange_id = $exchange->id;
        $transHistory->client_id = $req->client;
        $transHistory->transaction_id = $transaction->id;
        $transHistory->amount = $req->amount;
        $transHistory->bonus = $req->bonus;
        $transHistory->opening_balance = $exchange->amount;
        $transHistory->type = 'Deposit';
        $transHistory->save();

        //save exchange
        $exchange->amount = $exchange->amount + $req->amount + $req->bonus;
        $result = $exchange->update();
        // send sms
        /*$client = Client::find($req->client);
        $curl = curl_init();
        $receiverNumber = $client->number;
        $message = "hello";
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://103.241.136.228/api/mt/SendSMS?APIKey=0lDK5f3S5kyUc9gRUiUTUg&senderid=RRSMSG&channel=2&DCS=0&flashsms=0&number=' . $receiverNumber . '&text=Your%20login%20OTP%20code%20is%20' . $message . '%20RP&route=1', CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'User-Agent: PostmanRuntime/7.31.3',
                'device_type: android',
                'version: 1.0',
                'lang: en',
                'device_token: 123123123',
                'user_token: ie9611bbbe9ce6bc572666a63',
                'id: 6'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);*/

        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Withdraw Request added successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function withdrawEditForm($id)
    {
        $transaction = Transaction::leftjoin('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
            ->select('transactions.*', 'bank_details.holder_name as holder_name')->find($id);
        $clients = Client::where('isDeleted', '=', 'No')->get();
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        $exchanges = Exchange::where('is_active', '=', 'Yes')->get();
        $banks = BankDetail::where('customer_id', '=', $transaction->client_id)->get();
        return view('Admin.Transactions.addWithdrawRequest', compact('exchanges', 'transaction', 'clients', 'todaysdate', 'currentDateTime', 'banks'));
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
            ->select('transactions.*', 'bank_details.holder_name as holder_name', 'bank_details.bank_name as customer_bank_name', 'bank_details.account_number as customer_account_number', 'bank_details.ifsc as customer_ifsc', 'bank_details.phone as customer_phone')->find($id);
        $clients = Client::where('isDeleted', '=', 'No')->get();
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        $banks = BankDetail::whereNull('customer_id')->where('is_active', '=', 'Yes')->get();
        $ledgers=Ledger::get();
        return view('Admin.Transactions.acceptPendingWithdraw', compact('ledgers','transaction', 'clients', 'todaysdate', 'currentDateTime', 'banks'));
    }
    // chaneg status for withdraw
    public function changeWithdrawStatus(Request $req)
    {
        $req->validate([
            'date' => 'required',
            'amount' => 'required',
            'utr' => 'required|unique:transactions,utr_no',
        ]);
        $withdrawal_banker = session('user');
        $transaction =  Transaction::find($req->hiddenid);
        $transaction->date = $req->date;
        $transaction->amount = $req->amount;
        $transaction->utr_no = $req->utr;
        $transaction->total = $req->total;
        $transaction->bank_account = $req->bank_account;
        $transaction->ledger = $req->ledger;
        $transaction->withdrawal_banker_id = $withdrawal_banker->id;
        $transaction->type = 'Withdraw';
        $transaction->status = 'Approve';

        if($req->bank_account!=0||$req->bank_account!=null)
        {
            $bankDetails = BankDetail::find($transaction->bank_account);
            //bank transaction history
            $deposit = new TransactionHistory();
            $deposit->agent_id = session('user')->id;
            $deposit->bank_id = $req->bank_account;
            $deposit->transaction_id = $transaction->id;
            $deposit->client_id = $transaction->client_id;
            $deposit->amount = $req->amount;
            $deposit->opening_balance = $bankDetails->amount;
            $deposit->current_balance = $bankDetails->amount - $req->amount;
            $deposit->type = "withdraw";
            $deposit->save();
            $bankDetails->amount = $bankDetails->amount - $req->amount;
            $bankDetails->update();
        }
        if($req->ledger)
        {
            $ledeger=Ledger::find($req->ledger);
            $ledgerHistory=new LadgerHistory();
            $ledgerHistory->opening_balance=$ledeger->amount;
            $ledgerHistory->user_id=session('user')->id;
            $ledgerHistory->closing_balance=$ledeger->amount-$req->amount;
            $ledgerHistory->amount=$req->amount;
            $ledgerHistory->type='Withdraw';
            $ledgerHistory->ledger_id=$req->ledger;
            $ledgerHistory->save();
            $ledeger->amount=$ledeger->amount-$req->amount;
            $ledeger->update();
        }

            





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
    public function listPendingDeposit(Request $req)
    {
        $start_date = $req->start_date;
        $end_date = $req->end_date;
        if (empty($start_date)) {
            $start_date = now()->toDateString();
        }
        // Default value if end date is not available
        if (empty($end_date)) {
            $end_date = now()->toDateString();
        }
        $transactions = Transaction::leftjoin('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
            ->where('transactions.type', 'Deposit')->where('transactions.status', 'Pending')
            ->select('transactions.*', 'bank_details.holder_name as holder_name')
            ->when($start_date != null, function ($query) use ($start_date, $end_date) {
                $query->whereDate('transactions.date', '>=', $start_date)
                    ->whereDate('transactions.date', '<=', $end_date);
            })
            ->get();
        $heading = "Pending Deposits";
        $url = 'transactions/pending-deposit';
        return view('Admin.Transactions.transAdmin', compact('heading', 'transactions', 'start_date', 'end_date', 'url'));
    }
    public function pendingWithdraw(Request $req)
    {
        $start_date = $req->start_date;
        $end_date = $req->end_date;
        if (empty($start_date)) {
            $start_date = now()->toDateString();
        }
        // Default value if end date is not available
        if (empty($end_date)) {
            $end_date = now()->toDateString();
        }
        $transactions = Transaction::leftjoin('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
            ->where('transactions.type', 'Withdraw')
            ->where('transactions.status', 'Pending')
            ->select('transactions.*', 'bank_details.holder_name as holder_name')
            ->when($start_date != null, function ($query) use ($start_date, $end_date) {
                $query->whereDate('transactions.date', '>=', $start_date)
                    ->whereDate('transactions.date', '<=', $end_date);
            })
            ->get();
        $heading = "Pending Withdraw";
        $url = 'transactions/pending-withdraw';
        return view('Admin.Transactions.transAdmin', compact('heading', 'transactions', 'start_date', 'end_date', 'url'));
    }
    // approved withdraws
    public function approvedWithdraws(Request $req)
    {
        $start_date = $req->start_date;
        $end_date = $req->end_date;
        if (empty($start_date)) {
            $start_date = now()->toDateString();
        }
        // Default value if end date is not available
        if (empty($end_date)) {
            $end_date = now()->toDateString();
        }

        $transactions = Transaction::leftjoin('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
            ->where('transactions.type', 'Withdraw')
            ->where('transactions.status', 'Approve')
            ->select('transactions.*', 'bank_details.holder_name as holder_name')
            ->when($start_date != null, function ($query) use ($start_date, $end_date) {
                $query->whereDate('transactions.date', '>=', $start_date)
                    ->whereDate('transactions.date', '<=', $end_date);
            })
            ->get();
        $heading = "Approved Withdraw";

        $url = 'transactions/approved-withdraws';
        return view('Admin.Transactions.transAdmin', compact('heading', 'transactions', 'start_date', 'end_date', 'url'));
    }
    public function approvedDeposit(Request $req)
    {
        $start_date = $req->start_date;
        $end_date = $req->end_date;
        if (empty($start_date)) {
            $start_date = now()->toDateString();
        }
        // Default value if end date is not available
        if (empty($end_date)) {
            $end_date = now()->toDateString();
        }
        $transactions = Transaction::leftjoin('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
            ->where('transactions.type', 'Deposit')->where('transactions.status', 'Approve')
            ->select('transactions.*', 'bank_details.holder_name as holder_name')
            ->when($start_date != null, function ($query) use ($start_date, $end_date) {
                $query->whereDate('transactions.date', '>=', $start_date)
                    ->whereDate('transactions.date', '<=', $end_date);
            })
            ->get();
        $heading = "Approved Deposits";
        $url = 'transactions/approved-deposits';
        return view('Admin.Transactions.transAdmin', compact('heading', 'transactions', 'start_date', 'end_date', 'url'));
    }
    // canceled
    public function depsoiterCancel(Request $req)
    {
        $transaction = Transaction::find($req->transID);
        if (session('user')->role = 'withdrawrer') {
            $transaction->status = 'Cancel';
            $transaction->update();
        } else {
            $transaction->client_id = '';
            $transaction->total = $$transaction->total - $transaction->bonus;
            $transaction->bonus = '';
        }
        $transaction->status = 'Cancel';
        $exchange = Exchange::find($transaction->exchange_id);
        $bank = BankDetail::find($transaction->bank_account);
        $transaction->update();
        $depositHistory = TransactionHistory::where('transaction_id', '=', $transaction->id)->get();
        if ($depositHistory) {
            foreach ($depositHistory as $key => $item) {
                $item->type = "deposit_revert";
                $item->update();
            }
        }
        $exchange->amount = $exchange->amount - $transaction->total;
        $bank->amount = $bank->amount - $transaction->total;
        $exchange->update();
        $bank->update();
        return redirect('/dashboard')->with(['msg-success' => 'Transaction has been cancelled']);
    }
    // withdraw cancel
    public function withdrawCancel(Request $req)
    {
        $transaction = Transaction::find($req->transID);
        if (session('user')->role = 'withdrawrer') {
            $transaction->status = 'Cancel';
            $transaction->update();
        } else {
            $transaction->client_id = '';
            $transaction->bonus = '';
        }
        $transaction->status = 'Cancel';
        $exchange = Exchange::find($transaction->exchange_id);
        $bank = BankDetail::find($transaction->bank_account);
        $transaction->update();
        $depositHistory = TransactionHistory::where('transaction_id', '=', $transaction->id)->get();
        if ($depositHistory) {
            foreach ($depositHistory as $key => $item) {
                $depositHistory->type = "withdraw_revert";
                $depositHistory->update();
            }
        }
        $exchange->amount = $exchange->amount + $transaction->total;
        $bank->amount = $bank->amount + $transaction->total;
        $exchange->update();
        $bank->update();
        return redirect('/dashboard')->with(['msg-success' => 'Transaction has been cancelled']);
    }

    public function exportPending(Request $req)
    {
        $transactions = Transaction::leftjoin('bank_details', 'transactions.bank_account', '=', 'bank_details.id')
            ->where('transactions.type', $req->type)->where('transactions.status', 'Pending')
            ->select('transactions.amount', 'transactions.bonus', 'transactions.utr_no', 'bank_details.holder_name as holder_name', 'bank_details.bank_name as bank', 'transactions.created_at')
            ->get();
        $export = new TransactionsExport($transactions);
        return Excel::download($export, 'transactions.xlsx');
    }



    // with draw banker edit
    public function widdrawBankerEditForm($id)
    {
        $transaction = Transaction::leftjoin('bank_details', 'transactions.customer_bank_id', '=', 'bank_details.id')
            ->select('transactions.*', 'bank_details.holder_name as holder_name', 'bank_details.bank_name as customer_bank_name', 'bank_details.account_number as customer_account_number', 'bank_details.ifsc as customer_ifsc', 'bank_details.phone as customer_phone')
            ->find($id);
        $clients = Client::where('isDeleted', '=', 'No')->get();
        $todaysdate = Carbon::now()->startOfDay()->toDateString();
        $currentDateTime = Carbon::now()->startOfDay();
        $banks = BankDetail::whereNull('customer_id')->where('is_active', '=', 'Yes')->get();
        $edit = "yes";
        return view('Admin.Transactions.acceptPendingWithdraw', compact('edit', 'transaction', 'clients', 'todaysdate', 'currentDateTime', 'banks'));
    }
    public function widdrawBankerEdit(Request $req)
    {
        $transaction = Transaction::find($req->hiddenid);
        $transaction->utr_no = $req->utr;
        $result = $transaction->update();
        if ($result) {
            return redirect('/dashboard')->with(['msg-success' => 'Updated successfully']);
        } else {
            return redirect('/dashboard')->with(['msg-error' => 'Something went wrong']);
        }
    }



    // revert withdraw 
    public function revertWithdraw(Request $req)
    {
        $transaction = Transaction::find($req->hiddenId);
        if ($transaction->bank_account) {
            $bank = BankDetail::find($transaction->bank_account);
            $bank->amount = $bank->amount + $transaction->amount;
            $bank->update();
            $transHistory = TransactionHistory::where('transaction_id', '=', $transaction->id)->where('type', '=', 'withdraw')->first();
            $transHistory->delete();
        }
        $resulthistory = $transaction->status = "Revert";
        if ($resulthistory) {
            $transaction->update();
        }
        return redirect('/dashboard');
    }

    function  cancelReverted(Request $req)
    {
        $transaction = Transaction::find($req->cancelId);
        $exchange = Exchange::find($transaction->exchange_id);
        $transHistory = TransactionHistory::where('transaction_id', '=', $transaction->id)->where('type', '=', 'Deposit')->first();
        $exchange->amount = $exchange->amount - ($transaction->amount + $transaction->bonus);
        $exchange->update();
        $transhistoryResult = $transHistory->delete();
        $transaction->status = "Cancel";
        if ($transhistoryResult) {
            $transaction->update();
        }


        return redirect('dashboard');
    }


    // import deposits
    public function DepositImportFOrm()
    {
        return view('depositImport');
    }
    public function DepositImport(Request $req)
    {
        $file = $req->file('excel_file');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->path());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->path());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        $entries = [];
        $columnHeaders = array_shift($rows);
        $clients = Client::pluck('ca_id', 'id')->map(function ($name) {
            return trim($name);
        })->toArray();
        $exchanges = Exchange::pluck('name', 'id')->map(function ($name) {
            return trim($name);
        })->toArray();

        foreach ($rows as $key => $row) {

            $data = array_combine($columnHeaders, $row);

            //            print_r($data);


            if ($row[2] != null) {
                $bank_number = explode('[', $row[2]);
                if (count($bank_number) > 2) {
                    $bank_account_number = (str_replace(']', '', $bank_number[3]));
                    if ($bank_account_number) {
                        $bank = BankDetail::where('account_number', '=', $bank_account_number)->first();
                    }
                }
                //                 exit;




                $leads_dateDateserialNumber = $data['Date']; // This is the serial number for the date "01/01/2021"
                $leads_dateunixTimestamp = ($leads_dateDateserialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
                $leads_date = \Carbon\Carbon::createFromTimestamp($leads_dateunixTimestamp);
                $leads_dateformattedDate = $leads_date->format('Y-m-d H:i:s');
                //for leads_date
                $exchnageID = array_search(strtolower(trim($data['Exchange Name'])), array_map('strtolower', $exchanges));

                $clientID = array_search(strtolower(trim($data['Client'])), array_map('strtolower', $clients));
                if (!$clientID) {
                    $clientID = 0;
                }
                $transaction = new Transaction();
                $transaction->client_id = $clientID;
                $transaction->amount = $data['Deposit'];
                $transaction->bonus = $data['Bonus'];
                $transaction->utr_no = $data['UTR No'];
                $transaction->type = 'Deposit';
                $transaction->status = 'Approve';
                $transaction->bank_account = $bank->id ?? '';
                $transaction->exchange_id = $exchnageID;
                $transaction->date = $leads_dateformattedDate;
                $transaction->save();
                $transactiHistory = new TransactionHistory();
                $transactiHistory->bank_id = $bank->id ?? '';
                $transactiHistory->amount = $data['Deposit'];
                $transactiHistory->bonus = $data['Bonus'];
                $transactiHistory->type = 'Deposit';
                $transactiHistory->amount = $data['Deposit'];
                $transactiHistory->opening_balance = $bank->amount;
                $transactiHistory->current_balance = $bank->amount + $data['Deposit'];
                $transactiHistory->client_id = $clientID;
                $transactiHistory->created_at = $leads_dateformattedDate;
                $transactiHistory->save();
                $bank->amount = $bank->amount + $data['Deposit'];
                $bank->save();
                if ($exchnageID) {

                    $exchange = Exchange::find($exchnageID);
                    // for exchange tranasactin history
                    $ExchnagedepositHistory = new TransactionHistory();
                    $ExchnagedepositHistory->type = "Withdraw";
                    $ExchnagedepositHistory->transaction_id = $transaction->id;
                    $ExchnagedepositHistory->exchange_id = $exchnageID;
                    $ExchnagedepositHistory->agent_id = session('user')->id;
                    $ExchnagedepositHistory->client_id = $clientID;
                    $ExchnagedepositHistory->amount = $data['Deposit'];
                    $ExchnagedepositHistory->opening_balance = $exchange->amount;
                    $ExchnagedepositHistory->bonus = $data['Bonus'];
                    $ExchnagedepositHistory->created_at = $leads_dateformattedDate;
                    $ExchnagedepositHistory->save();

                    //increase exchnage total
                    $exchange->amount = $exchange->amount - $data['Total'];
                    $result = $exchange->update();
                }
            }
        }
        exit;
    }
    public function WithdrawImportFOrm()
    {
        return view('withdrawImport');
    }
     public function WithdrawImport(Request $req)
    {
        $file = $req->file('excel_file');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->path());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->path());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        $entries = [];
        $columnHeaders = array_shift($rows);
        $clients = Client::pluck('ca_id', 'id')->map(function ($name) {
            return trim($name);
        })->toArray();
        $exchanges = Exchange::pluck('name', 'id')->map(function ($name) {
            return trim($name);
        })->toArray();

        foreach ($rows as $key => $row) {

            $data = array_combine($columnHeaders, $row);
            //echo "<pre>";
            // print_r($data);
            //continue;

            if ($row[2] != null) {
                $bank_number = explode('[', $row[2]);
                if (count($bank_number) > 2) {
                    $bank_account_number = (str_replace(']', '', $bank_number[3]));
                    if ($bank_account_number) {
                        $bank = BankDetail::where('account_number', '=', $bank_account_number)->first();
                    } else {
                        print_r($data);
                        exit;
                    }
                } else {
                    print_r($data);
                    exit;
                }





                $leads_dateDateserialNumber = $data['Date']; // This is the serial number for the date "01/01/2021"
                $leads_dateunixTimestamp = ($leads_dateDateserialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
                $leads_date = \Carbon\Carbon::createFromTimestamp($leads_dateunixTimestamp);
                $leads_dateformattedDate = $leads_date->format('Y-m-d H:i:s');
                //for leads_date
                $exchnageID = array_search(strtolower(trim($data['Exchange Name'])), array_map('strtolower', $exchanges));

                $clientID = array_search(strtolower(trim($data['Client'])), array_map('strtolower', $clients));
                if (!$clientID) {
                    $clientID = 0;
                    
                }
                $transaction = new Transaction();
                $transaction->client_id = $clientID;
                $transaction->amount = $data['Withdraw'];
                $transaction->bonus = $data['Bonus'];
                $transaction->utr_no = $data['UTR No'];
                $transaction->type = 'Withdraw';
                $transaction->status = 'Approve';
                $transaction->bank_account = $bank->id;
                $transaction->exchange_id = $exchnageID;
                $transaction->date = $leads_dateformattedDate;
                $transaction->save();
                
                $transactiHistory = new TransactionHistory();
                $transactiHistory->bank_id = $bank->id;
                $transactiHistory->amount = $data['Withdraw'];
                $transactiHistory->bonus = $data['Bonus'];
                $transactiHistory->type = 'Withdraw';
                $transactiHistory->opening_balance = $bank->amount;
                $transactiHistory->current_balance = $bank->amount - $data['Withdraw'];
                $transactiHistory->client_id = $clientID ?? '';
                $transactiHistory->created_at = $leads_dateformattedDate;
                $transactiHistory->save();
                $bank->amount = $bank->amount - $data['Withdraw'];
                $bank->save();
                
                if ($exchnageID) {

                    $exchange = Exchange::find($exchnageID);
                    // for exchange tranasactin history
                    $ExchnagedepositHistory = new TransactionHistory();
                    $ExchnagedepositHistory->type = "Deposit";
                    $ExchnagedepositHistory->transaction_id = $transaction->id;
                    $ExchnagedepositHistory->exchange_id = $exchnageID;
                    $ExchnagedepositHistory->agent_id = session('user')->id;
                    $ExchnagedepositHistory->client_id = $clientID;
                    $ExchnagedepositHistory->amount = $data['Withdraw'];
                    $ExchnagedepositHistory->opening_balance = $exchange->amount;
                    $ExchnagedepositHistory->bonus = $data['Bonus'];
                    $ExchnagedepositHistory->created_at = $leads_dateformattedDate;
                    $ExchnagedepositHistory->save();

                    //increase exchnage total
                    $exchange->amount = $exchange->amount + $data['Total'];
                    $result = $exchange->update();
                }
            }
        }
        exit;
    }

    
}
