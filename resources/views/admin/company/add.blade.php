@extends('layout.admin.layout')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Company</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Basic Information</h3>
                        </div>

                        <form role="form" action="{{ route('companyForm') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Company Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}" placeholder="Enter Company Name">
                                        </div>
                                        <div class="input-error">{{ $errors->first('name') }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Enter Email">
                                        </div>
                                        <div class="input-error">{{ $errors->first('email') }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="phone_no">Phone</label>
                                            <input type="text" class="form-control" id="phone_no" name="phone_no" value="{{old('phone_no')}}" placeholder="Enter Phone">
                                        </div>
                                        <div class="input-error">{{ $errors->first('phone_no') }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="text" class="form-control" id="address" name="address" value="{{old('address')}}" placeholder="Enter Address">
                                        </div>
                                        <div class="input-error">{{ $errors->first('address') }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="parser">CVR number</label>
                                            <input type="cvr_number" class="form-control" id="cvr_number" name="cvr_number" value="{{old('cvr_number')}}" placeholder="CVR number">
                                        </div>
                                        <div class="input-error"> {{$errors->first('cvr_number') }} </div>
                                    </div>


                                </div>
                            </div>

                            <div class="float-right" style="padding: 20px;">
                                <button type="submit" class="btn btn-primary">Add Company</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </section>


@endsection
