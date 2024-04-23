<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Invoice;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
    //
    private $db;
	private $user;
	//
	public function __construct(DB $db, User $user){
		$this->db = $db;
		$this->user = $user;
	}

    public function list(){
        return view('admin.user.list');
    }

	public function index(Request $request){

		$columns = array(
            0 => 'name',
            1 => 'phone_no',
            2 => 'email',
            3 => 'country',
            4 => 'state',
            5 => 'city',
            6 => 'address',
            7 => 'action',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalData = $totalFiltered = 0;

        if (empty($request->input('search.value'))) {
            $users = $this->user->where('id', '!=', auth()->user()->id)->offset($start)->limit($limit)->orderBy($order, $dir)->get();
            $totalData = $totalFiltered = $users->count();
        } else {
            $search = $request->input('search.value');

            $usersQuery = $this->user->where('id', '!=', auth()->user()->id)
            ->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('country', 'LIKE', "%{$search}%")->orWhere('phone_no', 'LIKE', "%{$search}%")
                ->orWhere('state', 'LIKE', "%{$search}%")->orWhere('city', 'LIKE', "%{$search}%")
                ->orWhere('address', 'LIKE', "%{$search}%");
            });

            $users = $usersQuery->offset($start)->limit($limit)->orderBy($order, $dir)->get();
            $totalFiltered = $usersQuery->count();
        }
        $data = array();
        if (!empty($users)) {
            foreach ($users as $user) {

                $edit = '<a title="Edit" href='.route("editUser", $user->id).'><button type="button" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i><span>Edit</span> </button></a>';
                $detail = ' <a title="See Detail" href='.route("userDetail", $user->id).'><button type="button" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</button></a>';
                $remove = ' <button type="button" data-id="'.$user->id.'" class="btn btn-secondary btn-sm removeUser"><i class="fas fa-trash-alt"></i></button>';

                $userData['name'] = $user->name;
                $userData['phone_no'] = $user->phone_no;
                $userData['email'] = $user->email;
                $userData['country'] = $user->country;
                $userData['state'] = $user->state;
                $userData['city'] = $user->city;
                $userData['address'] = $user->address;
                $userData['action'] = $edit.$detail.$remove;
                $data[] = $userData;
            }
        }

     	$json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );

        echo json_encode($json_data);
	}

	public function add(){
		return view('admin.user.add');
    }

	public function insert(UserRequest $request){
		try {
			$userData = [
				'name' => $request->name,
	            'password' => bcrypt($request->password),
	            'email' => $request->email,
	            'phone_no' => $request->phone_no,
	            'address' => $request->address,
	            'country' => $request->country,
	            'state' => $request->state,
	            'city' => $request->city,
                'is_admin' => $request->is_admin,
	            'status' => 1,
                'is_verified' => 1,
                'slug' => 'EMPTY-SLUG',
        	];

        	$user = $this->user->create($userData);
            $user->slug = Str::slug($user->name."-".$user->id);
            $user->save();

			return redirect(route('userList'))->with('message', flashMessage('insert','User'));
		} catch (Exception $e) {
			return back()->withInput()->withErrors(['errorMessage' => exceptionMessage($e)]);
		}
	}

    public function edit($userId){
        try {

            $user = User::where('id', $userId)->first();
            return view('admin.user.edit', compact('user'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function update(UserRequest $request, $userId){
        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'address' => $request->address,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'status' => $request->status,
                'is_admin' => $request->is_admin,
                'slug' => Str::slug($request->name."-".$userId),
            ];


            $this->user->updateOrCreate(['id' => $userId], $userData);
            return redirect(route('editUser', $userId))->with('message', flashMessage('update','User'));
        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function editProfile(){
        try {

            $user = User::where('id', auth()->user()->id)->first();
            return view('admin.user.profile', compact('user'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function updateProfile(ProfileRequest $request){
        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'address' => $request->address,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'slug' => Str::slug($request->name."-".auth()->user()->id),
            ];

            if(!empty($request->new_password)){
                $userData['password'] = bcrypt($request->new_password);
            }

            $this->user->updateOrCreate(['id' => auth()->user()->id], $userData);
            return redirect(route('editProfile'))->with('message', flashMessage('update','Profile'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function detail($userId){
        try {

            $user = User::with(['invoices:id,user_id,title,invoice_number,invoice_date,total,vat,grand_total'])->where('id', $userId)->first();
            return view('admin.user.detail', compact('user'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function delete($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['success' => 'true']);
        }
        catch (Exception $e) {
            return back()->withInput()->withErrors(['errorMessage' => exceptionMessage($e)]);
        }
    }

}
