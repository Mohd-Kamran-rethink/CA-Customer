<?php

namespace App\Http\Controllers;

use App\BankDetail;
use App\Client;
use App\Exchange;
use App\Franchise;
use App\Transaction;
use App\TransactionHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        return view('Admin.Manager.list', compact('managers', 'searchTerm'));
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
        $role = 'deposit_banker';
        $heading = 'Deposit Banker';
        $route = 'deposit-banker';
        return view('Admin.CommonUsers.list', compact('route', 'users', 'searchTerm', 'role', 'heading'));
    }
    public function depositbankerAdd(Request $req)
    {
        $franchises = Franchise::get();
        $role = 'deposit_banker';
        $heading = 'Deposit Banker';
        $route = 'deposit-banker';
        $id = $req->query('id');
        $user = null;
        if ($id) {
            $user = User::where("role", '=', 'deposit_banker')->find($id);
        }
        return view('Admin.CommonUsers.add', compact('user', 'franchises', 'route', 'role', 'heading'));
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
        $role = 'withdrawal_banker';
        $heading = 'Withdrawal Banker';
        $route = 'withdrawal-banker';
        return view('Admin.CommonUsers.list', compact('route', 'users', 'searchTerm', 'role', 'heading'));
    }
    public function withdrawlBankerAdd(Request $req)
    {
        $franchises = Franchise::get();
        $role = 'withdrawal_banker';
        $heading = 'Withdrawal Banker';
        $route = 'withdrawal-banker';
        $id = $req->query('id');
        $user = null;
        if ($id) {
            $user = User::where("role", '=', 'withdrawal_banker')->find($id);
        }
        return view('Admin.CommonUsers.add', compact('user', 'franchises', 'route', 'role', 'heading'));
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
        $role = 'depositer';
        $heading = 'Depositer';
        $route = 'depositers';
        return view('Admin.CommonUsers.list', compact('route', 'users', 'searchTerm', 'role', 'heading'));
    }
    public function depositersAdd(Request $req)
    {
        $franchises = Franchise::get();
        $role = 'depositer';
        $heading = 'Depositer';
        $route = 'depositers';
        $id = $req->query('id');
        $user = null;
        if ($id) {
            $user = User::where("role", '=', 'depositer')->find($id);
        }
        return view('Admin.CommonUsers.add', compact('user', 'franchises', 'route', 'role', 'heading'));
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
        $role = 'withdrawrer';
        $heading = 'Withdrawrers';
        $route = 'withdrawrers';
        return view('Admin.CommonUsers.list', compact('route', 'users', 'searchTerm', 'role', 'heading'));
    }
    public function withdrawrersAdd(Request $req)
    {
        $franchises = Franchise::get();
        $role = 'withdrawrer';
        $heading = 'Withdrawrers';
        $route = 'withdrawrers';
        $id = $req->query('id');
        $user = null;
        if ($id) {
            $user = User::where("role", '=', 'withdrawrer')->find($id);
        }
        return view('Admin.CommonUsers.add', compact('user', 'franchises', 'route', 'role', 'heading'));
    }


    // common functions for manager and agents
    public function add(Request $req)
    {

        if ($req->role == 'deposit_banker') {
            $redirect_url = 'deposit-banker';
        } elseif ($req->role == 'depositer') {
            $redirect_url = 'depositers';
        } elseif ($req->role == 'withdrawrer') {
            $redirect_url = 'withdrawrers';
        } elseif ($req->role == 'withdrawal_banker') {
            $redirect_url = 'withdrawal-banker';
        } else {
            $redirect_url = 'managers';
        }
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
        if ($req->role != 'customer_care_manager') {
            $user->franchise_id = $req->franchises_id;
        }
        $result = $user->save();
        if ($result) {
            return redirect($redirect_url)->with(['msg-success' => 'Added successfully']);
        } else {
            return redirect($redirect_url)->with(['msg-error' => 'Something went wrong']);
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
        if ($currentUser->role != 'customer_care_manager') {
            $currentUser->franchise_id = $req->franchises_id;
        }
        $result = $currentUser->save();
        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Updated successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Something went wrong']);
        }
    }
    public function delete(Request $req)
    {
        $User = User::where("role", '=', $req->role)->find($req->deleteId);
        $result = $User->delete();

        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Deleted successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Something went wrong']);
        }
    }
    // client form
    public function addClientForm()
    {
        return view('Admin.Client.add');
    }
    public function addClient(Request $req)
    {
        $html = '';
        $validator = Validator::make($req->all(), [
            // 'name' => 'required',
            // 'ca_id' => 'required|unique:clients,ca_id',
            'number' => 'required|unique:clients,number',
            'exchange' => 'required|not_in:0',
            'ca_id' => 'required|unique:clients,ca_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg-error' => $validator->errors()], 422);
        }
        $client = new Client();
        $client->name = $req->name;
        $client->number = str_replace('+91', '', $req->number);
        $client->ca_id = $req->ca_id;
        $client->exchange_id = $req->exchange;
        $result = $client->save();
        $clients = Client::get();
        $html = '
            <option value="0">--Choose--</option>';

        foreach ($clients as $item) {
            $html .= '<option data-exchange-id="' . $item->exchange_id . '" value=' . $item->id . ' data-client=' . $item->id . ' data-number=' . $item->number;

            if ($client->id == $item->id) {
                $html .= ' selected';
            }

            $html .= '>' . $item->ca_id  . ' - ' .  $item->number . '</option>';
        };
        if ($result) {
            return ['client' => $client, 'data' => $html];
        }
    }

    public function clientList(Request $req)
    {
        $filterData = $req->query('filterData');
        $clients = [];
        $clientsQuery = Client::leftJoin('users', 'clients.agent_id', 'users.id')
            ->select('clients.*', "users.name as agent_name");
        if ($filterData === 'all' || !$filterData) {
            $clients = $clientsQuery->paginate(20);
        } elseif ($filterData === 'without_agent') {
            $clients = $clientsQuery->whereNull('agent_id')->paginate(20);
        }

        foreach ($clients as $client) {
            $lastDeposit = Transaction::where('client_id', $client->id)
                ->where('type', 'deposit')
                ->latest('created_at')
                ->first();

            $lastWithdrawal = Transaction::where('client_id', $client->id)
                ->where('type', 'withdraw')
                ->latest('created_at')
                ->first();  

            $client->lastDepositDate = $lastDeposit ? $lastDeposit->created_at : null;
            $client->lastWithdrawalDate = $lastWithdrawal ? $lastWithdrawal->created_at : null;
            if ($client->lastDepositDate) {
                $client->lastDepositDaysAgo = Carbon::parse($client->lastDepositDate)->diffInDays(Carbon::now());
            }

            if ($client->lastWithdrawalDate) {
                $client->lastWithdrawalDaysAgo = Carbon::parse($client->lastWithdrawalDate)->diffInDays(Carbon::now());
            }
        }
        $agents = User::where('role', '=', 'agent')->get();
        return view('Admin.Client.list', compact('clients', 'agents', 'filterData'));
    }

    // show client transadction details
    public function showClientActivity(Request $req)
    {
        $id = $req->query('id');
        $startDate = $req->query('from_date') ?? null;
        $endDate = $req->query('to_date') ?? null;
        $type = $req->query('type') ?? 'null';
        if (!$startDate) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        }

        $activites = TransactionHistory::where('client_id', '=', $id)
                ->when($type !== 'null', function ($query) use ($type) {
                    $query->where('type', $type);
                })
            ->whereDate('created_at', '>=', date('Y-m-d', strtotime($startDate)))
            ->whereDate('created_at', '<=', date('Y-m-d', strtotime($endDate)))
            ->get();
        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();
        return view('Admin.Client.ViewDetails', compact('activites', 'id', 'startDate', 'endDate'));
    }
    function clientAssign(Request $req)
    {
        $req->validate(['agent_id' => 'required']);
        $client = Client::find($req->clientID);
        $client->agent_id = $req->agent_id;
        $result = $client->update();
        if ($result) {
            return redirect()->back()->with(['msg-success' => 'Assigned successfully']);
        } else {
            return redirect()->back()->with(['msg-error' => 'Somthing went wrong']);
        }
    }


    // history
    public function clientHistory(Request $req)
    {

        $html = '';
        $lastWithdraw = Transaction::where('Type', '=', 'Withdraw')->where('client_id', '=', $req->clientID)->latest()
            ->limit(5)->get();
        $lastDeposit = Transaction::where('Type', '=', 'Deposit')->where('client_id', '=', $req->clientID)->latest()
            ->limit(5)->get();

        $html .= '<div class="container">';
        $html .= '<div class="row">';

        $html .= '<div class="col-6">';
        $html .= '<h2>Last Withdrawals:</h2>';
        $html .= '<table class="table">';
        $html .= '<tr><th>Amount</th><th>Date</th></tr>';

        foreach ($lastWithdraw as $withdraw) {
            $html .= '<tr>';
            $html .= '<td>' . $withdraw->amount . '</td>';
            $html .= '<td>' . $withdraw->date . '</td>';
            // Add any other details you want to display
            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '</div>'; // Close col-6

        $html .= '<div class="col-6">';
        $html .= '<h2>Last Deposits:</h2>';
        $html .= '<table class="table">';
        $html .= '<tr><th>Amount</th><th>Date</th></tr>';

        foreach ($lastDeposit as $deposit) {
            $html .= '<tr>';
            $html .= '<td>' . $deposit->amount . '</td>';
            $html .= '<td>' . $deposit->date . '</td>';
            // Add any other details you want to display
            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '</div>'; // Close col-6

        $html .= '</div>'; // Close row
        $html .= '</div>'; // Close container

        return $html;
    }

    public function viewBankList(Request $req)
    {
        $id = $req->query('id');
        $banks = BankDetail::where('customer_id', '=', $id)->get();
        return view('Admin.Client.BankList', compact('banks'));
    }
    public function editbankFrom($id)
    {
        $bank = BankDetail::find($id);
        return view('Admin.Client.Editbank', compact('bank'));
    }
    public function editBank(Request $req)
    {
        $req->validate([
            'account_number' => 'required',
            'bank_name' => 'required',
            'name' => 'required',
            'ifcs_code' => 'required',
        ]);

        $bank = BankDetail::find($req->hiddenid);
        $bank->holder_name = $req->name;
        $bank->customer_id = $req->customer_id;
        $bank->bank_name = $req->bank_name;
        $bank->account_number = $req->account_number;
        $bank->ifsc = $req->ifcs_code;
        $bank->phone = $req->phone;
        $bank->email = $req->email;
        $bank->address = $req->address;
        $bank->save();
        return redirect()->back()->with(['msg-success' => 'updated successfully']);
    }











    // import client by manager
    public function clientImportFORM()
    {
        return view('clientImport');
    }
    function clientImport(Request $req)
    {

        $file = $req->file('excel_file');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->path());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->path());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        $entries = [];
        $columnHeaders = array_shift($rows);
        $exchanges = Exchange::pluck('name', 'id')->map(function ($name) {
            return trim($name);
        })->toArray();
        foreach ($rows as $row) {
            $data = array_combine($columnHeaders, $row);
            $exchnageID = array_search(strtolower(trim($data['Exchange'])), array_map('strtolower', $exchanges));
            //for leads_date
            $leads_dateDateserialNumber =$data['Date']; // This is the serial number for the date "01/01/2021"
            $leads_dateunixTimestamp = ($leads_dateDateserialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
            $leads_date = \Carbon\Carbon::createFromTimestamp($leads_dateunixTimestamp);
            $leads_dateformattedDate = $leads_date->format('Y-m-d');
            $entry = [
                'number' => str_replace('+91', '', $data['Number']),
                'ca_id' => $data['Client ID'],
                'exchange_id' => $exchnageID ?? '',
                'date' => $leads_dateformattedDate,
            ];
            Client::create($entry);
        }
        return redirect()->back()->with(['msg-success' => 'imported successfully']);
    }
}
