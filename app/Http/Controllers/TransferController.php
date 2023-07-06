<?php

namespace App\Http\Controllers;

use App\BankDetail;
use App\LadgerHistory;
use App\Ledger;
use App\TransactionHistory;
use App\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    // transfers
    public function TransferList(Request $req)
    {
        $startDate = $req->query('from_date') ?? null;
        $endDate = $req->query('to_date') ?? null;
        if (!$startDate) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        }
        $transfers = Transfer::whereDate('created_at', '>=', date('Y-m-d', strtotime($startDate)))
            ->whereDate('created_at', '<=', date('Y-m-d', strtotime($endDate)))
            ->get();
        foreach ($transfers as $transfer) {
            if ($transfer->from_bank) {
                $bank = BankDetail::find($transfer->from_bank);
                $transfer['bank_from'] = $bank->account_number;
            }
            if ($transfer->to_bank) {
                $bank = BankDetail::find($transfer->to_bank);
                $transfer['bank_to'] = $bank->account_number;
            }
        }
        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();
        return view('Admin.Transfers.list', compact('transfers', 'startDate', 'endDate'));
    }
    public function addTransferForm()
    {
        $banks = BankDetail::whereNull('customer_id')->where('is_active', '=', 'yes')->get();
        $ledgers= Ledger::where('status', '=', 'active')->get();
        return view('Admin.Transfers.addForm', compact('banks','ledgers'));
    }

    public function addTransfer(Request $req)
    {

        $transfer = new Transfer();
        $transfer->user_id = session('user')->id;
        $transfer->from_bank = $req->sender_bank;
        $transfer->transfer_type = $req->transfer_type;
        $transfer->payment_type = $req->payment_type;
        $transfer->to_bank = $req->receiver_bank;
        $transfer->amount = $req->amount;
        $transfer->remark = $req->remark;
        $transfer->save();

        if ($req->transfer_type == "internal") {
            $bankFrom = BankDetail::find($req->sender_bank);

            $trnascationForFromBank = new TransactionHistory();
            $trnascationForFromBank->agent_id = session('user')->id;
            $trnascationForFromBank->bank_id = $bankFrom->id;
            $trnascationForFromBank->transfer_id = $transfer->id;
            $trnascationForFromBank->amount = $req->amount;
            $trnascationForFromBank->opening_balance = $bankFrom->amount;
            $trnascationForFromBank->type = "Transfer Out";
            $trnascationForFromBank->current_balance = $bankFrom->amount - $req->amount;
            $trnascationForFromBank->save();
            $bankFrom->amount = $bankFrom->amount - $req->amount;

            // now do the same for to bank will add money here


            $bankTo = BankDetail::find($req->receiver_bank);

            $trnascationForToBank = new TransactionHistory();
            $trnascationForToBank->agent_id = session('user')->id;
            $trnascationForToBank->bank_id = $bankTo->id;
            $trnascationForToBank->transfer_id = $transfer->id;
            $trnascationForToBank->amount = $req->amount;
            $trnascationForToBank->opening_balance = $bankTo->amount;
            $trnascationForToBank->type = "Transfer In";
            $trnascationForToBank->current_balance = $bankTo->amount  + $req->amount;
            $trnascationForToBank->save();
            $bankTo->amount = $bankTo->amount  + $req->amount;

            $resultbank1 = $bankTo->save();
            $resultbank2 = $bankFrom->save();


            if ($resultbank1 && $resultbank2) {
                return redirect('/transfers')->with(['msg-success' => 'Transfered successfully']);
            }
        }
        if ($req->transfer_type == "external") {
            if ($req->accounting_type == "Debit") {
                if($req->payment_type == 'bank')
                {

                    $bankFrom = BankDetail::find($req->sender_bank);
                    $trnascationForFromBank = new TransactionHistory();
                    $trnascationForFromBank->agent_id = session('user')->id;
                    $trnascationForFromBank->bank_id = $bankFrom->id;
                    $trnascationForFromBank->transfer_id = $transfer->id;
                    $trnascationForFromBank->amount = $req->amount;
                    $trnascationForFromBank->opening_balance = $bankFrom->amount;
                    $trnascationForFromBank->type = "Transfer In";
                    $trnascationForFromBank->current_balance = $bankFrom->amount + $req->amount;
                    $trnascationForFromBank->save();
                    $bankFrom->amount = $bankFrom->amount + $req->amount;
                    $bankFrom->save();
                }

                // find lereger and manage money
                $ledger=Ledger::find($req->ledger_id);

                
                $ladgerHistory=new LadgerHistory();
                $ladgerHistory->user_id=session('user')->id;
                $ladgerHistory->amount=$req->amount;
                $ladgerHistory->opening_balance=$ledger->amount;
                $ladgerHistory->closing_balance=$ledger->amount-$req->amount;
                $ladgerHistory->ledger_id=$req->ledger_id;
                $ladgerHistory->type='Transfer In';
                $ladgerHistory->remark=$req->remark;
                $ledger->amount=$ledger->amount-$req->amount;
                $ladgerHistory->save();
                $result = $ledger->update();
            } else if ($req->accounting_type == "Credit") {
                if($req->payment_type == 'bank')
                {

                    $bankFrom = BankDetail::find($req->sender_bank);
                    $trnascationForFromBank = new TransactionHistory();
                    $trnascationForFromBank->agent_id = session('user')->id;
                    $trnascationForFromBank->bank_id = $bankFrom->id;
                    $trnascationForFromBank->transfer_id = $transfer->id;
                    $trnascationForFromBank->amount = $req->amount;
                    $trnascationForFromBank->opening_balance = $bankFrom->amount;
                    $trnascationForFromBank->type = "Transfer Out";
                    $trnascationForFromBank->current_balance = $bankFrom->amount - $req->amount;
                    $trnascationForFromBank->save();
                    $bankFrom->amount = $bankFrom->amount - $req->amount;
                    $result = $bankFrom->save();
                }
                
                $ledger=Ledger::find($req->ledger_id);

                
                $ladgerHistory=new LadgerHistory();
                $ladgerHistory->user_id=session('user')->id;
                $ladgerHistory->amount=$req->amount;
                $ladgerHistory->opening_balance=$ledger->amount;
                $ladgerHistory->closing_balance=$ledger->amount+$req->amount;
                $ladgerHistory->type='Transfer Out';
                $ladgerHistory->ledger_id=$req->ledger_id;
                $ladgerHistory->remark=$req->remark;
                $ledger->amount=$ledger->amount+$req->amount;
                $ladgerHistory->save();
                $result = $ledger->update();
            }
        }
       
       
            return redirect('/transfers')->with(['msg-success' => 'Transfered successfully']);
        
    }
    
}
