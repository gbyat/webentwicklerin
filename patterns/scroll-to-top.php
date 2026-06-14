<?php

/**
 * Title: Scroll to top
 * Slug: webentwicklerin/scroll-to-top
 * Categories: webentwicklerin
 * Description: A button fixed to the bottom-right corner that fades in after scrolling. Remove the scroll-to-top class from the group to place it in the page flow. Replace the arrow with an Icon block on WordPress 7.0+ if you prefer.
 * Keywords: scroll, top, back, anchor, icon
 *
 * @package webentwicklerin
 * @since 1.0.0
 */

?>
<!-- wp:group {"className":"scroll-to-top animated","layout":{"type":"constrained"}} -->
<div class="wp-block-group scroll-to-top animated">
	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"url":"#topofpage"} -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#topofpage"><?php echo esc_html('↑'); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->