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
 * Templates4U install script
 *
 * @package    atto_templates4u
 * @copyright  2020 University of Chichester {@link http://www.chi.ac.uk}
 * @author     Mark Sharp <m.sharp@chi.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Installation function.
 */
function xmldb_atto_templates4u_install() {
    $oldplugin = core_plugin_manager::instance()->get_plugin_info('atto_templates');
    if ($oldplugin) {
        // If the old plugin was previously installed, copy over the settings.

        $oldtemplatecount = get_config('atto_templates', 'templatecount');
        $newtemplatecount = get_config('atto_templates4u', 'templatecount');
        if ($oldtemplatecount > $newtemplatecount) {
            set_config('templatecount', $oldtemplatecount, 'atto_templates4u');
        }
        for ($x = 1; $x < ($oldtemplatecount + 1); $x++) {
            $template = get_config('atto_templates', 'template_' . $x);
            $templatekey = get_config('atto_templates', 'templatekey_' . $x);
            set_config('template_' . $x, $template, 'atto_templates4u');
            set_config('templatekey_' . $x, $templatekey, 'atto_templates4u');
        }
        $cleanhtml = get_config('atto_templates', 'cleanhtml');
        set_config('cleanhtml', $cleanhtml, 'atto_templates4u');
        $requirecap = get_config('atto_templates', 'requirecap');
        set_config('requirecap', $requirecap, 'atto_templates4u');
    }
}