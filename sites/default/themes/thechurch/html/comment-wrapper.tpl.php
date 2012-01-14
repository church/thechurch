<?php
// $Id: comment-wrapper.tpl.php,v 1.10 2010/05/05 06:41:22 webchick Exp $

/**
 * @file
 * Default theme implementation to provide an HTML container for comments.
 *
 * Available variables:
 * - $content: The array of content-related elements for the node. Use
 *   render($content) to print them all, or
 *   print a subset such as render($content['comment_form']).
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default value has the following:
 *   - comment-wrapper: The current template type, i.e., "theming hook".
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * The following variables are provided for contextual information.
 * - $node: Node object the comments are attached to.
 * The constants below the variables show the possible values and should be
 * used for comparison.
 * - $display_mode
 *   - COMMENT_MODE_FLAT
 *   - COMMENT_MODE_THREADED
 *
 * Other variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * @see template_preprocess_comment_wrapper()
 * @see theme_comment_wrapper()
 */
?>
<div id="node-<?php print $node->nid; ?>-comments" class="<?php print $classes; ?>"<?php print $attributes; ?>>
	<div class="comment-list">
  	<?php print render($content['comments']); ?>
  </div>
  <?php if (!empty($content['comment_form'])): ?>
  	<div class="comment-form clearfix">
  		<?php if (isset($user->picture)) : ?>
  			<div class="form-left">
		  		<?php $user = user_load($user->uid); ?>
		  		<?php
		  			if (!empty($user)) {
			   			$img = theme('image_style', array('style_name' => 'square', 'path' => $user->picture->uri, 'alt' => $user->name));
			  			print l($img, 'user/'.$user->uid, array('attributes' => array('class' => 'avatar'),'html' => true));
			  		}
		  		?>
		  	</div>
  		<?php endif; ?>
  		<div class="form-right">
    		<?php print render($content['comment_form']); ?>
    	</div>
    </div>
	<?php endif; ?>
  
</div>
