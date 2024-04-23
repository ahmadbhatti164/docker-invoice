<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\InvoiceProduct;
use App\Services\DashboardStatsService;
use Carbon\Carbon;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\Currency;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Khill\Lavacharts\Laravel\LavachartsFacade;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    //
    private $db;
    private $product;
    public function __construct(Product $product, DB $db){
    	$this->db = $db;
    	$this->product = $product;
    }

    public function list(){

         return view('admin.product.list');
    }

	public function index(Request $request){

        $response = collect();

        if(auth()->user()->is_admin == 1){
            $products = Product::with('invoiceProduct');
            $productList = $products->get();
        }
        else
            $users = User::whereId(Auth::user()->id)->with(['vendors.invoiceProducts', 'vendors.products'])->get();


        if(isset($users)){
            foreach ($users as $user) {
                $total = 0;
                foreach ($user->vendors as $vendor) {
                    $invoiceProducts = $vendor->invoiceProducts->groupBy('product_id', 'name');
                    $total += $vendor->invoiceProducts->sum('price');

                    foreach ($invoiceProducts as $product) {
                        $response->push([
                            'id'=> $product->first()->product_id,
                            'vendor_id'=>$vendor->id,
                            'vendorName'=>$vendor->name,
                            'content'=> $vendor->products->where('id', $product->first()->product_id)->first()->content,
                            'content_price'=> $vendor->products->where('id', $product->first()->product_id)->first()->content_price,
                            'unit'=> $vendor->products->where('id', $product->first()->product_id)->first()->unit,
                            'qty'=>$product->sum('qty'),
                            'price' => $product->avg('price'),
                            'total' => $product->sum('total'),
                            'name' => $vendor->products->where('id', $product->first()->product_id)->first()->name,
                        ]);
                    }
                }
            }
        }
        $data = array();
        if (!empty($response)) {
            foreach ($response as $product) {
                $edit = '<a title="Edit" href=' . route("editProduct", $product['id']) . '><button type="button" class="btn btn-primary">Edit</button></a>';
                $detail = '<a title="See Detail" class="customA" href=' . route("productDetail", $product['id']) . '><i class="fas fa-arrow-right "></i></a>';

                $productData['name'] = $product['name'];
                // $productData['user'] = '<a title="Detail" href='.route("userDetail", $product->user_id).' style="color: #1b55e2;">'.$product->userName.'</a>';
                $productData['vendor'] = '<a title="Detail" href=' . route("vendorDetail", $product['vendor_id']) . ' style="color: #1b55e2;">' . $product['vendorName'] . '</a>';
                // $productData['invoice'] = '<a title="Detail" href='.route("invoiceDetail", $product->invoice_id).' style="color: #1b55e2;">'.$product->invoiceTitle.'</a>';
                $productData['price'] = $product['price'];
                $productData['content'] = $product['content'];
                $productData['content_price'] = $product['content_price'];
                $productData['unit'] = $product['unit'];

                $productData['qty'] = $product['qty'];
                $productData['total'] = $product['total'];
                $productData['action'] = $detail;
                $data[] = $productData;
            }
        }

        if (!empty($productList)) {
            foreach ($productList as $product) {

                $edit = '<a title="Edit" href='.route("editProduct", $product->id).'><button type="button" class="btn btn-primary">Edit</button></a>';
                $detail = '<a title="See Detail" class="customA" href='.route("productDetail", $product->id).'><i class="fas fa-arrow-right "></i></a>';

                $productData['name'] = $product->name;
               // $productData['user'] = '<a title="Detail" href='.route("userDetail", $product->user_id).' style="color: #1b55e2;">'.$product->userName.'</a>';
                $productData['vendor'] = '<a title="Detail" href='.route("vendorDetail", $product->vendor_id).' style="color: #1b55e2;">'.$product->vendorName.'</a>';
               // $productData['invoice'] = '<a title="Detail" href='.route("invoiceDetail", $product->invoice_id).' style="color: #1b55e2;">'.$product->invoiceTitle.'</a>';
                $productData['price'] = $product->invoiceProduct->avg('price');
                $productData['content'] = $product->content;
                $productData['content_price'] = $product->content_price;
                $productData['unit'] = $product->unit;

                $productData['qty'] = $product->invoiceProduct->sum('qty');
                $productData['total'] = $product->invoiceProduct->sum('total');
                $productData['action'] = $detail;
                $data[] = $productData;
            }
        }
        return Datatables::of($data)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);

	}

	public function add(){
		$users = User::select('id','name')->where('id', '!=' , 1)->get();
		$invoices = Invoice::select('id','title')->get();
		return view('admin.product.add', compact('users', 'invoices'));
    }

	public function insert(ProductRequest $request){
		try {

            $invoice = Invoice::select('vendor_id')->where('id', $request->invoice_id)->first();
            if($invoice->vendor_id){
                $productData['vendor_id'] = $invoice->vendor_id;
            }
            $addProduct['name'] = $request->name;
            $addProduct['vendor_id'] = $invoice->vendor_id;
            $addProduct['user_id'] = $request->user_id;
            $addProduct['status'] = 1;
            $addProduct['slug'] = 'EMPTY-SLUG';
            $addProduct['price'] = $request->price;
            $addProduct['content'] = $request->content1;
            $addProduct['content_price'] = $request->content_price;
            $addProduct['unit'] = $request->unit;

            $invoiceProduct['invoice_id'] = $request->invoice_id;
            $invoiceProduct['qty'] = isset($request->qty)? $request->qty : 0;
            $invoiceProduct['price'] = $request->price;
            $invoiceProduct['total'] = $request->grand_total;
            $invoiceProduct['discount'] = isset($request->discount)? $request->discount : 0;
            $invoiceProduct['sub_total'] = isset($request->sub_total)? $request->sub_total : 0;
            $invoiceProduct['vat'] = isset($request->vat)? $request->vat : 0;
            $invoiceProduct['grand_total'] = $request->grand_total;


            $product = Product::select('id')->where(['name'=>$request->name,'vendor_id'=>$productData['vendor_id']])->first();

            if(!$product)
            {
                $product = Product::create($addProduct);
                $product->slug = Str::slug($product->name . "-" . $product->id);
                $product->save();
                $invoiceProduct['product_id'] = $product->id;
            }
            else
                $invoiceProduct['product_id'] = $product->id;

            InvoiceProduct::create($invoiceProduct);

			return redirect(route('productList'))->with('message', flashMessage('insert','Product'));
		} catch (Exception $e) {
			return back()->withInput()->withErrors(['errorMessage' => exceptionMessage($e)]);
		}
	}

    public function edit($productId){
        try {

            $product = Product::with(['user:id,name'])->where('id', $productId)->first();
            return view('admin.product.edit', compact('product'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function update(Request $request, $productId){
        try {
            $productData = [
	            'name' => $request->name,
	            'price' => $request->price,
	            'content' => $request->content1,
	            'content_price' => $request->content_price,
	            'unit' => $request->unit,
	            'slug' => Str::slug($request->name."-".$productId),
        	];

        	$this->product->updateOrCreate(['id' => $productId], $productData);

            return redirect(route('editProduct', $productId))->with('message', flashMessage('update','Product'));
        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function detail($productId){
        try {
            $products = DashboardStatsService::make()->productPriceDevelopment($productId);
            $unitBought = $products->count();
            $avgPrice = $products->avg('total');
            $total = $products->sum('total');

            $now = Carbon::now();
            $previous = $products->where('date', '<=', $now->toDateString());
            $next = $products->where('date', '>', $now->toDateString());
            $this->showGraphs($products);
            $product = Product::with(['user:id,name', 'vendor:id,name'])
            ->where('id', $productId)->first();
            return view('admin.product.detail', compact('product','unitBought','avgPrice','total','previous','next'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }
    private function showGraphs($products){

        // all date graph data
        $startOfYear = Carbon::now()->startOfYear()->format('Y-m-d');
        $endOfYear = Carbon::now()->endOfYear()->format('Y-m-d');

        $products =  $products->whereBetween('date', [$startOfYear, $endOfYear]);
        $reasons = LavachartsFacade::DataTable();
        $reasons->addDateColumn('Day')
            ->addNumberColumn('Total Cost')
            ->setDateTimeFormat('Y-m-d');

        foreach ($products as $key => $val) {
            $reasons->addRow([$val['date'],$val['total']]);
        }
       LavachartsFacade::LineChart('AllExpenses', $reasons, [
            'title' => 'Product Price Development',
            'elementId' => 'product_price_development',
            'isStacked'=> true,
            'colors' => ['#4c4c4c', '#6c757d'],
            'height'=>270,

        ]);
    }

    public function delete($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            $invoiceProduct = InvoiceProduct::where('product_id','=',$id)->delete();

            return response()->json(['success' => 'true']);
        }
        catch (Exception $e) {
            return back()->withInput()->withErrors(['errorMessage' => exceptionMessage($e)]);
        }
    }
}
