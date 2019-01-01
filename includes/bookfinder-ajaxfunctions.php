<?php


/*
 * Below is a bookfinder ajax function and callback, 
 * complete with console.logs and echos to verify functionality
 */

function wpbooklist_bookfinder_addbooks_action_javascript() { 
	?>
  	<script type="text/javascript" >
  	"use strict";
  	jQuery(document).ready(function($) {

  		var titleResponseDiv = $('#wpbooklist-bookfinder-title-response');
  		var smileIcon = $('#wpbooklist-smile-icon-1');
  		var statusDiv = $('#wpbooklist-bookfinder-status-div');
  		var spinner = $('#wpbooklist-spinner-bookfinder');
  		var titleResponse = '';
	  	var totalAdded = 0;
	  	var scrollTop = 0;
  		var totalAdded = 0;
		var errorFlag = false;
  		var errorCounter = 0;
  		var failedIsbns = '';
  		var isbnIterator = 0;
  		var totalIsbns = 0;

  		// Reset UI elements
  		titleResponseDiv.animate({'opacity':'0', 'height':'0px'}, 1000);
  		statusDiv.animate({'opacity':'0', 'margin-bottom':'0px'}, 1000);

  		// The 'Add Books' button
  		$(document).on("click","#wpbooklist-bookfinder-add-checked", function(event){

  			// Resetting UI elements and controlling vars
  			totalIsbns = 0;
  			totalAdded = 0;

  			$('#wpbooklist-bookfinder-div-for-hiding-scroll').animate({'height':'155px'})
			$('#wpbooklist-bookfinder-status-div').animate({'margin-bottom':'90px'})

  			$('#wpbooklist-bookfinder-add-checked').prop('disabled', true);

  			$('.wpbooklist-bookfinder-checkbox-div input').each(function(){
  				if($(this).prop('checked') == true){
  					totalIsbns++;
  				}
  			});

  			spinner.animate({'opacity':'1'}, 1000);
	  		statusDiv.animate({'opacity':'1', 'margin-bottom':'90px'}, 1000);
	  		statusDiv.html('<p>Adding <span class="wpbooklist-color-orange-italic">'+totalIsbns+'</span> Books...</p>');

	  		scrollTop = $('#wpbooklist-bookfinder-search-button').offset().top-50;
	  		if(scrollTop != 0){
			  $('html, body').animate({
			    scrollTop: scrollTop
			  }, 500);
			  scrollTop = 0;
			}

			var isbnArray = [];
  			$('.wpbooklist-bookfinder-checkbox-div input').each(function(){
  				if($(this).prop('checked') == true){
  					isbnArray.push($(this).parent().parent().attr('data-isbn'));
  				}
  			});

  			(function wpbooklist_bookfinder_add_book_worker() {
  				
  					var library = $('#wpbooklist-bookfinder-select-library').val();
  					var page = $('#bookfinder-create-page').prop('checked');
  					var post = $('#bookfinder-create-post').prop('checked');
  					var woo = $('#bookfinder-create-woo').prop('checked');
  					

	  				var data = {
						'action': 'wpbooklist_bookfinder_addbooks_action',
						'security': '<?php echo wp_create_nonce( "wpbooklist_bookfinder_addbooks_action_callback" ); ?>',
						'isbn':isbnArray[isbnIterator],
						'library':library,
						'page':page,
						'post':post,
						'woo':woo
					};

					var request = $.ajax({
					    url: ajaxurl,
					    type: "POST",
					    data:data,
					    timeout: 0,
					    success: function(response) {

					    	response = response.split('sep');
					    	// If the ajax call was succesful but the book wasn't found or some other error retreiving the book information occurred (probably due to a bad ISBN number)
					    	if(response[7] == '' || response[7] == 'undefined' || response[7] == undefined){
					    		failedIsbns = failedIsbns+','+response[8];
					    		errorCounter++;
					    	} else {
					    		totalAdded++;
					    		// Handle UI progress updates
						    	titleResponseDiv.scrollTop(titleResponseDiv.prop("scrollHeight"));
						    	titleResponseDiv.animate({'opacity':'1'}, 1000);
						    	smileIcon.animate({'opacity':'1'}, 1000);
						   		titleResponseDiv.css({'height':'155px'});
						    	titleResponse = titleResponse+" Added<br/><span class='wpbooklist-bookfinder-response-span'>'"+response[7]+"'</span><br/>";
						    	titleResponseDiv.html(titleResponse);

						    	statusDiv.html('<p>Adding <span class="wpbooklist-color-orange-italic">'+totalIsbns+'</span> Books...</p><p>Succesfully Added <span class="wpbooklist-color-orange-italic">'+totalAdded+'</span> books!<img id="wpbooklist-smile-icon-1" src="<?php echo ROOT_IMG_ICONS_URL; ?>smile.png" /><p>');
					    	}

					    	// handling UI stuff once all books have made an attempt to be added
					    	if(totalAdded == (totalIsbns-errorCounter)){
					    		spinner.animate({'opacity':'0'}, 1000);
					    		failedIsbns = failedIsbns.replace(/^,|,$/g,'');
					    		var failedIsbnsArray = failedIsbns.split(',');
					    		var failedIsbnsArrayUnique = [];

					    		// Making the failed ISBN unique array
								$.each(failedIsbnsArray, function(i, el){
								    if($.inArray(el, failedIsbnsArrayUnique) === -1) failedIsbnsArrayUnique.push(el);
								});

								// Creating ISBN error message
								var errorReportString = '';
								if(failedIsbnsArrayUnique.length > 0 && failedIsbnsArrayUnique[0] != ''){
									for (var i = failedIsbnsArrayUnique.length - 1; i >= 0; i--) {
										if(failedIsbnsArrayUnique[i] != 'undefined' && failedIsbnsArrayUnique[i] != undefined){
											errorReportString = errorReportString+'<p class="wpbooklist-bookfinder-error-isbn">'+failedIsbnsArrayUnique[i]+'</p>'
										}
									}
									titleResponseDiv.html('<p id="wpbooklist-bookfinder-isbn-error-message"><span class="wpbooklist-color-orange-italic">WPBookList</span> had trouble finding information for these ISBN Numbers:</p>'+errorReportString);
									titleResponseDiv.animate({ scrollTop: 0 }, "fast");
								}

					    		console.log(failedIsbnsArrayUnique);
					    		$('#wpbooklist-bookfinder-add-checked').prop('disabled', false);
					    	}
					    },
						error: function(jqXHR, textStatus, errorThrown) {
							console.log(errorThrown);
				            console.log(textStatus);
				            console.log(jqXHR);
				            errorCounter++;
						},
						complete: function() {
							isbnIterator++;
					      	// Schedule the next request when the current one's complete, if we're not doen already
					      	if(totalAdded != (totalIsbns-errorCounter)){
								setTimeout(wpbooklist_bookfinder_add_book_worker, 1000);
							}
					    }
					});
  			})();


  			event.preventDefault ? event.preventDefault() : event.returnValue = false;
  		});

  	});
	</script>
	<?php
}

// Callback function for creating backups
function wpbooklist_bookfinder_addbooks_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_bookfinder_addbooks_action_callback', 'security' );
	$library = filter_var($_POST['library'],FILTER_SANITIZE_STRING);
	$isbn = filter_var($_POST['isbn'],FILTER_SANITIZE_STRING);
	$page = filter_var($_POST['page'],FILTER_SANITIZE_STRING);
	$post = filter_var($_POST['post'],FILTER_SANITIZE_STRING);
	$woo = filter_var($_POST['woo'],FILTER_SANITIZE_STRING);
	


	$book_array = array(
		'use_amazon_yes' => 'true',
		'amazonauth' => 'true',
		'library' => $library,
		'isbn' => $isbn,
		'page_yes' => $page,
		'post_yes' =>$post,
		'woocommerce' => $woo
	);

	require_once(CLASS_BOOK_DIR.'class-wpbooklist-book.php');
	$book_class = new WPBookList_Book('add', $book_array, null);
	$insert_result = $book_class->add_result;

	// If book added succesfully, get the ID of the book we just inserted, and return the result and that ID
	if($insert_result == 1){
		$book_table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
  		$id_result = $wpdb->get_var("SELECT MAX(ID) from $library");
  		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $library WHERE ID = %d", $id_result));

  		// Get saved page URL
		$table_name = $wpdb->prefix.'wpbooklist_jre_saved_page_post_log';
  		$page_results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE book_uid = %s AND type = 'page'" , $row->book_uid));

  		// Get saved post URL
		$table_name = $wpdb->prefix.'wpbooklist_jre_saved_page_post_log';
  		$post_results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE book_uid = %s AND type = 'post'", $row->book_uid));

  		echo $insert_result.'sep'.$id_result.'sep'.$library.'sep'.$page_yes.'sep'.$post_yes.'sep'.$page_results->post_url.'sep'.$post_results->post_url.'sep'.$book_class->title.'sep'.$book_class->isbn;
	}

	// Require the Transients file.
	require_once ROOT_WPBL_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
	$transients = new WPBookList_Transients();
	$transients->delete_all_wpbl_transients();

	wp_die();

}

function wpbooklist_bookfinder_colorbox_action_javascript() { 

	$trans1 = __('Loading, Please wait', 'wpbooklist');

	?>
  	<script type="text/javascript" >
  	"use strict";
  	jQuery(document).ready(function($) {
  		$(document).on("click",".wpbooklist-bookfinder-colorbox", function(event){

			var title = $(this).parent().attr('data-title');
			var author = $(this).parent().attr('data-author');
			var category = $( "input[name='book-category']" ).val();
			var pages = $(this).parent().attr('data-pages');
			var pubYear = $(this).parent().attr('data-pubdate');
			var publisher = $(this).parent().attr('data-publisher');
			var description = $(this).parent().attr('data-description');
			var image = $(this).attr('src');
			var reviews = $(this).parent().attr('data-review');
			var isbn = $(this).parent().attr('data-isbn');
			var details = $(this).parent().attr('data-details');
			var similar = $(this).parent().attr('data-similar');

  			var data = {
				'action': 'wpbooklist_bookfinder_colorbox_action',
				'security': '<?php echo wp_create_nonce( "wpbooklist_bookfinder_colorbox_action_callback" ); ?>',
				'title':title,
				'author':author,
				'category':category,
				'pages':pages,
				'pubYear':pubYear,
				'publisher':publisher,
				'description':description,
				'image':image,
				'reviews':reviews,
				'isbn':isbn,
				'details':details,
				'similar':similar
			};

			$.colorbox({
		        iframe:true,
		        title: "<?php echo $trans1; ?>", 
		        width: "50%", 
		        height: "80%", 
		        html: "&nbsp;", 
		        fastIframe:false,
		        onComplete:function(){
		          $('#cboxLoadingGraphic').show();
		          $('#cboxLoadingGraphic').css({'display':'block'})
		        }
		    });
console.log(data);
	  		var request = $.ajax({
			    url: ajaxurl,
			    type: "POST",
			    data:data,
			    timeout: 0,
			    success: function(response) {

			    	console.log(response);

			    	$.colorbox({
						open: true,
						preloading: true,
						scrolling: true,
						width:'70%',
						height:'70%',
						html: response,
						onClosed:function(){
						  //Do something on close.
						},
						onComplete:function(){

							// Hide blank 'Similar Titles' images
							$('.wpbooklist-similar-image').load(function() {
								var image = new Image();
								image.src = $(this).attr("src");
								if(image.naturalHeight == '1'){
									$(this).parent().parent().css({'display':'none'})
								}
							});

							addthis.toolbox(
				              $(".addthis_sharing_toolbox").get()
				            );
				            addthis.toolbox(
				              $(".addthis_sharing_toolbox").get()
				            );
				            addthis.counter(
				              $(".addthis_counter").get()
				            );
						}
					});

			    	console.log(response);
			    }
			});


			event.preventDefault ? event.preventDefault() : event.returnValue = false;
	  	});
	});
	</script>
	<?php
}

// Callback function for creating backups
function wpbooklist_bookfinder_colorbox_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_bookfinder_colorbox_action_callback', 'security' );

	$title = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
	$author = filter_var($_POST['author'],FILTER_SANITIZE_STRING);
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	$pages = filter_var($_POST['pages'],FILTER_SANITIZE_STRING);
	$pub_year = filter_var($_POST['pubYear'],FILTER_SANITIZE_STRING);
	$publisher = filter_var($_POST['publisher'],FILTER_SANITIZE_STRING);
	$description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
	$image = filter_var($_POST['image'],FILTER_SANITIZE_URL);
	$reviews = filter_var($_POST['reviews'],FILTER_SANITIZE_STRING);
	$isbn = filter_var($_POST['isbn'],FILTER_SANITIZE_STRING);
	$details = filter_var($_POST['details'],FILTER_SANITIZE_STRING);
	$similar = filter_var($_POST['similar'],FILTER_SANITIZE_STRING);


	$book_array = array(
		'title' => $title,
		'author' => $author,
		'category' => $category,
		'pages' => $pages,
		'pub_year' => $pub_year,
		'publisher' => $publisher,
		'description' => $description,
		'image' => $image,
		'reviews' => $reviews,
		'isbn' => $isbn,
		'details' => $details,
		'similar_products' => $similar
	);

	require_once(CLASS_BOOK_DIR.'class-wpbooklist-book.php');
	$book_class = new WPBookList_Book('bookfinder-colorbox', $book_array, null);
	$category = $book_class->category;
	$itunes_page = $book_class->itunes_page;
	$kobo_link = $book_class->kobo_link;
	$bam_link = $book_class->bam_link;

	$book_array = array(
		'title' => $title,
		'author' => $author,
		'category' => $category,
		'pages' => $pages,
		'pub_year' => $pub_year,
		'publisher' => $publisher,
		'description' => $description,
		'image' => $image,
		'reviews' => $reviews,
		'isbn' => $isbn,
		'details' => $details,
		'category' => $category,
		'itunes_page' => $itunes_page,
		'similar_products' => $similar,
		'kobo_link' => $kobo_link,
		'bam_link' => $bam_link,

	);

	// Instantiate the class that shows the book in colorbox
	require_once(CLASS_DIR.'class-wpbooklist-show-book-in-colorbox.php');
	$colorbox = new WPBookList_Show_Book_In_Colorbox(null, null, $book_array, null);

	echo $colorbox->output;


	wp_die();
}

function wpbooklist_bookfinder_search_action_javascript() { 
	?>
  	<script type="text/javascript" >
  	"use strict";
  	jQuery(document).ready(function($) {

  		// The 'Check All' button
  		$(document).on("click","#wpbooklist-bookfinder-all-checked", function(event){
  			$('.wpbooklist-bookfinder-checkbox-div input').each(function(){
  				$(this).prop('checked', true);
  			});

  			$('#wpbooklist-bookfinder-add-checked').prop('disabled', false);

  			event.preventDefault ? event.preventDefault() : event.returnValue = false;
  		});

  		// The 'Uncheck All' button
  		$(document).on("click","#wpbooklist-bookfinder-add-uncheck", function(event){
  			$('.wpbooklist-bookfinder-checkbox-div input').each(function(){
  				$(this).prop('checked', false);
  			});

  			$('#wpbooklist-bookfinder-add-checked').prop('disabled', true);

  			event.preventDefault ? event.preventDefault() : event.returnValue = false;
  		});

  		// Enable 'Add Book' button when any checkbox is checked
  		$(document).on("click",".wpbooklist-bookfinder-checkbox-div input", function(event){
  			if($(this).prop('checked') == true){
  				$('#wpbooklist-bookfinder-add-checked').prop('disabled', false);
  			} else {
  				$('#wpbooklist-bookfinder-add-checked').prop('disabled', true);
  				$('.wpbooklist-bookfinder-checkbox-div input').each(function(){
  					if($(this).prop('checked') == true){
  						$('#wpbooklist-bookfinder-add-checked').prop('disabled', false);
  					}
  				});
  			}
  		});	


	  	$("#wpbooklist-bookfinder-search-button").click(function(event){

	  		var author = $('#wpbooklist-bookfinder-author-input').val();
	  		var title = $('#wpbooklist-bookfinder-title-input').val();

	  		if(title == 'Search by Title'){
	  			title = '';
	  		}

	  		if(author == 'Search by Author'){
	  			author = '';
	  		}

	  		$('#wpbooklist-spinner-bookfinder').animate({'opacity':'1'})
	  		$('#wpbooklist-bookfinder-results-div').animate({'opacity':'0'})
	  		$('#wpbooklist-bookfinder-results-div').html('');

	  		var data = {
				'action': 'wpbooklist_bookfinder_search_action',
				'security': '<?php echo wp_create_nonce( "wpbooklist_bookfinder_search_action_callback" ); ?>',
				'author':author,
				'title':title
			};

			//console.log(data);
			console.log(data)
			var request = $.ajax({
			    url: ajaxurl,
			    type: "POST",
			    data:data,
			    timeout: 0,
			    success: function(response) {

			    	response = response.split('--sep--seperator--sep--');
			    	console.log(response);
			    	var results = JSON.parse(response[0]);
			    //	var results2 = JSON.parse(response[3]);
			    //	var results3 = JSON.parse(response[4]);

			    	console.log('first array:'+response[0]);
			    	console.log('second array:'+response[3]);

			    	// If the Storefront extension is active...
			    	if(response[2] == 'true'){
			    		var html = '<div id="wpbooklist-bookfinder-results-container"><div id="wpbooklist-bookfinder-add-books-div"><button id="wpbooklist-bookfinder-all-checked">Check All</button><button id="wpbooklist-bookfinder-add-uncheck">Uncheck All</button><div id="wpbooklist-bookfinder-library-select"></div><div id="wpbooklist-bookfinder-page-post-container"><table><tr><td><p id="use-page-post-question-label">Create a Page and/or Post For Each Book?</p></td></tr><tr><td><input type="checkbox" id="bookfinder-create-post" name="bookfinder-create-post" /><label for="bookfinder-create-post">Create Posts</label><input type="checkbox" id="bookfinder-create-page" name="bookfinder-create-page" /><label for="bookfinder-create-page">Create Pages</label></td></tr><tr><td><input type="checkbox" name="bookfinder-create-woo" id="bookfinder-create-woo" /><label for="bookfinder-create-post">Create WooCommerce Products?</label></td></tr></table></div><button disabled id="wpbooklist-bookfinder-add-checked">Add Checked Books</button></div>';
			    	} else {
			    		var html = '<div id="wpbooklist-bookfinder-results-container"><div id="wpbooklist-bookfinder-add-books-div"><button id="wpbooklist-bookfinder-all-checked">Check All</button><button id="wpbooklist-bookfinder-add-uncheck">Uncheck All</button><div id="wpbooklist-bookfinder-library-select"></div><div id="wpbooklist-bookfinder-page-post-container"><table><tr><td><p id="use-page-post-question-label">Create a Page and/or Post For Each Book?</p></td></tr><tr><td><input type="checkbox" id="bookfinder-create-post" name="bookfinder-create-post" /><label for="bookfinder-create-post">Create Posts</label><input type="checkbox" id="bookfinder-create-page" name="bookfinder-create-page" /><label for="bookfinder-create-page">Create Pages</label></td></tr></table></div><button disabled id="wpbooklist-bookfinder-add-checked">Add Checked Books</button></div>';
			    	}

		    				
			    	if(results.Items == undefined || results.Items.Item == undefined || results.Items == undefined || results == undefined || results.Items.Item.length == undefined){
			    		$('#wpbooklist-bookfinder-results-div').html('<p>Whoops! We couldn\'t find any results for your Book Search! Be sure to check the Title and/or Author you provided and try again!</p>' );
				    	$('#wpbooklist-bookfinder-results-div').animate({'opacity':'1'});
				    	$('#wpbooklist-spinner-bookfinder').animate({'opacity':'0'});
			    	} else {
				    	//console.log(results.Items.Item);
				    	for (var i = 0; i < results.Items.Item.length; i++) {

				    		var image = '';
				    		var author = '';
				    		var category = '';
				    		var pages = '';
				    		var publisher = '';
				    		var pubdate = '';
				    		var title = '';
				    		var description = '';
				    		var isbn = '';
				    		var review = '';
				    		var similar = '';
				    		var image = '';
				    		var isbn = '';
				    		var details = '';
				    		var similarproductsstring = '';

				    		if(results.Items.Item[i] != undefined){

				    			if(results.Items.Item[i].SimilarProducts != undefined){
					    			if(results.Items.Item[i].SimilarProducts.SimilarProduct != undefined){
					    				for (var r = 0; r < results.Items.Item[i].SimilarProducts.SimilarProduct.length; r++) {
					    					similarproductsstring = similarproductsstring+';bsp;'+results.Items.Item[i].SimilarProducts.SimilarProduct[r].ASIN+'---'+results.Items.Item[i].SimilarProducts.SimilarProduct[r].Title;
					    				};
					    			}
				    			}

				    			//console.log(similarproductsstring);

				    			if(results.Items.Item[i].ASIN != undefined){
				    				isbn = results.Items.Item[i].ASIN;
				    			}

				    			if(results.Items.Item[i].CustomerReviews != undefined){
				    				if(results.Items.Item[i].CustomerReviews.IFrameURL != undefined){
				    					review = results.Items.Item[i].CustomerReviews.IFrameURL;
				    				}
				    			}

				    			if(results.Items.Item[i].DetailPageURL != undefined){
				    				details = results.Items.Item[i].DetailPageURL;
				    			}

				    			if(results.Items.Item[i].ItemAttributes != undefined){

					    			if(results.Items.Item[i].ItemAttributes.Author != undefined){
					    				author = results.Items.Item[i].ItemAttributes.Author;
					    			}

					    			if(results.Items.Item[i].ItemAttributes.EAN != undefined){
					    				isbn = results.Items.Item[i].ItemAttributes.EAN;
					    			}

					    			if(results.Items.Item[i].ItemAttributes.NumberOfPages != undefined){
					    				pages = results.Items.Item[i].ItemAttributes.NumberOfPages;
					    			}

					    			if(results.Items.Item[i].ItemAttributes.Publisher != undefined){
					    				publisher = results.Items.Item[i].ItemAttributes.Publisher;
					    			}

					    			if(results.Items.Item[i].ItemAttributes.PublicationDate != undefined){
					    				pubdate = results.Items.Item[i].ItemAttributes.PublicationDate;
					    			}

					    			if(results.Items.Item[i].ItemAttributes.Title != undefined){
					    				title = results.Items.Item[i].ItemAttributes.Title;
					    			}
				    			}

				    			if(results.Items.Item[i].LargeImage != undefined && results.Items.Item[i].LargeImage.URL != undefined){
				    				image = results.Items.Item[i].LargeImage.URL;
				    			} else {
				    				image = '';
				    			}

				    			if(results.Items.Item[i].EditorialReviews != undefined && results.Items.Item[i].EditorialReviews.EditorialReview != undefined && results.Items.Item[i].EditorialReviews.EditorialReview.Content){
				    				description = results.Items.Item[i].EditorialReviews.EditorialReview.Content;
				    				description = description.replace(/"/g,'\'');
				    				description = description.replace(/“/g,'\'');
				    			}

				    			if(description == ''){
				    				if(results.Items.Item[i].EditorialReviews != undefined && results.Items.Item[i].EditorialReviews.EditorialReview[0] != undefined){
					    				description = results.Items.Item[i].EditorialReviews.EditorialReview[0].Content;
					    				description = description.replace(/"/g,'\'');
					    				description = description.replace(/“/g,'\'');
				    				}
				    			}
				    		}
				    		if(image == null || image == undefined || image == ''){
				    			image = '<?php echo ROOT_IMG_URL; ?>image_unavaliable.png';
				    		}


				    		html = html+'<div class="wpbooklist-bookfinder-indiv-holder"><div data-similar="'+similarproductsstring+'" data-details="'+details+'" data-isbn="'+isbn+'" data-title="'+title+'" data-author="'+author+'" data-category="'+category+'" data-pages="'+pages+'" data-publisher="'+publisher+'" data-pubdate="'+pubdate+'" data-description="'+description+'" data-isbn="'+isbn+'" data-review="'+review+'" data-similar="'+similar+'"   class="wpbooklist-bookfinder-results-indiv"><img class="wpbooklist-bookfinder-colorbox" src="'+image+'" /><p class="wpbooklist_saved_title_link" id="wpbooklist_saved_title_link">'+title+'</p><div class="wpbooklist-bookfinder-checkbox-div"><label>Add Book</label><input type="checkbox" /></div></div></div>';
				    	};
					
				    	html = html+'</div>';
				    	$('#wpbooklist-bookfinder-results-div').html(html);
				    	$('#wpbooklist-bookfinder-library-select').html(response[1]);
				    	$('#wpbooklist-bookfinder-results-div').animate({'opacity':'1'})
				    	$('#wpbooklist-spinner-bookfinder').animate({'opacity':'0'})
			    	}
			    }
			});


			event.preventDefault ? event.preventDefault() : event.returnValue = false;
	  	});
	});
	</script>
	<?php
}

// Callback function for creating backups
function wpbooklist_bookfinder_search_action_callback(){
	global $wpdb;
	check_ajax_referer( 'wpbooklist_bookfinder_search_action_callback', 'security' );

	$author = filter_var($_POST['author'],FILTER_SANITIZE_STRING);
	$title = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
	$isbn = filter_var($_POST['isbn'],FILTER_SANITIZE_STRING);
	$storefront == 'false';
	$insert_result = array();
	$insert_result2 = array();
	$insert_result3 = array();

	require_once(CLASS_BOOK_DIR.'class-wpbooklist-book.php');

	for ($i=1; $i < 2; $i++) { 
		$book_array = array(
			'use_amazon_yes' => 'true',
			'amazonauth' => 'true',
			'title' => $title,
			'author' => $author,
			'isbn' => $isbn,
			'book_page'=> $i
		);

		$book_class = new WPBookList_Book('search', $book_array, null);

		if($i == 1){
			$insert_result = $book_class->amazon_array;
		}

		if($i == 2){
			$insert_result2 = $book_class->amazon_array;
		}

		if($i == 3){
			$insert_result3 = $book_class->amazon_array;
		}
	}

	$insert_result = array_merge($insert_result2, $insert_result);

	$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
	$db_row = $wpdb->get_results("SELECT * FROM $table_name");

	$string = '<p>Select a Library to Add These Books To:</p><select class="wpbooklist-addbook-select-default" id="wpbooklist-bookfinder-select-library">
		    			 <option value="'.$wpdb->prefix.'wpbooklist_jre_saved_book_log">'.__('Default Library','wpbooklist').'</option> ';

	foreach($db_row as $db){
		if(($db->user_table_name != "") || ($db->user_table_name != null)){
			$string = $string.'<option value="'.$wpdb->prefix.'wpbooklist_jre_'.$db->user_table_name.'">'.ucfirst($db->user_table_name).'</option>';
		}
	}

	$string = $string.'</select>';

	// Check to see if Storefront extension is active
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(is_plugin_active('wpbooklist-storefront/wpbooklist-storefront.php')){

		$storefront = 'true';
	}

	echo json_encode($insert_result).'--sep--seperator--sep--'.$string.'--sep--seperator--sep--'.$storefront.'--sep--seperator--sep--'.json_encode($insert_result2).'--sep--seperator--sep--'.json_encode($insert_result3);

	// Require the Transients file.
	require_once ROOT_WPBL_TRANSIENTS_DIR . 'class-wpbooklist-transients.php';
	$transients = new WPBookList_Transients();
	$transients->delete_all_wpbl_transients();

	wp_die();
}
?>
