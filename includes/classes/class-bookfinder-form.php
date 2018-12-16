<?php
/**
 * WPBookList WPBookList_Bookfinder_Form Tab Class
 *
 * @author   Jake Evans
 * @category ??????
 * @package  ??????
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Bookfinder_Form', false ) ) :
/**
 * WPBookList_Bookfinder_Form Class.
 */
class WPBookList_Bookfinder_Form {

	public static function output_bookfinder_form(){

		// Perform check for previously-saved Amazon Authorization
		global $wpdb;
		$string2 = '';
		$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
		$opt_results = $wpdb->get_row("SELECT * FROM $table_name");

		$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
		$db_row = $wpdb->get_results("SELECT * FROM $table_name");

		$string1 = '<div id="wpbooklist-bookfinder-container">
			<p id="wpbooklist-bookfinder-instructional">Simply enter a title, author, or both in the fields below, click the \'Find Books\' button, and select the books to add to your <span class="wpbooklist-color-orange-italic">WPBookList</span> Libraries!<br/><br/><span ';

				if($opt_results->amazonauth == 'true'){ 
					$string2 = 'style="display:none;"';
				} else {
					$string2 = '';
				}

		$string3 = ' ></span></p>
      		<form id="wpbooklist-bookfinder-form" method="post" action="">
	          	<div id="wpbooklist-authorize-amazon-container">
	    			<table>';

	    			if($opt_results->amazonauth == 'true'){ 
						$string4 = '<tr style="display:none;"">
	    					<td><p id="auth-amazon-question-label">Authorize Amazon Usage?</p></td>
	    				</tr>
	    				<tr style="display:none;"">
	    					<td>
	    						<input checked type="checkbox" name="authorize-amazon-yes" />
	    						<label for="authorize-amazon-yes">Yes</label>
	    						<input type="checkbox" name="authorize-amazon-no" />
	    						<label for="authorize-amazon-no">No</label>
	    					</td>
	    				</tr>';
					} else {
						$string4 = '<tr>
	    					<td><p id="auth-amazon-question-label">Authorize Amazon Usage?</p></td>
	    				</tr>
	    				<tr>
	    					<td>
	    						<input type="checkbox" name="authorize-amazon-yes" />
	    						<label for="authorize-amazon-yes">Yes</label>
	    						<input type="checkbox" name="authorize-amazon-no" />
	    						<label for="authorize-amazon-no">No</label>
	    					</td>
	    				</tr>';
					}

					$string5 = '</table>
		    		</div>
		    		<div id="wpbooklist-bookfinder-div-container">
		    			<div id="wpbooklist-bookfinder-search-title">
		    				<div>
		    					<label>Enter a Title</label>
		    					<input id="wpbooklist-bookfinder-title-input" placeholder="Search by Title" type="text"   />
		    				</div>
		    				<div>
		    					<label>Enter an Author</label>
		    					<input id="wpbooklist-bookfinder-author-input" placeholder="Search by Author" type="text"   />
		    				</div>
		    				<button id="wpbooklist-bookfinder-search-button">Find Books</button>
		    			</div>
		    			<div class="wpbooklist-spinner" id="wpbooklist-spinner-bookfinder"></div>
		    			<div id="wpbooklist-bookfinder-status-div"></div>
			    		<div id="wpbooklist-bookfinder-div-for-hiding-scroll">
			    			<div id="wpbooklist-bookfinder-title-response"></div>
			    		</div>
		    			<div id="wpbooklist-bookfinder-results-div"></div>
		    		</div>';


		    		$string6 = '<div id="wpbooklist-bookfinder-select-library-label" for="wpbooklist-bookfinder-select-library">Select a Library to Add These Books To:</div>
		    		<select class="wpbooklist-bookfinder-select-default" id="wpbooklist-bookfinder-select-library">
		    			<option value="'.$wpdb->prefix.'wpbooklist_jre_saved_book_log">Default Library</option> ';

		    		foreach($db_row as $db){
						if(($db->user_table_name != "") || ($db->user_table_name != null)){
							$string6 = $string6.'<option value="'.$wpdb->prefix.'wpbooklist_jre_'.$db->user_table_name.'">'.ucfirst($db->user_table_name).'</option>';
						}
					}

					$string7 = '    
	          		</select>
	          		<div id="wpbooklist-bookfinder-page-post-container">
		    			<table>
		    				<tr>
		    					<td><p id="use-page-post-question-label">Create a Page and/or Post For Each Book?</p></td>
		    				</tr>
		    				<tr>
		    					<td>
		    						<input type="checkbox" name="bookfinder-create-post" />
		    						<label for="bookfinder-create-post">Create Posts</label>
		    						<input type="checkbox" name="bookfinder-create-page" />
		    						<label for="bookfinder-create-page">Create Pages</label>
		    					</td>
		    				</tr>';

		    				// Check to see if Storefront extension is active
							include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
							if(is_plugin_active('wpbooklist-storefront/wpbooklist-storefront.php')){

								$string7 = $string7.'<tr>
			    					<td>
			    						<input type="checkbox" name="bookfinder-create-woo" />
			    						<label for="bookfinder-create-post">Create WooCommerce Products?</label>
			    					</td>
		    					</tr>';
		    				}
	$string7 = $string7.'</table>
		    		</div>
		    		<div id="wpbooklist-bookfinder-textarea-submit-div">
		    		<textarea id="wpbooklist-bookfinder-textarea" placeholder="9780761345086,9780761169764,9780761162345,9780761169086,9780545790352,9780553106633..."></textarea>
		    		<div id="wpbooklist-bookfinder-status-div"></div>
		    		<div class="wpbooklist-spinner" id="wpbooklist-spinner-bookfinder"></div>
		    		<div id="wpbooklist-bookfinder-div-for-hiding-scroll">
		    			<div id="wpbooklist-bookfinder-title-response"></div>
		    		</div>
		    		<button id="wpbooklist-bookfinder-button">Add Books</button>



		    		</div>';

		    		$string8 = '</form></div>';

		return $string1.$string2.$string3.$string4.$string5.$string8;
	}
}

endif;