@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>{{ $vendor->name }}</h1>
		  	</div>
            <div class="col-6 invoice-col">
                @if(auth()->user()->is_admin == 1)
                    <button type="button" data-id="{{$vendor->id}}" class="btn btn-secondary removeVendor float-right"><i class="fas fa-trash-alt"></i></button>
                    <b class="float-right" style="padding-right: 5px;"><a title="Edit" href='{{route("editVendor", $vendor->id)}}'><button type="button" class="btn btn-primary">Edit </button> </a></b>
                @elseif($user)
                    <b class="float-right" style="padding-right: 5px;"><a title="Edit" href='{{route("editVendor", $vendor->id)}}'><button type="button" class="btn btn-primary">Edit </button> </a></b>
                @endif
            </div>
		</div>
	  </div>
</section>

<section class="content">
	<div class="container-fluid">
		<div class="row">
		  	<div class="col-5">
			    <!-- Main content -->
                <div class="col-12">
                    <div class="row">
                        <div class="col-3">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <p class="mb-10">DUE DATES</p>
                                    <div class="row">
                                        <div class="col-12">
                                            <h3><b><span class="progress-description">{{$invoiceDueDateCount}}</span></b></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <p class="mb-10">UPCOMING EXPENSES</p>
                                    <div class="row">
                                        <div class="col-12">
                                            <h3><b><span class="progress-description">{{number_format($upcomingExpenses,2)}}</span></b></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <p class="mb-10">TOTAL EXPENSES</p>
                                    <div class="row">
                                        <div class="col-12">
                                            <h3><b><span class="progress-description">{{number_format($totalExpense,2)}}</span></b></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4>Top Product Expenses</h4><hr>
                            @if($topProducts)
                                @foreach($topProducts as $product)
                                    @php
                                        $max = $product['total'];
                                        $percentage = number_format(($product['sub_total']/ $max) * 100); @endphp
                                    <span class="skill">{{$product['name']}} <i
                                            class="val float-right">{{\Illuminate\Support\Facades\Config::get('invoice.currency_symbol')}} {{number_format($product['sub_total'])}} of {{\Illuminate\Support\Facades\Config::get('invoice.currency_symbol')}} {{number_format($max)}}</i></span>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: {{$percentage}}%"
                                             aria-valuenow="{{$percentage}}" aria-valuemin="0"
                                             aria-valuemax="{{$max}}"></div>


                                    </div>
                                @endforeach
                            @endif

                        </div>
                    </div>
                </div>
		  	</div>
            <div class="col-7">
                <div class="card">

                    <div class="card-body">
                        <div class="col-sm-4 p-0">
                            <div >
                                <h3>Invoice List</h3>
                            </div>
                        </div>
                        <!--                        <div class="col-sm-4 p-0">
                                                    <div id="report-range" class="report-range">
                                                        <i class="fa fa-calendar"></i>&nbsp;
                                                        <span></span> <i class="fa fa-caret-down"></i>
                                                    </div>
                                                </div>-->
                        <table id="page-table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>INVOICE NO.</th>
                                <th>SUB-TOTAL</th>
                                <th>VAT</th>
                                <th>TOTAL COST</th>
                                <th>CREATED</th>
                                <th>DATE</th>
                            </tr>
                            </thead>
                            <tbody>
                        </table>
                    </div>
                </div>
            </div>
		</div>
	</div>
</section>
<style>
    .skill {
        font: normal 14px "Open Sans Web";
        line-height: 35px;
        padding: 0;
        margin: 0 0 0 0px;

    }

    .skill .val {
        float: right;
        font-style: normal;
        margin: 0 20px 0 0;
    }
</style>

@endsection

@push('script')

    <script type="text/javascript">

        jQuery(document).ready(function () {
            initTable1(null, null);
        });

        function initTable1(start_date, end_date) {
            var app_route = '{{ route('invoiceListTable') }}';
            var lenght = 10;
            var table = $('#page-table').DataTable({

                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                /*info: true,*/
                ajax: {
                    url: app_route,
                    type: 'POST',
                    data: {_token: "{{csrf_token()}}", "start_date": start_date, "end_date": end_date,"vendor_id":{{$vendor->id}} },
                    error: function(data){
                        console.log(data);
                    }
                },
                columns: [
                    {data: 'invoice_number', orderable: true},
                    {data: 'sub_total', orderable: true,searchable: false},
                    {data: 'vat', orderable: true,searchable: false},
                    {data: 'grand_total', orderable: true,searchable: false},
                    {data: 'created_at', orderable: true,searchable: false},
                    {data: 'invoice_date', orderable: true,searchable: false},
                ],

                order: [[0, "asc"]],
                pageLength: lenght
            });
        }


        var ref = null;
    jQuery("body").on('click', '.removeVendor', function (e) {

    e.preventDefault();
    var $this = this;
    ref = this;
    var id = jQuery($this).attr('data-id');

    var deleted = confirm("Are you sure you want to delete!");
        if (deleted) {

            var delete_url = '{{route('removeVendor', ':id')}}';
            delete_url = delete_url.replace(':id', id);

            jQuery.ajax
            ({
                type: 'DELETE',
                url: delete_url,
                success: function (response, textStatus) {
                    window.location.href = "{{URL::to('vendor/list')}}"
                },
                error: function (err) {
                    let error = JSON.parse(err.responseText);
                }
            });
        }
    });
    </script>

@endpush
