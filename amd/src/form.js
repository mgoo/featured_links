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


define(['jquery', 'core/ajax', 'core/yui'], function($, ajax, Y){

    $.urlParam = function(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results === null){
            return null;
        }
        else{
            return results[1] || 0;
        }
    };

    var init = function(){
        $('#id_type').change(function(event){
            var promises = ajax.call([{
                methodname: 'block_featured_links_external_render_form',
                args: {
                    tileid: $.urlParam('tileid'),
                    form_type: $(event.target).val(),
                    parameters: JSON.stringify({
                        blockinstanceid: $.urlParam('blockinstanceid'),
                        tileid: $.urlParam('tileid'),
                        return_url: decodeURIComponent($.urlParam('return_url'))
                    })
                }
            }
            ]);
            promises[0].done(function (response) {
                $(event.target).closest('form').replaceWith(response);
                init(); // Sets the action listener for the select again.
                //CALL JAVASCIPT THAT WILL INITIATE THE FILE ITEMS
            });
        });
    };

    return {
        'init': init
    };
});