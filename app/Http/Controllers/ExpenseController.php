<?php

namespace App\Http\Controllers;

use App\BankDetail;
use App\Department;
use App\Expense;
use App\ExpenseType;
use App\Transaction;
use App\TransactionHistory;
use App\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function list() 
    {
       $expenses=Expense::where('user_id','=',session('user')->id)->get();
       $expenseType=ExpenseType::get();
       return view('Admin.Expenses.list',compact('expenses','expenseType'));     
    }

    public function addForm() {
        $expenseType=ExpenseType::get();
        $departments=Department::get();
        $banks=BankDetail::whereNull('customer_id')->where('is_active','=','Yes')->get();
        return view('Admin.Expenses.addForm',compact('expenseType','banks','departments'));     
    }
    public function add() {
        
    }
         
        





    // transfers
    public function TransferList(Request $req)
    {
        $startDate = $req->query('from_date')??null;
        $endDate = $req->query('to_date')??null;
        if (!$startDate) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        }
        $transfers=Transfer::
                            whereDate('created_at', '>=', date('Y-m-d', strtotime($startDate)))
                            ->whereDate('created_at', '<=', date('Y-m-d', strtotime($endDate)))
                            ->get();
        return view('Admin.Transfers.list',compact('transfers'));
    }
    public function addTransferForm() 
    {
        $banks=BankDetail::whereNull('customer_id')->where('is_active','=','yes')->get();
        return view('Admin.Transfers.addForm',compact('banks'));    
    }

    public function addTransfer(Request $req) 
    {
        $req->validate([
            'from_bank'=>'required|not_in:0',
            'to_bank'=>'required|not_in:0',
            'amount'=>'required',
        ]);
        $transfer=new Transfer();
        $transfer->user_id=session('user')->id;
        $transfer->from_bank=$req->from_bank;
        $transfer->to_bank=$req->to_bank;
        $transfer->amount=$req->amount;
        $transfer->remark=$req->remark;
        $transfer->save();
       
        // now manage transaction in both the banks and give the type transfer IN and Transfer Out
        //show transaction history in transactin history table one will be deposit and another is withdraws

        // first get from bank details and enter a trans entery with opening banale=current abalance
        //after entery deduct the money from bank
       
        $bankFrom=BankDetail::find($req->from_bank);
        
        $trnascationForFromBank=new TransactionHistory();
        $trnascationForFromBank->agent_id=session('user')->id;
        $trnascationForFromBank->bank_id=$bankFrom->id;
        $trnascationForFromBank->transfer_id=$transfer->id;
        $trnascationForFromBank->amount=$req->amount;
        $trnascationForFromBank->opening_balance=$bankFrom->amount;
        $trnascationForFromBank->type="withdraw";
        $trnascationForFromBank->save();
        $bankFrom->amount=$bankFrom->amount-$req->amount;
        
        // now do the same for to bank will add money here
        $bankTo=BankDetail::find($req->to_bank);
        
        $trnascationForToBank=new TransactionHistory();
        $trnascationForToBank->agent_id=session('user')->id;
        $trnascationForToBank->bank_id=$bankTo->id;
        $trnascationForToBank->transfer_id=$transfer->id;
        $trnascationForToBank->amount=$req->amount;
        $trnascationForToBank->opening_balance=$bankTo->amount;
        $trnascationForToBank->type="deposit";
        $trnascationForToBank->save();
        $bankTo->amount=$bankTo->amount  + $req->amount;
       
        $resultbank1=$bankTo->save();
        $resultbank2=$bankFrom->save();
        
       
        if($resultbank1 && $resultbank2)
        {
            return redirect()->back()->with(['msg-success'=>'Transfered successfully']);
        }
        
        


        
    }




}
