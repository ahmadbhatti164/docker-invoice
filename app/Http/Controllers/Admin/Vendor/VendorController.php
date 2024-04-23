<?php

namespace App\Http\Controllers\Admin\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParseFileToHtmlRequest;
use App\Http\Requests\VendorRequest;
use App\Libraries\ExcelToHtmlFile;
use App\Libraries\ImageToHtml;
use App\Libraries\PdfToHtmlFile;
use App\Libraries\WordToHtmlFile;
use App\Models\Category;
use App\Models\User;
use App\Models\UserVendor;
use App\Services\DashboardStatsService;
use App\Services\VendorStatsService;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Vendor;
use Khill\Lavacharts\Laravel\LavachartsFacade;
use Yajra\DataTables\DataTables;

class VendorController extends Controller
{
    use ApiResponse;
    //
    private $db;
    private $vendor;

    use ApiResponse;

    public function __construct(Vendor $vendor, DB $db)
    {
        $this->db = $db;
        $this->vendor = $vendor;
    }

    public function list(Request $request)
    {
        $topVendorsWithExpense = VendorStatsService::make()->topVendorsWithExpense();
        $this->showGraphs($topVendorsWithExpense);
        return view('admin.vendor.list');
    }
    private function showGraphs($topVendorsWithExpense){
        $reasons = LavachartsFacade::DataTable();
        $reasons->addStringColumn('Top Expenses')
            ->addNumberColumn('Top Expenses');

        $total = 0;
        foreach ($topVendorsWithExpense as $key => $val) {
            $total = $total + $val;
        }
        foreach ($topVendorsWithExpense as $key => $val) {
            $percentage = number_format(($val/$total) * 100,2).'%';
            $name = $key   .' '.$percentage;
            $reasons->addRow([$name, $val]);
        }

        LavachartsFacade::PieChart('IMDB', $reasons, [
            'title' => 'Top Expenses',
            'elementId' => 'poll_div',
            'slices' => [
                ['offset' => 0.2],
                ['offset' => 0.25],
                ['offset' => 0.25]
            ]
        ]);

        $totalExpenses = VendorStatsService::make()->vendorExpenseBreakDownYearly();
        $reasons = LavachartsFacade::DataTable();
        $reasons->addStringColumn('Total Expenses')
            ->addNumberColumn('Total Expenses')
            ->addNumberColumn('Total vat');

        foreach ($totalExpenses as $key => $val) {
            $reasons->addRow([$key, $val['total'],$val['vat']]);
        }
        LavachartsFacade::LineChart('IMDB', $reasons, [
            'title' => 'Top Expenses',
            'elementId' => 'total_expenses',
            'isStacked'=> true,
            'colors' => ['#4c4c4c', '#6c757d'],
            'height'=>270,
        ]);
    }

    public function index(Request $request)
    {
            $vendors = $this->vendor->withCount('invoices');

            if (auth()->user()->is_admin != 1) {
                $vendors = auth()->user()->vendors();
            }
            $vendors = $vendors->get();

        $data = array();
        if (!empty($vendors)) {
            foreach ($vendors as $vendor) {

                $edit = '<a title="Edit" href=' . route("editVendor",
                        $vendor->id) . '><button type="button" class="btn btn-primary">Edit</button></a>';
                $detail = '<a title="See Detail" class="customA" href=' . route("vendorDetail",
                        $vendor->id) . '><i class="fas fa-arrow-right "></i></a>';

                $vendorData['name'] = $vendor->name;
                $vendorData['invoices_count'] = ($vendor->invoices->count('id')) ? $vendor->invoices->count('id') : ' ';
                $vendorData['sub_total'] = round($vendor->invoices->sum('sub_total'),2);
                //$vat = ($vendor->invoices->avg('vat')) ? $vendorData['sub_total'] * $vendor->invoices->avg('vat') / 100 : ' ';
                $vendorData['vat'] = round($vendor->invoices->sum('vat'),2);
                $vendorData['total'] = round(($vendor->invoices->sum('grand_total')) ? $vendor->invoices->sum('grand_total') : ' ',2);
                $vendorData['action'] = $detail;
                $data[] = $vendorData;
            }
        }
        return Datatables::of($data)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);

       /* $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);*/
    }

    public function add()
    {
        $categories = Category::get();
        return view('admin.vendor.add', compact('categories'));
    }

    public function insert(VendorRequest $request)
    {
        try {
            $vendorData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'address' => $request->address,
                'parser' => 'GeneralParser',
                'category_id' => $request->category_id,
                'cvr_number' => $request->cvr_number,
                'status' => 1,
                'slug' => 'EMPTY-SLUG',
            ];

            $arr['invoice_no'] = $request->invoice_no;
            $arr['total'] = $request->total;
            //$arr['cvr_number'] = $request->cvr_number;
            $arr['invoice_date'] = $request->invoice_date;
            $arr['vat'] = $request->vat;
            $arr['sub_total'] = $request->sub_total;
            $arr['grand_total'] = $request->grand_total;
            $arr['products_start'] = $request->products_start;
            $arr['products_end'] = $request->products_end;
            $arr['product_row_length'] = $request->product_row_length;

            $arr['products_columns'] = (json_decode($request->products_columns, true));

            $myJSON = json_encode($arr);
            $vendorData['parser_parameters'] = $myJSON;

            $vendor = $this->vendor->create($vendorData);
            $vendor->slug = Str::slug($vendor->name . "-" . $vendor->id);
            $vendor->save();
            $user = User::find(auth()->user()->id);
            $user->vendors()->attach($vendor->id);


            return redirect(route('vendorList'))->with('message', flashMessage('insert', 'Vendor'));
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['errorMessage' => exceptionMessage($e)]);
        }
    }

    public function edit($vendorId)
    {
        try {
            $categories = Category::get();
            $vendor = Vendor::where('id', $vendorId)->first();
            $parser_parameters = json_decode($vendor->parser_parameters, true);

            return view('admin.vendor.edit', compact('vendor', 'categories', 'parser_parameters'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function update(Request $request, $vendorId)
    {
        try {
            $vendorData = $request->validate([
                'name' => 'required',
                'phone_no' => 'required',
            ]);
            $vendorData = [
                'name' => $request->name,
                'phone_no' => $request->phone_no,
                'address' => $request->address,
                'parser' => 'GeneralParser',
                'category_id' => $request->category_id,
                'cvr_number' => $request->cvr_number,
            ];

            $arr['invoice_no'] = $request->invoice_no;
            $arr['total'] = $request->total;
            //$arr['cvr_number'] = $request->cvr_number;
            $arr['invoice_date'] = $request->invoice_date;
            $arr['vat'] = $request->vat;
            $arr['sub_total'] = $request->sub_total;
            $arr['grand_total'] = $request->grand_total;

            $arr['company_name'] = $request->company_name;
            $arr['company_email'] = $request->company_email;
            $arr['company_phone'] = $request->company_phone;
            $arr['company_cvr'] = $request->company_cvr;
            $arr['company_address'] = $request->company_address;

            $arr['products_start'] = $request->products_start;
            $arr['products_end'] = $request->products_end;
            $arr['product_row_length'] = $request->product_row_length;

            $arr['products_columns'] = (json_decode($request->products_columns, true));

            $myJSON = json_encode($arr);
            $vendorData['parser_parameters'] = $myJSON;

            $vendorData['slug'] = Str::slug($request->name . "-" . $vendorId);
            $this->vendor->updateOrCreate(['id' => $vendorId], $vendorData);

            return redirect(route('editVendor', $vendorId))->with('message', flashMessage('update', 'Vendor'));
        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function parseFile(){
        return view('admin.vendor.parse');
    }

    public function parseFileToHtml(ParseFileToHtmlRequest $request){

        $data = array('attachment'=>$request->file('file'));
        $extension = $request->file('file')->getClientOriginalExtension();
        //dd($extension);
        if($extension == 'pdf' || $extension == 'PDF'){
            $pdfToHtml = new PdfToHtmlFile($data);
            $files = $pdfToHtml->convertPdfToHtml();

        }
        elseif ($extension == 'xls'||$extension == 'xlsx'){
            $excelToHtml = new ExcelToHtmlFile($data);
            $files = $excelToHtml->convertExcelToHtml();
        }
        elseif ($extension == 'doc'||$extension == 'docx'){
            $wordToHtml = new WordToHtmlFile($data);
            $files = $wordToHtml->convertWordToHtml();
        }elseif ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') {
            $imageToHtml = new ImageToHtml($data);
            $files = $imageToHtml->convertImageToHtml();
        }
        else{
            return "File is not in Required format. (E.g pdf,doc,xls)";
        }

        if($files){
            return $this->success(200, $files, 'Success');
        }
        else {
            return $this->error(400, false, 'Failed');
        }

        //return view('admin.vendor.parse');
    }

    public function detail($vendorId)
    {
        try {
            $user_id = auth()->user()->id;
            $user = UserVendor::where('user_id',$user_id)->first();
            $vendor = Vendor::where('id', $vendorId)->first();
            $topProducts = DashboardStatsService::make()->topProducts($vendorId);
            $invoiceDueDate = DashboardStatsService::make()->invoiceDueDate($vendorId);
            $invoiceDueDateCount = $invoiceDueDate->count();
            $upcomingExpenses = $invoiceDueDate->sum('grand_total');
            $totalExpense = DashboardStatsService::make()->vendorTotalExpense($vendorId);
            return view('admin.vendor.detail', compact('vendor','user','topProducts','invoiceDueDateCount','upcomingExpenses','totalExpense'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function delete($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $vendor->delete();

            return response()->json(['success' => 'true']);
        }
        catch (Exception $e) {
            return back()->withInput()->withErrors(['errorMessage' => exceptionMessage($e)]);
        }
    }
}
