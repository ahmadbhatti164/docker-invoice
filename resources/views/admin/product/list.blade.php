@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>Products List</h1>
		  	</div>
		</div>
	  </div>
</section>

<!-- Main content -->
<section class="content">
	@if(Session::has('message'))
  		<div class="alert alert-success alert-dismissible" data-auto-dismiss role="alert">
		    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		    <h4><i class="icon fa fa-check"></i> Success! <span style="font-weight: 200;">{{ Session::get('message') }} </span></h4>
		</div>
	@endif
	<div class="row">
		<div class="col-12">
		  	<div class="card">
			    <div class="card-body">
			      	<table id="page-table" class="table table-bordered table-striped">
				        <thead>
					        <tr>
                                <th>QUANTITY</th>
					          	<th style="width: 30%;">NAME</th>
					          	<th>CONTENT</th>
					          	<th>CONTENT PRICE</th>
					          	<th>UNIT</th>
					          	<th>UNIT PRICE</th>
					          	<th>TOTAL PRICE</th>
					          	<th>Action</th>
					        </tr>
				        </thead>
			        <tbody>
			      </table>
			    </div>
		  </div>
		</div>
	</div>
</section>
    <style>
        .table td {
            padding: 10px 10px 5px 10px;
            vertical-align: middle;
        }
        #page-table{
            font-size: 13px !important;
        }
    </style>

@endsection


@push('script')

    <script type="text/javascript">
        "use strict";
        // Hide alerts and errors of datatable
        $.fn.dataTable.ext.errMode = 'none';
        var KTDatatablesDataSourceAjaxServer = function() {
            var app_route = '{{ route('productListTable') }}';
            var lenght = 10;

            var initTable1 = function() {
                var table = $('#page-table').DataTable({

                    responsive: true,
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    /*info: true,*/
                    ajax: {
                        url: app_route,
                        type: 'POST',
                        error: function(data){
                            console.log(data);
                        }
                    },
                    columns: [
                        {data: 'qty', orderable: true,searchable: true},
                        {data: 'name', orderable: true,searchable: true},
                        {data: 'content', orderable: true,searchable: true},
                        {data: 'content_price', orderable: true,searchable: true},
                        {data: 'unit', orderable: true,searchable: true},
                        {data: 'price', orderable: true,searchable: true},
                        {data: 'total', orderable: true,searchable: true},
                        {data: 'action', orderable: false,searchable: false},
                    ],

                    order: [[0, "asc"]],
                    pageLength: lenght
                });
            };
            return {
                //main function to initiate the module
                init: function() {
                    initTable1();
                }
            };
        }();

        jQuery(document).ready(function() {
            KTDatatablesDataSourceAjaxServer.init();
        });

        </script>
@endpush
