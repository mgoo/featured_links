/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @package block_featured_links
 *
 *
 */

define([
    'jquery',
    'core/ajax',
    'core/templates',
    'block_featured_links/visibility_form',
    'core/str',
    'core/config'
], function ($, ajax, templates, vis_form, mdlstr, config){

    var tileid = '';
    var config_dialog = {};
    var handler;
    var strings = {
        ok: '',
        cancel: '',
        title: ''
    };
    //Copied stuff

    var make_dialog = function (){
        // Create handler for the dialog.
        var totaraDialog_handler_restrictcohorts = function() {
            this.baseurl = '';
        };

        totaraDialog_handler_restrictcohorts.prototype = new totaraDialog_handler_treeview_multiselect();

        totaraDialog_handler_restrictcohorts.prototype.every_load = function(){
            $('#audience_visible_table [cohortid]').each(function(){
                var cohortid = $(this).attr('cohortid');
                ehandler._toggle_items('item_' + cohortid, false);
            });
            this._make_selectable($('.treeview', this._container));
            this._make_deletable($('.selected', this._container));
        };

        tileid = config_dialog.instanceid;

        var ehandler = new totaraDialog_handler_restrictcohorts();
        handler = ehandler;

        var dbuttons = {};
        dbuttons[strings.ok ] = function() {ehandler._update();};
        dbuttons[strings.cancel] = function() {ehandler._cancel();};

        var url = config.wwwroot + '/totara/cohort/dialog/';

        new totaraDialog(
            'course-cohorts-visible-dialogue',
            'add_audience_id',
            {
                buttons: dbuttons,
                title: strings.title
            },
            url + 'cohort.php?selected=' + config_dialog.visibleselected
            + '&instancetype=' + config_dialog.instancetype
            + '&instanceid=' + config_dialog.instanceid
            + '&sesskey=' + config_dialog.sesskey,
            ehandler
        );

        /**
         * Add a row to a list on the visiblity form page
         * Also hides the dialog and any no item notice
         * @return void
         */
        totaraDialog_handler_restrictcohorts.prototype._update = function() {
            var elements = $('.selected [id^=item]', this._container);
            elements.each(function() {

                var itemid = $(this).attr('id').split('_');
                itemid = itemid[itemid.length-1];  // The last item is the actual id.
                itemid = parseInt(itemid);

                //check if list contains
                if (vis_form.audience_list_contains(itemid)){
                    return;
                }

                var promises = ajax.call([
                    {
                        methodname: 'block_featured_links_external_add_audience_list_item', args: {
                        cohortid: itemid
                        }
                    }
                ]);
                promises[0].done(function (response) {
                    vis_form.add_to_audience_list(response);
                    vis_form.add_to_audience_id(itemid);
                    vis_form.add_audience_table_listeners();
                });
            });
            ehandler._dialog.hide();
        };
    };

    return {
        init: function(instancetype, instanceid, sesskey){
            config_dialog.instancetype = instancetype;
            config_dialog.instanceid = instanceid;
            config_dialog.sesskey = sesskey;

            var required_strings = [];
            required_strings.push({key: 'ok', component: 'moodle'});
            required_strings.push({key: 'cancel', component: 'moodle'});
            required_strings.push({key: 'audience_add', component: 'block_featured_links'});

            mdlstr.get_strings(required_strings).done(function(string_results){
                strings = {
                    ok: string_results[0],
                    cancel: string_results[1],
                    title: string_results[2]
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