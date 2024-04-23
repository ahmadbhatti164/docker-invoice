<?php


namespace App\Services;


use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardStatsService extends BaseService
{

    /**
     * DashboardStatsService constructor.
     */
    public function __construct()
    {
        // action to do on init
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function topProducts($vendor_id = null)
    {
        $response = collect();

        if(isset($vendor_id))
            $vendors = Vendor::whereId($vendor_id)->with(['invoiceProducts', 'products'])->get();
        elseif(auth()->user()->is_admin == 1)
             $vendors =  Vendor::with(['invoiceProducts', 'products'])->get();
        else
            $users = User::whereId(Auth::user()->id)->with(['vendors.invoiceProducts', 'vendors.products'])->get();

        if(isset($vendors)) {
            $total = 0;
            foreach ($vendors as $vendor) {
                $invoiceProducts = $vendor->invoiceProducts->groupBy('product_id');
                $total += $vendor->invoiceProducts->sum('price');
                foreach ($invoiceProducts as $key => $value) {
                    $response->push([
                        'sub_total' => $value->sum('price'),
                        'name' => $vendor->products->where('id',$key)->first()->name,
                    ]);
                }
            }
        }
        if(isset($users)){
            foreach ($users as $user) {
                    $total = 0;
                    foreach ($user->vendors as $vendor) {
                        $invoiceProducts = $vendor->invoiceProducts->groupBy('product_id', 'name');
                        $total += $vendor->invoiceProducts->sum('price');

                        foreach ($invoiceProducts as $product) {

                            $response->push([
                                'sub_total' => $product->sum('price'),
                                'name' => $vendor->products->where('id', $product->first()->product_id)->first()->name,
                            ]);
                        }
                    }
                }
            }
        $response = $response->map(function ($r) use ($total) {
            $r['total'] = $total;
            return $r;
        });
        return $response->sortByDesc('sub_total')->take(10);
    }

    public function productPriceDevelopment($product_id = null)
    {
        $response = collect();

        $products = Product::with('invoiceProduct','user')->whereId($product_id);
        if(auth()->user()->is_admin == 1)
            $products =  $products->get();
        else
            $products = $products->where('user_id',Auth::user()->id)->get();

        //dd($products);
                    $total = 0;
                    foreach ($products as $product) {
                        $invoiceProducts = $product->invoiceProduct->groupBy('invoice_id');

                        foreach ($invoiceProducts as $invProduct) {
                            $response->push([
                                'total' => $invProduct->sum('price'),
                                'date' => Carbon::parse($invProduct[0]['created_at'])->format('Y-m-d'),
                                'qty' => $invProduct[0]['qty'],
                                'price' => $invProduct[0]['price'],
                            ]);
                        }
                    }
        //dd($response);

        /*$response = $response->map(function ($r) use ($total) {
            $r['total'] = $total;
            return $r;
        });*/
        return $response->sortByDesc('date');
    }

    /**
     * @param null $vendor_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function invoiceDueDate($vendor_id = null)
    {
        $now = Carbon::now();
        $response = collect();

        if(isset($vendor_id))
            $vendors = Vendor::whereId($vendor_id);
        elseif(auth()->user()->is_admin === 1){
            $vendors = Vendor::query();
        }else{
            $vendors = auth()->user()->vendors();
        }
        $vendors = $vendors->with([
            'invoices' => function ($vendors) use ($now) {
                $vendors->where('invoice_date', '>', $now->toDateString());
            }
        ])->get();

        foreach ($vendors as $vendor) {
            foreach ($vendor->invoices as $invoice) {
                $response->push([
                    'vendor_name' => $vendor->name,
                    'date' => $invoice->invoice_date,
                    'grand_total' => $invoice->grand_total
                ]);
            }
        }
        return $response->sortBy('date');
    }


    /**
     * @return int
     */
    public function remainderInvoicesCount(){
        $now = Carbon::now();
        $total = 0;
        if(auth()->user()->is_admin === 1){
            $vendors = Vendor::query();
        }else{
            $vendors = auth()->user()->vendors();
        }
        $vendors = $vendors->with([
            'invoices' => function ($vendors)  {
                $vendors->where('reminder', '=', 1);
            }
        ])->get();
        foreach ($vendors as $vendor) {
            $total += $vendor->invoices->count('id');
        }
        return $total;
    }
    public function dueInvoicesPerMonthCount(){
        $response = collect();
        $invoices = collect();
        $final = collect();

        if(auth()->user()->is_admin === 1)
            $query = Vendor::query();
        else
            $query = auth()->user()->vendors();

        $vendors = $query->with([
            'invoices' => function ($query) {
                $query->whereYear('invoice_date', Carbon::now()->year);
            }
        ])->get();

        foreach ($vendors as $vendor) {
            foreach ($vendor->invoices as $invoice) {
                $response->push([
                    'date' => $invoice->invoice_date,
                ]);
            }
        }
        $response =  $response->sortBy('date');

        foreach ($response as $month) {
               $invoices->push([
                    'invoice_date' => Carbon::parse($month['date'])->format('M'),
                    'dueDate' => $month['date'],
                ]);

        }
          $month = $invoices->groupBy('invoice_date')
                ->map(function ($inv) {
                    return ['dueDate' => $inv->count('dueDate')];
                });

        return $month;

    }
    public function vendorTotalExpense($vendor_id = null)
    {
        $now = Carbon::now();

        if(isset($vendor_id))
            $vendors = Vendor::whereId($vendor_id);
        elseif(auth()->user()->is_admin === 1){
            $vendors = Vendor::query();
        }else{
            $vendors = auth()->user()->vendors();
        }
       /* $vendors = $vendors->with([
            'invoices' => function ($vendors) use ($now) {
                $vendors->where('invoice_date', '>', $now->toDateString());
            }
        ]);*/

        $vendors = $vendors->get();
        $total = 0;
        foreach ($vendors as $vendor) {
            foreach ($vendor->invoices as $invoice) {
                $total +=$invoice['grand_total'];
            }
        }
        return $total;
    }
    /**
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|int|mixed
     */
    public function invoicesCount()
    {
        $total = 0;
        if(auth()->user()->is_admin === 1){
            $vendors = Vendor::query();
        }else{
            $vendors = auth()->user()->vendors();
        }
        $vendors = $vendors->withCount('invoices')->get();

        foreach ($vendors as $vendor) {
            $total +=$vendor->invoices_count;
        }

        return $total;
    }

    /**
     * @return int|mixed
     */
    public function totalExpense()
    {
        if(auth()->user()->is_admin === 1){
            $vendors = Vendor::query();
        }else{
            $vendors = auth()->user()->vendors();
        }
        $vendors = $vendors->with('invoices')->get();
        $now = Carbon::now();
        $startOfWeek = $now->startOfWeek()->format('Y-m-d');
        $endOfWeek = $now->endOfWeek()->format('Y-m-d');
        $startMonth = $now->startOfMonth()->format('Y-m-d');
        $endMonth = $now->endOfMonth()->format('Y-m-d');
        $year = $now->startOfYear()->format('Y-m-d');
        $data = collect();

        foreach ($vendors as $vendor) {
            $data->push([
                'week' => $vendor->invoices->where('invoice_date', '>=', $startOfWeek)->where('invoice_date', '<=', $endOfWeek)->sum('grand_total'),
                'month' => $vendor->invoices->where('invoice_date', '>=', $startMonth)->where('invoice_date', '<=', $endMonth)->sum('grand_total'),
                'year' => $vendor->invoices->where('invoice_date', '>=', $year)->sum('grand_total'),
                'week_vat' => $vendor->invoices->where('invoice_date', '>=', $startOfWeek)->where('invoice_date', '<=', $endOfWeek)->sum('vat'),
                'month_vat' => $vendor->invoices->where('invoice_date', '>=', $startMonth)->where('invoice_date', '<=', $endMonth)->sum('vat'),
                'year_vat' => $vendor->invoices->where('invoice_date', '>=', $year)->sum('vat'),
                'grand_total' => $vendor->invoices->sum('grand_total')
            ]);
        }
        $response = collect()->put('week', $data->sum('week'));
        $response->put('month', $data->sum('month'));
        $response->put('year', $data->sum('year'));
        $response->put('week_vat', $data->sum('week_vat'));
        $response->put('month_vat', $data->sum('month_vat'));
        $response->put('year_vat', $data->sum('year_vat'));
        $response->put('grand_total', $data->sum('grand_total'));
        $response->put('week_number', Carbon::now()->weekOfMonth);
        $response->put('month_number', $now->monthName);
        $response->put('year_number', $now->year);

        return $response;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return \Illuminate\Support\Collection
     */
    public function expenseBreakDown($startDate, $endDate)
    {
        $invoices = collect();
        $response = collect();
        $final = collect();
        if(auth()->user()->is_admin === 1){
            $query = Vendor::query();
        }else{
            $query = auth()->user()->vendors();
        }

        $vendors = $query->with([
            'invoices' => function ($query) use ($startDate, $endDate) {
                $query->where('invoice_date', '>=', $startDate)->where('invoice_date', '<=', $endDate);
            }
        ])->get();

        foreach ($vendors as $vendor) {
            $invoices->push($vendor->invoices->groupBy('invoice_date')
                ->map(function ($item) {
                    return ['total' => $item->sum('grand_total'), 'vat' => $item->sum('vat')];
                }));
        }

        foreach ($invoices as $month) {
            foreach ($month as $key => $value) {
                $response->push([
                    'date' => $key,
                    'total' => $value['total'],
                    'vat' => $value['vat']
                ]);
            }
        }
        $response = $response->sortBy('date');

        foreach ($response as $month) {
            $final->push([
                'invoice_date' => Carbon::parse($month['date'])->format('M'),
                'total' => $month['total'],
                'vat' => $month['vat'],
            ]);

        }

        return $final->groupBy('invoice_date')
            ->map(function ($item) {
                return ['total' => $item->sum('total'), 'vat' => $item->sum('vat')];
            });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function expenseBreakDownWeekly()
    {
        $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');
        return $this->expenseBreakDown($startOfWeek, $endOfWeek);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function expenseBreakDownMonthly()
    {
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

        return $this->expenseBreakDown($startOfMonth, $endOfMonth);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function expenseBreakDownYearly()
    {
        $startOfYear = Carbon::now()->startOfYear()->format('Y-m-d');
        $endOfYear = Carbon::now()->endOfYear()->format('Y-m-d');

        return $this->expenseBreakDown($startOfYear, $endOfYear);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function expenseBreakDownAll()
    {
        $startOfYear = Carbon::now()->addYear(-2)->format('Y-m-d');
        $endOfYear = Carbon::now()->addYear(2)->format('Y-m-d');

        return $this->expenseBreakDown($startOfYear, $endOfYear);
    }
}
