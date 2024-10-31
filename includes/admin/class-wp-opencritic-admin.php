<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Wp_Oc_Admin{	


	/**
	 * Register meta box(es).
	 */
	public function wp_opencritic_register_meta_boxe_for_open_critic() {
    	add_meta_box('wp-opencritic-open-critic',esc_html__( 'OpenCritic Review Data', 'opencritic' ),array( $this, 'wp_opencritic_render_metabox' ),'post','side','default');
	}


  	/* Renders the meta box.
     */
    public function wp_opencritic_render_metabox( $post ) {

        $default_score_format =  get_option('wp_opencritic_default_score_format');
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );

        $post_id        = isset($_GET['post'])?$_GET['post']:'';

        $enable_review_data_to_open_critic  = get_post_meta($post_id,'enable_review_data_to_open_critic',true);
        $game_reviewed      = get_post_meta($post_id,'game_reviewed',true);
        $author             = get_post_meta($post_id,'author',true);
        $review_quote       = get_post_meta($post_id,'review_quote',true);
        $GameMissingTitle   = get_post_meta($post_id,'GameMissingTitle',true);
        $GameMissing        = get_post_meta($post_id,'GameMissing',true);
        $score_format       = get_post_meta($post_id,'score_format',true);
        $score_numeric      = get_post_meta($post_id,'score_numeric',true);        
        $recommended        = get_post_meta($post_id,'recommended',true);        
        $platforms_reviewed = !empty(get_post_meta($post_id,'platforms_reviewed',true))?get_post_meta($post_id,'platforms_reviewed',true):array();      
        
		$oc_game_details = !empty(get_post_meta($post_id,'oc_game_details',true))?get_post_meta($post_id,'oc_game_details',true):array();      
        $wp_opencritic_api_key = get_option('wp_opencritic_api_key');

        if (!isset($wp_opencritic_api_key) || empty($wp_opencritic_api_key)) {
            ?>
            <p>
                You must input your API key in the plugin settings in order to use this plugin.
            </p>
            <?php
            return;
        }
        ?>
        <p class="wp-opencritic-meta-group">
        	<input type="checkbox" <?php echo ($enable_review_data_to_open_critic == 'on')?'checked="checked"':'' ?> name="enable_review_data_to_open_critic" id="enable_review_data_to_open_critic">
        	<label for="enable_review_data_to_open_critic"><?php esc_html_e('Send Review Metadata to OpenCritic?','opencritic' )?></label>
        	<span class="description"><?php esc_html_e('Your review will only appear on OpenCritic when this post is published','opencritic') ?></span>
        </p>
        <div class="enable_if_review_data">
            <p class="wp-opencritic-meta-group">
            	<label for="game_reviewed"><?php esc_html_e('Game Reviewed','opencritic' )?></label>
            	<select name="game_reviewed" id="game_reviewed">
				<?php 
				if(!empty($oc_game_details)){?>
					<option value="<?php echo $oc_game_details->id ?>" selected="selected"><?php echo $oc_game_details->name ?></option>	
				<?php } ?>
				
				</select>
            </p>
            <p class="wp-opencritic-meta-group">
                <input type="checkbox" <?php echo ($GameMissing == 'on')?'checked="checked"':'' ?>name="GameMissing" id="GameMissing">
                <label for="GameMissing" ><?php esc_html_e('My game is not yet on OpenCrit','opencritic' )?></label>            
            </p>
            <p class="wp-opencritic-meta-group">
                <label for="GameMissingTitle"><?php esc_html_e('Game Missing Title','opencritic' )?></label>
                <input type="text" name="GameMissingTitle" value="<?php echo $GameMissingTitle ?>" id="GameMissingTitle">
            </p>
            <p class="wp-opencritic-meta-group">
                <label for="author"><?php esc_html_e('Author','opencritic' )?></label>
                <input type="text" value="<?php echo $author; ?>" name="author" id="opencritic_author">
                
            </p>
             <p class="wp-opencritic-meta-group">
                <label for="review_quote"><?php esc_html_e('Review Quote (in English)','opencritic' )?></label>
                <textarea name="review_quote" cols="25" id="review_quote"><?php echo $review_quote ?></textarea>                
            </p>
             <p class="wp-opencritic-meta-group">
                <label for="platforms_reviewed"><?php echo sprintf(esc_html__('Platform(s) Reviewed (%s selected)','opencritic' ),' <span id="selectedText">0</span>')?></label>
               
                <select name="platforms_reviewed[]" id="platforms_reviewed" multiple="multiple">
                    <?php 
                    $platform_list = wp_opencritic_get_platforms();
                    foreach ($platform_list as $key => $platform) { ?>
                        <option value="<?php echo $platform->id ?>" <?php echo (in_array($platform->id, $platforms_reviewed))?'selected="selected"':'' ?>><?php echo $platform->name; ?></option>
                    <?php }  ?>
                </select>
            </p>
             <p class="wp-opencritic-meta-group">
                <label for="score_format"><?php esc_html_e('Score Format','opencritic' )?></label>               
                <select name="score_format" id="score_format">
                    <?php 
                    $Scrore_formats = wp_opencritic_get_score_formats();
                    $score_format_echo_type = '';
                    foreach ($Scrore_formats as $key => $Scrore_format) {
                        if($Scrore_format->isSelect){
                            $Scrore_format_type = 'verdict';
                        }elseif($Scrore_format->isNumeric){
                            $Scrore_format_type = 'numeric';
                        }else{
                            $Scrore_format_type = 'no-verdict';
                        } 
                        $selected_score_format = '';

                        if( !empty($score_format) &&  $Scrore_format->id == $score_format)
                        {                            
                           $selected_score_format =  'selected="selected"';
                           $score_format_echo_type = $Scrore_format_type;
                        }
                        elseif(empty($score_format) && $Scrore_format->id == $default_score_format){
                            $selected_score_format =  'selected="selected"';
                            $score_format_echo_type = $Scrore_format_type;
                        }
                    ?>
                        <option <?php echo $selected_score_format ?> data-score-format-type="<?php echo $Scrore_format_type ?>" value="<?php echo $Scrore_format->id ?>"><?php echo $Scrore_format->name; ?></option>
                    <?php }  ?>
                </select>
            </p>
            <input type="hidden" value="<?php echo $score_format_echo_type ?>" name="score_format_type" id="score_format_type" />
             <p class="wp-opencritic-meta-group">
                <label for="score_numeric"><?php esc_html_e('Score (normalized for 0-100)','opencritic' )?></label>
                <input type="number" min="1" value="<?php echo $score_numeric ?>" max="100" name="score_numeric"  id="score_numeric"></textarea>
            </p>

            <p class="wp-opencritic-meta-group">
                <label for="score_verdict"><?php esc_html_e('Verdict','opencritic' )?></label>                
                <select name="score_verdict" id="score_verdict">
                </select>
            </p>
            <p id="no_score_numeric">
                No Verdict 

            </p>
            <?php  if($this->get_recommend_game_status()){?>

            <p class="wp-opencritic-meta-group">
                <input type="checkbox" name="recommended" <?php echo ($recommended == 'on')?'checked="checked"':'' ?> id="recommended">
                <label for="recommended"><?php esc_html_e('Game is Recommended?','opencritic' )?></label> 
                <span class="description"><?php esc_html_e('Do you recommend that general gamers play this game instead of most other games releasing around the same time?','opencritic') ?></span> 
            </p>
        <?php } ?>
        </div>
        <?php
    }


    // AJAX for search game
    public function  wp_opencritic_search_open_critic_game(){    

        $game_list = array();

        if(!empty($_REQUEST['q'])){
           
            $response = wp_remote_get('https://api.opencritic.com/api/game/search?criteria='.urlEncode($_REQUEST['q']));
            $GameList = wp_remote_retrieve_body($response);
            
            if(!empty($GameList)){
               $GameList =  json_decode($GameList);
                foreach ($GameList as $key => $game) {
                    $game_list[]  = array($game->id,$game->name);
                }
            }                      
           echo json_encode($game_list );
           die();
        }
    }
 
    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function wp_opencritic_save_metabox( $post_id, $post ) {
        
        $postdata = get_post($post_id);
        $url = wp_get_canonical_url($post_id);
        $status         = isset($post->post_status)? sanitize_text_field($post->post_status):'';        
        $snippet        = !empty($_POST['review_quote'])?sanitize_text_field($_POST['review_quote']):'';
        $Platforms      = !empty($_POST['platforms_reviewed'])?$_POST['platforms_reviewed']:array();
        $Author         = !empty($_POST['author'])?sanitize_text_field($_POST['author']):'';
        $game_reviewed  = !empty($_POST['game_reviewed'])?intval($_POST['game_reviewed']):'';
        $GameMissing    = !empty($_POST['GameMissing'])?true:false;
        $GameMissingTitle = !empty($_POST['GameMissingTitle'])?sanitize_text_field($_POST['GameMissingTitle']):'';
        $score_format   = !empty($_POST['score_format'])?intval($_POST['score_format']):'';
        $score_format_type = !empty($_POST['score_format_type'])?sanitize_text_field($_POST['score_format_type']):'';
        $recommended    = !empty($_POST['recommended'])?true:false;
        $enable_review_data_to_open_critic = !empty($_POST['enable_review_data_to_open_critic'])?sanitize_text_field($_POST['enable_review_data_to_open_critic']):'';

        if($score_format_type == 'numeric' && isset($_POST['score_numeric']) && !empty($_POST['score_numeric'])){
            $score = !empty($_POST['score_numeric'])?intval($_POST['score_numeric']):'';    
        }elseif ($score_format_type == 'verdict' && isset($_POST['score_verdict']) && !empty($_POST['score_verdict'])){
            $score = !empty($_POST['score_verdict'])?intval($_POST['score_verdict']):'';    
        }

        $int_platforms = array();
        foreach ($Platforms as $key => $Platform) {
            settype($Platform,'int');
            $int_platforms[] = $Platform;
        }
        $platforms_sanitized = array();
        foreach ($Platforms as $value) {
            array_push($platforms_sanitized, intval($value));
        }
      
        if($status == 'publish'){
            $actual_status = 'LIVE';
        }
        elseif($status == 'draft'){
            $actual_status = 'DRAFT';
        }
        elseif($status == 'future'){
            $actual_status = 'SCHEDULED';
        }

        $datetime  = new DateTime($postdata->post_date);
        $post_date =  $datetime->format(DateTime::ATOM);
        
        // $actual_status = 'DRAFT';

        if( !empty($Author) && !empty($snippet) && !empty($score_format) ){           

            if($enable_review_data_to_open_critic == 'on'){
              
                
                $submission_body = array(
                    'status'        => $actual_status,
                    'snippet'       => $snippet,
                    'Platforms'     => $int_platforms,
                    'Author'        => $Author,
                    'GameId'        => $game_reviewed,
                    'GameMissing'   => $GameMissing,
                    'GameMissingTitle'   => $GameMissingTitle,
                    'ScoreFormatId'      => $score_format,
                    'recommended'        => $recommended,
                    'score'              => $score,
                    'publishedDate'      => $post_date,
                    'title'              => $postdata->post_title,
                    'key'                => get_option('wp_opencritic_api_key'),
                );
                if ( $url ) {
                    $submission_body['externalUrl'] = $url;
                }
                $SubmissionId  = get_post_meta($post_id,'SubmissionId',true);
                if(!empty($SubmissionId)){
                    $submission_body['SubmissionId'] = $SubmissionId;
                }
                $result = wp_remote_post('https://portal.opencritic.com/api/review/staged', array(
                    'headers' => array(
                        'Content-Type' => 'application/json'
                    ),
                    'body' => json_encode($submission_body)
                ));
                if (wp_remote_retrieve_response_code($result) != 200) {
                    $updateSubmission = false;
                } else {
                    $updateSubmission = true;
                    $PostResult = json_decode($result['body']);
                }
                
				
                if($updateSubmission && isset($PostResult->SubmissionId)) {
                   update_post_meta($post_id,'SubmissionId',sanitize_text_field($PostResult->SubmissionId));
                   $game_data = isset($PostResult->game)?$PostResult->game:'';
                    $game_data_to_save = new stdClass();
                    if(isset($game_data->name)){               
                        $game_data_to_save->name = sanitize_text_field($game_data->name);
                    }
                    if(isset($game_data->id)){               
                        $game_data_to_save->id = intval($game_data->id);
                    }
                    update_post_meta($post_id,'oc_game_details',$game_data_to_save);
                } elseif (isset($game_reviewed)) {
                    $game_data_to_save = new stdClass();
                    $game_data_to_save->name = 'Unknown Game Title';
                    $game_data_to_save->id = $game_reviewed;
                    update_post_meta($post_id,'oc_game_details',$game_data_to_save);
                } else {
                    delete_post_meta($post_id,'oc_game_details');
                }
                update_post_meta($post_id,'original_post_status',$post->post_status);
                update_post_meta($post_id,'review_quote',$snippet);
                update_post_meta($post_id,'platforms_reviewed',$platforms_sanitized); // Sanitized line 190
                update_post_meta($post_id,'author',$Author);
                update_post_meta($post_id,'game_reviewed',$game_reviewed);
                update_post_meta($post_id,'GameMissing',$GameMissing?'on':'');
                update_post_meta($post_id,'GameMissingTitle',$GameMissingTitle);
                update_post_meta($post_id,'score_format',$score_format);
                update_post_meta($post_id,'recommended',$recommended?'on':'');
                update_post_meta($post_id,'score_format_type',$score_format_type);
                update_post_meta($post_id,'enable_review_data_to_open_critic',$enable_review_data_to_open_critic);
                
                

                if($score_format_type == 'numeric' && isset($_POST['score_numeric']) && !empty($_POST['score_numeric'])){                    
                    update_post_meta($post_id,'score_numeric',$score);
                    delete_post_meta($post_id,'score_verdict');
                } elseif($score_format_type == 'verdict' && isset($_POST['score_verdict']) && !empty($_POST['score_verdict'])){
                    update_post_meta($post_id,'score_verdict',$score);
                    delete_post_meta($post_id,'score_numeric');
                } elseif($score_format_type == 'no-verdict') {
                    delete_post_meta($post_id,'score_verdict');
                    delete_post_meta($post_id,'score_numeric');
                }   
            }        
        }
    }
	
    public function wp_opencritic_search_verdict_option_by_id(){

        $verdict_options = array();
        if(!empty($_POST['verdict_id'])){
            $verdict_options = wp_opencritic_get_verdict_options_by_id($_POST['verdict_id']);
        }        

        $responce = array();
        if(isset($verdict_options->options) && !empty($verdict_options->options)){
            foreach ($verdict_options->options as $key => $option) {               
               $responce[] = array('id'=>$option->val, 'label' => $option->label);
            }
        }
        echo json_encode($responce);
        die();
    }

    public function wp_opencritic_register_menu_page() {
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		if(current_user_can('manage_options')) {
			add_menu_page('OpenCritic','OpenCritic','manage_options','oc-settings',array($this,'wp_opencritic_render_setting_page'));
		}
    }

    public function wp_opencritic_render_setting_page(){

        $wp_opencritic_default_score_format = get_option('wp_opencritic_default_score_format');
        $wp_opencritic_api_key = get_option('wp_opencritic_api_key');
        $score_formats = wp_opencritic_get_score_formats();
        
        ?>

        <div class="wrap">
            <h1>Open Critic Settings</h1>

            <form method="post" action="" >  
                <?php wp_nonce_field('save_settings_action', 'save_settings_nonce'); ?>
                   <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="blogname">API Key</label></th>
                            <td><input name="wp_opencritic_api_key" value="<?php echo $wp_opencritic_api_key ?>" type="text" id="wp_opencritic_api_key" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="blogname">Default Score Format</label></th>
                            <td>
                                <select name="wp_opencritic_default_score_format">
                                <?php 
                                    
                                 if(!empty($score_formats) && is_array($score_formats)){
                                    ?>
                                    <option>-- Select Default Score Format  -- </option>
                                    <?php
                                    foreach ($score_formats as $key => $score_format) {
                                       ?>
                                       <option <?php echo ($score_format->id == $wp_opencritic_default_score_format) ?'selected="selected"':'' ?>value="<?php echo $score_format->id ?>"><?php echo $score_format->name ?></option>
                                       <?php
                                    }
                                 }
                                 ?>
                                 </select>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit"><input type="submit" name="wp_opencritic_save_settings" id="submit" class="button button-primary" value="Save Changes"></p>
            </form>
        </div>
        <?php 
    }

    public function wp_opencritic_save_settings(){
        if(current_user_can('manage_options') && isset($_POST['wp_opencritic_save_settings'])){
            if (!isset( $_POST['save_settings_nonce'] ) || !wp_verify_nonce( $_POST['save_settings_nonce'], 'save_settings_action')) {
                print('Failed security check');
                exit;
            } else {
        
                $wp_opencritic_api_key = isset($_POST['wp_opencritic_api_key'])?sanitize_text_field($_POST['wp_opencritic_api_key']):'';
                $wp_opencritic_default_score_format = isset($_POST['wp_opencritic_default_score_format'])?intval($_POST['wp_opencritic_default_score_format']):'';

                update_option('wp_opencritic_default_score_format',$wp_opencritic_default_score_format);
                update_option('wp_opencritic_api_key',$wp_opencritic_api_key);
            }
        }
    }


    public function get_recommend_game_status(){
       
       $needsRecommendation = false;


       $site_key =  get_option('wp_opencritic_api_key');


        $response = wp_remote_get("https://portal.opencritic.com/api/outlet/external?key=".$site_key);
        if (wp_remote_retrieve_response_code($response) != 200) {
            return true;
        }
	    $data = json_decode( wp_remote_retrieve_body($response));
        if(isset($data->needsRecommendation) && $data->needsRecommendation){
            $needsRecommendation = $data->needsRecommendation;
        }
        
        return $needsRecommendation;
      
    }


	public function wp_opencritic_update_feature_status_post($post_id){
	//public function wp_opencritic_update_feature_status_post(){
		
		//	$post_id = '443';
		$postdata = get_post($post_id);    
		 
		get_post_meta($post_id,'SubmissionId',true);                
		get_post_meta($post_id,'original_post_status',true);  
		$snippet = sanitize_text_field(get_post_meta($post_id,'review_quote',true));  
		$Platforms = get_post_meta($post_id,'platforms_reviewed',true);  
		$Author = get_post_meta($post_id,'author',true);  
		$game_reviewed = get_post_meta($post_id,'game_reviewed',true);  
		$GameMissing = get_post_meta($post_id,'GameMissing',true);  
		$GameMissingTitle = get_post_meta($post_id,'GameMissingTitle',true);  
		$score_format = get_post_meta($post_id,'score_format',true);  
        $recommended = get_post_meta($post_id,'recommended',true);  
        $score_format_type = get_post_meta($post_id, 'score_format_type', true);
		
		$is_recommended = false;
		if($recommended  == 'on'){
			$is_recommended = true;
		}
		if($score_format_type =='numeric' && !empty( get_post_meta($post_id,'score_numeric',true) ) ){                    
            $score = get_post_meta($post_id,'score_numeric',true);
		}
		elseif ($score_format_type =='verdict') {
			$score = get_post_meta($post_id,'score_verdict',true);
		}
		
		$enable_review_data_to_open_critic = get_post_meta($post_id,'enable_review_data_to_open_critic',true);  
		
		$int_platforms = array();
        foreach ($Platforms as $key => $Platform) {
            settype($Platform,'int');
            $int_platforms[] = $Platform;
        }
		$externalUrl = wp_get_canonical_url($post_id);;       
		$datetime  = new DateTime($postdata->post_date);
        $post_date =  $datetime->format(DateTime::ATOM);
		
		if( !empty($Author) && !empty($snippet) && !empty($score_format) ){           

            if(isset($_POST['enable_review_data_to_open_critic']) && $_POST['enable_review_data_to_open_critic'] == 'on'){
              
                
                $submission_body = array(
                    'status'        => 'LIVE',
                    'externalUrl'   => $externalUrl,
                    'snippet'       => $snippet,
                    'Platforms'     => $int_platforms,
                    'Author'        => $Author,
                    'GameId'        => (int)$game_reviewed,
                    'GameMissing'   => $GameMissing,
                    'GameMissingTitle'   => $GameMissingTitle,
                    'ScoreFormatId'      => (int)$score_format,
                    'recommended'        => $recommended,
                    'score'              => (int)$score,
                    'publishedDate'      => $post_date,
                    'title'              => $postdata->post_title,
                    'key'                => get_option('wp_opencritic_api_key'),
                );
                $SubmissionId  = get_post_meta($post_id,'SubmissionId',true);
                if(!empty($SubmissionId)){
                    $submission_body['SubmissionId'] = $SubmissionId;
                }
                $result = wp_remote_post('https://portal.opencritic.com/api/review/staged', array(
                    'headers' => array(
                        'Content-Type' => 'application/json'
                    ),
                    'body' => json_encode($submission_body)
                ));
                if (wp_remote_retrieve_response_code($result) != 200) {
                    return true;
                }
                $PostResult = json_decode($result['body']);

                $platforms_sanitized = array();
                foreach ($Platforms as $value) {
                    array_push($platforms_sanitized, intval($value));
                }
				
                if(isset($PostResult->SubmissionId)) {
                   update_post_meta($post_id,'SubmissionId',sanitize_text_field($PostResult->SubmissionId));                
                   update_post_meta($post_id,'original_post_status',$post->post_status);
                }               
            }
        }
	}
    
	public function add_hooks(){
		
        add_action( 'add_meta_boxes', array($this,'wp_opencritic_register_meta_boxe_for_open_critic'));		
        add_action( 'save_post', array( $this, 'wp_opencritic_save_metabox' ), 10, 2 );	

        add_action('wp_ajax_wp_opencritic_search_open_critic_game',array($this,'wp_opencritic_search_open_critic_game'));
        add_action('wp_ajax_wp_opencritic_search_verdict_option_by_id',array($this,'wp_opencritic_search_verdict_option_by_id'));

        add_action( 'admin_menu', array($this,'wp_opencritic_register_menu_page' ));
        add_action('init',array($this,'wp_opencritic_save_settings'));       
        add_action('init',array($this,'get_recommend_game_status'),999);       


		add_action( 'publish_future_post', array($this,'wp_opencritic_update_feature_status_post'),10,1 );
		//add_action( 'init', array($this,'wp_opencritic_update_feature_status_post'),10,1 );

        
	}
}

// if ( ! function_exists('write_log')) {
//     function write_log ( $log )  {
//        if ( is_array( $log ) || is_object( $log ) ) {
//           error_log( print_r( $log, true ) );
//        } else {
//           error_log( $log );
//        }
//     }
//  }