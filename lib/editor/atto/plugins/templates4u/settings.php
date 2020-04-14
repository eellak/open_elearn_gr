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
 * Atto_templates4u settings
 * @package   atto_templates4u
 * @author    Mark Sharp <m.sharp@chi.ac.uk>
 * @copyright 2017 University of Chichester {@link www.chi.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
if (!defined('ATTO_TEMPLATES4U_TEMPLATE_COUNT')) {
    define('ATTO_TEMPLATES4U_TEMPLATE_COUNT', 3);
}


if (is_siteadmin()) {
    $config = get_config('atto_templates4u');
    $ADMIN->add('editoratto', new admin_category('atto_templates4u', new lang_string('pluginname', 'atto_templates4u')));

    $settings = new admin_settingpage('atto_templates4u_settings', new lang_string('settings', 'atto_templates4u'));
    $settings->add(new admin_setting_configtext('atto_templates4u/templatecount',
        get_string('templatecount', 'atto_templates4u'),
        get_string('templatecount_desc', 'atto_templates4u'),
        ATTO_TEMPLATES4U_TEMPLATE_COUNT, PARAM_INT, 20));


    if ($config && property_exists($config, 'templatecount')) {
        $templatecount = $config->templatecount;
    } else {
        $templatecount = ATTO_TEMPLATES4U_TEMPLATE_COUNT;
    }

    $settings->add(new admin_setting_configcheckbox('atto_templates4u/cleanhtml',
        new lang_string('cleanhtml', 'atto_templates4u'),
        new lang_string('cleanhtml_desc', 'atto_templates4u'),
        '1'));

    $settings->add(new admin_setting_configcheckbox('atto_templates4u/requirecap',
        get_string('requirecap', 'atto_templates4u'),
        get_string('requirecap_desc', 'atto_templates4u'),
        0));

    for ($i = 1; $i <= $templatecount; $i++) {
        if ($config && property_exists($config, 'templatekey_' . $i)) {
            $tname = $config->{'templatekey_' . $i};
            if (empty($tname)) {
                $tname = $i;
            }
        } else {
            $tname = $i;
        }

        $settings->add(new admin_setting_heading('atto_templates4u/templatepageheading_' . $i,
                get_string('templateheading', 'atto_templates4u', $tname), ''));

        // Template key.
        $settings->add(new admin_setting_configtext('atto_templates4u/templatekey_' . $i ,
            get_string('templatekey', 'atto_templates4u', $i),
            get_string('templatekey_desc', 'atto_templates4u'),
            '', PARAM_ALPHANUMEXT));

        // Template body.
        $settings->add(new admin_setting_configtextarea('atto_templates4u/template_' . $i,
            get_string('template', 'atto_templates4u', $i),
            get_string('template_desc', 'atto_templates4u'), ''));
    }
}
