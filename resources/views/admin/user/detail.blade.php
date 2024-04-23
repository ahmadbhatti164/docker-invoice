@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>{{$user->name}} Detail</h1>
		  	</div>
		</div>
	  </div>
</section>

<section class="content">
	<div class="container-fluid">
		<div class="row">
		  	<div class="col-12">
			    <!-- Main content -->
			    <div class="invoice p-3 mb-3">
			      	<div class="row invoice-info">
				        <div class="col-sm-6 invoice-col">
					        <b>Name:</b> {{ $user->name }} <br>
					        <b>Phone No:</b> {{ $user->phone_no }} <br>
					        <b>Email:</b> {{ $user->email }} <br>
					        <b>User Type:</b> {{ $user->is_admin == 1 ? 'Admin' : 'User' }} <br>
				        </div>
				        <div class="col-sm-6 invoice-col">
					        <b>Country:</b> {{ $user->country }} <br>
					        <b>State:</b> {{ $user->state }} <br>
					        <b>City:</b> {{ $user->city }} <br>
				        </div>
				    </div><br><br>

				    <div class="col-sm-6 invoice-col">
				        <b>Invoice List</b><br>
				        <br>
			        </div>
				    <div class="row">
		                <div class="col-12 table-responsive">
                            <table id="page-table" class="table table-bordered table-striped">
		                    	<thead>
				                    <tr>
				                      <th width="5%"><b>Sr</b></th>
				                      <th width="10%"><b>Invoice Number</b></th>
				                      <th width="10%"><b>Invoice Date</b></th>
				                      <th width="10%"><b>Total</b></th>
				                      <th width="10%"><b>Vat</b></th>
				                      <th width="10%"><b>Grand Total</b></th>
				                    </tr>
		                    	</thead>
			                    <tbody>
			                    	@if(count($user->invoices) > 0)
				                    	@foreach($user->invoices as $k => $inv)
					                    <tr>
						                    <td>{{ $k+1 }}</td>
						                    <td> <a href="{{ route('invoiceDetail', $inv->id) }}">{{ $inv->invoice_number }}</a> </td>
						                    <td>{{ $inv->invoice_date }}</td>
						                    <td>{{ $inv->total }}</td>
						                    <td>{{ $inv->vat }}</td>
						                    <td>{{ $inv->grand_total }}</td>
					                    </tr>
				                    	@endforeach
			                    	@endif
			                    </tbody>
		                 	</table>
		                </div>
		            </div>
				</div>
		  	</div>
		</div>
	</div>
</section>


@endsection
