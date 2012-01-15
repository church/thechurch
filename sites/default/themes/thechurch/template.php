<?php

drupal_add_js('misc/ajax.js', 'file');

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
 	$node = $variables['content']['#node'];
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
	
	global $user;
	// drupal_set_message('<pre>'.print_r($variables, true).'</pre>');
	unset($variables['content']['links']['node']);
	unset($variables['content']['links']['comment']);
	
	$variables['content']['links'] = array(
		'#theme' => 'links__node__node', 
	);
	if ($variables['comment'] == 2 && user_access('post comments')) {
		$variables['content']['links']['#links']['node-comment'] = array(
			'title' => t('comment'),
			'href' => 'node/'.$variables['nid'],
		);
	} else if ($variables['comment'] == 2 && $user->uid == 0) {
		$variables['content']['links']['#links']['node-comment-login'] = array(
			'title' => t('comment'),
			'href' => 'user/login',
		);
	}
	
	if (user_access('delete any '.$variables['type'].' content') || ($user->uid == $variables['uid'] && user_access('delete own '.$variables['type'].' content'))) {
		// Add the Delete Links
		$variables['content']['links']['#links']['node-delete'] = array(
			'title' => t('delete'),
			'href' => 'node/'.$variables['nid'].'/delete/nojs',
			'attributes' => array(
				'class' => array(
					'ajax-link',
				),
			),
		);
	}
	
	// drupal_set_message('<pre>'.print_r($variables, true).'</pre>');
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

	$comment = $variables['elements']['#comment'];
	
	// Remove the Reply link
	unset($variables['content']['links']['comment']['#links']['comment-reply']);
	
	// Remove the Comment Forbidden link
	unset($variables['content']['links']['comment']['#links']['comment_forbidden']);
	
	// Remove the Comment Delete link and add it back (do this to change who the link appears to)
	unset($variables['content']['links']['comment']['#links']['comment-edit']);
	if (user_access('administer comments')) {
		$variables['content']['links']['comment']['#links']['comment-edit'] = array(
        'title' => t('edit'),
        'href' => "comment/$comment->cid/edit",
        'html' => TRUE,
      );
  }
	
	// Remove the Comment Delete link and add it back (do this to change who the link appears to)
	unset($variables['content']['links']['comment']['#links']['comment-delete']);
  if (user_access('administer comments') || (user_access('edit own comments') && comment_access('edit', $comment))) {
		$variables['content']['links']['comment']['#links']['comment-delete'] = array(
	    'title' => t('delete'),
	    'href' => "comment/$comment->cid/delete/nojs",
	    'attributes' => array(
	    	'class' => 'ajax-link',
	    	'id' => 'comment-'.$comment->cid.'-delete',
	    ),
    );
      
  }

}

/**
 * Implements template_preprocess_pager()
 */
function thechurch_preprocess_pager(&$variables) {
	
	$variables['quantity'] = 0;

}

/**
 * Implements template_preprocess_item_list()
 */
function thechurch_preprocess_item_list(&$variables) {
	
	$i = 0;
	$remove = array();
	foreach ($variables['items'] as $item) {
		if (is_array($item['class'])) {
			foreach ($item['class'] as $class) {
				if ($class == 'pager-first' || $class == 'pager-ellipsis' || $class == 'pager-last') {
					$remove[] = $i;
				}
			}
		}
		$i++;
	}
	
	if (is_array($remove)) {
		foreach ($remove as $gone) {
			unset($variables['items'][$gone]);
		}
	}
}

