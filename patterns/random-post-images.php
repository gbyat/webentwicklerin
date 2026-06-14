<?php

/**
 * Title: Random post images
 * Slug: webentwicklerin/random-post-images
 * Categories: webentwicklerin
 * Description: A grid of linked featured images in random order. No pagination. Adjust the item count, columns, aspect ratio, or block styles after inserting.
 * Keywords: random, posts, images, featured, query, grid, blur
 *
 * @package webentwicklerin
 * @since   2.0.0
 */

?>
<!-- wp:query {"queryId":10,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"is-style-random-order","layout":{"type":"constrained"}} -->
<div class="wp-block-query is-style-random-order">
	<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
	<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","className":"is-style-blur-backdrop"} /-->
	<!-- /wp:post-template -->
</div>
<!-- /wp:query -->
