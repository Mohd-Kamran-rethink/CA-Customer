<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannkController;
use App\Http\Controllers\franchises;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// AUTH ROUTES
Route::get('/',[AuthController::class,'loginView'])->name('loginView');
Route::post('/login',[AuthController::class,'login'])->name('login');
Route::get('/logout',[AuthController::class,'logout'])->name('logout')->middleware('CommonMiddleware');

// common user route
Route::post('user/delete',[UserController::class,'delete'])->name('delete');    

// MANAGER CRUD
Route::middleware('ValidateManager')->prefix('/managers')->group(function () {
    Route::get('/',[UserController::class,'listManager'])->name('listManager');
    Route::get('/add',[UserController::class,'ManagerView'])->name('ManagerView');
    Route::post('/add',[UserController::class,'add'])->name('add');
    Route::get('/edit',[UserController::class,'ManagerView'])->name('ManagerView');
    Route::post('/edit',[UserController::class,'edit'])->name('edit');
    // delete will be common to use
    Route::get('/edit',[UserController::class,'ManagerView'])->name('ManagerView');
});

// deposit-banker   
Route::middleware('ValidateManager')->prefix('deposit-banker')->group(function () {
    Route::get('',[UserController::class,'depositbanker'])->name('depositbanker');
    Route::get('/add',[UserController::class,'depositbankerAdd'])->name('depositbankerAdd');
    Route::post('/add',[UserController::class,'add'])->name('add');
    Route::get('/edit',[UserController::class,'depositbankerAdd'])->name('depositbankerAdd');
    Route::post('/edit',[UserController::class,'edit'])->name('edit');
});

//withdrawl-banker   
Route::middleware('ValidateManager')->prefix('withdrawal-banker')->group(function () {
    Route::get('',[UserController::class,'withdrawlBanker'])->name('withdrawlBanker');
    Route::get('/add',[UserController::class,'withdrawlBankerAdd'])->name('withdrawlBankerrAdd');
    Route::get('/edit',[UserController::class,'withdrawlBankerAdd'])->name('withdrawlBankerrAdd');
    Route::post('/add',[UserController::class,'add'])->name('add');
    Route::post('/edit',[UserController::class,'edit'])->name('edit');
});
//depositers   
Route::middleware('ValidateManager')->prefix('depositers')->group(function () {
    Route::get('',[UserController::class,'depositers'])->name('depositers');
    Route::get('/add',[UserController::class,'depositersAdd'])->name('depositersAdd');
    Route::get('/edit',[UserController::class,'depositersAdd'])->name('depositersAdd');
    Route::post('/add',[UserController::class,'add'])->name('add');
    Route::post('/edit',[UserController::class,'edit'])->name('edit');
});
//withdeaers   
Route::middleware('ValidateManager')->prefix('withdrawrers')->group(function () {
    Route::get('',[UserController::class,'withdrawrers'])->name('withdrawrers');
    Route::get('/add',[UserController::class,'withdrawrersAdd'])->name('withdrawrersAdd');
    Route::post('/add',[UserController::class,'add'])->name('add');
    Route::get('/edit',[UserController::class,'withdrawrersAdd'])->name('withdrawrersAdd');
    Route::post('/edit',[UserController::class,'edit'])->name('edit');
}); 

 //Franchise   
 Route::middleware('ValidateManager')->prefix('franchises')->group(function () {
     Route::get('',[franchises::class,'list'])->name('list');
     Route::get('/add',[franchises::class,'addForm'])->name('addForm');
     Route::post('/add',[franchises::class,'add'])->name('add');
     Route::get('/edit/{id}',[franchises::class,'addForm'])->name('addForm');
     Route::post('/edit',[franchises::class,'edit'])->name('edit');
     Route::post('/delete',[franchises::class,'delete'])->name('delete');
});   
//bank-accounts   
Route::middleware('ValidateManager')->prefix('bank-accounts')->group(function () {
    Route::get('',[BannkController::class,'list'])->name('list');
    Route::get('/add',[BannkController::class,'addForm'])->name('addForm');
    Route::post('/add',[BannkController::class,'add'])->name('add');
    Route::get('/edit/{id}',[BannkController::class,'addForm'])->name('addForm');
    Route::post('/edit',[BannkController::class,'edit'])->name('edit');
    Route::post('/delete',[BannkController::class,'delete'])->name('delete');
});   

// Transactions
Route::get('/dashboard',[TransactionController::class,'dashboard'])->name('dashboard')->middleware('CommonMiddleware');
Route::post('/dashboard',[TransactionController::class,'dashboard'])->name('dashboard')->middleware('CommonMiddleware');

Route::middleware('CommonMiddleware')->prefix('transactions')->group(function () {
    Route::get('/add',[TransactionController::class,'addForm'])->name('addForm');
    Route::post('/add',[TransactionController::class,'add'])->name('add');
    Route::post('/delete',[TransactionController::class,'delete'])->name('delete');
    Route::get('/edit/{id}',[TransactionController::class,'editForm'])->name('editForm');
    Route::post('/edit',[TransactionController::class,'edit'])->name('edit');
    Route::get('/change-status/{id}',[TransactionController::class,'acceptPendingDepositForm'])->name('acceptPendingDepositForm');
    Route::post('/change-status',[TransactionController::class,'changeStatus'])->name('changeStatus');
    Route::post('/change-status/cancel',[TransactionController::class,'rejectApproal'])->name('rejectApproal');
    // withdraw
    Route::get('/withdraw/add',[TransactionController::class,'withdrawAddForm'])->name('withdrawAddForm');
    Route::post('/withdraw/add',[TransactionController::class,'withdrawAdd'])->name('withdrawAdd');
    Route::get('withdraw/edit/{id}',[TransactionController::class,'withdrawEditForm'])->name('withdrawEditForm');
    Route::post('withdraw/edit',[TransactionController::class,'withdrawEdit'])->name('withdrawEdit');
    Route::get('/change-status-withdraw/{id}',[TransactionController::class,'acceptPendingWithdrawForm'])->name('acceptPendingWithdrawForm');
    Route::post('/change-status-withdraw',[TransactionController::class,'changeWithdrawStatus'])->name('changeWithdrawStatus');
}); 
Route::get('/clients/add',[UserController::class,'addClient'])->name('addClient');
Route::get('/bankaccount/add',[BannkController::class,'bankAccouAddAjax'])->name('bankAccouAddAjax');
