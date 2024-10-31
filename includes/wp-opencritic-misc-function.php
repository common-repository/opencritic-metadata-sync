<?php
function wp_opencritic_get_platforms(){

	$response = wp_remote_get('https://api.opencritic.com/api/platform');
	return json_decode( wp_remote_retrieve_body($response));

}

function wp_opencritic_get_score_formats(){

	$response = wp_remote_get('https://api.opencritic.com/api/score-format');
	return json_decode( wp_remote_retrieve_body($response));
}

function wp_opencritic_get_verdict_options_by_id($verdict_id){

	$response = wp_remote_get('https://api.opencritic.com/api/score-format');
	$ScoreFormat = json_decode( wp_remote_retrieve_body($response));
	$result = array();
	foreach ($ScoreFormat as $key => $Score) {
		if($Score->id == $verdict_id){
			$result = $Score;
		}
	}
	return $result;
}

