<?php
/**
 * Plugin Name: Pagelink QR Code
 * Plugin URI: https://mybio.philaquarters.com/
 * Description: Generates QR code for the link to the current page
 * Version: 0.0.1
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
// Register a URL that will set this variable to true
add_action( 'init', 'qrcode_init' );
function qrcode_init() {
    add_rewrite_rule( '^qrcode$', 'index.php?qrcode_opt=true', 'top' );
}

// But WordPress has a whitelist of variables it allows, so we must put it on that list
add_action( 'query_vars', 'qrcode_query_vars' );
function qrcode_query_vars( $query_vars )
{
    $query_vars[] = 'qrcode_opt';
    return $query_vars;
}

// If this is done, we can access it later
// This example checks very early in the process:
// if the variable is set, we include our page and stop execution after it
add_action( 'parse_request', 'qrcode_parse_request' );
function qrcode_parse_request( &$wp )
{
    if ( array_key_exists( 'qrcode_opt', $wp->query_vars ) ) {
        include( dirname( __FILE__ ) . '/qrimage.php' );
        exit();
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
echo  '<img src="/qrcode/?qrcode_opt=true" alt="' .$altcode . '">';
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
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
     
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class wpb_widget ends here