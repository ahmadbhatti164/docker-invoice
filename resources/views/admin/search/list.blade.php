@extends('layout.admin.layout')

@section('content')



    <!-- Main content -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Search Results</h3>
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

            <div class="col-9">
                <div class="card">

                    <div class="card-body">
                    <div class="card-body">
                        <div class="sub-card">
                            @if($searchResult)
                            <h5>  <i class="far fa-user"></i>    VENDORS</h5><hr>
                            @foreach($searchResult as $result)
                                @if($result['type'] == 'vendor')
                                        <p><a title="See Detail" class="customA" href={{route("vendorDetail", $result['id'])}}>{{$result['name']}} <span class="searchNameSpan"><i class="fas fa-circle searchDot"></i> Vendor </span></a></p>
                                @endif
                            @endforeach
                            @endif
                        </div>

                        <div class="sub-card">
                            <h5>  <i class="fas fa-list-alt"></i>    INVOICES</h5><hr>
                            @if($searchResult)
                                @foreach($searchResult as $result)
                                    @if($result['type'] == 'invoice')
                                            <p><a title="See Detail" class="customA" href={{route("vendorDetail", $result['id'])}}>{{$result['name']}} <span class="searchNameSpan"><i class="fas fa-circle searchDot"></i> Invoice <i class="fas fa-circle searchDot"></i> {{$result['vendor']}} </span></a></p>
                                        @endif
                                @endforeach
                            @endif
                        </div>

                        <div class="sub-card">
                            <h5>  <i class="fas fa-list-ol"></i>    PRODUCTS</h5><hr>
                            @if($searchResult)
                                @foreach($searchResult as $result)
                                    @if($result['type'] == 'product')
                                            <p><a title="See Detail" class="customA" href={{route("productDetail", $result['id'])}}>{{$result['name']}} <span class="searchNameSpan"><i class="fas fa-circle searchDot"></i> Product <i class="fas fa-circle searchDot"></i> {{$result['vendor']}} </span></a></p>
                                        @endif
                                @endforeach
                            @endif
                        </div>

                    </div>
                    </div>
                </div>
            </div>

        </div>

    </section>

@endsection
