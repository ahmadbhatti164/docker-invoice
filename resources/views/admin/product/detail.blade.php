@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>{{ $product->name }}</h1>
		  	</div>
            <div class="col-6 invoice-col">
                @if(auth()->user()->is_admin == 1)
                    <button type="button" data-id="{{$product->id}}" class="btn btn-secondary removeProduct float-right"><i class="fas fa-trash-alt"></i></button>
                    <b class="float-right" style="padding-right: 5px;"><a title="Edit" href='{{route("editProduct", $product->id)}}'><button type="button" class="btn btn-default"><i class="fas fa-pen"></i> Edit </button> </a></b>
                @elseif($user)
                    <b class="float-right" style="padding-right: 5px;"><a title="Edit" href='{{route("editProduct", $product->id)}}'><button type="button" class="btn btn-default"><i class="fas fa-pen"></i> Edit </button> </a></b>
                @endif
            </div>
		</div>
	  </div>
</section>

<section class="content">
	<div class="container-fluid">
		<div class="row">
			    <!-- Main content -->
                <div class="col-8">
                    <div class="row">
                        <div class="col-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <p class="mb-10">Units Bought</p>
                                    <div class="row">
                                        <div class="col-12">
                                            <h3><b><span class="progress-description">{{$unitBought}}</span></b></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <p class="mb-10">Avg. Unit Price</p>
                                    <div class="row">
                                        <div class="col-12">
                                            <h3><b><span class="progress-description">{{ $avgPrice }}</span></b></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <p class="mb-10">TOTAL</p>
                                    <div class="row">
                                        <div class="col-12">
                                            <h3><b><span class="progress-description">{{$total}}</span></b></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="product_price_development"></div>
                </div>
                <div class="col-4">
                    <div class="invoice p-3 mb-3">
                        <div class="row invoice-info">

                            <div class="col-sm-12 invoice-col">
                                <h3>Purchases</h3><br>

                                <b>Upcoming</b><br>
                                <table>
                                    @if(count($next) )
                                        @foreach($next as $item)
                                            <tr><td>{{$item['date']}}</td><td> <span>{{$item['qty']}}</span> </td><td><span class="float-right">{{$item['price']}}</span></td> </tr>
                                        @endforeach
                                    @else
                                        <tr><td>No Upcoming purchases</td></tr>
                                    @endif
                                </table>
                                <br>
                                <br>
                                <b>Previous</b><br>
                                <table>
                                    @if(count($previous) )
                                        @foreach($previous as $item)
                                            <tr><td>{{$item['date']}}</td><td> <span>{{$item['qty']}}</span> </td><td><span class="float-right">{{$item['price']}}</span></td> </tr>
                                        @endforeach
                                    @else
                                        <tr><td>No Upcoming purchases</td></tr>
                                    @endif
                                </table>

                            </div>
                        </div>
                    </div>
                </div>


		</div>
	</div>
</section>


@endsection


@push('script')

    <script type="text/javascript">

        var ref = null;
        jQuery("body").on('click', '.removeProduct', function (e) {

            e.preventDefault();
            var $this = this;
            ref = this;
            var id = jQuery($this).attr('data-id');

            var deleted = confirm("Are you sure you want to delete!");
            if (deleted) {

                var delete_url = '{{route('removeProduct', ':id')}}';
                delete_url = delete_url.replace(':id', id);

                jQuery.ajax
                ({
                    type: 'DELETE',
                    url: delete_url,
                    success: function (response, textStatus) {
                        window.location.href = "{{URL::to('product/list')}}"
                    },
                    error: function (err) {
                        let error = JSON.parse(err.responseText);
                    }
                });
            }
        });
    </script>

@endpush

