<?php

drupal_add_html_head(array('#tag' => 'meta', '#attributes' => array('name' => 'viewport', 'content' => 'width=520')), 'meta-viewpoint');

/**
 * Implements template_preprocess_comment_wrapper()
 */
 function thechurch_preprocess_comment_wrapper(&$variables) {
 	$variables['content']['comment_form']['actions']['submit']['#value'] = 'Share';
 	$variables['content']['comment_form']['comment_body']['und'][0]['value']['#title'] = null;
 	$variables['content']['comment_form']['comment_body']['und'][0]['value']['#resizable'] = 0;
 	unset($variables['content']['comment_form']['author']['_author']);
 	unset($variables['content']['comment_form']['comment_body']['und'][0]['format']);
		
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
	
}
