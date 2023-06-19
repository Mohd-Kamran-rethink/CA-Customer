<?php

namespace App\Http\Controllers;

use App\BankDetail;
use App\Department;
use App\Expense;
use App\ExpenseType;
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
         
        

}
