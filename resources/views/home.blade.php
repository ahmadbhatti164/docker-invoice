@extends('layout.admin.layout')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>

                </div>
            </div><!-- /.container-fluid -->
        </div>
    </section>
    <section class="content">
        @if(Session::has('message'))
            <div class="alert alert-success alert-dismissible" data-auto-dismiss role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i> Success! <span
                        style="font-weight: 200;">{{ Session::get('message') }} </span></h4>
            </div>
        @endif
        <div class="row">
            <div class="col-4">
                <div class="info-box">
                    <div class="info-box-content">
                        <b><span class="info-box-text">Weekly Expenses</span></b>
                        <span class="info-box-text">Week {{$totalExpense['week_number']}}</span>
                        <div class="row">
                            <div class="col-6">
                                 <span
                                     class="info-box-number">{{\Illuminate\Support\Facades\Config::get('invoice.currency_symbol')}} {{$totalExpense['week']}}</span>

                                <span class="progress-description">
                                {{\Illuminate\Support\Facades\Config::get('invoice.currency_symbol')}} {{$totalExpense['week_vat']}} VAT
                             </span>
                            </div>
                            <div class="col-6">
                                <i class="fas fa-3x fa-chart-bar float-right" style="color: #6b747c;cursor:pointer;" data-toggle="modal" data-target="#weekly_expense_modal"></i>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box ">
                    <div class="info-box-content">
                        <b><span class="info-box-text">Monthly Expenses</span></b>
                        <span class="info-box-text">{{$totalExpense['month_number']}}</span>
                        <div class="row">
                            <div class="col-6">
                        <span
                            class="info-box-number">{{\Illuminate\Support\Facades\Config::get('invoice.currency_symbol')}} {{$totalExpense['month']}}</span>
                        <span class="progress-description">
                           {{\Illuminate\Support\Facades\Config::get('invoice.currency_symbol')}}  {{$totalExpense['month_vat']}} VAT
                         </span>
                            </div>
                            <div class="col-6">
                                <i class="fas fa-3x fa-chart-bar float-right" style="color: #6b747c;cursor:pointer;" data-toggle="modal" data-target="#monthly_expense_modal"></i>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="info-box ">
                    <div class="info-box-content">
                        <b><span class="info-box-text">Yearly Expenses</span></b>
                        <span class="info-box-text">{{$totalExpense['year_number']}}</span>

                        <div class="row">
                            <div class="col-6">
                                <span
                            class="info-box-number">{{\Illuminate\Support\Facades\Config::get('invoice.currency_symbol')}} {{$totalExpense['year']}}</span>
                        <span class="progress-description">
                            {{\Illuminate\Support\Facades\Config::get('invoice.currency_symbol')}} {{$totalExpense['year_vat']}} VAT
                         </span>
                            </div>
                            <div class="col-6">
                                <i class="fas fa-3x fa-chart-bar float-right" style="color: #6b747c;cursor:pointer;" data-toggle="modal" data-target="#yearly_expense_modal"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <label>Upcoming Due Dates</label><hr>
                        <table id="page-table" class="table borderless">

                            @if($invoiceDueDate)
                                @foreach($invoiceDueDate as $invoice)
                                    <tr>
                                        <td class="">{{$invoice['vendor_name']}}</td>
                                        <td class="">{{$invoice['date']}}</td>
                                        <td class="float-right">{{\Illuminate\Support\Facades\Config::get('invoice.currency_symbol')}} {{ $invoice['grand_total']}}</td>
                                    </tr>
                                @endforeach
                            @endif
                            <tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <label>Biggest Product Expenses</label><hr>
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
                        @else
                            <label>No Products Found</label>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </section>


    <!-- Modal -->
    <div class="modal fade" id="weekly_expense_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 1500px;margin: 50px auto;">
            <div class="modal-content">
                <div class="modal-body">
                        <div id="weekly_expense_div">

                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="monthly_expense_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 1500px;margin: 50px auto;">
            <div class="modal-content">
                <div class="modal-body">
                        <div id="monthly_expense_div">

                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="yearly_expense_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 1500px;margin: 50px auto;">
            <div class="modal-content" >
                <div class="modal-body">
                        <div id="yearly_expense_div">

                        </div>
                </div>
            </div>
        </div>
    </div>
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

