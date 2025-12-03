<?php

/**
 * Title: Index posts
 * Slug: webentwicklerin/hidden-index
 * Inserter: no
 *
 * @package webentwicklerin
 * @subpackage webentwicklerin
 * @since 1.0.0
 */

?>
<!-- wp:query {"queryId":2,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"layout":{"type":"constrained"}} -->
<div class="wp-block-query">
    <!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"grid","columnCount":2}} -->
    <!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20","right":"var:preset|spacing|20"},"blockGap":"var:preset|spacing|20"},"border":{"width":"0px","style":"none"}},"layout":{"type":"constrained"}} -->
    <div class="wp-block-group"
        style="border-style:none;border-width:0px;margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)">
        <!-- wp:post-title {"level":3,"isLink":true,"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"medium"} /-->

        <!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"blockGap":"0"}},"layout":{"type":"flex","allowOrientation":false,"orientation":"vertical"}} -->
        <div class="wp-block-group" style="margin-top:0;margin-bottom:0">
            <!-- wp:post-date {"textColor":"contrast"} /-->

            <!-- wp:post-terms {"term":"category","textColor":"contrast"} /-->
        </div>
        <!-- /wp:group -->

        <!-- wp:post-excerpt {"moreText":"<?php echo esc_html_x('Read more', 'Link text for post excerpts.', 'webentwicklerin'); ?>","excerptLength":25} /-->
    </div>
    <!-- /wp:group -->
    <!-- /wp:post-template -->

    <!-- wp:query-pagination {"paginationArrow":"chevron","layout":{"type":"flex","justifyContent":"space-between"}} -->
    <!-- wp:query-pagination-previous {"label":"<?php echo esc_html_x('Previous', 'Previous posts pagination link.', 'webentwicklerin'); ?>"} /-->

    <!-- wp:query-pagination-next {"label":"<?php echo esc_html_x('Next', 'Next posts pagination link.', 'webentwicklerin'); ?>"} /-->
    <!-- /wp:query-pagination -->

    <!-- wp:query-pagination {"paginationArrow":"chevron","layout":{"type":"flex","justifyContent":"center"}} -->
    <!-- wp:query-pagination-numbers /-->
    <!-- /wp:query-pagination -->
</div>
<!-- /wp:query -->