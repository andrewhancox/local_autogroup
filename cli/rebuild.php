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
 * CLI Tool to rebuild all groupings
 *
 * @package local_autogroup
 * @author Andrew Hancox <andrewdchancox@googlemail.com>
 * @author Open Source Learning <enquiries@opensourcelearning.co.uk>
 * @link https://opensourcelearning.co.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2021, Andrew Hancox
 */

define('CLI_SCRIPT', true);
define('NO_OUTPUT_BUFFERING', true);

// No logging.
define('LOG_MANAGER_CLASS', '\core\log\dummy_manager');

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/user/lib.php');

foreach ($DB->get_recordset_sql("select u.*, uid.data
from {user_info_field} f
         inner join {user_info_data} uid on f.id = uid.fieldid
         inner join {user} u on u.id = uid.userid
where f.shortname = 'organisation_name'
  and u.city = '' <> uid.data and uid.data <> '' and uid.data is not null", []) as $usertofix) {
    $user = core_user::get_user($usertofix->id);
    $user->city = $usertofix->data;
    echo "updating user " . fullname($user);
    user_update_user($user);
}