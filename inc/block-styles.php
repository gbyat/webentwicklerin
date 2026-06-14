<?php

/**
 * Register block styles.
 *
 * @package webentwicklerin
 * @since   1.0.0
 */

add_action( 'init', 'webentwicklerin_block_styles' );

/**
 * Register custom block styles.
 *
 * @return void
 */
function webentwicklerin_block_styles() {
	register_block_style(
		'core/list',
		array(
			'name'         => 'colored-bullets',
			'label'        => __( 'Colored Bullets', 'webentwicklerin' ),
			'inline_style' => '
            ul.is-style-colored-bullets {
                list-style: none;
            }

            ul.is-style-colored-bullets > li::before {
                content: "\2022";
                color: var(--wp--preset--color--accent);
                font-weight: bold;
                display: inline-block;
                width: 0.75em;
                margin-left: -0.75em;
            }
            ul.is-style-colored-bullets ul li::before {
                content: "\29BF";
                color: var(--wp--preset--color--accent);
                font-weight: bold;
                display: inline-block;
                width: 0.75em;
                margin-left: -0.75em;
            }
            ul.is-style-colored-bullets > li > div,
            ul.is-style-colored-bullets ul li > div {
                display: inline-block;
            }',
		)
	);

	$blur_backdrop_style = array(
		'name'  => 'blur-backdrop',
		'label' => __( 'Blur backdrop', 'webentwicklerin' ),
	);

	foreach ( array( 'core/post-featured-image', 'core/image', 'core/gallery' ) as $block_name ) {
		register_block_style( $block_name, $blur_backdrop_style );
	}
}
