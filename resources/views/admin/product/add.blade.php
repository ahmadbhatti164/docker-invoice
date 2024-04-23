@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>Add Product</h1>
		  	</div>
		</div>
	  </div>
</section>

<!-- Main content -->
<section class="content">
  	<div class="container-fluid">
		<div class="row">
		  	<div class="col-md-12">
				<div class="card card-primary">
				  	<div class="card-header">
						<h3 class="card-title">Basic Information</h3>
				  	</div>

				  	<form role="form" action="{{ route('productForm') }}" method="POST" enctype="multipart/form-data">
				  		@csrf
						<div class="card-body">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="name">Name</label>
										<input type="text" class="form-control" id="name" name="name" value="{{old('name')}}" placeholder="Enter Name">
									</div>
        							<div class="input-error">{{ $errors->first('name') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="user_id">User</label>
										<select class="form-control" id="user_id" name="user_id">
											<option value="">Select User</option>
											@foreach ($users as $user)
												<option value="{{$user->id}}" {{ ((old('user_id')== $user->id)?'selected':'') }}>{{$user->name}}</option>
											@endforeach
										</select>
									</div>
        							<div class="input-error">{{ $errors->first('user_id') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="invoice_id">Invoice</label>
										<select class="form-control" id="invoice_id" name="invoice_id">
											<option value="">Select Invoice</option>
										</select>
									</div>
        							<div class="input-error">{{ $errors->first('invoice_id') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="price">Price</label>
										<input type="text" class="form-control" id="price" name="price" value="{{old('price')}}" placeholder="Enter Price">
									</div>
        							<div class="input-error">{{ $errors->first('price') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="discount">Discount</label>
										<input type="text" class="form-control" id="discount" name="discount" value="{{old('discount')}}" placeholder="Enter Discount">
									</div>
        							<div class="input-error">{{ $errors->first('discount') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="sub_total">Sub Total</label>
										<input type="text" class="form-control" id="sub_total" name="sub_total" value="{{old('sub_total')}}" placeholder="Enter Sub Total">
									</div>
        							<div class="input-error">{{ $errors->first('sub_total') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="vat">Vat</label>
										<input type="text" class="form-control" id="vat" name="vat" value="{{old('vat')}}" placeholder="Enter Vat">
									</div>
        							<div class="input-error">{{ $errors->first('vat') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="grand_total">Grand Total</label>
										<input type="text" class="form-control" id="grand_total" name="grand_total" value="{{old('grand_total')}}" placeholder="Enter Grand Total">
									</div>
        							<div class="input-error">{{ $errors->first('grand_total') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="qty">Quantity</label>
										<input type="text" class="form-control" id="qty" name="qty" value="{{old('qty')}}" placeholder="Enter Quantity">
									</div>
        							<div class="input-error">{{ $errors->first('qty') }}</div>
								</div>
                                <div class="col-sm-6">
									<div class="form-group">
										<label for="qty">Content</label>
										<input type="text" class="form-control" id="content1" name="content1" value="{{old('content1')}}" placeholder="Enter Content">
									</div>
        							<div class="input-error">{{ $errors->first('content1') }}</div>
								</div>
                                <div class="col-sm-6">
									<div class="form-group">
										<label for="qty">Content Price</label>
										<input type="text" class="form-control" id="content_price" name="content_price" value="{{old('content_price')}}" placeholder="Enter Content Price">
									</div>
        							<div class="input-error">{{ $errors->first('content_price') }}</div>
								</div>
                                <div class="col-sm-6">
									<div class="form-group">
										<label for="qty">Unit</label>
										<input type="text" class="form-control" id="unit" name="unit" value="{{old('unit')}}" placeholder="Enter Unit">
									</div>
        							<div class="input-error">{{ $errors->first('unit') }}</div>
								</div>
							</div>
						</div>

						<div class="float-right" style="padding: 20px;">
						  	<button type="submit" class="btn btn-primary">Add Product</button>
						</div>
				  </form>

				</div>
		  	</div>
		</div>
	  </div>
</section>


@endsection

@push('script')
	<script type="text/javascript">

		$(document).ready(function($) {

			var oldInvoice = "{{old('invoice_id')}}";
			$('#user_id').on('change', function(){

				$('#invoice_id').html('');
				$('#invoice_id').append('<option value="">Select Invoice</option>');

				$.ajax({
					url: '{{ route('userInvoiceList') }}',
					type: 'POST',
					data: {user_id: $(this).val()},
				})
				.done(function(response) {
					var html = '';
					var resp = JSON.parse(response);
					$.each(resp, function(index, val) {
						if(oldInvoice == val.id){
							html += '<option value="'+val.id+'" selected> '+val.title+' </option>';
						}else{
							html += '<option value="'+val.id+'"> '+val.title+' </option>';
						}
					});
					$('#invoice_id').append(html);
				})
				.fail(function() {
					console.log("error");
				});
			});
		});
	</script>
@endpush
