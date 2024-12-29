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

namespace tool_quizui\local\hooks\output;

/**
 * Hook callbacks for tool_quizui
 *
 * @package    tool_quizui
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_standard_footer_html_generation {

    /**
     * Output items at the end of pages
     * The Javascript might be migrated to amd at some point
     * @return void
     * @package tool_quizui
     */
    public static function callback(\core\hook\output\before_standard_footer_html_generation $hook): void {
        global $DB, $OUTPUT;

        if (! get_config('tool_quizui', 'enabled')) {
          //  return;
        }
        //if (!self::in_uicohort()) {
            //    return;
        //}

        $content = '';
        if ($pagetype !== "mod-quiz-mod") {
          $content =   self::quiz_page_edit();
        }
        $hook->add_html($content);
        return ;
        global $PAGE;
        $pagetype = $PAGE->pagetype;
        if ($pagetype !== "mod-quiz-mod") {
            return;
        }
        $showall = optional_param('showall', '', PARAM_TEXT);
        $checkedstatus = "";

        if ($showall == "true") {
             $checkedstatus = "checked=true";
             $checkboxlabel = get_string('showall', 'tool_quizui');
        } else {
            // Should this be set to simplify?.
            $checkboxlabel = get_string('showall', 'tool_quizui');
        }

        $content = "
        <div id='id_showhide' class='custom-control custom-switch'>
            <input type='checkbox' ".$checkedstatus." name='xsetmode' class='custom-control-input' data-initial-value='on'>
            <span class='custom-control-label'>".$checkboxlabel."</span>
        </div>
        ";
        $content .= "<script>
        var showall ='".$showall."';
        const cbx_showhide = document.getElementById('id_showhide');
        const header = document.getElementById('user-notifications');
        cbx_showhide.addEventListener('click', function(event) {
        debugger;

        window.location.href = window.location.href;
            const url = new URL(window.location.href);
            if(showall == 'true') {
               url.searchParams.delete('showall');
            } else {
               url.searchParams.append('showall', 'true');
            }
            window.location.href = url.href;
            event.preventDefault();

        });

        function insertAfter(referenceNode, newNode) {
            referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
        }

        insertAfter(header, cbx_showhide);
        </script>";

        if ($showall == '') {
            $content .= self::hide_elements();
        }
        $hook->add_html($content);

    }
    public static function quiz_page_edit() {
        $elementstohide = get_config('tool_quizui', 'elementstohide');
        $content .= self::hide_elements($elementstohide);
        return $content;
    }
    /**
    * Create the javascript event code and insert the toggle element
    * html.
    *
    * @param string $content
    * @param string $elementid
    * @param [type] $checkboxlabel
    * @return string
    */
   public static function toggle_checkbox(string $elementid, string $checkboxlabel): string {
       $showall = optional_param('showall', '', PARAM_TEXT);
       if ($showall == "true") {
           $checkedstatus = "checked=true";
           $checkboxlabel = get_string('showall', 'tool_stackui');
      }

       $content = "
       <div>
           <div id='cbx_$elementid' class='custom-control custom-switch'>
           <input type='checkbox' $checkedstatus name='xsetmode' class='custom-control-input' data-initial-value='on'>
           <span class='custom-control-label'>$checkboxlabel</span>
       </div>

       <script>
           debugger;
           var showall = '$showall';
           const header = document.getElementById('$elementid');

           var cbx = 'cbx_'+$elementid;
           cbx = document.getElementById('cbx_$elementid');
           cbx.addEventListener('click', function(event) {
               const url = new URL(window.location.href);
               if(showall == 'true') {
                   url.searchParams.delete('showall');
               } else {
                   url.searchParams.append('showall', 'true');
               }
               window.location.href = url.href;
               event.preventDefault();
           });

           function insertAfter(referenceNode, newNode) {
               referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
           }

          insertAfter(header, cbx);
       </script>";
       if ($showall == '') {
           $content .= self::hide_elements();
       }
       return $content;
   }

    /**
     * Hide elements with javascript
     *
     * @return string
     */
    public static function hide_elements($elementstohide): string {
        global $DB, $OUTPUT;
        $array = explode(',', $elementstohide);
        $trimmedarray = array_map('trim', $array);

        $tohide = array_filter($trimmedarray, function($value) {
            return $value !== ''; // Remove empty strings only.
        });
        $content = '<style>';
        foreach ($tohide as $element) {
            $content .= PHP_EOL;
            $content .= '#'.$element. '{'.PHP_EOL;
            $content .= 'display:none;'.PHP_EOL;
            $content .= '}'.PHP_EOL;
        }

        $content .= '</style>';
        return $content;
    }



    /**
     * Check if the user is in the UI cohort
     *
     * @param array $tweaks
     * @return array
     * @package tool_quizui
     */
    public static function in_uicohort(): array {
        global $DB, $USER;
        $incohort = [];
        $uicohort = get_config('tool_quizui', 'uicohort');
        $cache = \cache::make('tool_quizui', 'quizuicache');
        if (($incohort = $cache->get('incohort')) === false) {
            $sql = "SELECT * FROM {cohort} co
                    JOIN {cohort_members} cm
                    ON co.id = cm.cohortid
                    WHERE cm.userid = :userid
                    AND co.name = :uicohort
                    ";
            $incohort = $DB->get_records_sql($sql, ['userid' => $USER->id, 'uicohort' => $uicohort]);
            $cache->set('incohort', $incohort);
        }
        return $incohort;

    }
}
