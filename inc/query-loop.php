<?php

/**
 * Query Loop block helpers.
 *
 * @package webentwicklerin
 * @since   2.0.0
 */

add_action( 'init', 'webentwicklerin_register_query_loop_block_styles' );
add_filter( 'register_block_type_args', 'webentwicklerin_query_loop_block_type_args', 10, 2 );
add_filter( 'render_block_data', 'webentwicklerin_query_loop_random_order_data', 10, 3 );
add_filter( 'render_block_context', 'webentwicklerin_query_loop_random_order_context', 10, 3 );
add_filter( 'query_loop_block_query_vars', 'webentwicklerin_query_loop_random_order', 10, 2 );

/**
 * Register Query Loop block styles.
 *
 * @since 2.0.0
 *
 * @return void
 */
function webentwicklerin_register_query_loop_block_styles() {
	register_block_style(
		'core/query',
		array(
			'name'  => 'random-order',
			'label' => __( 'Random order', 'webentwicklerin' ),
		)
	);
}

/**
 * Ensure Query Loop blocks can store block style class names.
 *
 * @since 2.0.0
 *
 * @param array  $args       Block type registration arguments.
 * @param string $block_type Block name including namespace.
 * @return array
 */
function webentwicklerin_query_loop_block_type_args( $args, $block_type ) {
	if ( 'core/query' !== $block_type ) {
		return $args;
	}

	if ( empty( $args['supports']['className'] ) ) {
		$args['supports']['className'] = true;
	}

	return $args;
}

/**
 * Apply random order to the Query Loop block query attributes.
 *
 * @since 2.0.0
 *
 * @param array $parsed_block Parsed block data.
 * @return array
 */
function webentwicklerin_query_loop_apply_random_order( array $parsed_block ) {
	if ( ! isset( $parsed_block['attrs']['query'] ) || ! is_array( $parsed_block['attrs']['query'] ) ) {
		$parsed_block['attrs']['query'] = array();
	}

	$parsed_block['attrs']['query']['orderBy'] = 'rand';
	unset( $parsed_block['attrs']['query']['order'] );

	return $parsed_block;
}

/**
 * Set random order on Query Loop blocks with the Random order style.
 *
 * @since 2.0.0
 *
 * @param array         $parsed_block  Parsed block data.
 * @param array         $source_block  Original block data.
 * @param WP_Block|null $parent_block  Parent block instance.
 * @return array
 */
function webentwicklerin_query_loop_random_order_data( $parsed_block, $source_block, $parent_block ) {
	if ( 'core/query' !== ( $parsed_block['blockName'] ?? '' ) ) {
		return $parsed_block;
	}

	if ( ! webentwicklerin_block_has_style( $parsed_block, 'random-order' ) ) {
		return $parsed_block;
	}

	return webentwicklerin_query_loop_apply_random_order( $parsed_block );
}

/**
 * Pass random order through Query Loop context to Post Template children.
 *
 * The query runs from Post Template, not the Query Loop wrapper block.
 *
 * @since 2.0.0
 *
 * @param array         $context      Block context.
 * @param array         $parsed_block Parsed block data.
 * @param WP_Block|null $parent_block Parent block instance.
 * @return array
 */
function webentwicklerin_query_loop_random_order_context( $context, $parsed_block, $parent_block ) {
	if ( 'core/post-template' !== ( $parsed_block['blockName'] ?? '' ) ) {
		return $context;
	}

	if ( ! $parent_block instanceof WP_Block || 'core/query' !== $parent_block->name ) {
		return $context;
	}

	if ( ! webentwicklerin_block_has_style( $parent_block, 'random-order' ) ) {
		return $context;
	}

	if ( ! isset( $context['query'] ) || ! is_array( $context['query'] ) ) {
		$context['query'] = array();
	}

	$context['query']['orderBy'] = 'rand';
	unset( $context['query']['order'] );

	return $context;
}

/**
 * Apply random post order when the Random order block style is selected.
 *
 * @since 2.0.0
 *
 * @param array    $query Query vars for WP_Query.
 * @param WP_Block $block Block instance.
 * @return array
 */
function webentwicklerin_query_loop_random_order( $query, $block ) {
	if ( ! $block instanceof WP_Block ) {
		return $query;
	}

	$order_by = $block->context['query']['orderBy'] ?? '';

	if ( 'rand' === $order_by ) {
		$query['orderby'] = 'rand';
		unset( $query['order'] );
		return $query;
	}

	if ( webentwicklerin_block_has_style( $block, 'random-order' ) ) {
		$query['orderby'] = 'rand';
		unset( $query['order'] );
	}

	return $query;
}

/**
 * Check whether a block has a registered block style applied.
 *
 * @since 2.0.0
 *
 * @param array|WP_Block $block      Parsed block data or block instance.
 * @param string         $style_slug Block style slug without the is-style- prefix.
 * @return bool
 */
function webentwicklerin_block_has_style( $block, $style_slug ) {
	$class_name = 'is-style-' . $style_slug;
	$sources    = array();

	if ( $block instanceof WP_Block ) {
		$sources[] = $block->attributes['className'] ?? '';
		$sources[] = $block->parsed_block['attrs']['className'] ?? '';
	} elseif ( is_array( $block ) ) {
		$sources[] = $block['attrs']['className'] ?? '';
	}

	foreach ( $sources as $class_names ) {
		if ( is_string( $class_names ) && str_contains( $class_names, $class_name ) ) {
			return true;
		}
	}

	return false;
}
