<?php

namespace App\Http\Controllers;

use App\Ledger;
use App\LedgerGroup;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    // groups CRUD
    function listgroup(Request $req)
    {
        $groups = LedgerGroup::where('status','=','active')->paginate();
        return view('Admin.LedgerGroups.list', compact('groups'));
    }
    function groupaddForm(Request $req)
    {
        $id = $req->query('id');
        if ($id) {
            $group = LedgerGroup::find($id);
            return view('Admin.LedgerGroups.addForm', compact('group'));
        } else {
            return view('Admin.LedgerGroups.addForm');
        }
    }
    function groupadd(Request $req)
    {
        $group = new LedgerGroup();
        $group->name = $req->name;
        $result = $group->save();
        if ($result) {
            return redirect('ledgers-groups')->with(['msg-success' => 'Added successfully']);
        } else {
            return redirect('ledgers-groups')->with(['msg-error' => 'Something went wrong']);
        }
    }
    function groupedit(Request $req)
    {

        $group = LedgerGroup::find($req->hiddenid);
        $group->name = $req->name;
        $result = $group->update();
        if ($result) {
            return redirect('ledgers-groups')->with(['msg-success' => 'Updated successfully']);
        } else {
            return redirect('ledgers-groups')->with(['msg-error' => 'Something went wrong']);
        }
    }
    function groupdelete(Request $req) {
        $group=LedgerGroup::find($req->deleteId);
        $group->status='inactive';
        $result=$group->update();
        if ($result) {
            return redirect('ledgers-groups')->with(['msg-success' => 'Updated successfully']);
        } else {
            return redirect('ledgers-groups')->with(['msg-error' => 'Something went wrong']);
        }
        
    }


    // ledgers
    function list(Request $req) {
        $ledgers=Ledger::where('ledgers.status','=','active')->leftjoin('ledger_groups','ledgers.ledger_group','ledger_groups.id')
                            ->select('ledgers.*','ledger_groups.name as group_name')
                            ->paginate();
        return view('Admin.Ledger.list',compact('ledgers'));
    }
    function addForm(Request $req) {
        $groups=LedgerGroup::where('status','=','active')->get();
        if($req->query('id'))
        {
            $ledger=Ledger::find($req->query('id'));
            return view('Admin.Ledger.add',compact('groups','ledger'));
        }
        else
        {
            return view('Admin.Ledger.add',compact('groups'));
        }

    }
    function add(Request $req) {
        $req->validate([
            'name'=>'required',
            'group'=>'required|not_in:0'
            ]);
        $ledger=new Ledger();
        $ledger->name=$req->name;
        $ledger->ledger_group=$req->group;
        $result=$ledger->save();
        if ($result) {
            return redirect('ledgers')->with(['msg-success' => 'Added successfully']);
        } else {
            return redirect('ledgers')->with(['msg-error' => 'Something went wrong']);
        }
        
    }
    function edit(Request $req) 
    {
        $req->validate([
            'name'=>'required',
            'group'=>'required|not_in:0'
            ]);
        $ledger=Ledger::find($req->hiddenid);
        $ledger->name=$req->name;
        $ledger->ledger_group=$req->group;
        $result=$ledger->update();
        if ($result) {
            return redirect('ledgers')->with(['msg-success' => 'updated successfully']);
        } else {
            return redirect('ledgers')->with(['msg-error' => 'Something went wrong']);
        }
    }
    function delete(Request $req) 
    {
        $ledger=Ledger::find($req->deleteId);
        $ledger->status='inactive';
        $result=$ledger->update();
        if ($result) {
            return redirect('ledgers')->with(['msg-success' => 'Deleted successfully']);
        } else {
            return redirect('ledgers')->with(['msg-error' => 'Something went wrong']);
        }
        
    }
    function addEnteryFrom(Request $req) 
    {
        return view('Admin.Ledger.addEntery');
    }  
        
}
