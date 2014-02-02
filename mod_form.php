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
 * The main digitalization configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod_digisem
 * @copyright 2011-2013 Patrick Meyer, Tobias Niedl
 * @license   GNU General Public License (GPL) 3.0 (http://www.gnu.org/licenses/gpl.html)
 *
 */

//Moodle uses HTML_QuickForm, see http://www.midnighthax.com/quickform.php

//error_reporting(E_ALL);
//ini_set('display_errors','On');

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_digisem_mod_form extends moodleform_mod {

    private $media_data = null;
    

    function definition() {

        global $COURSE;
        $mform =& $this->_form;


        //If user is coming back from selecting a media in InfoGuide, 
        //store the media information in an seperate object ($media_data)
		$this->set_media_data();

        //Adding the "general" fieldset, where all the common settings are displayed
        $mform->addElement('header', 'general', get_string('general', 'form'));
        
        //Adding the standard "name" field
        $name_attributes = array('size' => '45');
        if (isset($_SESSION['dig_name']) && $_SESSION['dig_name'] != '') {
            $name_attributes['value'] = $_SESSION['dig_name'];
        } else {
            $name_attributes['value'] = '';
        }

        $mform->addElement('text', 'name', get_string('name', 'digisem'), $name_attributes);

        
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'name', 'digisem');
      
		// If this form is used to modify an existing digitalization, we do not want the user to update the order details
		if(!isset($_GET['update']) || empty($_GET['update'])) {
	        
			//Frame for fields
			$mform->addElement('header', 'book_specifiers', get_string('book_specifiers', 'digisem'));
		
	
			
			if ($this->media_data == null) {
	
			    /*
	    		     * If the has not imported any media data, we display the import button.
			     */
	
	
			    //As the SUBMIT-element does not support adding a help button, we pack the button into a element group 
			    //and add the help button to the group.
			    $elementsArray=array();		
			    $elementsArray[] =& $mform->createElement('submit', 'import_from_opac', get_string('import_from_opac', 'digisem'));
			
			    /* Params of addGroup:
			     1: Element-Array
			     2: Name
			     3: Label-text
			     4: ?
			     5: Switch on/off the enumeration of elements (will change name of elements in the group)
			    */
			    $mform->addGroup($elementsArray, 'import_from_opac_group', '', array(' '), false); 
			    $mform->addHelpButton('import_from_opac_group', 'import_from_opac', 'digisem');
	
			} else {
	
	
			    /*
	    		     * If user was relocated to the form after selecting a book/journal in 
			     * InfoGuide, show the meta data of the ordered media (as static text).
	  		     * If a needed field (e.g. 'sign') is not already set, a textbox is 
			     * displayed, which the user has to fill.
			     */
	
			    //Author
			    if ($this->media_data->aufirst != '' || $this->media_data->aulast != '') {
	
			        //First- and/or lastname is given -> show them (static)
			        if ($this->media_data->aufirst != '' && $this->media_data->aulast != '') {
			            $author = $this->media_data->aulast . ', ' . $this->media_data->aufirst;
			        } else {
			            $author = $this->media_data->aulast . $this->media_data->aufirst;
			        }
	
			        $mform->addElement('static', 'static_author', get_string('author', 'digisem'), $author);
			        $mform->addElement('hidden', 'author', $author);
	
			    } else {
			        //No author name is set -> show textbox
			        $author_attributes = array('size'  => '45');
			        $mform->addElement('text', 'author', get_string('author', 'digisem'), $author_attributes);
			        $mform->addRule('author', null, 'required', null, 'client');
			    }
	
			    //Title of chapter/article
			    if ($this->media_data->atitle != '') {
			        $mform->addElement('static', 'static_atitle', get_string('article_title', 'digisem'), $this->media_data->atitle);
			        $mform->addElement('hidden', 'atitle', $this->media_data->atitle);
			    } else {
			        $mform->addElement('text', 'atitle', get_string('article_title', 'digisem'));
			        $mform->addRule('atitle', null, 'required', null, 'client');
			    }
	
			    //Title of book/journal
			    if ($this->media_data->title != '') {
			        $mform->addElement('static', 'static_title', get_string('media_title', 'digisem'), $this->media_data->title);
			        $mform->addElement('hidden', 'title', $this->media_data->title);
			    } else {
			        $mform->addElement('text', 'title', get_string('media_title', 'digisem'));
	                        $mform->addRule('title', null, 'required', null, 'client');
			    }
	
			    /*
			     * If the selected media has a ISSN, it's a journal for which additional information is needed:
			     *  - volume number
			     *  - issue number 
			     */
			    if ($this->media_data->type == 'issn') {
			        
				//Volume
			        if ($this->media_data->volume != '') {
			            $mform->addElement('static', 'static_volume', get_string('volume', 'digisem'), $this->media_data->volume);
			            $mform->addElement('hidden', 'volume', $this->media_data->volume);
			        } else {
			            $mform->addElement('text', 'volume', get_string('volume', 'digisem'));
			            $mform->addRule('volume', null, 'required', null, 'client');
			        }
	
			        //Issue
			        if ($this->media_data->issue != '') {
			            $mform->addElement('static', 'static_issue', get_string('issue', 'digisem'), $this->media_data->issue);
			            $mform->addElement('hidden', 'issue', $this->media_data->issue);
			        } else {
			            $mform->addElement('text', 'issue', get_string('issue', 'digisem'));
			            $mform->addRule('issue', null, 'required', null, 'client');
			        }
			    }
	
			    //Publication date
			    if ($this->media_data->date != '') {
			        $mform->addElement('static', 'static_pub_date', get_string('date', 'digisem'), $this->media_data->date);
			        $mform->addElement('hidden', 'pub_date', $this->media_data->date);
			    } else {
			        $mform->addElement('text', 'pub_date', get_string('date', 'digisem'));
			        $mform->addRule('pub_date', null, 'required', null, 'client');
			    }
	
	
			    //Pages
			    $pages_attributes = array('size'  => '45');
			    $mform->addElement('text', 'pages', get_string('pages', 'digisem'), $pages_attributes);
			
			    $mform->addRule('pages', null, 'required', null, 'client');
			    $mform->addRule('pages', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
			    $mform->addHelpButton('pages', 'pages', 'digisem');
		      
	
			    //ISSN/ISBN
			    if ($this->media_data->type == 'issn') {
			        
				//Journal has a ISSN
				$mform->addElement('static', 'static_issn', 'ISSN', $this->media_data->issn);
			        $mform->addElement('hidden', 'issn', $this->media_data->issn);
	
			    } else {
	
			        //Book has a ISBN
			        $mform->addElement('static', 'static_isbn', 'ISBN', $this->media_data->isbn);
			        $mform->addElement('hidden', 'isbn', $this->media_data->isbn);
			    }
	
	
			    //Signature
			    if ($this->media_data->sign != '') {
			        $mform->addElement('static', 'static_sign', get_string('sign', 'digisem'), $this->media_data->sign);
			        $mform->addElement('hidden', 'sign', $this->media_data->sign);
			    } else {
			        $mform->addElement('text', 'sign', get_string('sign', 'digisem'));
			        $mform->addRule('sign', null, 'required', null, 'client');
			    }
	
	
			    //Comment
			    $comment_attributes = array('size'  => '45');
			    $mform->addElement('text', 'dig_comment', get_string('comment', 'digisem'), $comment_attributes);
			
			    $mform->addRule('dig_comment', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
			    $mform->addHelpButton('dig_comment', 'comment', 'digisem');
	
	                }
	
		} // if (isset(GET->update) OR empty(GET->update))




        //Add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        
        /* 
        $features = new stdClass;
        $features->groups = false;
        $features->groupings = true;
        $features->groupmembersonly = true;
        $this->standard_coursemodule_elements($features);
        */


        //Add standard buttons, common to all modules
        //$this->add_action_buttons(true, false, null);
		$this->add_action_buttons();

    }




    private function set_media_data() {

        //If user is coming back from selecting a media in InfoGuide, create an 
        //object holding all the information of the selected media
        if (isset($_SESSION['dig_issn']) && $_SESSION['dig_issn'] != '' || 
            isset($_SESSION['dig_isbn']) && $_SESSION['dig_isbn'] != '') {

            $this->media_data = new stdClass();

            //ISSN or ISBN
            if (isset($_SESSION['dig_issn']) && $_SESSION['dig_issn'] != '') {
                
                //ordered media is a journal 
                $this->media_data->type = 'issn';
                $this->media_data->issn = $_SESSION['dig_issn'];     

                //Volume
                if (isset($_SESSION['dig_volume']) && $_SESSION['dig_volume'] != '') {
                    $this->media_data->volume = $_SESSION['dig_volume']; 
                } else {
                    $this->media_data->volume = ''; 
                }

                //Issue
                if (isset($_SESSION['dig_issue']) && $_SESSION['dig_issue'] != '') {
                    $this->media_data->issue = $_SESSION['dig_issue']; 
                } else {
                    $this->media_data->issue = ''; 
                }

            } else {

                //ordered media is a book
                $this->media_data->type = 'isbn';
                $this->media_data->isbn = $_SESSION['dig_isbn'];
            }

            //Signature
            if (isset($_SESSION['dig_sign']) && $_SESSION['dig_sign'] != '') {
                $this->media_data->sign = $_SESSION['dig_sign']; 
            } else {
                $this->media_data->sign = ''; 
            }

            //Title of book/journal
            if (isset($_SESSION['dig_title']) && $_SESSION['dig_title'] != '') {
                $this->media_data->title = $_SESSION['dig_title']; 
            } else {
                $this->media_data->title = ''; 
            }

            //Title of chapter/article
            if (isset($_SESSION['dig_atitle']) && $_SESSION['dig_atitle'] != '') {
                $this->media_data->atitle = $_SESSION['dig_atitle']; 
            } else {
                $this->media_data->atitle = ''; 
            }

            //Author - firstname
            if (isset($_SESSION['dig_aufirst']) && $_SESSION['dig_aufirst'] != '') {
                $this->media_data->aufirst = $_SESSION['dig_aufirst']; 
            } else {
                $this->media_data->aufirst = ''; 
            }

            //Author - lastname
            if (isset($_SESSION['dig_aulast']) && $_SESSION['dig_aulast'] != '') {
                $this->media_data->aulast = $_SESSION['dig_aulast']; 
            } else {
                $this->media_data->aulast = ''; 
            }

            //Publishing date
            if (isset($_SESSION['dig_date']) && $_SESSION['dig_date'] != '') {
                $this->media_data->date = $_SESSION['dig_date']; 
            } else {
                $this->media_data->date = '';
            }
       }

    }
}
?>
