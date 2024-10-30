<?php
/*
Plugin Name: Child page by Atlas
Description: Creating widget for views child page
Version: 1.0.0
Author: Atlas-it
Author URI: http://atlas-it.by
Text Domain: atl-sub-page-widget
Domain Path: /lang/
License:     GPL2

Copyright 2020  Atlas  (email: atlas.webdev89@gmail.com)

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

// строки для перевода заголовков плагина, чтобы они попали в .po файл.
__('Child page by Atlas', 'atl-sub-page-widget' );
__('Creating widget for views child page', 'atl-sub-page-widget' );
/*langs file*/
$plugin_dir = basename( dirname( __FILE__ ) );
load_plugin_textdomain( 'atl-sub-page-widget', null, $plugin_dir.'/lang/' );

/*Регистрация виджета*/
add_action('widgets_init', 'atl_sub_page');
function atl_sub_page () { 
    register_widget('ATL_WP_subpage');
}

class ATL_WP_subpage extends WP_Widget {
 
    public function __construct() {
    $args = array (
        'name'=>__('Child pages', 'atl-sub-page-widget'),
        'description'=>__('Widget fot view child pages','atl-sub-page-widget'),
         );
        parent::__construct ('atl_sub_page', '', $args);
    }
    
    public function form ($instance) {
        $parent_id = isset($instance['id_parent']) ? $instance['id_parent']:'';  
        $title = isset($instance['title'])?$instance['title']:__('Catalog','atl-sub-page-widget');
        ?> 
            <p>
                <label for = "<?php echo $this->get_field_id('title');?>"><?php _e('Header', 'atl-sub-page-widget');?></label>
                <input class="widefat title" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" value="<?php echo $title;?>">
            </p>
            <p>
                <label for = "<?php echo $this->get_field_id('id_parent');?>"><?php _e('Select page', 'atl-sub-page-widget');?></label>
                    <select class = "widefat" id="<?php echo $this->get_field_id('id_parent');?>" name="<?php echo $this->get_field_name('id_parent');?>">
                            <option></option>
                    <?php 
                          		$page_parent = get_posts(array(  
                                                        'numberposts' => -1,
                                                        'post_status' => 'publish',
                                                        'post_type' => 'page',
                                                        'orderby' => 'title',
                                                        'order' => 'ASC'
                                                         )
                                                      );
                                foreach($page_parent as $pages) {
                                            if ($pages->ID == $parent_id ) {
                                                echo '<option value ='.  $pages->ID.' selected="selected">'. $pages->post_title.'</option>';
                                            } else {
                                                echo '<option value ='.$pages->ID.' >'. $pages->post_title.'</option>';
                                            }
                                } ?>
                    </select>       
            </p>
            <?php
               
                 }

    public function widget ($args, $instance) { 
                                    $children_page = get_children( array(
                                            'numberposts' => -1,
                                            'post_type' => 'page',
                                            'post_status' => 'publish',
                                            'post_parent' => $instance['id_parent'],
                                            'orderby' => 'title ',
                                            'order' => 'ASC'
                                        ) ); 
        
                /*Вывод списка дочерних страниц*/
                echo $args['before_widget'];
                echo $args['before_title'].$instance['title'].$args['after_title'];   
                ?>
                    <ul>
                        <?php  if ($children_page) {
                                    foreach ($children_page as $page) {  ?>
                                        <li><a href="<?php echo get_permalink($page->ID);?>" ><?php echo $page->post_title; ?></a></li>
                        <?php }}
                                else  { ?>
                                        <li><?php _e('Not found child page','atl-sub-page-widget');?></li>
                               <?php }; ?>
                    </ul>
                <?php   
                echo $args['after_widget'];
    }  
    
    public function update ($new_instance, $old_instance) {
            $new_instance['id_parent'] = isset($new_instance['id_parent'])&&!empty($new_instance['id_parent']) ? $new_instance['id_parent']:1;
            $new_instance['title']=isset($new_instance['title']) && !empty($new_instance['title'])?strip_tags($new_instance['title']):__('Catalog','atl-sub-page-widget');
        return $new_instance;
    }
}