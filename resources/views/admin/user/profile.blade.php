@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>Profile</h1>
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
				<form role="form" action="{{ route('updateProfile') }}" method="POST">
				@csrf
					<div class="card card-primary">
					  	<div class="card-header">
							<h3 class="card-title">Basic Information</h3>
					  	</div>
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
								<div class="col-sm-6"></div>
								<div class="col-sm-12">
									<div class="form-group">
										<label for="address">Address</label>
										<textarea class="form-control" rows="3" id="address" name="address" placeholder="Address...">{{ $user->address }}</textarea>
									</div>
	    							<div class="input-error">{{ $errors->first('address') }}</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card card-primary">
					  	<div class="card-header">
							<h3 class="card-title">Change Password</h3>
					  	</div>
						<div class="card-body">
							<div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="current_password">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" value="{{old('current_password')}}" placeholder="Current Password">
                                    </div>
                                    <p class="input-error">{{ $errors->first('current_password') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" value="{{old('new_password')}}" placeholder="New Password | e.g password123">
                                    </div>
                                    <p class="input-error">{{ $errors->first('new_password') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirm_new_password">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" value="{{old('confirm_new_password')}}" placeholder="Confirm New Password">
                                    </div>
                                    <p class="input-error">{{ $errors->first('confirm_new_password') }}</p>
                                </div>
							</div>
						</div>
					</div>

					<div class="float-right" style="padding: 10px;">
					  	<button type="submit" class="btn btn-primary">Update Profile</button>
					</div>
				</form>
		  	</div>
		</div>
	</div>
</section>


@endsection
