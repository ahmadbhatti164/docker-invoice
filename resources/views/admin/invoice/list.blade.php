@extends('layout.admin.layout')

@section('content')



    <!-- Main content -->
    <section class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3>Invoices</h3>
                    </div>

                </div>
            </div>
        @if(Session::has('message'))
            <div class="alert alert-success alert-dismissible" data-auto-dismiss role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> Success! <span
                        style="font-weight: 200;">{{ Session::get('message') }} </span></h4>
            </div>
        @endif
    </section>
        <section class="content">
        <div class="row">
                <div class="col-3">

                        <div class="info-box">
                            <span class="round-circle"></span>
                            <div class="info-box-content">
                                <span class="info-box-number">{{ $invoicesCount }}</span>
                                <span class="info-box-text">Invoices</span>
                            </div>
                        </div>
                        <div class="info-box">
                            <span class="round-circle"></span>
                            <div class="info-box-content">

                                <span class="info-box-number">{{ $dueInvoicesCount }}</span>
                                <span class="info-box-text">Upcoming Due Dates</span>
                            </div>
                        </div>
                    <div class="info-box">
                        <span class="round-circle"></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Reminder Invoices</span>
                            <span
                                class="info-box-number">{{ $remainderInvoicesCount }}</span>
                        </div>
                    </div>

                </div>
                <div class="col-9">
                    <div class="row graph-wrap">
                        <div class="col-6">
                            <div id="due_date_div"></div>
                        </div>
                        <div class="col-6">
                            <div id="expense_distribution_div"></div>
                        </div>

                    </div>
                </div>


        </div>
        <div class="row">

            <div class="col-12">

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
                                <th>VENDORS</th>
                                <th>INVOICE NO.</th>
                                <th>SUB-TOTAL</th>
                                <th>VAT</th>
                                <th>TOTAL COST</th>
                                <th>CREATED</th>
                                <th>DUE DATE</th>
                                <th>DAYS LEFT</th>
                                <th>REMINDER</th>
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
                    data: {_token: "{{csrf_token()}}", "start_date": start_date, "end_date": end_date},
                    error: function(data){
                        console.log(data);
                    }
                },
                columns: [
                    {data: 'vendor', orderable: true,searchable: true},
                    {data: 'invoice_number', orderable: true,searchable: true},
                    {data: 'sub_total', orderable: true,searchable: true},
                    {data: 'vat', orderable: true,searchable: false},
                    {data: 'grand_total', orderable: true,searchable: false},
                    {data: 'created_at', orderable: true,searchable: true},
                    {data: 'invoice_date', orderable: true,searchable: true},
                    {data: 'days_left', orderable: true,searchable: true},
                    {data: 'reminder', orderable: true,searchable: true},
                    {data: 'action', orderable: false,searchable: false},
                ],

                order: [[0, "asc"]],
                pageLength: lenght
            });
        }



        jQuery(function () {

            var start = moment().subtract(29, 'days');
            var end = moment();

            function cb(start, end) {
                jQuery('#report-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            jQuery('#report-range').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);
            jQuery('#report-range').on('apply.daterangepicker', function (ev, picker) {
                jQuery(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                jQuery('#page-table').DataTable().destroy();
                initTable1(picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
            });

            jQuery('#report-range').on('cancel.daterangepicker', function (ev, picker) {
                jQuery('#page-table').DataTable().destroy();
                initTable1(null, null);
                // jQuery(this).val('');
            });

        });
    </script>

@endpush
