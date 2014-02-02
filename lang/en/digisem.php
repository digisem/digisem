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
* @package   mod_digisem
* @copyright 2011-2013 Patrick Meyer, Tobias Niedl
* @license   GNU General Public License (GPL) 3.0 (http://www.gnu.org/licenses/gpl.html)
*/

defined('MOODLE_INTERNAL') || die();

//Common module fields
$string['modulename'] = 'Digital Course Reserve';
$string['modulenameplural'] = 'Digital Course Reserves';
$string['modulename_help'] = 'Order Digital Course Reserves from magazine articles or book chapters from the stock of the university library. ';
$string['Digitalization'] = 'Digisem order';
$string['Digisem'] = 'Digisem';
$string['pluginadministration'] = 'Digisem administration';
$string['pluginname'] = 'Digisem';

//New digitalization activity
$string['name'] = 'Name';
$string['name_help'] = 'Type in the name for your order. Students will see this name as the filename for the resulting PDF document.';
$string['book_specifiers'] = 'Book metadata';
$string['import_from_opac'] = 'Import Metadata from OPAC';
$string['import_from_opac_help'] = 'On click you are referred to the online library system.';

$string['sign'] = 'Signature';
$string['article_title'] = 'Title of the chapter or article';
$string['author'] = 'Author';
$string['media_title'] = 'Published in';
$string['date'] = 'Date';
$string['volume'] = 'Volume';
$string['issue'] = 'Issue';
$string['pages'] = 'Pages';
$string['pages_help'] = 'Please consider the local copy right before you order. ';
$string['comment'] = 'Comment';
$string['comment_help'] = 'Type in some additional information to your order for the library staff. ';

$string['header_field_name'] = 'Name';
$string['header_field_value'] = 'Value';
$string['ordered_by'] = 'Ordered by';
$string['course'] = 'For the course';
$string['timecreated'] = 'Ordered at';
$string['status'] = 'Order status';
$string['isbn'] = 'ISBN';
$string['issn'] = 'ISSN';
$string['volume_issue'] = 'Volume (Issue)';
$string['publication_date'] = 'Year of publication';
$string['digitalization_name'] = 'Name of digisem';

$string['form_error'] = 'You have to import the medium of which you would like to order digitalizized extracts from the library catalogue. Please navigate back to the form and select a library item from the OPAC library system. ';

//View
$string['permission_error'] = 'You do not have access permissions to view this page.';
$string['file_not_found_error'] = 'The file for this digisem was not found in the filesystem! ';


//Bridge
$string['bridge_title'] = 'New digisem order';

$string['bridge_select_text'] = 'Please select the course to which you want to add the new digitalization document.';
$string['bridge_submit'] = ' Next ';

$string['bridge_error_course_permission'] = 'You don\'t have the permission to edit any course!';

//Configuration
$string['ftp_config'] = 'FTP and File Handling';
$string['ftp_config_desc'] = 'This module fetches incoming digisems from a FTP server. You can configure the respective FTP server below and instruct the module e.g. to delete or not delete temporary files. ';
$string['delete_files'] = 'Delete files from FTP';
$string['delete_files_desc'] = 'When the digisem files have been uploaded to the Moodle file system, the module can delete the temporary files on the FTP server. ';
$string['ftps_config'] = 'Use FTPs';
$string['ftps_config_desc'] = 'If checked, a secure SSL connection with the server will be established. ';
$string['ftp_host'] = 'FTP host';
$string['ftp_host_desc'] = '';
$string['ftp_user'] = 'FTP user';
$string['ftp_user_desc'] = '';
$string['ftp_pwd'] = 'FTP password';
$string['ftp_pwd_desc'] = '';
$string['ftp_dir'] = 'FTP directory';
$string['ftp_dir_desc'] = 'Insert the name of the directory from the root node of your login. E.g. when you login with the ftp credentials provided above you will possibly have a choice of directories to save into. Provide the desired path with a slash (/) at the end. Leave this field empty, if the base directory is used.';
$string['filearea'] = 'Filearea';
$string['filearea_desc'] = 'The module uses the Moodle file system to store files, which uses so called fileareas. If you know of this concept, please consider to create a folder in moodledata/temp so that the module can store temporarilly downloaded files there. Otherwise keep empty or insert \'content\'.';

$string['mail_config'] = 'Mail and Order Management';
$string['mail_config_desc'] = 'These settings regard the forms for course managers. When a manager wishes to make an digisem order, we need to send a mail to the library.';
$string['order_mail'] = 'Order mail address';
$string['order_mail_desc'] = 'Type in the mail address of the library system to process the orders.';
$string['sender_sign'] = 'Mail sender';
$string['sender_sign_desc'] = 'This is the name and address shown to the library system as sender.';
$string['mail_subject'] = 'Mail subject';
$string['mail_subject_desc'] = 'The inserted text appears as the mail subject header field for orders from Moodle.';

$string['opac_url'] = 'OPAC URL';
$string['opac_url_desc'] = 'Course managers are redirected to this URL when they want to select the meta data of a library item.';

$string['delivery_config'] = 'Delivery Management';
$string['delivery_config_desc'] = '';
$string['delivery_send_mail'] = 'Send an email to the user';
$string['delivery_send_mail_desc'] = 'Send an email to the user about delivered media.';
$string['delivery_email_sender'] = 'Sender of email';
$string['delivery_email_sender_desc'] = '';
$string['delivery_email_attach_details'] = 'Attach order details to email';
$string['delivery_email_attach_details_desc'] = 'Send the details of the digitalization order in the email like name, title, author, etc.';

$string['delivery_email_subject'] = 'UNI/MOODLE: Digitalization completed';
$string['delivery_email_body'] = 'Your digitalization order is completed and the delivered file was linked to your Moolde course.';

$string['view_error'] = 'This document is temporarily not available.';
?>
