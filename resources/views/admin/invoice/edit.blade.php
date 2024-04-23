@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>Edit Invoice</h1>
		  	</div>
		</div>
	  </div>
</section>

<!-- Main content -->
<section class="content">
  	<div class="container-fluid">
  		@if(Session::has('message'))
	  		<div class="alert alert-success alert-dismissible" data-auto-dismiss role="alert">
			    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			    <h4><i class="icon fa fa-check"></i> Success! <span style="font-weight: 200;">{{ Session::get('message') }} </span></h4>
			</div>
		@endif
		<div class="row">
		  	<div class="col-md-12">
				<div class="card card-primary">
				  	<div class="card-header">
						<h3 class="card-title">Basic Information</h3>
				  	</div>

				  	<form role="form" action="{{ route('updateInvoice', $invoice->id) }}" method="POST" enctype="multipart/form-data">
				  		@csrf
						<div class="card-body">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="title">Title</label>
										<input type="text" class="form-control" id="title" name="title" value="{{old('title', $invoice->title)}}" placeholder="Enter Title">
									</div>
        							<div class="input-error">{{ $errors->first('title') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="user_id">User</label>
										<input type="text" class="form-control" id="user_id" name="user_id" value="{{old('user_id', $invoice->user->name)}}" placeholder="Enter Title" disabled>
									</div>
        							<div class="input-error">{{ $errors->first('user_id') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="vendor_id">Vendor</label>
										<input type="text" class="form-control" id="vendor_id" name="vendor_id" value="{{old('vendor_id', $invoice->vendor->name)}}" placeholder="Enter Title" disabled>
									</div>
        							<div class="input-error">{{ $errors->first('vendor_id') }}</div>
								</div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="vendor_id">Company</label>
                                        <select class="form-control" id="company_id" name="company_id">
                                            <option value="">Select Company</option>
                                            @foreach ($companies as $comp)
                                                @if($comp->id == $invoice->company_id)
                                                    <option value="{{$comp->id}}" selected>{{$comp->name}}</option>
                                                @else
                                                    <option value="{{$comp->id}}">{{$comp->name}}</option>
                                                @endif

                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input-error">{{ $errors->first('company_id') }}</div>
                                </div>

								<div class="col-sm-6">
									<div class="form-group">
										<label for="invoice_number">Invoice Number</label>
										<input type="text" class="form-control" id="invoice_number" name="invoice_number" value="{{old('invoice_number', $invoice->invoice_number)}}" placeholder="Enter Invoice Number">
									</div>
        							<div class="input-error">{{ $errors->first('invoice_number') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="invoice_date">Invoice Date</label>
										<input type="date" class="form-control" id="invoice_date" name="invoice_date" value="{{old('invoice_date', $invoice->invoice_date)}}" placeholder="Enter Invoice Date">
									</div>
        							<div class="input-error">{{ $errors->first('invoice_date') }}</div>
								</div>

								<div class="col-sm-6">
									<div class="form-group">
										<label for="total">Total</label>
										<input type="text" class="form-control" id="total" name="total" value="{{old('total', $invoice->total)}}" placeholder="Enter Total">
									</div>
        							<div class="input-error">{{ $errors->first('total') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="discount">Discount</label>
										<input type="text" class="form-control" id="discount" name="discount" value="{{old('discount', $invoice->discount)}}" placeholder="Enter Discount">
									</div>
        							<div class="input-error">{{ $errors->first('discount') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="sub_total">Sub Total</label>
										<input type="text" class="form-control" id="sub_total" name="sub_total" value="{{old('sub_total', $invoice->sub_total)}}" placeholder="Enter Sub Total">
									</div>
        							<div class="input-error">{{ $errors->first('sub_total') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="vat">Vat</label>
										<input type="text" class="form-control" id="vat" name="vat" value="{{old('vat', $invoice->vat)}}" placeholder="Enter Vat">
									</div>
        							<div class="input-error">{{ $errors->first('vat') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="grand_total">Grand Total</label>
										<input type="text" class="form-control" id="grand_total" name="grand_total" value="{{old('grand_total', $invoice->grand_total)}}" placeholder="Enter Grand Total">
									</div>
        							<div class="input-error">{{ $errors->first('grand_total') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="currency_id">Currency</label>
										<select class="form-control" id="currency_id" name="currency_id">
											<option value="">Select Currency</option>
											@foreach ($currencies as $currency)
												<option value="{{$currency->id}}" {{ ((old('currency_id', $invoice->currency_id)== $currency->id)?'selected':'') }}>{{$currency->name}}</option>
											@endforeach
										</select>
									</div>
        							<div class="input-error">{{ $errors->first('currency_id') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
					                  	<label for="pdf_file">Pdf File</label>
					                  	<a href="{{ asset('storage/'.$invoice->pdf_file) }}" download class="form-control" >{{ str_replace('invoice/pdf/', '', $invoice->pdf_file) }}</a>
					                </div>
        							<div class="input-error">{{ $errors->first('pdf_file') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
					                  	<label for="html_file">Html File</label>
					                  	<a href="{{ asset('storage/'.$invoice->html_file) }}" download class="form-control" >{{ str_replace('invoice/html/', '', $invoice->html_file) }}</a>
					                </div>
        							<div class="input-error">{{ $errors->first('html_file') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="billing_address">Billing Address</label>
										<textarea class="form-control" rows="3" id="billing_address" name="billing_address" placeholder="Billing Address...">{{ $invoice->billing_address }}</textarea>
									</div>
        							<div class="input-error">{{ $errors->first('billing_address') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="shipping_address">Shipping Address</label>
										<textarea class="form-control" rows="3" id="shipping_address" name="shipping_address" placeholder="Shipping Address...">{{ $invoice->shipping_address }}</textarea>
									</div>
        							<div class="input-error">{{ $errors->first('shipping_address') }}</div>
								</div>
							</div>
						</div>

						<div class="float-right" style="padding: 20px;">
						  	<button type="submit" class="btn btn-primary">Update Invoice</button>
						</div>
				  </form>

				</div>
		  	</div>
		</div>
	  </div>
</section>


@endsection
