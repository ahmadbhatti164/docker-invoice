@extends('layout.admin.layout')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Update Category</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible" data-auto-dismiss role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Success! <span style="font-weight: 200;">{{ Session::get('message') }} </span></h4>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Information</h3>
                        </div>

                        <form role="form" action="{{ route('updateCategory', $category->id) }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{old('name', $category->name)}}" placeholder="Enter Name">
                                        </div>
                                        <div class="input-error">{{ $errors->first('name') }}</div>
                                    </div>

                                </div>
                            </div>

                            <div class="float-right" style="padding: 20px;">
                                <button type="submit" class="btn btn-primary">Update Category</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
