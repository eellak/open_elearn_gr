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
 * Templates Lib.
 * @package   atto_templates4u
 * @author    Mark Sharp <m.sharp@chi.ac.uk>
 * @copyright 2017 University of Chichester {@link www.chi.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Set language strings for js
 */
function atto_templates4u_strings_for_js() {
    global $PAGE;
    $PAGE->requires->strings_for_js(
        ['dialogtitle',
         'template',
         'selectatemplate',
         'description',
         'insert',
         'cancel',
         'preview',
        ], 'atto_templates4u');
}

/**
 * Return the js params required for this module
 * @param string $elementid Selected element
 * @param stdClass $options - the options for the editor
 * @param stdClass $fpoptions - unused
 * @return array List of templates
 */
function atto_templates4u_params_for_js($elementid, $options, $fpoptions) {
    global $COURSE;
    $templates = get_config('atto_templates4u');
    $tcount = ($templates->templatecount) ? $templates->templatecount : ATTO_TEMPLATES_TEMPLATE_COUNT;
    $items = [];
    for ($i = 1; $i <= $tcount; $i++) {
        $key = 'templatekey_' . $i;
        if (isset($templates->{$key}) && !empty(trim($templates->{$key}))) {
            $item = new stdClass();
            $item->templatekey = trim($templates->{'templatekey_' . $i});
            if ($templates->cleanhtml) {
                $item->template = purify_html(
                    $templates->{'template_' . $i}, ['allowid' => true]);
            } else {
                $item->template = $templates->{'template_' . $i};
            }
            $items[] = $item;
        }
    }

    $coursecontext = context_course::instance($COURSE->id);
    $enablebutton = ($templates->requirecap) ? has_capability('atto/templates4u:usetemplates', $coursecontext) : true;
    return array('templates' => $items, 'enablebutton' => $enablebutton);
}

/**
 * Get icon mapping for font-awesome.
 */
function atto_templates4u_get_fontawesome_icon_map() {
    return [
        'atto_templates4u:icon' => 'fa-wpforms'
    ];
}
