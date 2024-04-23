@extends('layout.admin.layout')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Category</h1>
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
                            <h3 class="card-title">Information</h3>
                        </div>

                        <form role="form" action="{{ route('categoryForm') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}" placeholder="Enter Name">
                                        </div>
                                        <div class="input-error">{{ $errors->first('name') }}</div>
                                    </div>

                                </div>
                            </div>

                            <div class="float-right" style="padding: 20px;">
                                <button type="submit" class="btn btn-primary">Add Category</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
