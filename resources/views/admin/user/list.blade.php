@extends('layout.admin.layout')

@section('content')

<section class="content-header">
  	<div class="container-fluid">
		<div class="row mb-2">
		  	<div class="col-sm-6">
				<h1>Users List</h1>
		  	</div>
		</div>
	  </div>
</section>

<!-- Main content -->
<section class="content">
	@if(Session::has('message'))
  		<div class="alert alert-success alert-dismissible" data-auto-dismiss role="alert">
		    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		    <h4><i class="icon fa fa-check"></i> Success! <span style="font-weight: 200;">{{ Session::get('message') }} </span></h4>
		</div>
	@endif
	<div class="row">
		<div class="col-12">
		  	<div class="card">
			    <div class="card-body">
			      	<table id="page-table" class="table table-bordered table-striped">
				        <thead>
					        <tr>
					          	<th>Name</th>
					          	<th>Phone No</th>
					          	<th>Email</th>
					          	<th>Country</th>
					          	<th>State</th>
					          	<th>City</th>
					          	<th>Address</th>
					          	<th style="width: 205px;">Action</th>
					        </tr>
				        </thead>
			        <tbody>
			      </table>
			    </div>
		  </div>
		</div>
	</div>
</section>

@endsection

@push('script')

<script type="text/javascript">
	$(document).ready(function() {

		$('#page-table').DataTable({
	        "serverSide": true,
	        "processing": true,
	        "columnDefs": [
	    	{ orderable: false, targets: [-1] },
	    	{ className: "text-center", "targets":  -1  }
		    ],
	     	"oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                // "sInfo": "Showing page _PAGE_ of _PsAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
               "sLengthMenu": "Results :  _MENU_",
            },
            "stripeClasses": [],
            "lengthMenu": [10, 20, 30, 50],
            "pageLength": 10,
            "fixedHeader": {
                "header":true,
            },

	        "ajax": {
	        	"url": "{{ route('userListTable') }}",
	        	"dataType": "json",
	        	"type": "POST",
	        	"data":{ _token: "{{csrf_token()}}"}
	        },
	        "columns": [
                { "data": "name" },
                { "data": "phone_no" },
                { "data": "email" },
                { "data": "country" },
                { "data": "state" },
                { "data": "city" },
                { "data": "address" },
                { "data": "action" },
            ],
	    });
	});

    var ref = null;
    $("body").on('click', '.removeUser', function (e) {

        e.preventDefault();
        var $this = this;
        ref = this;
        var id = jQuery($this).attr('data-id');

        var deleted = confirm("Are you sure you want to delete!");
        if (deleted) {

            var delete_url = '{{route('removeUser', ':id')}}';
            delete_url = delete_url.replace(':id', id);

            jQuery.ajax
            ({
                type: 'DELETE',
                url: delete_url,
                success: function (response, textStatus) {
                    jQuery(ref).parent('td').parent('tr').remove();
                },
                error: function (err) {
                    let error = JSON.parse(err.responseText);
                }
            });
        }
    });
</script>

@endpush
