<?php

namespace App\Http\Controllers;

use App\BankDetail;
use Illuminate\Http\Request;

class BannkController extends Controller
{
    
    public function list()
    {
        $banks=BankDetail::paginate(20);
        return view('Admin.BAccountData.list',compact('banks'));
    }
    public function addForm($id = null)
    {
        if(isset($id))
        {
            $bank=BankDetail::find($id);
            return view('Admin.BAccountData.add',compact('bank'));
        }

        return view('Admin.BAccountData.add');
    }
    public function add(Request $req)
    {
        $req->validate([
            'account_number' => 'required|unique:bank_details,account_number',
        ]);
           
        $bank = new BankDetail();
        $bank->holder_name = $req->name;
        $bank->bank_name = $req->bank_name;
        $bank->account_number = $req->account_number;
        $bank->ifsc= $req->ifcs_code;
        $bank->phone = $req->phone;
        $bank->email = $req->email;
        $bank->address = $req->address;
        $result = $bank->save();
        if ($result ) {
                return redirect()->back()->with(['msg-success' => 'Added successfully']);
            } else {
                return redirect()->back()->with(['msg-error'=>'Something went wrong']);   
            }
         
    }
    public function edit(Request $req)
    {
        $req->validate([
            'account_number' => 'required|unique:bank_details,account_number,'. $req->hiddenid,
        ]);
           
        $bank =  BankDetail::find($req->hiddenid);
        $bank->holder_name = $req->name;
        $bank->bank_name = $req->bank_name;
        $bank->account_number = $req->account_number;
        $bank->ifsc= $req->ifcs_code;
        $bank->phone = $req->phone;
        $bank->email = $req->email;
        $bank->address = $req->address;
        $result = $bank->save();
        if ($result ) {
                return redirect()->back()->with(['msg-success' => 'Added successfully']);
            } else {
                return redirect()->back()->with(['msg-error'=>'Something went wrong']);   
            }
         
    }
    public function delete(Request $req)
    {
        $BankDetail = BankDetail::find($req->deleteId);
        $result = $BankDetail->delete();
        
            if ($result ) {
                return redirect()->back()->with(['msg-success' => 'Deleted successfully']);
            } else {
                return redirect()->back()->with(['msg-error'=>'Something went wrong']);   
            }
       
    }
    public function bankAccouAddAjax(Request $req)
    {
        $req->validate([
            'account_number' => 'required|unique:bank_details,account_number',
            'bank_name' => 'required',
            'name' => 'required',
            'ifcs_code' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'address' => 'required',
        ]);
           
        $bank = new BankDetail();
        $bank->holder_name = $req->name;
        $bank->bank_name = $req->bank_name;
        $bank->account_number = $req->account_number;
        $bank->ifsc= $req->ifcs_code;
        $bank->phone = $req->phone;
        $bank->email = $req->email;
        $bank->address = $req->address;
        $result=$bank->save();
        if($result)
        {
            $banks=BankDetail::get();
            $html = '<label class="form-label" for="form3Example1n1">Clients</label>
            <select name="client"  class="form-control">
            <option value="0">--Choose--</option>
            
            ';

        foreach ($banks as $item) {
            $html .= '<option value=' . $item->id . '>' . $item->holder_name . '-('.$item->account_number.')</option>';
        }
        '</select>';
        return $html;    
    }
        
    }
    }
