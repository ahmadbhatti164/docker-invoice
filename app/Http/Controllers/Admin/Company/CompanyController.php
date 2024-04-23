<?php

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\VendorRequest;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use App\Models\UserVendor;
use App\Models\Vendor;
use App\Services\DashboardStatsService;
use App\Services\VendorStatsService;
use App\Traits\ApiResponse;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class CompanyController extends Controller
{
    use ApiResponse;
    //
    private $db;
    private $company;

    use ApiResponse;

    public function __construct(Company $company, DB $db)
    {
        $this->db = $db;
        $this->company = $company;
    }

    public function list(Request $request)
    {
        return view('admin.company.list');
    }
    public function index(Request $request)
    {
        $company = $this->company->withCount('invoices');

        if (auth()->user()->is_admin != 1) {
            $company = auth()->user()->companies();
        }
        $companies = $company->get();


        $data = array();
        if (!empty($companies)) {
            foreach ($companies as $company) {

                $edit = '<a title="Edit" class="customA" href=' . route("editCompany",
                        $company->id) . '><i class="fas fa-pen"></i></a>';
                $detail = '<a title="See Detail" class="customA" href=' . route("companyDetail",
                        $company->id) . '><i class="fas fa-arrow-right "></i></a>';

                $companyData['name'] = $company->name;
                $companyData['invoices_count'] = ($company->invoices->count('id')) ? $company->invoices->count('id') : ' ';
                $companyData['sub_total'] = round($company->invoices->sum('sub_total'),2);
                //$vat = ($vendor->invoices->avg('vat')) ? $vendorData['sub_total'] * $vendor->invoices->avg('vat') / 100 : ' ';
                $companyData['vat'] = round($company->invoices->sum('vat'),2);
                $companyData['total'] = round(($company->invoices->sum('grand_total')) ? $company->invoices->sum('grand_total') : ' ',2);
                $companyData['action'] = $edit;
                $data[] = $companyData;
            }
        }
        return Datatables::of($data)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);

    }
    public function add()
    {
        return view('admin.company.add');
    }
    public function store(CompanyRequest $request)
    {
        try {
            $companyData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'address' => $request->address,
                'cvr_number' => $request->cvr_number,
                'status' => 1,
                'slug' => 'EMPTY-SLUG',
            ];


            $company = $this->company->create($companyData);
            $company->slug = Str::slug($company->name . "-" . $company->id);
            $company->save();
            $user = User::find(auth()->user()->id);
            $user->companies()->attach($company->id);


            return redirect(route('companyList'))->with('message', flashMessage('insert', 'Company'));
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['errorMessage' => exceptionMessage($e)]);
        }
    }
    public function edit($companyId)
    {
        try {
            $user_id = auth()->user()->id;
            $user = CompanyUser::where('user_id',$user_id)->first();
            $company = $this->company::where('id', $companyId)->first();

            return view('admin.company.edit', compact('company','user'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function delete($id)
    {
        try {
            $vendor = $this->company::findOrFail($id);
            $vendor->delete();

            return response()->json(['success' => 'true']);
        }
        catch (Exception $e) {
            return back()->withInput()->withErrors(['errorMessage' => exceptionMessage($e)]);
        }
    }
}
