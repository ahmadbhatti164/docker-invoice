<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\DashboardStatsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\Currency;
use App\Http\Requests\InvoiceRequest;
use Khill\Lavacharts\Laravel\LavachartsFacade;
use Yajra\DataTables\DataTables;

class InvoiceController extends Controller
{
    //
    private $db;
    private $invoice;
    public function __construct(Invoice $invoice, DB $db){
    	$this->db = $db;
    	$this->invoice = $invoice;
    }

    public function list(){
        $invoicesCount = DashboardStatsService::make()->invoicesCount();
        $dueInvoices = DashboardStatsService::make()->invoiceDueDate();
        $dueInvoicesCount = $dueInvoices->count();
        $remainderInvoicesCount = DashboardStatsService::make()->remainderInvoicesCount();
        $this->showGraphs();
        return view('admin.invoice.list',compact('invoicesCount','dueInvoicesCount','remainderInvoicesCount'));
    }

    private function showGraphs(){

        // all date graph data
        $yearly = DashboardStatsService::make()->expenseBreakDownYearly();
        $reasons = LavachartsFacade::DataTable();
        $reasons->addStringColumn('Day')
            ->addNumberColumn('Total Cost')
            ->addNumberColumn('vat')
            ->setDateTimeFormat('Y-m-d');
        foreach ($yearly as $key => $val) {
            $reasons->addRow([$key,$val['total'],$val['vat']]);
        }
        LavachartsFacade::ColumnChart('AllExpenses', $reasons, [
            'title' => 'Expense Distribution',
            'elementId' => 'expense_distribution_div',
            'isStacked'=> true,
            'colors' => ['#4c4c4c', '#6c757d'],
            'height'=>270,

        ]);

        $dueInvoicesPerMonthCount = DashboardStatsService::make()->dueInvoicesPerMonthCount();
        $reasons = LavachartsFacade::DataTable();
        $reasons->addStringColumn('Date')
            ->addNumberColumn('Due Date')
            ->setDateTimeFormat('Y-m-d');

        foreach ($dueInvoicesPerMonthCount as $key => $val) {
            $reasons->addRow([$key,$val['dueDate']]);
        }
        LavachartsFacade::ColumnChart('DueDateExpenses', $reasons, [
            'title' => 'Due Date Distribution',
            'elementId' => 'due_date_div',
            'isStacked'=> true,
            'colors' => ['#4c4c4c', '#6c757d'],
              'height'=>270,

        ]);

    }
	public function index(Request $request){


        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        //$invoiceList = $this->invoice->with('user','vendor');

        if (auth()->user()->is_admin === 1) {
            $vendors = Vendor::query();
        }
        else {
            $vendors = auth()->user()->vendors();
        }

        if(isset($request['vendor_id']))
            $vendors = Vendor::whereId($request['vendor_id']);

        if(isset($startDate) && isset($endDate)) {
            $invoiceList = $vendors->with([
                'invoices' => function ($vendors) use ($startDate, $endDate) {
                    $vendors->whereBetween('invoice_date', [$startDate, $endDate]);
                }
            ])->get();
        }else
            $invoiceList = $vendors->with(['invoices'])->get();;

        $data = array();

        if (!empty($invoiceList)) {
            foreach ($invoiceList as $vendor) {
                foreach ($vendor->invoices as $invoice) {

                    $edit = '<a title="Edit" href=' . route("editInvoice", $invoice->id) . '><button type="button" class="btn btn-primary">Edit</button></a>';
                    $detail = '<a title="See Detail" class="customA" href=' . route("invoiceDetail", $invoice->id) . '><i class="fas fa-arrow-right "></i></a>';

                    $vendorName = '<a title="See Detail" class="customA" href=' . route("vendorDetail", $vendor->id) . '>' . $invoice->vendor["name"] . '</a>';

                    //$invoiceData['user'] = '<a title="Detail" href='.route("userDetail", $invoice->user_id).' style="color: #1b55e2;">'.$invoice->userName.'</a>';
                    $invoiceData['vendor'] = $vendorName;
                    $invoiceData['invoice_number'] = $invoice->invoice_number;
                    $invoiceData['invoice_date'] = $invoice->invoice_date;
                    $invoiceData['sub_total'] = $invoice->sub_total;
                    $invoiceData['vat'] = $invoice->vat;
                    $invoiceData['grand_total'] = $invoice->grand_total;
                    $invoiceData['created_at'] = Carbon::parse($invoice->created_at)->format('yy-m-d');
                    $invoiceData['reminder'] = ($invoice->reminder == 1) ? 'Yes' : '-';
                    $invoiceData['days_left'] = ($invoice->invoice_date > $invoice->created_at) ? Carbon::now()->diffInDays(Carbon::parse($invoice->invoice_date)) : '-';
                    $invoiceData['action'] = $detail;
                    $data[] = $invoiceData;
                }
            }
        }
        return Datatables::of($data)
            ->addIndexColumn()
            ->rawColumns(['vendor','action'])
            ->make(true);

	}

	public function add(){
        $users = User::select('id','name')->where('id', '!=' , 1)->get();
        $vendors = Vendor::select('id','name')->where('status', 1)->get();
        $companies = Company::select('id','name')->where('status', 1)->get();
        $currencies = Currency::select('id','name')->get();
		return view('admin.invoice.add', compact('users', 'vendors', 'currencies','companies'));
    }

	public function insert(InvoiceRequest $request){
		try {
			$invoiceData = [
				'vendor_id' => $request->vendor_id,
	            'user_id' => $request->user_id,
	            'currency_id' => $request->currency_id,
	            'title' => $request->title,
	            'invoice_number' => $request->invoice_number,
	            'invoice_date' => $request->invoice_date,
	            'total' => $request->total,
	            'discount' => $request->discount,
	            'sub_total' => $request->sub_total,
	            'vat' => $request->vat,
	            'grand_total' => $request->grand_total,
	            'company_id' => $request->company_id,
	            'billing_address' => $request->billing_address,
	            'shipping_address' => $request->shipping_address,
                'status' => 1,
                'slug' => 'EMPTY-SLUG',
                'pdf_file' => 'EMPTY-FILE',
                'html_file' => 'EMPTY-FILE',
        	];

        	$invoice = $this->invoice->create($invoiceData);
        	$pdf_file = $request->file('pdf_file');
			$html_file = $request->file('html_file');
			if($pdf_file){
				$pdfName = "invoice_".$invoice->id.'.'.$pdf_file->extension();
				$invoice->pdf_file  = saveFile($pdf_file, "invoice/pdf", $pdfName);
			}
			if($html_file){
				$htmlName = "invoice_".$invoice->id.'.'.$html_file->extension();
				$invoice->html_file = saveFile($html_file, "invoice/html", $htmlName);
			}

            // Add user vendors
            $results = DB::select('select id FROM user_vendors where user_id = "'.$invoice->user_id.'" AND vendor_id = "'.$invoice->vendor_id.'" ');
            if(count($results) == 0){
                DB::insert('insert into user_vendors (user_id, vendor_id) values ('.$invoice->user_id.', '.$invoice->vendor_id.')');
            }
            $invoice->slug = Str::slug($invoice->title."-".$invoice->id);
            $invoice->save();

			return redirect(route('invoiceList'))->with('message', flashMessage('insert','Invoice'));
		} catch (Exception $e) {
			return back()->withInput()->withErrors(['errorMessage' => exceptionMessage($e)]);
		}
	}

    public function edit($invoiceId){
        try {

            $invoice = Invoice::with(['user:id,name', 'vendor:id,name','company:id,name'])->where('id', $invoiceId)->first();
            $companies = Company::select('id','name')->where('status', 1)->get();
            $currencies = Currency::select('id','name')->get();
            return view('admin.invoice.edit', compact('invoice', 'currencies','companies'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function update(InvoiceRequest $request, $invoiceId){
        try {
            $invoiceData = [
                'currency_id' => $request->currency_id,
                'title' => $request->title,
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'total' => $request->total,
                'discount' => $request->discount,
                'sub_total' => $request->sub_total,
                'vat' => $request->vat,
                'grand_total' => $request->grand_total,
                'qty' => $request->qty,
                'company_id' => $request->company_id,
                'billing_address' => $request->billing_address,
                'shipping_address' => $request->shipping_address,
                'slug' => Str::slug($request->title."-".$invoiceId),
            ];

            $this->invoice->updateOrCreate(['id' => $invoiceId], $invoiceData);

            return redirect(route('editInvoice', $invoiceId))->with('message', flashMessage('update','invoice'));
        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function detail($invoiceId){
        try {

            $invoice = Invoice::with(['user:id,name', 'vendor:id,name,phone_no,address,email,cvr_number', 'currency:id,name,country','products','company:id,name,phone_no,address,email,cvr_number'])->where('id', $invoiceId)->first();
            return view('admin.invoice.detail', compact('invoice'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function userInvoiceList(Request $request){

        $invoices = Invoice::select('id','title')->where('user_id', $request->user_id)->get();
        return json_encode($invoices);
    }
}
