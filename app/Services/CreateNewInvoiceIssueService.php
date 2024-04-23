<?php


namespace App\Services;


use App\Models\InvoiceParsingIssue;

class CreateNewInvoiceIssueService {


    public function __construct( ) {
    }

    public function create($data)
    {
       return InvoiceParsingIssue::create($data);
    }
}
