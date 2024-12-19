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
 * TODO describe file settings
 *
 * @package    tool_quizui
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

if (is_siteadmin()) {
    $settingspage = new admin_settingpage('quizuisettings' , get_string('settings:quizuisettings', 'tool_quizui'));
    $settingspage->add(new admin_setting_configcheckbox('tool_stackui/enabled',
         get_string('settings:enabled', 'tool_quizui'),
         get_string('settings:enabled_text', 'tool_quizui') , 0));

    $settingspage->add(new admin_setting_configtext('tool_stackui/hidecorrecttag',
         get_string('settings:hidecorrecttag', 'tool_quizui'),
         get_string('settings:hidecorrecttag_text', 'tool_quizui') , 0));

    $settingspage->add(new admin_setting_configtext(
        'tool_quizui/simplify',
        get_string('settings:simplifytag', 'tool_quizui'),
        get_string('settings:simplifytag_desc', 'tool_quizui'),
        'quiz-simplify',  // default value
        PARAM_TEXT
    ));

    $formelements = 'id_seb,id_tagshdr,id_layouthdr,id_security,id_modstandardelshdr,
                    id_competenciessection,id_availabilityconditionsheader,id_display,id_activitycompletionheader';


     $settingspage->add(new admin_setting_configtextarea('tool_quizui/elementstohide',
         get_string('settings:elementstohide', 'tool_quizui'),
         get_string('settings:elementstohide_text', 'tool_quizui') ,
          $formelements));

    $ADMIN->add('tools', $settingspage);
}
