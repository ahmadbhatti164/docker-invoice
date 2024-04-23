<?php

namespace App\Console\Commands;

use App\Libraries\ConvertToHtmlFile;
use App\Services\CreateNewInvoiceIssueService;
use Illuminate\Console\Command;
use App\Libraries\ImapEmailFetcher;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Invoice;

class EmailParsingScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:emailparser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will parse all emails and save relevent data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $host = env('IMAP_HOST');
        $username = env('IMAP_USERNAME');
        $password = env('IMAP_PASSWORD');

        $imap = new ImapEmailFetcher($host, $username, $password);
        $emailData = $imap->fetchEmails();
        $successEmails = [];
        $alreadyParsed = [];

        if(!empty($emailData)){
            foreach ($emailData as $key => $value) {
                $invoice = Invoice::where('title', $value['emailSubject'])->where('status', 1)->first();
                if($invoice){
                    array_push($alreadyParsed, $value);
                }else{
                    $error = '';

                    $converter  = new ConvertToHtmlFile($value['attachments']);
                    $files = $converter->convert();

                    $data['vendor_email'] = $value['vendorEmail'];
                    $data['user_email'] = $value['userEmail'];

                    $user = User::select('id','name','email')->where('email', $value['userEmail'])
                    ->where('status', 1)->first();

                    $vendor = Vendor::select('id','name','email','parser','parser_parameters')->where('email', $value['vendorEmail'])
                    ->where('status', 1)->first();

                    if($user){

                        if($vendor){
                                $parserClass = 'App\Libraries\Parser\GeneralParserOCR';
                                $parser = new $parserClass($user, $vendor, $value['emailSubject'], $files);
                                $parser = $parser->parse();
                            if($parser !== true){
                                $error = $parser;
                            }

                            array_push($successEmails, $value);

                        }else{
                            $error = 'No Vendor Found';
                        }
                    }
                    else
                        $error = 'No User Found';

                    if($error){
                       //  (isset($user->id)? $data['user_id'] = $user->id : '');
                       //  (isset($vendor->id)?  $data['vendor_id'] = $vendor->id : '');
                        $data['pdf_file'] = 'storage/invoice/pdf/' . $files[0]['filePath'];
                        $data['html_file'] = 'storage/invoice/html/' . $files[0]['htmlPath'];
                        $data['email_title'] = $value['emailSubject'];
                        $data['is_new'] = 1;
                        $data['comments'] = $error;

                         (isset($user->email)? $data['user_email'] = $user->email : '');
                         (isset($vendor->email)? $data['vendor_email'] = $vendor->email : '');
                        $createNewInvoiceIssueService = new CreateNewInvoiceIssueService();
                        $createNewInvoiceIssueService->create($data);
                    }
                }
            }
            echo 'Successfully Parsed. '.json_encode(($successEmails));
            echo 'Already Parsed. '.json_encode(($alreadyParsed));
        }else{
            echo 'No Email Found';
        }
    }
}
