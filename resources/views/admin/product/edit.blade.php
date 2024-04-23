@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>Edit Product</h1>
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

				  	<form role="form" action="{{ route('updateProduct', $product->id) }}" method="POST" enctype="multipart/form-data">
				  		@csrf
						<div class="card-body">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="name">Name</label>
										<input type="text" class="form-control" id="name" name="name" value="{{old('name', $product->name)}}" placeholder="Enter Name">
									</div>
        							<div class="input-error">{{ $errors->first('name') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="user_id">User</label>
											<input type="text" class="form-control" id="user_id" name="user_id" value="{{old('title', $product->user->name)}}"  disabled>
									</div>
        							<div class="input-error">{{ $errors->first('user_id') }}</div>
								</div>
<!--								<div class="col-sm-6">
									<div class="form-group">
										<label for="invoice_id">Invoice</label>
										<input type="text" class="form-control" id="invoice_id" name="invoice_id" value="old('title', $product->invoice->title)}}" disabled>
									</div>
        							<div class="input-error"> $errors->first('invoice_id') }}</div>
								</div>-->
								<div class="col-sm-6">
									<div class="form-group">
										<label for="price">Price</label>
										<input type="text" class="form-control" id="price" name="price" value="{{old('price', $product->price)}}" placeholder="Enter Price">
									</div>
        							<div class="input-error">{{ $errors->first('price') }}</div>
								</div>
							<!--	<div class="col-sm-6">
									<div class="form-group">
										<label for="discount">Discount</label>
										<input type="text" class="form-control" id="discount" name="discount" value="old('discount', $product->discount)}}" placeholder="Enter Discount">
									</div>
        							<div class="input-error"> $errors->first('discount') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="sub_total">Sub Total</label>
										<input type="text" class="form-control" id="sub_total" name="sub_total" value="old('sub_total', $product->sub_total)}}" placeholder="Enter Sub Total">
									</div>
        							<div class="input-error"> $errors->first('sub_total') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="vat">Vat</label>
										<input type="text" class="form-control" id="vat" name="vat" value="old('vat', $product->vat)}}" placeholder="Enter Vat">
									</div>
        							<div class="input-error"> $errors->first('vat') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="grand_total">Grand Total</label>
										<input type="text" class="form-control" id="grand_total" name="grand_total" value="old('grand_total', $product->grand_total)}}" placeholder="Enter Grand Total">
									</div>
        							<div class="input-error"> $errors->first('grand_total') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="qty">Quantity</label>
										<input type="text" class="form-control" id="qty" name="qty" value="old('qty', $product->qty)}}" placeholder="Enter Quantity">
									</div>
        							<div class="input-error"> $errors->first('qty') }}</div>
								</div>-->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="qty">Content</label>
                                        <input type="text" class="form-control" id="content1" name="content1" value="{{old('content1',$product->content)}}" placeholder="Enter Content">
                                    </div>
                                    <div class="input-error">{{ $errors->first('content1') }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="qty">Content Price</label>
                                        <input type="text" class="form-control" id="content_price" name="content_price" value="{{old('content_price',$product->content_price)}}" placeholder="Enter Content Price">
                                    </div>
                                    <div class="input-error">{{ $errors->first('content_price') }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="qty">Unit</label>
                                        <input type="text" class="form-control" id="unit" name="unit" value="{{old('unit',$product->unit)}}" placeholder="Enter Unit">
                                    </div>
                                    <div class="input-error">{{ $errors->first('unit') }}</div>
                                </div>
							</div>
						</div>

						<div class="float-right" style="padding: 20px;">
						  	<button type="submit" class="btn btn-primary">Update Product</button>
						</div>
				  </form>

				</div>
		  	</div>
		</div>
	  </div>
</section>

@endsection
