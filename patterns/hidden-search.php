<?php

/**
 * Title: Search results
 * Slug: webentwicklerin/hidden-search
 * Inserter: no
 *
 * @package webentwicklerin
 * @subpackage webentwicklerin
 * @since 1.0.0
 */

?>
<!-- wp:query-title {"type":"search","level":2} /-->

<!-- wp:search {"label":"<?php echo esc_attr_x('Search', 'Search form label.', 'webentwicklerin'); ?>","showLabel":false,"buttonText":"<?php echo esc_attr_x('Search', 'Search button text.', 'webentwicklerin'); ?>","style":{"border":{"radius":"15px"}},"textColor":"base"} /-->

<!-- wp:query {"queryId":2,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"layout":{"type":"constrained"}} -->
<div class="wp-block-query">
    <!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"default"}} -->
    <!-- wp:post-title {"level":3,"isLink":true,"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"medium"} /-->

    <!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","allowOrientation":false}} -->
    <div class="wp-block-group" style="margin-top:0;margin-bottom:0">
        <!-- wp:post-date {"textColor":"contrast"} /-->

        <!-- wp:post-terms {"term":"category"} /-->
    </div>
    <!-- /wp:group -->

    <!-- wp:post-excerpt {"moreText":"<?php echo esc_html_x('Read more', 'Link text for post excerpts.', 'webentwicklerin'); ?>","showMoreOnNewLine":false} /-->
    <!-- /wp:post-template -->

    <!-- wp:query-pagination {"paginationArrow":"chevron","layout":{"type":"flex","justifyContent":"space-between"}} -->
    <!-- wp:query-pagination-previous {"label":"<?php echo esc_html_x('Previous', 'Previous posts pagination link.', 'webentwicklerin'); ?>"} /-->

    <!-- wp:query-pagination-next {"label":"<?php echo esc_html_x('Next', 'Next posts pagination link.', 'webentwicklerin'); ?>"} /-->
    <!-- /wp:query-pagination -->
</div>
<!-- /wp:query -->