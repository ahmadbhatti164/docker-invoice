<?php

namespace App\Libraries;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\Currency;

class ImapEmailFetcher
{
	private $host;
	private $username;
	private $password;
	public function __construct($host, $username, $password){

		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
	}

	public function fetchEmails(){

	    //The location of the mailbox.
	    $mailbox = $this->host;
	    //The username / email address that we want to login to.
	    $username = $this->username;
	    //The password for this email address.
	    $password = $this->password;

	    //Attempt to connect using the imap_open function.
	    $imapResource = imap_open($mailbox, $username, $password);

	    //Lets get UNSEEN emails that were received since a given date.
	    $search = 'UNSEEN';
	    $emails = imap_search($imapResource, $search);
	    
	    $data = [];

	    //If the $emails variable is not a boolean FALSE value or
	    //an empty array.
	    if(!empty($emails)){

	        $count = 0;
	        //Loop through the emails.
	        foreach($emails as $email){
	            //Fetch an overview of the email.
	            $count++;
	            $overview = imap_fetch_overview($imapResource, $email);
	            $emailSubject = $overview['0']->subject;
	            $emailSeen = $overview['0']->seen; // read(seen=1) or unread(seen=0)

	            if($emailSeen == 0){

	                $headers = imap_headerinfo($imapResource, $email);
	                $headers = json_decode(json_encode($headers),true);
	                $from    = json_decode(json_encode($headers['from']),true);

	                //User Email
	                // $name           = $from[0]['personal'];
	                $emailAddress   = $from[0]['mailbox'].'@'.$from[0]['host'];
					$message = (imap_fetchbody($imapResource, $email, 1));
					$message = strtolower($message);

	                // Vendor Detail
	                $name = '';
					$vendorEmail = '';
					// Match Database Vendors With Email Body
					$vendors = Vendor::where('status', 1)->get(['id', 'email']);
					if(count($vendors) > 0){
						foreach($vendors as $vendor){
							$vendor->email  = strtolower($vendor->email);
							$emailIndex     = strpos($message, $vendor->email);
							if($emailIndex !== false){
								$vendorEmail = $vendor->email;
								break;
							}
						}
					}

	                // $indexFra = strpos($message, "Fra::");
	                // $indexFrom = strpos($message, "From::");

	                // if($indexFra !== false || $indexFrom !== false){

					// 	$tempMessage1 = substr($message, 0, $indexFrom);
	                //     $tempMessage2 = str_replace($tempMessage1,"",$message);
	                //     $tempMessage3 = explode(">", $tempMessage2);
	                //     $tempMessage4 = $tempMessage3[0];
	                //     $index        = strpos($tempMessage4, "<");
	                //     $tempMessage5 = substr($tempMessage4, 0, $index);
	                //     $tempMessage6 = str_replace($tempMessage5,"",$tempMessage4);
	                //     $vendorEmail  = str_replace("<","",$tempMessage6);
					// 	$vendorEmail  = str_replace(['[1]','From: ','Fra: '],['',''],$vendorEmail);
					// 	$vendorEmail  = explode(" ", $vendorEmail);
					// 	$vendorEmail  = $vendorEmail[0];

	                //     if($indexFra !== false){
	                //         $name = str_replace("Fra: *","",$tempMessage2);
	                //     }
	                //     else if($indexFrom !== false){
	                //         $name = str_replace("From: ","",$tempMessage2);
	                //     }
	                // }

	                $structure = imap_fetchstructure($imapResource, $email);
				    $attachments = [];

	                /* if any attachments found... */
	                if(isset($structure->parts) && count($structure->parts)){
	                    for($i = 0; $i < count($structure->parts); $i++){
	                        $attachments[$i] = array(
	                            'is_attachment' => false,
	                            'filename' => '',
	                            'name' => '',
	                            'attachment' => ''
	                        );

	                        if($structure->parts[$i]->ifdparameters){
	                            foreach($structure->parts[$i]->dparameters as $object){
	                                if(strtolower($object->attribute) == 'filename'){
	                                    $attachments[$i]['is_attachment'] = true;
	                                    $attachments[$i]['filename'] = $object->value;
	                                }
	                            }
	                        }

	                        if($structure->parts[$i]->ifparameters){
	                            foreach($structure->parts[$i]->parameters as $object){
	                                if(strtolower($object->attribute) == 'name'){
	                                    $attachments[$i]['is_attachment'] = true;
	                                    $attachments[$i]['name'] = $object->value;
	                                }
	                            }
	                        }

	                        if($attachments[$i]['is_attachment']){
	                            $attachments[$i]['attachment'] = imap_fetchbody($imapResource, $email, $i+1);
	                            if($structure->parts[$i]->encoding == 3){
	                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
	                            }
	                            elseif($structure->parts[$i]->encoding == 4){
	                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
	                            }
	                        }
	                    }
	                }

		            $data[] = array(
						'name' => $name,
		            	'emailSubject' => str_replace(['Fwd: ','FW: '], ['',''], $emailSubject),
		            	'userEmail' => $emailAddress,
		    			'vendorEmail' => $vendorEmail,
		    			'attachments' => $attachments,
		            );
	            }
	       	}
	    }

	    return $data;
	}

}
