<?php

/**
 * @file
 * Default theme implementation to display a term.
 *
 * Available variables:
 * - $name: the (sanitized) name of the term.
 * - $content: An array of items for the content of the term (fields and
 *   description). Use render($content) to print them all, or print a subset
 *   such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $term_url: Direct url of the current term.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the following:
 *   - taxonomy-term: The current template type, i.e., "theming hook".
 *   - vocabulary-[vocabulary-name]: The vocabulary to which the term belongs to.
 *     For example, if the term is a "Tag" it would result in "vocabulary-tag".
 *
 * Other variables:
 * - $term: Full term object. Contains data that may not be safe.
 * - $view_mode: View mode, e.g. 'full', 'teaser'...
 * - $page: Flag for the full page state.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the term. Increments each time it's output.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * @see template_preprocess()
 * @see template_preprocess_taxonomy_term()
 * @see template_process()
 */
?>
<div id="taxonomy-term-<?php print $term->tid; ?>" class="<?php print $classes; ?> clearfix">
		<div id="term-header" class="clearfix">
	    <?php if ($term_name  && !$is_front): ?>
			<div class="title">
		  	<h1 class="citytitle first" id="page-title"><?php print $term_name; ?></h1>
		 		<?php print render($content['field_state']); ?>
		 		<?php print render($content['field_country']); ?>
		 	</div>
		 	<?php endif; ?>
		 	<?php if (isset($content['showusers'])) : ?>
		 		<?php print render($content['showusers']); ?>
		 	<?php endif; ?>
		</div>
		<?php if ($logged_in && isset($content['post_form'])) : ?>
			<div id="post-form">
			 <?php if ($user->picture) : ?>
	  		<?php $user = user_load($user->uid); ?>
	  		<?php
	   			$img = theme('image_style', array('style_name' => 'square', 'path' => $user->picture->uri, 'alt' => $user->name));
	  			print l($img, 'user/'.$user->uid, array('attributes' => array('class' => 'avatar'),'html' => true));
	  		?>
  		<?php endif; ?>
			<?php print render($content['post_form']); ?>
			</div>
		<?php endif; ?>
  <div class="content">
    <?php print render($content); ?>
  </div>

</div>
