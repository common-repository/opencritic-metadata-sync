"use strict";
jQuery( document ).ready( function( $ ) {	
	$( document ).on( 'change', '#enable_review_data_to_open_critic', function() {	

		$('.enable_if_review_data').hide();
		if ($(this).is(':checked')) {
			$('.enable_if_review_data').show();
		}
	});		
	$("#enable_review_data_to_open_critic").trigger('change');


	$( document ).on( 'change', '#GameMissing', function() {	

		$("#GameMissingTitle").parent().hide();

		if ($(this).is(':checked')) {
			$("#GameMissingTitle").parent().show();
		}		

	});		
	$("#GameMissing").trigger('change');





	// simple multiple select
	$('#game_reviewed').select2();
	$('#platforms_reviewed').select2();

	$("#platforms_reviewed").change(function() {
    	var ids = $("#platforms_reviewed").val(); // works  
    	$('#selectedText').text(ids.length);
	});
 	

	// multiple select with AJAX search
	$('#game_reviewed').select2({
  		ajax: {
    			url: WpOcAdmin.ajaxurl, // AJAX URL is predefined in WordPress admin
    			dataType: 'json',
    			delay: 250, // delay in ms while typing when to perform a AJAX search
    			data: function (params) {
      				return {
        				q: params.term, // search query
        				action: 'wp_opencritic_search_open_critic_game' // AJAX action for admin-ajax.php
      				};
    			},
    			processResults: function( data ) {
				var options = [];
				if ( data ) {
 
					// data is the array of arrays, and each of them contains ID and the Label of the option
					$.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
						options.push( { id: text[0], text: text[1]  } );
					});
 
				}
				return {
					results: options
				};
			},
			cache: false
		},
		minimumInputLength: 3 // the minimum of symbols to input before perform a search
	});


	
	$( document ).on( 'change', '#score_format', function() {

		var select = $(this).val();
		var score_format_type =  $(this).find(':selected').attr('data-score-format-type');
		
		$("#score_numeric").parent().hide();
		$("#score_verdict").parent().hide();
		$("#no_score_numeric").hide();
		$("#score_format_type").val()
		
		if($("#enable_review_data_to_open_critic").is(":checked")){
			$("#no_score_numeric").parent().show();
			
		}
		if(score_format_type == 'numeric'){
			$("#score_numeric").parent().show();
			$("#score_verdict").parent().hide();
			$("#no_score_numeric").hide();
		
		}
		if(score_format_type == 'no-verdict'){
			$("#no_score_numeric").show();
		}
		if(score_format_type == 'verdict'){
			
			var html = '';oc_game_details
			$("#score_numeric").parent().hide();
			$("#score_verdict").parent().show();
			$("#no_score_numeric").hide();

			 var ajaxData = {
			    'action': 'wp_opencritic_search_verdict_option_by_id',
			    'verdict_id': select
			  }

			  $('#score_verdict').html('<option>Loading..</option');
			  jQuery.post(ajaxurl, ajaxData, function(response){
			    
			    var response_obj = JSON.parse(response);		    

			    $.each(response_obj, function(i, item) {				    
				    html+= '<option value="'+response_obj[i].id+'">'+response_obj[i].label+'</option>';
				});
			    $('#score_verdict').html(html);
			});
		}
	});
	$("#score_format").trigger('change');


	$( document ).on( 'click', '.editor-post-publish-button', function() {
		wp_opencritic_render_meta_box_error();
	});
	
	$( document ).on( 'click', '#publish', function(event) {

		var author 			= $('#author').val();
		var review_quote 	= $('#review_quote').val();
		var score_verdict 	= $('#score_verdict').val();
		var score_numeric 	= $('#score_numeric').val();		

		if($("#enable_review_data_to_open_critic").is(":checked")){
			if(  score_verdict == '' || score_verdict == '' || review_quote == '' || author ==''){
				wp_opencritic_render_meta_box_error();
				event.preventDefault();
			}
		}
	});
	
	function wp_opencritic_render_meta_box_error(){

		if($("#enable_review_data_to_open_critic").is(":checked")){	
		
			var author_msg 			= '';
			var score_verdict_msg 	= '';		
			var review_quote_msg 	= '';
			var score_numeric_msg 	= '';
				
			var format_type = $("#score_format").find(':selected').attr('data-score-format-type');
			
			$('p.error').hide();
			
			var author = $('#author').val();
			if(author == ''){
				 author_msg = '<p class="error">Author is required</span>';
				  $(author_msg).insertAfter("#author");
			}

			var review_quote = $('#review_quote').val();
			if(review_quote == ''){
				review_quote_msg = '<p class="error">Review Quote is required</span>';
				$(review_quote_msg).insertAfter("#review_quote");
			}

			if(format_type == 'verdict'){
				var score_verdict = $('#score_verdict').val();
				if(score_verdict == ''){
					score_verdict_msg = '<p class="error">Score Format is required</span>';
					$(score_verdict_msg).insertAfter("#score_verdict");
				}
			}

			if(format_type == 'numeric'){
				var score_numeric = $('#score_numeric').val();
				if(score_numeric == ''){
					score_numeric_msg = '<p class="error">Score is required</span>';
					$(score_numeric_msg).insertAfter("#score_numeric");
				}
			}
		}
	}
	 
});