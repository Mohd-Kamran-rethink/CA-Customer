<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannkController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\franchises;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\User;
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
// Transactions
Route::get('/dashboard',[TransactionController::class,'dashboard'])->name('dashboard')->middleware('CommonMiddleware');
Route::post('/dashboard',[TransactionController::class,'dashboard'])->name('dashboard')->middleware('CommonMiddleware');

// common user route
Route::post('user/delete',[UserController::class,'delete'])->name('delete');    

// MANAGER CRUD
Route::middleware('superManager')->prefix('/managers')->group(function () {
    Route::get('/',[UserController::class,'listManager'])->name('listManager');
    Route::get('/add',[UserController::class,'ManagerView'])->name('ManagerView');
    Route::post('/add',[UserController::class,'add'])->name('add');
    Route::get('/edit',[UserController::class,'ManagerView'])->name('ManagerView');
    Route::post('/edit',[UserController::class,'edit'])->name('edit');
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
Route::prefix('bank-accounts')->group(function () {
    Route::get('',[BannkController::class,'list'])->name('list');
    Route::get('/add',[BannkController::class,'addForm'])->name('addForm');
    Route::post('/add',[BannkController::class,'add'])->name('add');
    Route::get('/edit/{id}',[BannkController::class,'addForm'])->name('addForm');
    Route::post('/edit',[BannkController::class,'edit'])->name('edit');
    Route::post('/delete',[BannkController::class,'delete'])->name('delete');
    // deposit money
    Route::get('deposit-money/{id}',[BannkController::class,'adddepositForm'])->name('adddepositForm');
    Route::post('/deposit',[BannkController::class,'addDeposit'])->name('addDeposit');
    Route::get('/withdraw-money/{id}',[BannkController::class,'addWithdrawForm'])->name('addWithdrawForm');
    Route::post('/withdraw',[BannkController::class,'addWithdraw'])->name('addWithdraw');
    // view detai;s
    Route::get('/details',[BannkController::class,'viewDetails'])->name('viewDetails');
    Route::post('/reactive',[BannkController::class,'reactivebaNK'])->name('reactivebaNK');
    
});   


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
    Route::post('depositer/recancel',[TransactionController::class,'depsoiterCancel'])->name('depsoiterCancel');
    Route::post('withdrawrer/recancel',[TransactionController::class,'withdrawCancel'])->name('withdrawCancel');
    // for manager
}); 

// exchanges

Route::middleware('ValidateManager')->prefix('exchanges')->group(function () {
    Route::get('',[ExchangeController::class,'list'])->name('list');
    Route::get('/add',[ExchangeController::class,'addForm'])->name('addForm');
    Route::post('/add',[ExchangeController::class,'add'])->name('add');
    Route::get('/edit/{id}',[ExchangeController::class,'addForm'])->name('addForm');
    Route::post('/edit',[ExchangeController::class,'edit'])->name('edit');
    Route::post('/delete',[ExchangeController::class,'delete'])->name('delete');
    Route::get('/view-details',[ExchangeController::class,'viewDetails'])->name('viewDetails');
    Route::get('/add-money',[ExchangeController::class,'addMoneyForm'])->name('addMoneyForm');
    Route::post('/add-money',[ExchangeController::class,'addMoney'])->name('addMoney');
    Route::get('/withdraw-money',[ExchangeController::class,'withdrawMoneyForm'])->name('withdrawMoneyForm');
    Route::post('/withdraw-money',[ExchangeController::class,'withdrawMoney'])->name('withdrawMoney');
});



// admin transaction
Route::get('/transactions/pending-deposit',[TransactionController::class,'listPendingDeposit'])->name('listPendingDeposit')->middleware('ValidateManager');
Route::get('/transactions/pending-withdraw',[TransactionController::class,'pendingWithdraw'])->name('pendingWithdraw')->middleware('ValidateManager');
Route::get('/clients/add',[UserController::class,'addClient'])->name('addClient');
Route::get('/bankaccount/add',[BannkController::class,'bankAccouAddAjax'])->name('bankAccouAddAjax');
Route::get('/render-client-account',[BannkController::class,'renderClientAccounts'])->name('renderClientAccounts');
Route::get('/clients',[UserController::class,'clientList'])->name('clientList')->middleware('CommonMiddleware');

// show all the activeity of partitculat clients

Route::get('/clients/transactions/view-details',[UserController::class,'showClientActivity'])->name('showClientActivity')->middleware('CommonMiddleware');
Route::get('clients/view-banks',[UserController::class,'viewBankList'])->name('viewBankList')->middleware('CommonMiddleware');
Route::get('client/bank-accounts/edit/{id}',[UserController::class,'editbankFrom'])->name('editbankFrom')->middleware('CommonMiddleware');
Route::post('client/bank-accounts/edit',[UserController::class,'editBank'])->name('editBank')->middleware('CommonMiddleware');


// expenses
Route::prefix('expenses')->group(function () {
    Route::get('', [ExpenseController::class, 'listMyExpenses'])->name('listMyExpenses');
    Route::get('add', [ExpenseController::class, 'addExpenseForm'])->name('addExpenseForm');
    Route::post('add', [ExpenseController::class, 'addExpense'])->name('addExpense');
    Route::post('/delete', [ExpenseController::class, 'deleteExpense'])->name('deleteExpense');
    Route::get('/render-expense-type', [ExpenseController::class, 'renderExpensesType'])->name('renderExpensesType');
    Route::get('/download/attatchement/{id}', [ExpenseController::class, 'downloadAttatchment'])->name('downloadAttatchment');
});
// transfers functions are in expense controller
Route::middleware('ValidateManager')->prefix('transfers')->group(function () {
    Route::get('',[ExpenseController::class,'TransferList'])->name('TransferList');
    Route::get('/add',[ExpenseController::class,'addTransferForm'])->name('addTransferForm');
    Route::post('/add',[ExpenseController::class,'addTransfer'])->name('addTransfer');
});


// creditor
Route::prefix('expense-users')->group(function () {
    Route::get('creditors', [ExpenseController::class, 'creditors'])->name('creditors');
    Route::get('creditors/add', [ExpenseController::class, 'creditorsAddFrom'])->name('creditorsAddFrom');
    Route::post('creditor/add', [ExpenseController::class, 'creditorAdd'])->name('creditorAdd');
    Route::get('debitors', [ExpenseController::class, 'debitors'])->name('debitors');
    Route::get('debitors/add', [ExpenseController::class, 'debitorsAddFrom'])->name('debitorsAddFrom');
    Route::post('debitors/add', [ExpenseController::class, 'debitorAdd'])->name('debitorAdd');
});

Route::get('getClientHistory', [UserController::class, 'clientHistory'])->name('clientHistory');


Route::prefix('expense-type')->group(function () {
    Route::get('', [ExpenseController::class, 'list'])->name('list');
    Route::post('/delete', [ExpenseController::class, 'delete'])->name('delete');
    Route::get('/edit', [ExpenseController::class, 'editForm'])->name('editForm');
    Route::post('/edit', [ExpenseController::class, 'edit'])->name('edit');
    Route::get('/add', [ExpenseController::class, 'addForm'])->name('addForm');
    Route::post('/add', [ExpenseController::class, 'add'])->name('add');
});
