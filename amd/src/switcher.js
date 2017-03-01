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
 * @package block_featured_links
 */

/*
 * This switches the images on the tiles with multiple images.
 */
define(['jquery'], function($){
    return {
        init: function(interval, id){
            var current_img = 1;
            var max_img = 0;
            max_img = $('#'+id).children('img').length;
            current_img = Math.floor(Math.random() * (max_img)) + 1;
            $('#'+id+' img:nth-of-type('+current_img+')').show();
            if (max_img <= 1){
                return;
            }
            if (interval == 0){
                return;
            }
            window.setInterval(function(){
                $('#'+id+' img:nth-of-type('+current_img+')').css({'z-index':'2'}).fadeOut('slow');

                var new_current_img = -1;
                do {
                    new_current_img = Math.floor(Math.random() * (max_img)) + 1;
                } while (current_img == new_current_img);
                current_img = new_current_img;

                $('#'+id+' img:nth-of-type('+current_img+')').css({'z-index':'1'}).show();
            }, interval);
        },
        resize_images: function(){
            // This imitates the background-size: cover attribute in css.
            $('.block-featured-links-gallery-images').each(function() {
                var th = $(this).height(),
                    tw = $(this).width();
                $(this).find('img').each(function(){
                    var ih = $(this).height(),
                        iw = $(this).width();
                    if (ih>iw) {
                        $(this).css('width', '100%');
                    } else {
                        $(this).css('height', '100%');
                    }
                    var nh = $(this).height(),
                        nw = $(this).width(),
                        hd = (nh-th)/2,
                        wd = (nw-tw)/2;
                    if (nh<nw) {
                        $(this).css({marginLeft: '-'+wd+'px', marginTop: 0});
                    } else {
                        $(this).css({marginTop: '-'+hd+'px', marginLeft: 0});
                    }
                });

            });
        }
    };
});
