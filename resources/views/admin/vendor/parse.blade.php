@extends('layout.admin.layout')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Parser PDF</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Main content -->
                    <div class="invoice p-3 mb-3">
                        <div class="row invoice-info">
                            <div class="col-sm-6 invoice-col">
                                <form enctype="multipart/form-data" id="parsePdfForm">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="name">Name</label>
                                                    <input type="hidden" class="form-control" id="file_type" name="file_type" value="pdf" >
                                                    <input type="file" class="form-control" id="pdf_file" name="pdf_file" value="{{old('pdf_file')}}" accept=".pdf">
                                                </div>
                                                <div class="input-error">{{ $errors->first('name') }}</div>
                                            </div>
                                            <div class="col-sm-12">
                                                <hr>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="float-right" style="padding: 20px;">
                                        <button type="submit" class="btn btn-primary">Get Pdf File</button>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 card-body">
                                            <span id="output">

                                                 <a class="html_file customA" href="{{ asset('invoice\pdf\asd.pdf') }}" download ><i class="fab fa-html5 ">HTML </i> <span class="htmlspan"></span></a><br>
                                                <a class="pdf_file customA" href="{{ asset('invoice\pdf\asd.pdf') }}" download > <i class="fas fa-file-pdf ">PDF </i> <span class="pdfspan"></span></a>
                                            </span>
                                        </div>
                                    </div>
                                </form>
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
        jQuery(document).ready(function () {
            jQuery(document).on('submit', '#parsePdfForm', function (e) {

                e.preventDefault();
                let $serializeData = new FormData(this);
                let pdf_file = '{{ asset(':pdf_file') }}';
                let html_file = '{{ asset(':html_file') }}';
                var route = '{{ route("parseFileForm") }}';
                jQuery.ajax
                ({
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    url: route,
                    data: $serializeData,
                    success: function (response) {

                        pdf_file = pdf_file.replace(':pdf_file', response.data['pdfPath']);
                        html_file = html_file.replace(':html_file', response.data['htmlPath']);

                        jQuery(".html_file").attr("href", html_file);
                        jQuery(".htmlspan").html(response.data['htmlPath']);
                        jQuery(".pdf_file").attr("href", pdf_file);
                        jQuery(".pdfspan").html(response.data['pdfPath']);
                    },
                    error: function (err) {
                        let error = JSON.parse(err.responseText);
                       // toastr.error(error.message);
                    }
                });
                //route = route.replace(model_id, ':model_id');

            });
            });

    </script>
@endpush
