<?php

add_shortcode( 'page_sections', function ( $atts, $content=null ) {

	$html = '';
	
	$filter = $atts['category'];
	
	if ( have_rows('sections') ) {

		$count = 0;

		while ( have_rows('sections') ) {

			the_row();

			if ( get_sub_field('hide') ) {
				continue;
			}

			if ( ! empty($filter) ) {
				if ( $filter != get_sub_field('section_category') ) {
					continue;
				}
			}

			$wpautop = ! get_sub_field( 'disable_wpautop' );
			$contain = ! get_sub_field( 'no_container' );

			$classes = get_sub_field( 'wrapper_classes' );
			$id      = get_sub_field( 'wrapper_id' );
			$content = get_sub_field( 'content', false, $wpautop );
			$count++;

			if ( empty($id) ) {
				$id = 'section-' . $count;
			}

			if ( empty($classes) ) {
				$classes = 'section-classless';
			}

			if ( $count % 2 ) {
				$classes .= ' section-odd';
				if ( $count === 1 ) {
					$classes .= ' section-first';
				}
			}
			else {
				$classes .= ' section-even';
			}

			$html .= sprintf( '<section id="%s" class="page-section %s">', $id, $classes );
			if ( $contain ) {
				$html .= '<div class="container">';
			}
			$html .= do_shortcode( $content );
			if ( $contain ) {
				$html .= '</div>';
			}
			$html .= "</section>\n";
		}

	}

	if ( empty($html) ) {
		return $content;
	}

	return $html;
} );
