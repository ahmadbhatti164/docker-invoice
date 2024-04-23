@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>Update Vendor</h1>
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

				  	<form role="form" action="{{ route('updateVendor', $vendor->id) }}" method="POST">
				  		@csrf
						<div class="card-body">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="name">Name</label>
										<input type="text" class="form-control" id="name" name="name" value="{{old('name', $vendor->name)}}" placeholder="Enter Name">
									</div>
        							<div class="input-error">{{ $errors->first('name') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="email">Email</label>
										<input type="text" class="form-control" id="email" name="email" value="{{old('email', $vendor->email)}}" placeholder="Enter Email" disabled>
									</div>
        							<div class="input-error">{{ $errors->first('email') }}</div>
								</div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="phone_no">Phone</label>
                                        <input type="text" class="form-control" id="phone_no" name="phone_no" value="{{old('phone_no', $vendor->phone_no)}}" placeholder="Enter Phone">
                                    </div>
                                    <div class="input-error">{{ $errors->first('phone_no') }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" value="{{old('address', $vendor->address)}}" placeholder="Enter Address">
                                    </div>
                                    <div class="input-error">{{ $errors->first('address') }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="parser">CVR number</label>
                                        <input type="cvr_number" class="form-control" id="cvr_number" name="cvr_number" value="{{old('address', $vendor->cvr_number)}}" placeholder="CVR number">
                                    </div>
                                    <div class="input-error"> {{$errors->first('cvr_number') }} </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="parser">Category</label>
                                        <select class="form-control bootstrap-select" id="category_id" name="category_id" >
                                            <option value="">Select</option>

                                            @foreach($categories as $cat)
                                                @if($cat->id == $vendor->category_id)
                                                    <option value="{{ $cat->id }}" selected>{{ $cat->name }}</option>
                                                @else
                                                    <option value="{{ $cat->id }}" >{{ $cat->name }}</option>
                                                @endif
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="input-error">{{ $errors->first('parser_parameters') }}</div>
                                </div>

                                <div class="col-sm-12">
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="parser">Parser Parameters</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9 float-right">
                                            <div class="form-group">
                                                <button type="button" data-toggle="modal" data-target="#parserModal" class="btn btn-primary float-right">Parse Invoice</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="fileBox" style="display: none;">
                                        <div class="row invoice-info">
                                            <div class="col-sm-12 invoice-col">
                                                <div class="row">
                                                    <div class="col-sm-12 card-body">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <iframe class="doc iframe-file" src=""></iframe>
                                                            </div>
                                                            <div class="col-sm-6" style="overflow: auto;border: 1px solid #eeeeee;">
                                                                {{--                                                                    <iframe class="" src="view-source:https://stackoverflow.com/questions/12359178/php-in-html-php-code-showing"></iframe>--}}

                                                                <xmp class="doc paracode" style="height: 455px;">

                                                                </xmp>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                </div>

                                <div class="col-sm-9" style="padding: 15px 10px 20px 10px;">
                                    <div class="small">
                                        <span class="mr-5">
                                            <i class="fas fa-circle text-primary"></i> Simple Index : 26
                                        </span>
                                        <span class="mr-5">
                                            <i class="fas fa-circle text-success"></i> if count from end : Total index - 6
                                        </span>
                                        <span class="mr-5">
                                            <i class="fas fa-circle text-info"></i> to eliminate any words : @DKK
                                        </span>
                                        <span class="mr-5">
                                            <i class="fas fa-circle text-danger"></i> find next word : |item|count
                                        </span>
                                    </div>

                                </div>

                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="parser">Invoice Number</label>
                                                <input type="text" class="form-control" id="invoice_no" name="invoice_no" value="{{old('invoice_no',isset($parser_parameters['invoice_no'])? $parser_parameters['invoice_no'] : '')}}" placeholder="invoice no">
                                            </div>
                                            <div class="input-error">{{ $errors->first('invoice_no') }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="parser">Total</label>
                                                <input type="text" class="form-control" id="total" name="total" value="{{old('total',isset($parser_parameters['total'])? $parser_parameters['total'] : '')}}" placeholder="total">
                                            </div>
                                            <div class="input-error">{{ $errors->first('total') }}</div>
                                        </div>
                                        <!--     <div class="col-4">
                                               <div class="form-group">
                                                    <label for="parser">CVR Number</label>
                                                    <input type="text" class="form-control" id="cvr_number" name="cvr_number" value="old('cvr_number',isset($parser_parameters['cvr_number'])? $parser_parameters['cvr_number'] : '')}}" placeholder="cvr_number">
                                                </div>
                                                <div class="input-error"> $errors->first('cvr_number') }}</div>
                                        </div>-->
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="parser">Invoice Date</label>
                                                <input type="text" class="form-control" id="invoice_date" name="invoice_date" value="{{old('invoice_date',isset($parser_parameters['invoice_date'])? $parser_parameters['invoice_date'] : '')}}" placeholder="invoice date">
                                            </div>
                                            <div class="input-error">{{ $errors->first('invoice_date') }}</div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="parser">Vat</label>
                                                <input type="text" class="form-control" id="vat" name="vat" value="{{old('vat',isset($parser_parameters['vat'])? $parser_parameters['vat'] : '')}}" placeholder="vat">
                                            </div>
                                            <div class="input-error">{{ $errors->first('vat') }}</div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="parser">Sub Total</label>
                                                <input type="text" class="form-control" id="sub_total" name="sub_total" value="{{old('sub_total',isset($parser_parameters['sub_total'])? $parser_parameters['sub_total'] : '')}}" placeholder="sub total">
                                            </div>
                                            <div class="input-error">{{ $errors->first('sub_total') }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="parser">Grand Total</label>
                                                <input type="text" class="form-control" id="grand_total" name="grand_total" value="{{old('grand_total',isset($parser_parameters['grand_total'])? $parser_parameters['grand_total'] : '')}}" placeholder="grand total">
                                            </div>
                                            <div class="input-error">{{ $errors->first('grand_total') }}</div>
                                        </div>
                                    </div>
                                        <div class="row mb-3 rowBorderBottom">
                                            <div class="col-4">
                                                <div class="form-group mb-2 mt-3">
                                                    <label for="parser">Company Information</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="parser">Company Name</label>
                                                    <input type="text" class="form-control" id="company_name" name="company_name" value="{{old('company_name',isset($parser_parameters['company_name'])? $parser_parameters['company_name'] : '')}}" placeholder="company name">

                                                </div>
                                                <div class="input-error">{{ $errors->first('company_name') }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="parser">Company Email</label>
                                                    <input type="text" class="form-control" id="company_email" name="company_email" value="{{old('company_email',isset($parser_parameters['company_email'])? $parser_parameters['company_email'] : '')}}" placeholder="company email">
                                                </div>
                                                <div class="input-error">{{ $errors->first('company_email') }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="parser">Company Phone</label>
                                                    <input type="text" class="form-control" id="company_phone" name="company_phone" value="{{old('company_phone',isset($parser_parameters['company_phone'])? $parser_parameters['company_phone'] : '')}}" placeholder="company phone">
                                                </div>
                                                <div class="input-error">{{ $errors->first('company_phone') }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="parser">Company CVR</label>
                                                    <input type="text" class="form-control" id="company_cvr" name="company_cvr" value="{{old('company_cvr',isset($parser_parameters['company_cvr'])? $parser_parameters['company_cvr'] : '')}}" placeholder="company cvr">
                                                </div>
                                                <div class="input-error">{{ $errors->first('company_cvr') }}</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="parser">Company Address</label>
                                                    <input type="text" class="form-control" id="company_address" name="company_address" value="{{old('company_address',isset($parser_parameters['company_address'])? $parser_parameters['company_address'] : '')}}" placeholder="company address">
                                                </div>
                                                <div class="input-error">{{ $errors->first('company_address') }}</div>
                                            </div>
                                        </div>
                                    <div class="row mb-3 rowBorderBottom">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="parser">Product Information</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="parser">Products Start</label>
                                                <input type="text" class="form-control" id="products_start" name="products_start" value="{{old('products_start',isset($parser_parameters['products_start'])? $parser_parameters['products_start'] : '')}}" placeholder="products start">
                                            </div>
                                            <div class="input-error">{{ $errors->first('products_start') }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="parser">Products End</label>
                                                <input type="text" class="form-control" id="products_end" name="products_end" value="{{old('products_end',isset($parser_parameters['products_end'])? $parser_parameters['products_end'] : '')}}" placeholder="products end">

                                            </div>
                                            <div class="input-error">{{ $errors->first('products_end') }}</div>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="parser">Product Row Length</label>
                                                <input type="text" class="form-control" id="product_row_length" name="product_row_length" value="{{old('product_row_length',isset($parser_parameters['product_row_length'])? $parser_parameters['product_row_length'] : '')}}" placeholder="products row length">
                                            </div>
                                            <div class="input-error">{{ $errors->first('product_row_length') }}</div>
                                        </div>

                                        <div class="col-8">
                                            <div class="form-group">
                                                <label for="parser">Products Columns</label>
                                                <input type="text" class="form-control" id="products_columns" name="products_columns" value="{{old('products_columns',isset($parser_parameters['products_columns'])? json_encode($parser_parameters['products_columns']): '')}}" placeholder='{"name":1,"price":2,"product_total":3}'>
                                            </div>
                                            <div class="input-error">{{ $errors->first('products_Columns') }}</div>
                                        </div>
                                    </div>
                                </div>


							</div>
						</div>

						<div class="float-right" style="padding: 20px;">
						  	<button type="submit" class="btn btn-primary">Update Vendor</button>
						</div>
				  </form>

				</div>
		  	</div>
		</div>
	  </div>

    <!-- Modal -->
    <div class="modal fade" id="parserModal" tabindex="-1" role="dialog" aria-labelledby="parserModallabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Select File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form enctype="multipart/form-data" id="parseFileForm">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-sm-12">

                                <div class="form-group">
                                    <label for="name">File</label>
                                    <input type="hidden" class="form-control" id="file_type" name="file_type" value="pdf" >
                                    <input type="file" class="form-control" id="file" value="{{old('file')}}" name="file" accept=".xlsx,.xls,.doc, .docx,.pdf,.PDF" >
                                </div>
                                <div class="input-error">{{ $errors->first('name') }}</div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="parseFileButton" class="btn btn-primary">Generate</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>


@endsection
@push('script')
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery(document).on('submit', '#parseFileForm', function (e) {

                e.preventDefault();

                jQuery('#parseFileButton').prop( "disabled", true );
                jQuery('.iframe-file').attr('src', '');

                let file = '{{ asset(':file') }}';
                let html_file = '{{ asset(':html_file') }}';
                var route = '{{ route("parseFileForm") }}';
                jQuery.ajax
                ({
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    url: route,
                    dataType: "JSON",
                    data: new FormData(this),
                    success: function (response) {
                        console.log(response.data);

                        file = file.replace(':file', response.data['filePath']);
                        jQuery('.iframe-file').attr('src', file);
                        html_file = response.data['htmlData'];
                        html_file = html_file.split('\n').map( (line,index) => index + " " + line ).join('\n')
                        jQuery('.paracode').text( html_file);

                        jQuery("#fileBox").show();
                        jQuery('#parserModal').modal('hide');

                    },
                    error: function (err) {
                        let error = JSON.parse(err.responseText);
                        // toastr.error(error.message);
                    }
                });
                //route = route.replace(model_id, ':model_id');
                jQuery('#parseFileButton').prop( "disabled", false );
            });
        });

    </script>
@endpush
