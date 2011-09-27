<?php

drupal_add_html_head(
	array(
		'#tag' => 'meta',
		'#attributes' => array(
			'name' => 'viewport',
			'content' => 'width=device-width, initial-scale=1.0, maximum-scale=1.0'
			),
		),
		'meta-viewpoint'
);

/**
 * Implements template_preprocess_comment_wrapper()
 */
 function thechurch_preprocess_comment_wrapper(&$variables) {
 	
 	if (!empty($variables['content']['comment_form'])) {
	 	$variables['content']['comment_form']['actions']['submit']['#value'] = 'Share';
	 	$variables['content']['comment_form']['comment_body']['und'][0]['value']['#title'] = null;
	 	$variables['content']['comment_form']['comment_body']['und'][0]['value']['#resizable'] = 0;
	 	unset($variables['content']['comment_form']['author']['_author']);
	 	unset($variables['content']['comment_form']['comment_body']['und'][0]['format']);
	}
		
 }
 
 /**
 * Implements template_preprocess_username()
 */
function thechurch_preprocess_username(&$variables) {
	
	$user = user_load($variables['uid']);
	
	$fullname = isset($user->field_fullname['und'][0]['safe_value']) ? $user->field_fullname['und'][0]['safe_value'] : null;
	
	$variables['name'] = $fullname ? $fullname : $variables['name'];
	
}

/**
 * Implements template_preprocess_taxonomy_term()
 */
function thechurch_preprocess_taxonomy_term(&$variables) {

	if ($variables['vocabulary_machine_name'] == 'city') {
		module_load_include('inc', 'node', 'node.pages');
		$variables['content']['post_form'] = node_add('post');
		unset($variables['content']['post_form']['body']['und'][0]['format']);
	}
	
	// Fix the Title
	if (isset($variables['name'])) {
		drupal_set_title(' '.strtolower($variables['name']));
	}
	
}

/**
 * Implements template_preprocess_node()
 */
function thechurch_preprocess_node(&$variables) {

	// Fix the Title
	if ($variables['page'] && isset($variables['title'])) {
		if ($variables['is_front']) {
			drupal_set_title('');
		} else {
			drupal_set_title(' | '.$variables['title']);
		}
	}
	
}

/**
 * Implements template_preprocess_html()
 */
function thechurch_preprocess_html(&$variables) {

	if (isset($variables['head_title_array'])) {
		if (isset($variables['head_title_array']['title'])) {
			$variables['head_title'] = $variables['head_title_array']['name'].$variables['head_title_array']['title'];
		} else {
			$variables['head_title'] = $variables['head_title_array']['name'];
		}
	}
	
}

/**
 * Implements template_preprocess_user_profile()
 */
function thechurch_preprocess_user_profile(&$variables) {
	
	$account = isset($variables['elements']['#account']) ? $variables['elements']['#account'] : null;

	// Fix the Title
	if (isset($account->field_fullname['und'][0]['safe_value'])) {
		drupal_set_title(' | '.$account->field_fullname['und'][0]['value']);
	}
	
}

/**
 * Implements template_preprocess_comment()
 */
function thechurch_preprocess_comment(&$variables) {
	
	// Remove the Reply link
	unset($variables['content']['links']['comment']['#links']['comment-reply']);
	
	// Remove the Comment Forbidden link
	unset($variables['content']['links']['comment']['#links']['comment_forbidden']);


}
