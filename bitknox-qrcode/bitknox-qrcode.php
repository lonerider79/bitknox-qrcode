<?php
/**
 * Plugin Name: Pagelink QR Code
 * Plugin URI: https://mybio.philaquarters.com/
 * Description: Generates QR code for the link to the current page
 * Version: 0.0.2
 * Author: Vinu Felix
 * Author URI: http://mybio.philaquarters.com
 * Requires at least: 4.0
 * Tested up to: 4.8
 * License: GPL3
 * License URI:https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain: pageqr
 * @author Vinu Felix
{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
 
{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {License URI}.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

//url generation
// Register a URL that will set the qrcode options for displaying image
add_action( 'init', 'pagelinkqr_init' );
function pagelinkqr_init() {
    add_rewrite_rule( '^qrcode$', 'index.php?pagelinkqr=default&plqrecc=0&plqrframe=4&plqrzoom=1', 'top' );
}

// Each expected variable for the QR Code display page must be specifically mentioned.
// Otherwise WP ignores them
add_action( 'query_vars', 'pagelinkqr_query_vars' );
function pagelinkqr_query_vars( $query_vars )
{
    $query_vars[] = 'pagelinkqr';
    $query_vars[] = 'plqrecc';
    $query_vars[] = 'plqrframe';
    $query_vars[] = 'plqrzoom';
    return $query_vars;
}

// Register allowed variables on URL 
// if the variable is set, we include our page and stop execution after it
add_action( 'parse_request', 'pagelinkqr_parse_request' );
function pagelinkqr_parse_request( &$wp )
{
    if ( array_key_exists( 'pagelinkqr', $wp->query_vars ) ) {
        include( dirname( __FILE__ ) . '/qrimage.php' );
        exit();//image is to be displayed so no further processing
    }
}


// Register and load the widget
function pagelinkqr_load_widget() {
    register_widget('pagelinkqr_widget');

}
add_action( 'widgets_init', 'pagelinkqr_load_widget' );
 
// Creating the widget 
//class wpb_widget extends WP_Widget {
class pagelinkqr_widget extends WP_Widget{
    public function __construct() {
        parent::__construct(
         
        // Base ID of your widget
        'pagelinkqr_widget', 
         
        // Widget name will appear in UI
        'Pagelink QR Code Widget', 
         
        // Widget description
        array( 'description' => 'Insert QR Code for page url' ) 
        );
    }
 
// Creating widget front-end
 
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) )
        echo $args['before_title'] . $title . $args['after_title'];
        
        // This is where you run the code and display the output
        $altcode = $_SERVER['REQUEST_URI'];
        $ecc = (isset($instance['ecc']))?$instance['ecc']:0;
        $frame = (isset($instance['frame']))?$instance['frame']:4;
        $zoom = (isset($instance['zoom']))?$instance['zoom']:1;
        echo  '<img src="/qrcode/?pagelinkqr=default&plqrecc='. $ecc .'&plqrframe='. $frame .'&plqrzoom='. $zoom .'" alt="' .$altcode . '">';
        echo $args['after_widget'];
    }
         
    // Widget Backend 
    public function form( $instance ) {
        
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'pagelinkqr_widget_domain' );
        }
        if ( isset( $instance[ 'ecc' ] ) ) {
            $ecc = $instance[ 'ecc' ];
        }
        else {
            $ecc = 0;
        }
        if ( isset( $instance[ 'zoom' ] ) ) {
            $zoom = $instance[ 'zoom' ];
        }
        else {
            $zoom = 1;
        }
        if ( isset( $instance[ 'frame' ] ) ) {
            $frame = $instance[ 'frame' ];
        }
        else {
            $frame = 4;
        }
    // Widget admin form
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    <label for="<?php echo $this->get_field_id( 'ecc' ); ?>"><?php _e( 'ECC Level:' ); ?></label> 
    <select class="widefat" id="<?php echo $this->get_field_id( 'ecc' ); ?>" name="<?php echo $this->get_field_name( 'ecc' ); ?>">
        <option <?php if($ecc == 0) echo ' selected ';?> value="0">Level L</option>
        <option <?php if($ecc == 1) echo ' selected ';?> value="1">Level M</option>
        <option <?php if($ecc == 2) echo ' selected ';?> value="2">Level Q</option>
        <option <?php if($ecc == 3) echo ' selected ';?>value="3">Level H</option>
    </select>
    <label for="<?php echo $this->get_field_id( 'zoom' ); ?>"><?php _e( 'Zoom Factor:' ); ?></label> 
    <select class="widefat" id="<?php echo $this->get_field_id( 'zoom' ); ?>" name="<?php echo $this->get_field_name( 'zoom' ); ?>">
        <option <?php if($zoom == 1) echo ' selected ';?> value="1">1</option>
        <option <?php if($zoom == 2) echo ' selected ';?> value="2">2</option>
        <option <?php if($zoom == 3) echo ' selected ';?> value="3">3</option>
        <option  <?php if($zoom == 4) echo ' selected ';?> value="4">4</option>
    </select>
    <label for="<?php echo $this->get_field_id( 'zoom' ); ?>"><?php _e( 'Frame Size:' ); ?></label> 
    <select class="widefat" id="<?php echo $this->get_field_id( 'frame' ); ?>" name="<?php echo $this->get_field_name( 'frame' ); ?>">
        <option <?php if($frame == 4) echo ' selected ';?> value="4">4</option>
        <option <?php if($frame == 6) echo ' selected ';?> value="6">6</option>
        <option <?php if($frame == 10) echo ' selected ';?> value="10">10</option>
    </select>
    </p>
    <?php 
    }
     
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['frame'] = ( ! empty( $new_instance['frame'] ) ) ? strip_tags( $new_instance['frame'] ) : 4;
        $instance['zoom'] = ( ! empty( $new_instance['zoom'] ) ) ? strip_tags( $new_instance['zoom'] ) : 1;
        $instance['ecc'] = ( ! empty( $new_instance['ecc'] ) ) ? strip_tags( $new_instance['ecc'] ) : 0;
        return $instance;
    }
} // Class wpb_widget ends here