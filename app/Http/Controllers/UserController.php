<?php

namespace App\Http\Controllers;

use App\Franchise;
use App\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
     //NOTE:: There will be 7 roles in the global CA in this project we are only using 5 roles 
    // i.e customer_care_manager,deposit_banker,withdrawal_banker,depositer,withdrawer
   
    // uncommon functions for search
    public function ManagerView(Request $req)
    {
        $id = $req->query('id');
        if ($id) {
            $manager = User::where("role", '=', 'customer_care_manager')->find($id);
            return view('Admin.Manager.add', compact('manager'));
        } else {
            return view('Admin.Manager.add');
        }
    }
    public function listManager(Request $req)
    {
        $searchTerm = $req->query('table_search');
        $managers = User::where('role', 'customer_care_manager')
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('Admin.Manager.list', compact('managers','searchTerm'));
    }
   
    // list deposit banker
    public function depositbanker(Request $req)
    {
        $searchTerm = $req->query('table_search');
        $users = User::where('role', 'deposit_banker')
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        $role='deposit_banker';
        $heading='Deposit Banker';
        $route='deposit-banker';
        return view('Admin.CommonUsers.list', compact('route','users','searchTerm','role','heading'));
    }
    public function depositbankerAdd(Request $req)
    {
       $franchises=Franchise::get();
        $role='deposit_banker';
        $heading='Deposit Banker';
        $route='deposit-banker';
        $id = $req->query('id');
        $user=null;
        if ($id) 
        {
            $user = User::where("role", '=', 'deposit_banker')->find($id);
        }
        return view('Admin.CommonUsers.add', compact('user','franchises','route','role','heading'));
    }
    // list wirhdraeal banker
    public function withdrawlBanker(Request $req)
    {
        $searchTerm = $req->query('table_search');
        $users = User::where('role', 'withdrawal_banker')
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        $role='withdrawal_banker';
        $heading='Withdrawal Banker';
        $route='withdrawal-banker';
        return view('Admin.CommonUsers.list', compact('route','users','searchTerm','role','heading'));
    }
    public function withdrawlBankerAdd(Request $req)
    {
        $franchises=Franchise::get();
        $role='withdrawal_banker';
        $heading='Withdrawal Banker';
        $route='withdrawal-banker';
        $id = $req->query('id');
        $user=null;
        if ($id) 
        {
            $user = User::where("role", '=', 'withdrawal_banker')->find($id);
        }
        return view('Admin.CommonUsers.add', compact('user','franchises','route','role','heading'));
    }
    // list depositers banker
    public function depositers(Request $req)
    {
        $searchTerm = $req->query('table_search');
        $users = User::where('role', 'depositer')
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        $role='depositer';
        $heading='Depositer';
        $route='depositers';
        return view('Admin.CommonUsers.list', compact('route','users','searchTerm','role','heading'));
    }
    public function depositersAdd (Request $req)
    {
       $franchises=Franchise::get();
        $role='depositer';
        $heading='Depositer';
        $route='depositers';
        $id = $req->query('id');
        $user=null;
        if ($id) 
        {
            $user = User::where("role", '=', 'depositer')->find($id);
        }
        return view('Admin.CommonUsers.add', compact('user','franchises','route','role','heading'));
    }
       // list withdrawrers banker
    public function withdrawrers(Request $req)
    {
        $searchTerm = $req->query('table_search');
        $users = User::where('role', 'withdrawrer')
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        $role='withdrawrer';
        $heading='Withdrawrers';
        $route='withdrawrers';
        return view('Admin.CommonUsers.list', compact('route','users','searchTerm','role','heading'));
    }
    public function withdrawrersAdd (Request $req)
    {
       $franchises=Franchise::get();
        $role='withdrawrer';
        $heading='Withdrawrers';
        $route='withdrawrers';
        $id = $req->query('id');
        $user=null;
        if ($id) 
        {
            $user = User::where("role", '=', 'withdrawrer')->find($id);
        }
        return view('Admin.CommonUsers.add', compact('user','franchises','route','role','heading'));
    }  
        

    // common functions for manager and agents
    public function add(Request $req)
    {
        $req->validate([
            'name' => 'required|unique:users,name',
            'phone' => 'required|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|same:confirmPassword',
            'confirmPassword' => 'required|'
        ]);
        $conditionalRules = [
            'password' => 'nullable|min:8|same:confirmPassword',
        ];
        $user = new User();
        $user->name = $req->name;
        $user->phone = $req->phone;
        $user->email = $req->email;
        $user->password = Hash::make($req->password);
        $user->role = $req->role;
        if ($req->role!='customer_care_manager') {
            $user->franchise_id =$req->franchises_id;
        }
        $result = $user->save();
        if ($result ) {
                return redirect()->back()->with(['msg-success' => 'Added successfully']);
            } else {
                return redirect()->back()->with(['msg-error'=>'Something went wrong']);   
            }
        
    }

    public function edit(Request $req)
    {
        $currentUser = User::where("role", '=', $req->role)->find($req->userId);

        $rules = [
            'name' => 'required|unique:users,name,' . $currentUser->id,
            'phone' => 'required|unique:users,phone,' . $currentUser->id,
            'email' => 'required|email|unique:users,email,' . $currentUser->id,
            'confirmPassword' => 'required_with:password',
        ];

        $conditionalRules = [
            'password' => 'nullable|min:8|same:confirmPassword',
        ];
        if ($req->password) {
            $req->validate(array_merge($rules, $conditionalRules));
        }
        $currentUser->name = $req->name;
        $currentUser->phone = $req->phone;
        $currentUser->email = $req->email;
        if ($req->password) {
            $currentUser->password = Hash::make($req->password);
        }
        if ($currentUser->role!='customer_care_manager') {
            $currentUser->franchise_id =$req->franchises_id;
        }
        $result = $currentUser->save();
        if ($result ) {
                return redirect()->back()->with(['msg-success' => 'Updated successfully']);
            } else {
                return redirect()->back()->with(['msg-error'=>'Something went wrong']);   
            }
        
    }
    public function delete(Request $req)
    {
        $User = User::where("role", '=', $req->role)->find($req->deleteId);
        $result = $User->delete();
        
            if ($result ) {
                return redirect()->back()->with(['msg-success' => 'Deleted successfully']);
            } else {
                return redirect()->back()->with(['msg-error'=>'Something went wrong']);   
            }
       
    }
}

        
        

    
    