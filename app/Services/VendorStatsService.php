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

class VendorStatsService extends BaseService
{

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function topVendorsWithExpense()
    {
        $response = collect();
        if(auth()->user()->is_admin === 1){
            $vendors = Vendor::query();
        }else{
            $vendors = auth()->user()->vendors();
        }
        $vendors = $vendors->with('invoices')->get();
        foreach ($vendors as $vendor) {
            $total = 0;
            foreach ($vendor->invoices as $invoice) {
                $response->push([
                    'vendor_name' => $vendor->name,
                    'grand_total' => $total += $invoice['grand_total'],
                    'vendor_total' => $invoice->sum('grand_total')
                ]);
            }
        }
        $grouped = $response->groupBy('vendor_name')
            ->map(function ($item) {
                return $item->sum('grand_total');
            });
        return $grouped->toArray();
    }
    /**
     * @param $startDate
     * @param $endDate
     * @return \Illuminate\Support\Collection
     */
    public function vendorExpenseBreakDown($startDate, $endDate)
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
                ->map(function ($item) use($vendor) {
                    return ['total' => $item->sum('grand_total'), 'vat' => $item->sum('vat'),'vendor_name'=>$vendor->name];
                }));
        }
        foreach ($invoices as $month) {
            foreach ($month as $key => $value) {
                $response->push([
                    'date' => $key,
                    'total' => $value['total'],
                    'vat' => $value['vat'],
                    'vendor_name'=>$value['vendor_name']
                ]);
            }
        }
        $response = $response->sortBy('date');

        foreach ($response as $month) {
            $final->push([
                'invoice_date' => Carbon::parse($month['date'])->format('M'),
                'total' => $month['total'],
                'vat' => $month['vat'],
                'vendor_name' => $month['vendor_name'],
            ]);

        }
        return $final->groupBy('invoice_date')
            ->map(function ($item) {
                return ['total' => $item->sum('total'), 'vat' => $item->sum('vat')];
            });
    }
    public function vendorExpenseBreakDownYearly()
    {
        $startOfYear = Carbon::now()->startOfYear()->format('Y-m-d');
        $endOfYear = Carbon::now()->endOfYear()->format('Y-m-d');

        return $this->vendorExpenseBreakDown($startOfYear, $endOfYear);
    }

}
