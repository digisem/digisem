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


/**
 * Define the complete digisem structure for backup, with file and id annotations
 */     
class backup_digisem_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');
 

        // Define each element separated
        // (Note: Params of the class constructor: name, array-of-attributes, array-of-child-elements)
        $digitalization = new backup_nested_element('digisem', array('id'), array(
            'user', 'name', 'timecreated', 'timemodified', 'status', 'sign', 'isbn', 'issn', 'author', 
            'atitle', 'title', 'volume', 'issue', 'pub_date', 'pages', 'dig_comment')); 

        // Build the tree
        // (not used here)

        // Define sources
        $digitalization->set_source_table('digisem', array('id' => backup::VAR_ACTIVITYID));
   
 
        // Define id annotations
        // (not used here)
 
        // Define file annotations
        // (not used here)

        // Return the root element (choice), wrapped into standard activity structure
        return $this->prepare_activity_structure($digitalization);

 
    }
}

