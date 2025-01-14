<?php
/*

Plugin name: SBS Blogroll
Plugin URI: http://www.someblogsite.com/web-stuff/sbs-blogroll
Description: Link to latest articles from your RSS/Atom feeds.
Version: 0.3
Author: Some Guy
Author URI: http://www.someblogsite.com
History: This plugin is a tweak of RSS Blogroll by Greg Jackson. (see below)

Original Plugin name: RSS Blogroll
Original Plugin URI: http://www.pantsonhead.com/wordpress/rss-blogroll/
Original Description: Link to latest articles from your favorite RSS feeds.
Original Version: 0.1
Original Author: Greg Jackson
Original Author URI: http://www.pantsonhead.com

Copyright 2009  Greg Jackson  (email : greg@pantsonhead.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/


class SBSBlogroll extends WP_Widget {

    function SBSBlogroll() {
      $widget_ops = array('classname' => 'SBSBlogroll',
                      'description' => 'Blogroll from RSS/Atom');
        $control_ops = array( 'width' => 400);
        $this->WP_Widget('SBSBlogroll', 'SBS Blogroll', $widget_ops, $control_ops);
    }
    
    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
        $max_items = intval($instance['max_items']);
        $max_per_feed = intval($instance['max_per_feed']);
        $cachetime = intval($instance['cachetime']);
        $display_style = $instance['style'];
        $show_date = intval($instance['show_date']);
        $show_icon = intval($instance['show_icon']);
        $show_feedname = intval($instance['show_feedname']);
        $show_author = intval($instance['show_author']);
        $new_window = intval($instance['new_window']);
        $date_format = $instance['date_format'];        
        $url = explode("\n",$instance['feeds']);
        

        // Build it
        $items = array();
        foreach($url as $rss_url){
            $rss_url = trim($rss_url);
            if($rss_url!='' AND ($display_style=='time' OR count($items)<$max_items)){
                $feed = $this->fetch_feed($rss_url,$cachetime);
                if(is_wp_error($feed)) {
                    // report the error?
                } else {
                    $feedname = $feed->get_title();
                    $feedicon = $feed->get_favicon();

                    $feedlink = esc_url(strip_tags($feed->get_permalink()));
                    while ( stristr($feedlink, 'http') != $feedlink )
                        $feedlink = substr($link, 1);
                    $counter=0;
                    foreach ($feed->get_items(0, $max_per_feed) as $item) {
                        $itemdata = array();
                        $itemdata['id'] = $item->get_id();
                        $itemdata['feedname'] = $feedname;
                        $itemdata['feedlink'] = $feedlink;
                        $itemdata['feedicon'] = $feedicon;
                        $itemdata['title'] = $item->get_title();
                        $itemdata['timestamp'] = strtotime($item->get_date());
                        $itemdata['link'] = $item->get_permalink();
                        $itemdata['description'] = str_replace(array("\n", "\r"), ' ', esc_attr(strip_tags(@html_entity_decode($item->get_description(), ENT_QUOTES, get_option('blog_charset')))));;
                        $itemdata['description'] =  wp_html_excerpt( $itemdata['description'], 200 ) . '&hellip; ';
                        $itemdata['description'] = esc_html( $itemdata['description'] );
                        $itemdata['author'] = '';
                        if($show_author) {
                            $author = $item->get_author();
                            if(is_object($author)) {
                                $itemdata['author'] = $author->get_name();
                                $itemdata['author'] = esc_html(strip_tags($itemdata['author']));
                            }
                        }
                        
                        if($display_style == 'time') {
                            $key = $itemdata['timestamp'];
                            while(isset($items[$key])) { // I know, it's a kludge...
                                $key++;
                            }
                        } else {
                            $key = $itemdata['id'];
                        }
                        $items[$key] = $itemdata;
                    } // end foreach $item
                }
            }
        } // end foreach $url
        
        
        // Sort it chronologically?
        if($display_style == 'time') {
            krsort($items);
        }
        
        // Display it
        $output = '';
        $total_items = 0;
        $feed_items = array();
        foreach($items as $item) {
            if($total_items < $max_items){
                if(!isset($feed_items[$item['feedname']])){
                    $feed_items[$item['feedname']]=1;
                    if($display_style=='feed'){
                        if($total_items){
                            $output .= '</ul>';
                        }
                        $output .= '<div class="sbsblogroll-feedtitle"><a href="'.$item['feedlink'].'">'.$item['feedname'].'</a></div>'."\n<ul>";
                    } elseif($output=='') {
                        $output .= "\n" . '<ul style="margin-left: 0; padding-left: 0; list-style-type: none;">';
                    }
                } else {
                    $feed_items[$item['feedname']]++;
                }
                $item_date = date_i18n($date_format,$item['timestamp']);
                $output .= '<li class="sbsblogroll-feeditem"';
                if($show_icon) {
                    $output .= ' style="list-style-type: none;"><img src="' . $item['feedicon'] . '">';
                }
                else {
                    $output .= '>';
                }
                if($new_window) {
                    $window_link = ' target="_blank"';
                } else {
                    $window_link = '';
                }
                if($show_feedname and $display_style!='feed') {
                    $output .= '<span class="sbsblogroll-feedname"><a href="'.$item['feedlink'].'"'.$window_link.'>'.$item['feedname'].'</a></span><br />';
                    if($show_date)
                        $output .= '';
                }
                $output .= '<a href="'.$item['link'].'" title="'.$item['description'].' '.$item['feedname'].' '.$item_date.'"'.$window_link.'>'.$item['title'].'</a><br />';
                if($show_date) {
                    $output .= '<div class="sbsblogroll-date">'.$item_date.'</div><br />';
                }
                if($show_author)
                 $output .= '  <cite>'.$item['author'].'</cite>';
                $output .= "</li>\n";
                $total_items++;
            }
        }
        $output .= '</ul>';
        
        // output
        echo $before_widget;
        if($title)
            echo $before_title.$title.$after_title;
        echo $output;
        echo $after_widget;
    
    }
    
    // lifted from wp-includes/feed.php so that we could have flexible caching
    function fetch_feed($url, $cache_seconds=43200) {
        require_once (ABSPATH . WPINC . '/class-feed.php');

        $feed = new SimplePie();
        $feed->set_feed_url($url);
        $feed->set_cache_class('WP_Feed_Cache');
        $feed->set_file_class('WP_SimplePie_File');
        $feed->set_cache_duration(apply_filters('wp_feed_cache_transient_lifetime', $cache_seconds));
        $feed->init();
        $feed->handle_content_type();

        if ( $feed->error() )
            return new WP_Error('simplepie-error', $feed->error());

        return $feed;
}
    
    function update($new_instance, $old_instance) {
      $instance = $old_instance;
      $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['feeds'] = strip_tags(stripslashes($new_instance['feeds']));
        $instance['style'] = $new_instance['style'];
        $instance['max_items'] = intval($new_instance['max_items']);
        $instance['max_per_feed'] = intval($new_instance['max_per_feed']);
        $instance['cachetime'] = intval($new_instance['cachetime']);
        $instance['show_date'] = intval($new_instance['show_date']);
        $instance['show_feedname'] = intval($new_instance['show_feedname']);
        $instance['show_author'] = intval($new_instance['show_author']);
        $instance['show_icon'] = intval($new_instance['show_icon']);
        $instance['new_window'] = intval($new_instance['new_window']);
        $instance['date_format'] = $new_instance['date_format'];
      return $instance;
    }
    
    function form($instance) {
        
      $instance = wp_parse_args((array)$instance, array(
            'title' => 'SBS Blogroll',
            'style' => 'time', 
            'max_items' => 10,
            'max_per_feed' => 1,
            'cachetime' => 600,
            'show_date' => 1,
            'show_feedname' => 1,
            'show_icon' => 1,
            'new_window' => 1,
            'url' => "http://wordpress.org/development/feed/\nhttp://clanbase.ggl.com/rss.php\nhttp://feeds2.feedburner.com/MeasurableWins"
            ));
        
      $title = htmlspecialchars($instance['title']);
        $feeds = htmlspecialchars($instance['feeds']);
        $style = $instance['style'];
      $max_items = intval($instance['max_items']);
        $max_per_feed = intval($instance['max_per_feed']);
        $cachetime = intval($instance['cachetime']);
        $show_date = intval($instance['show_date']);
        $show_feedname = intval($instance['show_feedname']);
        $show_author = intval($instance['show_author']);
        $show_icon = intval($instance['show_icon']);
        $new_window = intval($instance['new_window']);
        $date_format = empty($instance['date_format']) ? get_option('date_format') : $instance['date_format'];

        ${'style_'.$style} = 'SELECTED';
  
        echo '
            <p>
            <label for="'.$this->get_field_name('title').'">Title: </label> 
            <input type="text" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.$title.'"/>
            </p>
            <p>
            <label for="'.$this->get_field_name('feeds').'">Feed URLs: </label> <br/>
            <textarea class="widefat" id="'.$this->get_field_id('feeds').'" name="'.$this->get_field_name('feeds').'">'.$feeds.'</textarea><br/>
            <span class="description">Enter one feed URL per line. Order is observed by Group by Feed.</span><br/>
            </p>
            <p>
                <label for="'.$this->get_field_name('cachetime').'">Cache Period (seconds): </label>
                <input type="text" id="'.$this->get_field_id('cachetime').'" name="'.$this->get_field_name('cachetime').'" value="'.$cachetime.'" style="width:50px" />
            </p>
            <p>
                <label for="'.$this->get_field_name('style').'">Display Style: </label>
                <select id="'.$this->get_field_id('style').'" name="'.$this->get_field_name('style').'">
                    <option value="time" '.$style_time.'>Chronological</option>
                    <option value="feed" '.$style_feed.'>Group by Feed</option>
                </select>
            </p>
            <table width="400"><tr>
            <td width="50%">
                <p>
                    <label for="'.$this->get_field_name('max_items').'">Maximum Items: </label>
                    <input type="text" id="'.$this->get_field_id('max_items').'" name="'.$this->get_field_name('max_items').'" value="'.$max_items.'" style="width:50px" />
                </p>
            </td><td>
                <p>
                <label for="'.$this->get_field_name('max_per_feed').'">Items per Feed: </label>
                    <input type="text" id="'.$this->get_field_id('max_per_feed').'" name="'.$this->get_field_name('max_per_feed').'" value="'.$max_per_feed.'" style="width:50px" />
                </p>
            </td></tr>
            <tr><td>
            <p>
                <input type="checkbox" id="'.$this->get_field_id('show_date').'" name="'.$this->get_field_name('show_date').'"  value="1" '.(($show_date)?'checked="checked"': '').'/>
                <label for="'.$this->get_field_name('show_date').'">Display item date? </label>
            </p>
            </td><td>
                <p>
                    <label for="'.$this->get_field_name('date_format').'">Date Format: </label>
                    <input type="text" id="'.$this->get_field_id('date_format').'" name="'.$this->get_field_name('date_format').'" value="'.$date_format.'" style="width:80px" />
                </p>
            </td></tr>
            </table>
            <p>
                <input type="checkbox" id="'.$this->get_field_id('show_feedname').'" name="'.$this->get_field_name('show_feedname').'"  value="1" '.(($show_feedname)?'checked="checked"': '').'/>
                <label for="'.$this->get_field_name('show_feedname').'">Display item source? (only for Chronological)</label>
            </p>
            <p>
                <input type="checkbox" id="'.$this->get_field_id('show_author').'" name="'.$this->get_field_name('show_author').'"  value="1" '.(($show_author)?'checked="checked"': '').'/>
                <label for="'.$this->get_field_name('show_author').'">Display item author if available? </label>
            </p>
            <p>
                <input type="checkbox" id="'.$this->get_field_id('show_icon').'" name="'.$this->get_field_name('show_icon').'"  value="1" '.(($show_icon)?'checked="checked"': '').'/>
                <label for="'.$this->get_field_name('show_icon').'">Display favicon if available? </label>
            </p>
            <p>
                <input type="checkbox" id="'.$this->get_field_id('new_window').'" name="'.$this->get_field_name('new_window').'"  value="1" '.(($new_window)?'checked="checked"': '').'/>
                <label for="'.$this->get_field_name('new_window').'">Open links in a new browser tab/window? </label>
            </p>

            ';
    }
    
}

function SBSBlogroll_init() {
  register_widget('SBSBlogroll');
}

add_action('widgets_init', 'SBSBlogroll_init');

?>