<?php

namespace App\Http\Controllers;

use App\Franchise;
use Illuminate\Http\Request;

class franchises extends Controller
{
    public function list()
    {
        $franchises=Franchise::paginate(20);
        return view('Admin.Franchises.list',compact('franchises'));
    }
    public function addForm($id = null)
    {
        if(isset($id))
        {
            $franchise=Franchise::find($id);
            return view('Admin.Franchises.add',compact('franchise'));
        }

        return view('Admin.Franchises.add');
    }
    public function add(Request $req)
    {
        $req->validate([
            'name' => 'required|unique:franchises,name',
           
        ]);
        $Franchise = new Franchise();
        $Franchise->name = $req->name;
        $Franchise->monthly_target = $req->monthly_target;
        $Franchise->users_count = $req->users_count;
        $result = $Franchise->save();
        if ($result ) {
                return redirect()->back()->with(['msg-success' => 'Added successfully']);
            } else {
                return redirect()->back()->with(['msg-error'=>'Something went wrong']);   
            }
         
    }
    public function edit(Request $req)
    {
        $req->validate([
            'name' => 'required|unique:franchises,name,'. $req->hiddenid,
        ]);
           
        $Franchise =  Franchise::find($req->hiddenid);
        $Franchise->name = $req->name;
        $Franchise->monthly_target = $req->monthly_target;
        $Franchise->users_count = $req->users_count;
        $result = $Franchise->update();
        if ($result ) {
                return redirect()->back()->with(['msg-success' => 'Added successfully']);
            } else {
                return redirect()->back()->with(['msg-error'=>'Something went wrong']);   
            }
    }
    public function delete(Request $req)
    {
        $Franchise = Franchise::find($req->deleteId);
        $result = $Franchise->delete();
        
            if ($result ) {
                return redirect()->back()->with(['msg-success' => 'Deleted successfully']);
            } else {
                return redirect()->back()->with(['msg-error'=>'Something went wrong']);   
            }
       
    }
}
