@extends('layout.admin.layout')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Companies</h1>
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
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <table id="page-table" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Invoices</th>
                                <th>Sub Total</th>
                                <th>Vat</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('script')

    <script type="text/javascript">
        "use strict";
        // Hide alerts and errors of datatable
        $.fn.dataTable.ext.errMode = 'none';
        var KTDatatablesDataSourceAjaxServer = function() {
            var app_route = '{{ route('companyListTable') }}';
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
                        {data: 'name', orderable: true,searchable: true},
                        {data: 'invoices_count', orderable: true,searchable: true},
                        {data: 'sub_total', orderable: true,searchable: true},
                        {data: 'vat', orderable: true,searchable: true},
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

        /* $(function () {

             var table = $('.data-table').DataTable({
                 processing: true,
                 serverSide: true,
                 ajax: "{{ route('vendorListTable') }}",
            type: 'POST',
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

    });*/
        /*$(document).ready(function() {

            $('#page-table').DataTable({
                "serverSide": true,
                "processing": true,
                "columnDefs": [
                { orderable: false, targets: [-1] },
                { className: "text-center", "targets":  -1  }
                ],
                 "oLanguage": {
                    "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                    // "sInfo": "Showing page _PAGE_ of _PsAGES_",
                    "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    "sSearchPlaceholder": "Search...",
                   "sLengthMenu": "Results :  _MENU_",
                },
                "stripeClasses": [],
                "lengthMenu": [10, 20, 30, 50],
                "pageLength": 10,
                "fixedHeader": {
                    "header":true,
                },

                "ajax": {
                    "url": "{{ route('vendorListTable') }}",
	        	"dataType": "json",
	        	"type": "POST",
	        	"data":{ _token: "{{csrf_token()}}"}
	        },
	        "columns": [
                { "data": "name" },
                { "data": "invoices_count" },
                { "data": "sub_total" },
                { "data": "vat" },
                { "data": "total" },
                { "data": "action" },
            ],
	    });
	});*/
    </script>
@endpush
