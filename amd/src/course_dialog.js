/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Andrew McGhie <andrew.mcghie@totaralearning.com>
 * @package block_totara_featured_links
 */
define([
    'jquery',
    'core/str',
    'core/config'
], function ($, mdlstr, config){
    var selected_html = '';
    var config_dialog = {};
    var strings = {
        ok: '',
        cancel: '',
        title: ''
    };

    var make_dialog = function(){

        var url = config.wwwroot + '/blocks/totara_featured_links/course_dialog.php?';

        totaraSingleSelectDialog('course',
            strings.title + selected_html,
            url,
            'course_name_id',
            'course-name'
        );

        $('input[name="course_name"]').attr('readonly', 'readonly');
    };

    return {
        init: function(instancetype, instanceid, sesskey, selected){
            selected_html = selected;
            config_dialog.instancetype = instancetype;
            config_dialog.instanceid = instanceid;
            config_dialog.sesskey = sesskey;

            var required_strings = [];
            required_strings.push({key: 'cancel', component: 'moodle'});
            required_strings.push({key: 'course_select', component: 'block_totara_featured_links'});

            mdlstr.get_strings(required_strings).done(function(string_results){
                strings = {
                    cancel: string_results[0],
                    title: string_results[1]
                };

                if (window.dialogsInited) {
                    make_dialog();
                } else {
                    // Queue it up.
                    if (!$.isArray(window.dialoginits)) {
                        window.dialoginits = [];
                    }
                    window.dialoginits.push(make_dialog);
                }
            });
        }
    };
});