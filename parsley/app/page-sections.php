<?php

namespace App;

function parsley_render_col_html ( &$classes, &$heading_in_column, &$heading_tag, &$heading_classes, &$heading ) {

	$opts = get_sub_field('options');
	$col_wpautop = ! $opts['exact_html'];
	$col_classes =   $opts['classes'];

	$col_content = get_sub_field('content', false, false);
	if ( $col_wpautop ) {
		$col_classes .= ' column-wpautop';
		$col_content  = wpautop( $col_content );
	}

	if ( $heading_in_column ) {
		$classes .= ' section-with-heading-in-column';
		$col_classes .= ' column-containing-the-heading';
		if ( $heading_tag != 'none' ) {
			$heading_classes .= ' heading-in-column';
			$col_content = sprintf( '<%s classes="%s"><span>%s</span></%s>', $heading_tag, $heading_classes, $heading, $heading_tag ) . $col_content;
			$heading_tag = 'none'; // prevent duplicate heading
		}
	}

	return sprintf( '<div class="col-type-html %s">%s</div>', $col_classes, do_shortcode($col_content) );
}

function parsley_render_col_image ( &$classes, &$heading_in_column, &$heading_tag, &$heading_classes, &$heading ) {
	$opts = get_sub_field('options');
	$col_classes = $opts['classes'];

	$iatts = [ 'loading' => 'lazy', 'class' => '' ];
	if ( get_sub_field('img_class') ) {
		$iatts['class'] = get_sub_field('img_class');
	}
	if ( get_sub_field('img_id') ) {
		$iatts['id'] = get_sub_field('img_id');
	}
	if ( get_sub_field('rounded') ) {
		$iatts['class'] .= ' rounded';
	}
	if ( get_sub_field('shadow') ) {
		$iatts['class'] .= ' shadow';
	}
	$col_content = wp_get_attachment_image( get_sub_field('image'), 'large', false, $iatts );

	return sprintf( '<div class="col-type-image %s">%s</div>', $col_classes, $col_content);
}

function parsley_render_sections () {
	$count = 0;
	while ( have_rows('design_sections') ) {
		the_row();

		if ( get_sub_field('hidden') ) {
			continue;
		}

		$wpautop = ! get_sub_field( 'exact_html' );

		$contain =   get_sub_field( 'full_width' );
		if ( $contain == 'wide' ) {
			$contain = false;
		}

		$style = get_sub_field('style');

		$classes = '';
		$id      = get_sub_field( 'id' );
		$content = get_sub_field( 'content', false, false );
		$count++;

		if ( $count % 2 ) {
			$classes = 'section-odd';
			if ( $count === 1 ) {
				$classes .= ' section-first';
			}
		}
		else {
			$classes = 'section-even';
		}

		if ( is_array($style) ) {
			if ( ! empty($style['text_colour']) ) {
				$classes .= ' text-' . $style['text_colour'];
			}
			if ( ! empty($style['background_colour']) ) {
				$classes .= ' bg-' . $style['background_colour'];
			}
			if ( ! empty($style['vertical_padding']) ) {
				$classes .= ' py-' . $style['vertical_padding'];
			}
			if ( ! empty($style['additional_classes']) ) {
				$classes .= ' ' . $style['additional_classes'];
			}
		}

		$heading = get_sub_field( 'heading' );
		$heading_tag = 'h2';
		$heading_classes = 'section-title';
		if ( empty($heading) ) {
			$heading_tag = 'none';
		}
		else {
			$heading_level = get_sub_field( 'heading_level' );
			if ( $heading_level['real'] ) {
				$heading_tag = $heading_level['real'];
			}
			if ( $heading_level['visual'] ) {
				if ( $heading_level['visual'] != $heading_tag ) {
					$heading_classes .= ' ' . $heading_level['visual'];
				}
			}
		}

		if ( empty($id) ) {
			$id = 'section-' . $count;
		}

		$layout  = get_row_layout();
		$content = 'CONTENT';
		if ( $layout == 'html_content' ) {
			$content = get_sub_field( 'content', false, false );
			$classes .= ' section-type-html-content';
			if ( $wpautop ) {
				$content  = wpautop( $content );
				$classes .= ' section-wpautop';
			}
			$content = do_shortcode( $content );
		}
		elseif ( $layout == 'columns' || $layout == 'columns2' ) {
			$before = get_sub_field( 'before_columns', false, false );
			$after  = get_sub_field( 'after_columns',  false, false );
			$classes .= ' section-type-columns';
			if ( $wpautop ) {
				$before   = wpautop( $before );
				$after    = wpautop( $after  );
				$classes .= ' section-wpautop';
			}
			$content  = do_shortcode( $before );
			$content .= '<div class="row">';
			$heading_in_column = get_sub_field('heading_in_column');
			while ( have_rows('columns') ) {
				the_row();

				if ( $layout === 'columns' ) {
					$col_type = 'col_html';
				}
				else {
					$col_type = get_row_layout();
				}

				$got = call_user_func_array(
					'App\parsley_render_' . $col_type,
					[ &$classes, &$heading_in_column, &$heading_tag, &$heading_classes, &$heading ] 
				);

				$content .= ( $got === false ) ? '<div class="col">ERROR</div>' : $got;
			}
			$content .= '</div>';
			$content .= do_shortcode( $after );
		}
		elseif ( $layout == 'primary_content' ) {
			$post_id = get_the_ID();
			$content = get_the_content( false, false, $post_id );
			$classes .= ' section-type-primary-content';
			if ( ! get_field( 'disable_wpautop', $post_id ) ) {
				$content  = wpautop( $content );
				$classes .= ' section-wpautop';
			}
			$content = do_shortcode( $content );
		}
		elseif ( $layout == 'post' ) {
			$post_id = get_sub_field('post_id');
			$content = get_the_content( false, false, $post_id );
			$classes .= ' section-type-post';
			if ( ! get_field( 'disable_wpautop', $post_id ) ) {
				$content  = wpautop( $content );
				$classes .= ' section-wpautop';
			}
			$content = do_shortcode( $content );
		}
		elseif ( $layout == 'foogallery' ) {
			$content = '<p>Please install FooGallery.</p>';
			$classes .= ' section-type-foogallery';
			if ( function_exists( 'foogallery_render_gallery' ) ) {
				ob_start();
				foogallery_render_gallery( get_sub_field('gallery_id') );
				$content = ob_get_contents();
				ob_end_clean();
			}
		}
		elseif ( $layout == 'image' ) {
			$classes .= ' section-type-image';
			$heading_tag = 'none';
			$contain = false;
			$iatts = [ 'loading' => 'lazy' ];
			if ( get_sub_field('img_class') ) {
				$iatts['class'] = get_sub_field('img_class');
			}
			if ( get_sub_field('img_id') ) {
				$iatts['id'] = get_sub_field('img_id');
			}
			$content = wp_get_attachment_image( get_sub_field('image'), 'full', false, $iatts );
		}

		printf( '<section id="%s" class="page-section %s">', $id, $classes );
		if ( $contain ) {
			echo '<div class="' . $contain . '">';
		}
		if ( $heading_tag != 'none' ) {
			printf( '<%s classes="%s"><span>%s</span></%s>', $heading_tag, $heading_classes, $heading, $heading_tag );
		}
		echo $content;
		if ( $contain ) {
			echo '</div>';
		}
		echo "</section>\n";
	}
}
