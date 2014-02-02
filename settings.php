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
 *
 * @package   mod_digisem
 * @copyright 2011-2013 Patrick Meyer, Tobias Niedl
 * @license   GNU General Public License (GPL) 3.0 (http://www.gnu.org/licenses/gpl.html)
 *
 */


defined('MOODLE_INTERNAL') || die;



if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/digisem/lib.php');

    // FTP and storage settings
    $settings->add(new admin_setting_heading('digisem_ftp_config', get_string('ftp_config', 'digisem'), get_string('ftp_config_desc', 'digisem')));

    $settings->add(new admin_setting_configcheckbox('digisem_delete_files', get_string('delete_files', 'digisem'), 
			get_string('delete_files_desc', 'digisem'), 1));

    $settings->add(new admin_setting_configselect('digisem_use_ftp', get_string('ftps_config', 'digisem'), get_string('ftps_config_desc',         
            'digisem'), 'ftp', array('ftp'=>'FTP', 'ftps'=>'FTPs', 'sftp'=>'PHP internal SFTP', 'sftplib' => 'SFTP with third party lib')));

    $settings->add(new admin_setting_configtext('digisem_ftp_host', get_string('ftp_host', 'digisem'), 
			get_string('ftp_host_desc', 'digisem'), 'localhost'));

    $settings->add(new admin_setting_configtext('digisem_ftp_user', get_string('ftp_user', 'digisem'), 
			get_string('ftp_user_desc', 'digisem'), 'user'));

    $settings->add(new admin_setting_configpasswordunmask('digisem_ftp_pwd', get_string('ftp_pwd', 'digisem'), 
			get_string('ftp_pwd_desc', 'digisem'), 'abc123'));

    $settings->add(new admin_setting_configtext('digisem_ftp_dir', get_string('ftp_dir', 'digisem'), 
			get_string('ftp_dir_desc', 'digisem'), 'mdocs/'));

    $settings->add(new admin_setting_configtext('digisem_filearea', get_string('filearea', 'digisem'), 
			get_string('filearea_desc', 'digisem'), 'content'));

    // Order mgmt and mail settings
    $settings->add(new admin_setting_heading('digisem_mail_config', get_string('mail_config', 'digisem'), get_string('mail_config_desc', 'digisem')));

    $settings->add(new admin_setting_configtext('digisem_opac_url', get_string('opac_url', 'digisem'), 
			get_string('opac_url_desc', 'digisem'), 'http://www.ub.uni.de/opac'));

    $settings->add(new admin_setting_configtext('digisem_order_mail', get_string('order_mail', 'digisem'), 
			get_string('order_mail_desc', 'digisem'), 'order@ub.uni.de'));

    $settings->add(new admin_setting_configtext('digisem_sender_sign', get_string('sender_sign', 'digisem'), 
			get_string('sender_sign_desc', 'digisem'), 'UNI/MOODLE <noreply@moodle.uni.de>'));

    $settings->add(new admin_setting_configtext('digisem_mail_subject', get_string('mail_subject', 'digisem'), 
			get_string('mail_subject_desc', 'digisem'), 'Digitalization Order (UNI/MOODLE)'));


    // Information to user about delivered files
    $settings->add(new admin_setting_heading('digisem_delivery_config', get_string('delivery_config', 'digisem'), get_string('delivery_config_desc', 'digisem')));

    $settings->add(new admin_setting_configcheckbox('digisem_delivery_sendmail', get_string('delivery_send_mail', 'digisem'), 
			get_string('delivery_send_mail_desc', 'digisem'), 1));

    $settings->add(new admin_setting_configcheckbox('digisem_delivery_attach_details', get_string('delivery_email_attach_details', 'digisem'), 
			get_string('delivery_email_attach_details_desc', 'digisem'), 1));

    $settings->add(new admin_setting_configtext('digisem_delivery_sender_sign', get_string('delivery_email_sender', 'digisem'), 
			get_string('delivery_email_sender_desc', 'digisem'), 'UNI/MOODLE <noreply@moodle.uni.de>'));


}
