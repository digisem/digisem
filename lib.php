<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Library of interface functions and constants for module digitalizaion
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * @package   mod_digisem
 * @copyright 2011-2013 Patrick Meyer, Tobias Niedl
 * @license   GNU General Public License (GPL) 3.0 (http://www.gnu.org/licenses/gpl.html)
 *
 */

defined('MOODLE_INTERNAL') || die();

// Load constants for the order email
// Set configurations for the order email
$DIGISEM_OPTIONS = array();
require_once('config/email_constants.php');

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $digitalization An object from the form in mod_form.php
 * @return int The id of the newly inserted newmodule record
 */
function digisem_add_instance($digitalization) {
    
    global $DB, $USER, $CFG;

    //This Method is called at two different times in the ordering process:
    // 1. When the "Import Metadata from OPAC"-Button is clicked
    // 2. When one of the "Save"-Buttons is clicked

    if (isset($digitalization->import_from_opac) && $digitalization->import_from_opac != '') 
    {
        //Case 1: save user input (digitalization name) and course number in the session. 
        //Then send a redirect to the OPAC URL

		/*
         What is stored for the digitalization in the current session?
         - Name of the digitalization activity
         - ID of the course to which the digitalization should be added
         - Section in the course in which the digitalization should be added. 
           (A course consists of a number of sections, e.g. one section per course week)
         */
        
        //Save user input in session
        $_SESSION['dig_name']       = $digitalization->name;    //Course Name
        $_SESSION['digisem_course_id']  = $digitalization->course;  //Course ID
        $_SESSION['digisem_section']    = $digitalization->section; //Section 



        //Redirect to the OPAC
        redirect($CFG->digisem_opac_url);
    

    //Case 2: create a new database entry with the new digitalisation activity
    // We proceed only, if the minimum data is present which is needed for processing the order
    } elseif(!empty($digitalization->sign) && !empty($digitalization->author)
		 && !empty($digitalization->title) && !empty($digitalization->pages)) {

        //Extend the given digitalization object:
        $digitalization->timecreated = time();
        $digitalization->timemodified = time();
        $digitalization->status = 'ordered';

        $digitalization->username  = $USER->lastname . ', ' . $USER->firstname;
        $digitalization->useremail = $USER->email;
        $digitalization->userphone = $USER->phone1;
		$digitalization->user = $USER->id;
      
        if (!isset($digitalization->issn)) {
            $digitalization->issn = '';
        }

        if (!isset($digitalization->isbn)) {
            $digitalization->isbn = '';
        }

        if (!isset($digitalization->volume)) {
            $digitalization->volume = '';
        }

        if (!isset($digitalization->issue)) {
            $digitalization->issue = '';
        }

        
        //Insert the digitalization order to the database
        //Notice: insert_record returns the ID of the new record (if 3rd parameter is not set or set to TRUE)
        $id = $DB->insert_record('digisem', $digitalization);
      
        //Set the ID of the database recordset to the object, because it's needed for
        //for sending the order email in the next step
        $digitalization->id = $id;

        //Send digitalization order email to the mybib system
        digisem_helper_send_order($digitalization);

        //Reset the values for the digitalization order in the current session
        digisem_helper_clear_session();

        return $id;
    
    } else {
     
        // If we come to this point, there is data missing, so we print an error message and abort
        print_error(get_string('form_error', 'digisem'));
    }
}

/**
 * List of features supported in Folder module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function digisem_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:         return false;
	    case FEATURE_BACKUP_MOODLE2:    return true;
	    case FEATURE_MOD_ARCHETYPE:     return MOD_ARCHETYPE_RESOURCE;

        default: return null;
    }
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $digitalization An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function digisem_update_instance($digitalization) {
    global $DB;

    $digitalization->timemodified = time();
    $digitalization->id = $digitalization->instance;

    # You may have to add extra stuff in here #

    return $DB->update_record('digisem', $digitalization);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function digisem_delete_instance($id) {
    global $DB;

    if (! $digitalization = $DB->get_record('digisem', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $DB->delete_records('digisem', array('id' => $digitalization->id));

    return true;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function digisem_user_outline($course, $user, $mod, $digitalization) {
    $return = new stdClass;
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function digisem_user_complete($course, $user, $mod, $digitalization) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in newmodule activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function digisem_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Checks for digitalizations in the ftp dir provided in the
 * module settings. It then compares the found files with 
 * open digitalization requests and matches them. 
 * The files are stored in the Moodle file structure,
 * users can access them via the pluginfile.php (normal procedure)
 *
 * @return boolean
 **/
function digisem_cron() { 
	global $DB, $CFG;
	
	$delete_file = $CFG->digisem_delete_files; 
	$filearea = $CFG->digisem_filearea;
	$ftp_server = $CFG->digisem_ftp_host;
	$ftp_dir = $CFG->digisem_ftp_dir;
	$ftp_user = $CFG->digisem_ftp_user;
	$ftp_pass = $CFG->digisem_ftp_pwd;
	$use_ftp = $CFG->digisem_use_ftp;

    $send_delivery_email = $CFG->digisem_delivery_sendmail;

    require_once("filetransfer/stub.php");

	// Establish connection to ftp server
	if ($use_ftp === "ftps") {
	    echo "\nTrying to open a ftps connection. ";
	    require_once("filetransfer/ftps.php");
	    $ftp_handler = new FTPsHandler($ftp_server, $ftp_user, $ftp_pass);
	} else if ($use_ftp === "sftp") {
	    echo "\nTrying to open a sftp connection. ";
	    require_once('filetransfer/sftp.php');
        $ftp_handler = new SFTPHandler($ftp_server, $ftp_user, $ftp_pass);
	} else if ($use_ftp === "sftplib") {
	    echo "\nTrying to open a sftp external lib connection. ";
	    require_once('filetransfer/sftp-lib.php');
	    $ftp_handler = new SFTPLibHandler($ftp_server, $ftp_user, $ftp_pass);
	}else {
	    echo "\nTrying to open a ftp connection. ";
	    require_once("filetransfer/ftp.php");
	    $ftp_handler = new FTPHandler($ftp_server, $ftp_user, $ftp_pass);
	}
	
	// Establish connection
	if (!$ftp_handler->connect()) {
	    echo "\nMod_digisem: Cannot establish FTP connection.\n";
	    return false;
	}

	// Try to login with username and password
	if (!$ftp_handler->login()) {
	    echo "\nMod_digisem: Username or password for ftp connection incorrect.\n";
	    return false;
	}

	// List the files in the directory
	$contents = $ftp_handler->listDir($ftp_dir);
	
	// List of requested digitalizations still open
	$open_requests = $DB->get_records('digisem', array('status' => 'ordered')); 

	// Build list of comparable names from the open requests, format of each element (id, name)
	$request_names = array();
	$request_objects = array();
	foreach($open_requests as $request) {
		// this is error safe, since $request->id are unique
		$request_names[$request->id] = digisem_helper_create_order_id_for($request->id);
		$request_objects[$request->id] = $request;
	}

	unset($open_requests);

	// This array's elements are built up as follows: (id, filename)
	$combinations_found = array();

	foreach($contents as $filename) {
		// very simple strategy to look for the string in the file name, just adds a 3-char long postfix and cmps the endings
		$intermediary = strtoupper(substr($filename, -17, 13)); 

		// Compare file name with list of open digitalization requests
		if(($result_key = array_search($intermediary, $request_names)) === FALSE)
			continue;

		// If we found the element, we append it to $combinations_found
		$combinations_found[$result_key] = $filename;
	}

	if($combinations_found === array()) {
	    return true;
    }

	// Needed vars:		
	$filearea_slashed = ($filearea) ? $filearea . '/' : '';
	
	// Now that we found at least one new document, start to download and move them into the Moodle file system
	foreach($combinations_found as $id => $filename) {
		$rel_path_to_tmp_data = $CFG->dataroot.'/temp/'.$filearea_slashed.substr($filename, -17);

		// Start a new file in the temp directory
		if(!($fp = @fopen($rel_path_to_tmp_data, 'w'))) {
        		echo "mod_digisem: Cannot write to temp dir.\n";
    		} else {
			// Download file and write it to tmp dir
			if (!$ftp_handler->recvFile($filename, $fp)) {
			   echo "mod_digisem: Could not download one file. Will attempt to later.\n";
			} else {
			   	if ($delete_file && !$ftp_handler->remFile($filename)) {
					echo "mod_digisem: Could not delete file from foreign ftp server. \n";
			 	}

				$fs = get_file_storage();

				// Workaround because course module id isn't known in this context
				$module = $DB->get_record('modules', array('name' => 'digisem'))->id;
				$cm = $DB->get_record('course_modules', array('module' => $module, 
									      'course' => $request_objects[$id]->course,
									      'instance' => $id));

				// CONTEXT_MODULE is set statically to 70 for every module
	 			$context = get_context_instance(CONTEXT_MODULE, $cm->id); 
			
				// First step: Create new file in regular data structure
				$file_record = array('contextid'=>$context->id, 'component'=>'mod_digisem', 
						 'filearea'=>$filearea, 'itemid'=>$id, 'filepath'=>'/'.$filearea_slashed, 
						 'filename'=>$request_objects[$id]->name . substr($filename, -4),
						 'timecreated'=>time(), 'timemodified'=>time(), 'userid' => $request_objects[$id]->user);

				$stored_file = $fs->create_file_from_pathname($file_record, $rel_path_to_tmp_data);

				// Alter the file_record to insert a course file
				$file_record['contextid'] = get_context_instance(CONTEXT_COURSE, $request_objects[$id]->course)->id;
				$file_record['component'] = 'course';
				$file_record['filearea'] = 'summary';
				// The following setting leads to an error, if a file with the same name exists - we catch the exception and continue...
				$file_record['itemid'] = 0;
				$file_record['filepath'] = '/';
				
				try {
				    	// Now add the file to the course files
				    	$fs->create_file_from_storedfile($file_record, $stored_file); 
				} catch (stored_file_creation_exception $e) {
				    	echo "\nCourse file could not be registered - will continue anyway. \n";
				}

				// Delete temp file
				unlink($rel_path_to_tmp_data);

				// Update digitalization instance
				$data = new stdClass();
				$data->id = $id;
				$data->status = 'delivered';

				$DB->update_record('digisem', $data);

                                // Send an email about the delivered media to the user who has ordered it
                                if ($send_delivery_email) {
                                    // Search for the email address of the user who has ordered current digitalization 
                                    $digitalization = $DB->get_record('digisem', array('id' => $id));
                                    $user = $DB->get_record('user', array('id' => $digitalization->user));

                                    if (isset($user->email) && $user->email != '') {
                                        // Send notification email
                                        digisem_helper_send_delivery($user->email, $digitalization);
                                    }
                                }

				// Update all clones of the instance, so that they contain the same status (they link to their parent, see view.php)
				$clones = $DB->get_records('digisem', array('copy_of' => $id)); 
				
				// Since moodle does not know update_records, we need to do it separately for each clone...
				foreach($clones AS $clone) {

					$cm = $DB->get_record('course_modules', array('module' => $module, 
									      'course' => $clone->course,
									      'instance' => $clone->id));

					// CONTEXT_MODULE is set statically to 70 for every module
	 				$context = get_context_instance(CONTEXT_MODULE, $cm->id); 

					$file_record['contextid'] = $context->id;
					$file_record['component'] = 'mod_digisem';
					$file_record['filearea'] = $filearea;
					$file_record['itemid'] = $clone->id;
					$file_record['filepath'] = '/' . $filearea_slashed;
					$file_record['filename'] = $clone->name . substr($filename, -4);
					
					// Now add the file to the course files
					$fs->create_file_from_storedfile($file_record, $stored_file); 
					
					// we already have $data->status = delivered and only want to update this field!
					$data->id = $clone->id;
					$DB->update_record('digisem', $data);

			 	} // foreach: $clones

			} // else: $ret != FTP_FINISHED
		} // else: $fp = fopen
	} // foreach: $combination_found
	
	// Close connection
	$ftp_handler->close();  

	return true;
}

/**
 * Must return an array of users who are participants for a given instance
 * of newmodule. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $digitalizationid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function digisem_get_participants($digitalizationid) {
    return false;
}

/**
 * This function returns if a scale is being used by one new module
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $digitalizationid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function digisem_scale_used($digitalizationid, $scaleid) {
    global $DB;

    $return = false;

    //$rec = $DB->get_record("digitalization", array("id" => "$digitalizationid", "scale" => "-$scaleid"));
    //
    //if (!empty($rec) && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}

/**
 * Checks if scale is being used by any instance of newmodule.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any newmodule
 */
function digisem_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('digisem', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Execute install custom actions for the module
 *
 * @return boolean true if success, false on error
 */
function digisem_install() {
	return true;
}



/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function digisem_uninstall() {
    return true;
}


/********************************************************/
/* Own support functions for digisem module      */
/********************************************************/

/**
 * Coming from pluginfile.php we must process the download of the file... aaaargh, what a dumb design! :)
 * 
 * @param  course
 * @param  cm
 * @param  context
 * @param  filearea
 * @param  args
 * @param  forcedownload
 * @return void
 */
function digisem_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
	global $CFG;
	$filearea = $CFG->digisem_filearea;	

	$fs = get_file_storage();	
	
	// We only need to know whether the user is allowed to see this resource or not!
	require_course_login($course, true, $cm);
        
	// taken from moodle/pluginfile.php
        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'mod_'.$cm->modname, 'content', 
		$cm->instance, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        $lifetime = isset($CFG->filelifetime) ? $CFG->filelifetime : 86400;

        // finally send the file
	send_stored_file($file, $lifetime, 0);
	die();
}

/**
 * Creates and sends the order email in subito format for a new 
 * digitalization to the MyBib eDoc System.
 *
 * @param  object digitalization
 * @return void
 */
function digisem_helper_send_order($digitalization) {
    global $CFG, $DIGISEM_OPTIONS; 
    digisem_append_options();

    $DIGISEM_OPTIONS['orderemail'] = array(
      'receiver' => $CFG->digisem_order_mail,
      'sender'   => $CFG->digisem_sender_sign,
      'subject'  => $CFG->digisem_mail_subject
    );
    
    //Step 1: Create email-body
    $email_body = 'message-type: REQUEST
transaction-id: '. $DIGISEM_OPTIONS['subito']['transaction-id'] .'
transaction-initial-req-id: '. $DIGISEM_OPTIONS['subito']['transaction-initial-req-id'] .'
transaction-group-qualifier: '. digisem_helper_create_order_id_for($digitalization->id) .'
transaction-type: '. $DIGISEM_OPTIONS['subito']['transaction-type'] .'
transaction-qualifier: '. $DIGISEM_OPTIONS['subito']['transaction-qualifier'] .'
service-date-time: '. date('YmdHis') .'
requester-id: '. $DIGISEM_OPTIONS['subito']['requester-id'] .'
country-delivery-target: '. $DIGISEM_OPTIONS['subito']['country-delivery-target'] .' 
client-id: '. $DIGISEM_OPTIONS['subito']['client-id'] .'
client-identifier: '. $DIGISEM_OPTIONS['subito']['client-identifier'] .'
delivery-address: '. $DIGISEM_OPTIONS['subito']['delivery-address'] .'
del-email-address: '. $digitalization->useremail .'
del-postal-name-of-person-or-institution: '. $digitalization->username .'
del-postal-street-and-number: '. $DIGISEM_OPTIONS['subito']['del-postal-street-and-number'] .'
del-postal-city: '. $DIGISEM_OPTIONS['subito']['del-postal-city'] .'
del-postal-code: '. $DIGISEM_OPTIONS['subito']['del-post-code'] .'
del-status-level-user: '. $DIGISEM_OPTIONS['subito']['del-status-level-user'] .'
del-status-level-requester: '. $DIGISEM_OPTIONS['subito']['del-status-level-requester'] .'
delivery-service: '. $DIGISEM_OPTIONS['subito']['delivery-service'] .'
delivery-service-format: '. $DIGISEM_OPTIONS['subito']['delivery-service-format'] .'
delivery-service-alternative: '. $DIGISEM_OPTIONS['subito']['delivery-service-alternative'] .' 
contact-person-name: '. $digitalization->username .'
contact-person-phone:
contact-person-email: '. $digitalization->useremail .'
billing-address: '. $DIGISEM_OPTIONS['subito']['billing-address'] .'
billing-method: '. $DIGISEM_OPTIONS['subito']['billing-method'] .'
billing-type: '. $DIGISEM_OPTIONS['subito']['billing-type'] .'
billing-name: '. $DIGISEM_OPTIONS['subito']['billing-name'] .'
billing-street: '. $DIGISEM_OPTIONS['subito']['billing-street'] .'
billing-city: '. $DIGISEM_OPTIONS['subito']['billing-city'] .'
billing-country: '. $DIGISEM_OPTIONS['subito']['billing-country'] .'
billing-code-type: '. $DIGISEM_OPTIONS['subito']['billing-code-type'] .'
ill-service-type: '. $DIGISEM_OPTIONS['subito']['ill-service-type'] .'
search-type: '. $DIGISEM_OPTIONS['subito']['search-type'] .'
item-id:
item-type: OTHER
item-call-number: '. $digitalization->sign .'
item-title: '. $digitalization->title .'
item-volume-issue: '. $digitalization->volume .' ('. $digitalization->issue .')
item-publication-date: '. $digitalization->pub_date .'
item-author-of-article: '. $digitalization->author .'
item-title-of-article: '. $digitalization->atitle .'
item-pagination: '. $digitalization->pages .'
item-issn: '. $digitalization->issn .'
item-isbn: '. $digitalization->isbn .'
order-comment: '. $digitalization->dig_comment .'
';
    


  
    //Step 2: Send email

    $headers  = "From: ". $DIGISEM_OPTIONS['orderemail']['sender'] ."\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=utf-8\r\n";

    @mail($DIGISEM_OPTIONS['orderemail']['receiver'], $DIGISEM_OPTIONS['orderemail']['subject'], $email_body, $headers);
  
    
}




/**
 * Creates a formated identifier for the subito order email.
 * The Identifier has the following format: MTP-iiiiiiiii
 * 'MTP' is a configured prefix and 'iiiiiiiii' is a number 
 * of 9 integers, which is the param $id integer with 0s prefixed.
 *
 * @param  integer $id
 * @return string
 */
function digisem_helper_create_order_id_for($id) {

    //Hard coded prefix here
    $order_id = 'MTP-';

    $string_id = $id . '';
    for ($i = 0; $i < (9 - strlen($string_id)); $i++)
    {
        $order_id .= '0';
    }

    $order_id .= $string_id;

    return $order_id;
}




/**
 * Sets SESSION-Entries for a digitalization back to empty strings
 *
 * @param  void
 * @return void
 */
function digisem_helper_clear_session() {
    $_SESSION['dig_name']      = '';
    $_SESSION['dig_course_id'] = '';
    $_SESSION['dig_section']   = '';

    $_SESSION['dig_sign']      = '';
    $_SESSION['dig_title']     = '';
    $_SESSION['dig_volume']    = '';
    $_SESSION['dig_issue']     = '';
    $_SESSION['dig_date']      = '';
    $_SESSION['dig_aufirst']   = '';	
    $_SESSION['dig_aulast']    = '';
    $_SESSION['dig_atitle']    = '';
    $_SESSION['dig_issn']      = '';
    $_SESSION['dig_isbn']      = '';
}


/**
 * Creates and sends a notification email to the moodle user when his/her
 * order was completed (file was delivered and linked in moodle)
 *
 * @param  string $receiver_email -> email of user to be notified
 * @return void
 */
function digisem_helper_send_delivery($receiver_email, $digitalization=null) {
    global $CFG;

    $headers  = "From: ". $CFG->digisem_delivery_sender_sign ."\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=utf-8\r\n";

    // Attach some detail information about the order if this feature is selected by the admin
    // and information are available
    if ($CFG->digisem_delivery_attach_details && $digitalization != null) {
        $order_details  = "\n\n";
        $order_details .= get_string('name', 'digisem')          . ': ' . $digitalization->name   . "\n";
        $order_details .= get_string('sign', 'digisem')          . ': ' . $digitalization->sign   . "\n";
        $order_details .= get_string('article_title', 'digisem') . ': ' . $digitalization->atitle . "\n";
        $order_details .= get_string('author', 'digisem')        . ': ' . $digitalization->author . "\n";
        $order_details .= get_string('media_title', 'digisem')   . ': ' . $digitalization->title  . "\n";
    } else {
        $order_details = '';
    }

    // Attach the URL to the course anyway
    $moodle_url  = "\n\n";
    if ($digitalization != null) {
        $moodle_url .= $CFG->wwwroot . "/course/view.php?id=" . $digitalization->course;
    } else {
        $moodle_url .= $CFG->wwwroot;
    }

    $email_subject = get_string('delivery_email_subject', 'digisem');
    $email_body    = get_string('delivery_email_body', 'digisem') . $order_details . $moodle_url;
    
    
    mail($receiver_email, $email_subject, $email_body, $headers);
}



?>
