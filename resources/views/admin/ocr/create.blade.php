@extends('layout.admin.layout')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add OCR</h1>
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
{{--                            <img src="{{asset('invoice/ocrs/text.png')}}">--}}
                        </div>
                        <form role="form" action="{{ route('ocr.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="ocr_file">OCR File</label>
                                            <input type="file" class="form-control" id="ocr_file" name="ocr_file" style="border: 0px">
                                        </div>
                                        <div class="input-error">{{ $errors->first('ocr_file') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="float-right" style="padding: 20px;">
                                <button type="submit" class="btn btn-primary">Add OCR</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
