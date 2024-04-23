@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>Invoice Detail</h1>
		  	</div>
		</div>
	  </div>
</section>

<section class="content">
	<div class="container-fluid">
		<div class="row">
		  	<div class="col-12">
			    <!-- Main content -->
                <div id="invoice">

                    <div class="row">
                        <div class="col-4">
                            <h3>{{ $invoice->invoice_number }}</h3>
                        </div>
                        <div class="col-8">
                            <a title="Edit" href={{route("editInvoice", $invoice->id)}}><button type="button" class="btn btn-default float-right"><i class="fas fa-pen"></i> Edit </button></a>
                        </div>
                    </div>
                    <div class="invoice overflow-auto">
                        <div style="min-width: 600px">
                            <header>
                                <div class="row">
                                    <div class="col-8">
                                        <div class="address">
                                        @if($invoice->company)

                                            <h3 class="to">{{$invoice->company->name}}</h3>
                                            <span class="to">{{$invoice->company->address}}</span><br>
                                            <span class="to"> {{ ($invoice->company->cvr_number)? 'CVR. '.$invoice->company->cvr_number: '' }}</span>
                                            <p class="to">{{ ($invoice->company->phone_no)? 'Phone. '.$invoice->company->phone_no: '' }}</p>
                                        @else
                                            <h3 class="to">{{ ($invoice->user->name)? $invoice->user->name: '' }}</h3>
                                        @endif
                                                {{ $invoice->billing_address }}
                                            </div>
                                        <div class="div-download hidden-print">
                                            <a class="customA" href="#"><i class="fas fa-print fa-2x" id="printInvoice"></i></a>
                                            <a class="customA" href="{{ asset($invoice->html_file) }}" download ><i class="fab fa-html5 fa-2x"></i></a>
                                            <a class="customA" href="{{ asset($invoice->pdf_file) }}" download > <i class="fas fa-file-pdf fa-2x"></i></a>
                                        </div>
                                        <!--                            <button class="btn btn-info"><i class="fa fa-file-pdf-o"></i> Export as PDF</button>-->
<!--                                        <a target="_blank" href="https://lobianijs.com">
                                            <img src="http://lobianijs.com/lobiadmin/version/1.0/ajax/img/logo/lobiadmin-logo-text-64.png" data-holder-rendered="true" />
                                        </a>-->
                                    </div>
                                    <div class="col-4 company-details">
                                        <div class="row">
                                            <div class="col-12">
                                                <table border="0" cellspacing="0" cellpadding="0" style=" width: 295px;float: right;text-align: left">
                                                    <thead>
                                                    <tr>
                                                        <td colspan="2" class="text-center bk-white">  <h2 class="name">
                                                                <a target="_blank"  href="{{ route('vendorDetail', $invoice->vendor->id) }}">{{ $invoice->vendor->name }}</a>
                                                            </h2>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="text-center bk-white"> {{ $invoice->vendor->address }}</td>

                                                    </tr>
                                                    <tr>
                                                        <td> <b>Phone:</b></td>
                                                        <td class="text-left"> {{ $invoice->vendor->phone_no }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td> <b>CVR:</b></td>
                                                        <td class="text-left"> {{ $invoice->vendor->cvr_number }}</td>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <div class="col-12">
                                                <table border="0" cellspacing="0" cellpadding="0" style=" width: 295px;float: right;text-align: left">
                                                    <thead>

                                                    <tr>
                                                        <td>Invoice no :</td>
                                                        <td class="text-left"> {{ $invoice->invoice_number }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td> Invoice Date:</td>
                                                        <td class="text-left"> {{ $invoice->invoice_date }}</td>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </header>
                            <main>

                                <table border="0" cellspacing="0" cellpadding="0">
                                    <thead>
                                    <tr>
                                        <th class="text-left"><b>QUANTITY</b></th>
                                        <th class="text-left" style="width: 40%;"><b>PRODUCT</b></th>
                                        <th class="text-left"><b>CONTENT</b></th>
                                        <th class="text-left"><b>CONTENT PRICE</b></th>
                                        <th class="text-right"><b>TOTAL PRICE</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($invoice->products) > 0)
                                        @foreach($invoice->products as $k => $prod)
                                            <tr>
                                                <td class="text-left qty unit ">{{ $prod['pivot']['qty'] }}</td>
                                                <td class="text-left unit"><a href="{{ route('productDetail', $prod->id) }}">{{ $prod->name }}</a></td>
                                                <td class="unit text-left">{{ $prod->content }}</td>
                                                <td class="unit text-left">{{ $prod->content_price }}</td>
                                                <td class="total ">{{ number_format($prod['pivot']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    </tbody>
                                </table>

                                <hr>
                                <div class="row">
                                    <div class="col">
                                <table border="0" cellspacing="0" cellpadding="0" style=" width: 295px;float: right;text-align: left">
                                    <tbody>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td >SUBTOTAL:</td>
                                        <td>{{ number_format($invoice->sub_total,2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td >TOTAL:</td>
                                        <td>{{ number_format($invoice->total,2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td >DISCOUNT:</td>
                                        <td>{{ number_format($invoice->discount,2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td >TAX {{ round(($invoice->vat / $invoice->total) * 100, 2) }} %:</td>
                                        <td>{{ number_format($invoice->vat, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td ><b>TOTAL {{ $invoice->currency->name }}:</b></td>
                                        <td><b> {{ number_format($invoice->grand_total,2) }}</b></td>
                                    </tr>

                                    </tbody>
                                </table>
                                </div>
                                </div>
                            </main>

                            <footer>
                                Invoice was created on a computer and is valid without the signature and seal.
                            </footer>
                        </div>
                        <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
                        <div>
                        </div>
                    </div>
                </div>

		  	</div>
		</div>
	</div>
</section>
    <style>
        .address{
            width: 25%;
            height: 210px;
        }
    </style>


@endsection
