<?php
/* vim: set ts=4 sw=4: */
// +----------------------------------------------------------------------+
// | Copyright (C) 2002-2003 Michael Yoon                                 |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU General Public License          |
// | as published by the Free Software Foundation; either version 2       |
// | of the License, or (at your option) any later version.               |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the         |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA            |
// | 02111-1307, USA.                                                     |
// +----------------------------------------------------------------------+
// | Authors: Michael Yoon <michael@yoon.org>                             |
// +----------------------------------------------------------------------+
//
// $Id$

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     resource.calendar.php
 * Type:     resource
 * Name:     calendar
 * Purpose:  returns a template for use by the calendar function
 * -------------------------------------------------------------
 */

define('SMARTY_CALENDAR_TEMPLATE_BASENAME', 'calendar.tpl');

function smarty_resource_calendar_source($tpl_name, &$tpl_source, &$smarty)
{
  // Look for the template in the same directory as the plugin.
  $tpl_dir = dirname(__FILE__);
  $tpl_filename = $tpl_dir . '/' . SMARTY_CALENDAR_TEMPLATE_BASENAME;
  $tpl_file = fopen($tpl_filename, 'r');
  $tpl_source = fread($tpl_file, filesize($tpl_filename));
  fclose($tpl_file);
  return true;
}

function smarty_resource_calendar_timestamp($tpl_name, &$tpl_timestamp,
                                            &$smarty)
{
  // Look for the template in the same directory as the plugin.
  $tpl_dir = dirname(__FILE__);
  $tpl_filename = $tpl_dir . '/' . SMARTY_CALENDAR_TEMPLATE_BASENAME;
  $tpl_timestamp = filemtime($tpl_filename);
  return true;
}

function smarty_resource_calendar_secure($tpl_name, &$smarty)
{
  return true;
}

function smarty_resource_calendar_trusted($tpl_name, &$smarty)
{
  // unused for templates
}
?>
