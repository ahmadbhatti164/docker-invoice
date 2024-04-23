@extends('layout.admin.layout')

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{$company->name}}</h1>
                </div>
                <div class="col-6 invoice-col">
                    @if(auth()->user()->is_admin == 1 || $user)
                        <button type="button" data-id="{{$company->id}}" class="btn btn-secondary removeCompany float-right"><i class="fas fa-trash-alt"></i></button>
                    @endif
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
                            <h3 class="card-title">Basic Information</h3>
                        </div>

                        <form role="form" action="{{ route('updateCompany', $company->id) }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Company Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{old('name', $company->name)}}" placeholder="Enter Company Name">
                                        </div>
                                        <div class="input-error">{{ $errors->first('name') }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" id="email" name="email" value="{{old('email', $company->email)}}" placeholder="Enter Email" disabled>
                                        </div>
                                        <div class="input-error">{{ $errors->first('email') }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="phone_no">Phone</label>
                                            <input type="text" class="form-control" id="phone_no" name="phone_no" value="{{old('phone_no', $company->phone_no)}}" placeholder="Enter Phone">
                                        </div>
                                        <div class="input-error">{{ $errors->first('phone_no') }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="text" class="form-control" id="address" name="address" value="{{old('address', $company->address)}}" placeholder="Enter Address">
                                        </div>
                                        <div class="input-error">{{ $errors->first('address') }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="parser">CVR number</label>
                                            <input type="cvr_number" class="form-control" id="cvr_number" name="cvr_number" value="{{old('address', $company->cvr_number)}}" placeholder="CVR number">
                                        </div>
                                        <div class="input-error"> {{$errors->first('cvr_number') }} </div>
                                    </div>


                                </div>
                            </div>

                            <div class="float-right" style="padding: 20px;">
                                <button type="submit" class="btn btn-primary">Update Company</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </section>


@endsection
@push('script')
    <script type="text/javascript">
        jQuery(document).ready(function () {

            var ref = null;
            jQuery("body").on('click', '.removeCompany', function (e) {

                e.preventDefault();
                var $this = this;
                ref = this;
                var id = jQuery($this).attr('data-id');

                var deleted = confirm("Are you sure you want to delete!");
                if (deleted) {

                    var delete_url = '{{route('removeCompany', ':id')}}';
                    delete_url = delete_url.replace(':id', id);

                    jQuery.ajax
                    ({
                        type: 'DELETE',
                        url: delete_url,
                        success: function (response, textStatus) {
                            window.location.href = "{{URL::to('company/list')}}"
                        },
                        error: function (err) {
                            let error = JSON.parse(err.responseText);
                        }
                    });
                }
            });
        });

    </script>
@endpush
