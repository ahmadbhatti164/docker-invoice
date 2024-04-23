@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>Add User</h1>
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

				  	<form role="form" action="{{ route('userForm') }}" method="POST">
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
								<div class="col-sm-6">
									<div class="form-group">
										<label for="phone_no">Phone No</label>
										<input type="phone_no" class="form-control" id="phone_no" name="phone_no" value="{{old('phone_no')}}" placeholder="Phone No e.g +125xxxxxx ">
									</div>
        							<div class="input-error">{{ $errors->first('phone_no') }}</div>
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
										<label for="password">Password</label>
										<input type="password" class="form-control" id="password" name="password" value="{{old('password')}}" placeholder="Password">
									</div>
        							<div class="input-error">{{ $errors->first('password') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="country">Country</label>
										<input type="country" class="form-control" id="country" name="country" value="{{old('country')}}" placeholder="Country">
									</div>
        							<div class="input-error">{{ $errors->first('country') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="state">State</label>
										<input type="state" class="form-control" id="state" name="state" value="{{old('state')}}" placeholder="State">
									</div>
        							<div class="input-error">{{ $errors->first('state') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="city">City</label>
										<input type="city" class="form-control" id="city" name="city" value="{{old('city')}}" placeholder="City">
									</div>
        							<div class="input-error">{{ $errors->first('city') }}</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="is_admin">User Type</label>
										<select class="form-control" id="is_admin" name="is_admin">
											<option value="">Select User Type</option>
											<option value="1" {{old('is_admin') == 1 ? 'selected' : ''}}>Admin</option>
											<option value="0" {{old('is_admin') == 0 ? 'selected' : ''}}>User</option>
										</select>
									</div>
        							<div class="input-error">{{ $errors->first('is_admin') }}</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label for="address">Address</label>
										<textarea class="form-control" rows="3" id="address" name="address" placeholder="Address...">{{ old('address') }}</textarea>
									</div>
        							<div class="input-error">{{ $errors->first('address') }}</div>
								</div>
							</div>
						</div>

						<div class="float-right" style="padding: 20px;">
						  	<button type="submit" class="btn btn-primary">Add User</button>
						</div>
				  </form>

				</div>
		  	</div>
		</div>
	  </div>
</section>


@endsection