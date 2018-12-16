<?php

// Adding the front-end ui css file for this extension
function wpbooklist_jre_bookfinder_frontend_ui_style() {
    wp_register_style( 'wpbooklist-bookfinder-frontend-ui', BOOKFINDER_ROOT_CSS_URL.'bookfinder-frontend-ui.css' );
    wp_enqueue_style('wpbooklist-bookfinder-frontend-ui');
}

// Code for adding the general admin CSS file
function wpbooklist_jre_bookfinder_admin_style() {
  if(current_user_can( 'administrator' )){
      wp_register_style( 'wpbooklist-bookfinder-admin-ui', BOOKFINDER_ROOT_CSS_URL.'bookfinder-admin-ui.css');
      wp_enqueue_style('wpbooklist-bookfinder-admin-ui');
  }
}


// Code for adding file that prevents computer sleep during backup
function wpbooklist_jre_bookfinder_sleep_script() {
	if(current_user_can( 'administrator' )){
    	wp_register_script( 'wpbooklist-jre-bookfinder-sleepjs', BOOKFINDER_JS_URL.'nosleep/sleep.js', array('jquery') );
    	wp_enqueue_script('wpbooklist-jre-bookfinder-sleepjs');
	}
}

function wpbooklist_jre_bookfinder_shortcode_function(){
  global $wpdb;

  ob_start();
  include_once( BOOKFINDER_CLASS_DIR . 'class-bookfinder-form.php');
  $front_end_library_ui = new WPBookList_Bookfinder_Form();
  echo $front_end_library_ui->output_bookfinder_form();
  return ob_get_clean();
}



?>