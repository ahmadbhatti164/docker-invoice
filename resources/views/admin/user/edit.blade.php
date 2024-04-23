@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>Update {{$user->name}}</h1>
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

				  	<form role="form" action="{{ route('updateUser', $user->id) }}" method="POST">
				  		@csrf
						<div class="card-body">
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="name">Name</label>
										<input type="text" class="form-control" id="name" name="name" value="{{old('name', $user->name)}}" placeholder="Enter Name">
									</div>
        							<div class="input-error">{{ $errors->first('name') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="phone_no">Phone No</label>
										<input type="phone_no" class="form-control" id="phone_no" name="phone_no" value="{{old('phone_no', $user->phone_no)}}" placeholder="Phone No">
									</div>
        							<div class="input-error">{{ $errors->first('phone_no') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="email">Email</label>
										<input type="text" class="form-control" id="email" name="email" value="{{old('email', $user->email)}}" placeholder="Enter Email">
									</div>
        							<div class="input-error">{{ $errors->first('email') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="country">Country</label>
										<input type="country" class="form-control" id="country" name="country" value="{{old('country', $user->country)}}" placeholder="Country">
									</div>
        							<div class="input-error">{{ $errors->first('country') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="state">State</label>
										<input type="state" class="form-control" id="state" name="state" value="{{old('state', $user->state)}}" placeholder="State">
									</div>
        							<div class="input-error">{{ $errors->first('state') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="city">City</label>
										<input type="city" class="form-control" id="city" name="city" value="{{old('city', $user->city)}}" placeholder="City">
									</div>
        							<div class="input-error">{{ $errors->first('city') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="status">Status</label>
										<select class="form-control" id="status" name="status">
						                    <option value="1" {{ old('status', $user->status) == '1' ? 'selected' : '' }}>Active</option>
						                    <option value="0" {{ old('status', $user->status) == '0' ? 'selected' : '' }}>Inactive</option>
						                </select>
									</div>
        							<div class="input-error">{{ $errors->first('status') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="is_admin">User Type</label>
										<select class="form-control" id="is_admin" name="is_admin">
											<option value="">Select User Type</option>
											<option value="1" {{old('is_admin', $user->is_admin) == 1 ? 'selected' : ''}}>Admin</option>
											<option value="0" {{old('is_admin', $user->is_admin) == 0 ? 'selected' : ''}}>User</option>
										</select>
									</div>
        							<div class="input-error">{{ $errors->first('is_admin') }}</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label for="address">Address</label>
										<textarea class="form-control" rows="3" id="address" name="address" placeholder="Address...">{{ $user->address }}</textarea>
									</div>
        							<div class="input-error">{{ $errors->first('address') }}</div>
								</div>
							</div>
						</div>

						<div class="float-right" style="padding: 20px;">
						  	<button type="submit" class="btn btn-primary">Update User</button>
						</div>
				  </form>

				</div>
		  	</div>
		</div>
	  </div>
</section>


@endsection
