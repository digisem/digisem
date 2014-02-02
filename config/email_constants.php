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
 
defined('MOODLE_INTERNAL') || die();

function digisem_append_options() { 
 global $DIGISEM_OPTIONS;
 
 $DIGISEM_OPTIONS['subito'] = array(
  'transaction-id'               => '',
  'transaction-initial-req-id'   => 'UNI.MOODLE',
  'transaction-type'             => 'SIMPLE',
  'transaction-qualifier'        => '1',
  'requester-id'                 => 'UNI/MOODLE',
  'country-delivery-target'      => 'DE',
  'client-id'                    => '',
  'client-identifier'            => '',
  'delivery-address'             => 'Uni street 12',
  'del-postal-street-and-number' => 'University',
  'del-postal-city'              => 'Town',
  'del-post-code'                => '11111',
  'del-status-level-user'        => 'NEGATIVE',
  'del-status-level-requester'   => 'NONE',
  'delivery-service'             => 'FTP-P',
  'delivery-service-format'      => 'PDF',
  'delivery-service-alternative' => 'N',
  'billing-address'              => '',
  'billing-method'               => '',
  'billing-type'                 => '',
  'billing-name'                 => '',
  'billing-street'               => '',
  'billing-city'                 => '',
  'billing-country'              => '',
  'billing-code-type'            => '',
  'ill-service-type'             => '',
  'search-type'                  => ''
);
}

?>