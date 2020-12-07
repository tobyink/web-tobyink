@php

  if ( have_rows('design_sections') ) {
    $count = 0;
    while ( have_rows('design_sections') ) {
      the_row();

      if ( get_sub_field('hidden') ) {
        continue;
      }

      $wpautop = ! get_sub_field( 'exact_html' );
      $contain = ! get_sub_field( 'full_width' );

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

      $content = 'CONTENT';
      if ( get_row_layout() == 'html_content' ) {
        $content = get_sub_field( 'content', false, false );
        $classes .= ' section-type-html-content';
        if ( $wpautop ) {
          $content  = wpautop( $content );
          $classes .= ' section-wpautop';
        }
        $content = do_shortcode( $content );
      }
      elseif ( get_row_layout() == 'columns' ) {
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
          $col = [
            'options' => get_sub_field('options'),
            'content' => get_sub_field('content', false, false),
          ];
          $col_wpautop = ! $col['options']['exact_html'];
          $col_classes =   $col['options']['classes'];
          $col_content =   $col['content'];
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
          $content .= sprintf( '<div class="%s">%s</div>', $col_classes, do_shortcode($col_content) );
        }
        $content .= '</div>';
        $content .= do_shortcode( $after );
      }
      elseif ( get_row_layout() == 'primary_content' ) {
        $post_id = get_the_ID();
        $content = get_the_content( false, false, $post_id );
        $classes .= ' section-type-primary-content';
        if ( ! get_field( 'disable_wpautop', $post_id ) ) {
          $content  = wpautop( $content );
          $classes .= ' section-wpautop';
        }
        $content = do_shortcode( $content );
      }
      elseif ( get_row_layout() == 'post' ) {
        $post_id = get_sub_field('post_id');
        $content = get_the_content( false, false, $post_id );
        $classes .= ' section-type-post';
        if ( ! get_field( 'disable_wpautop', $post_id ) ) {
          $content  = wpautop( $content );
          $classes .= ' section-wpautop';
        }
        $content = do_shortcode( $content );
      }
      elseif ( get_row_layout() == 'foogallery' ) {
        $content = '<p>Please install FooGallery.</p>';
        $classes .= ' section-type-foogallery';
        if ( function_exists( 'foogallery_render_gallery' ) ) {
          ob_start();
          foogallery_render_gallery( get_sub_field('gallery_id') );
          $content = ob_get_contents();
          ob_end_clean();
        }
      }
      elseif ( get_row_layout() == 'image' ) {
        $classes .= ' section-type-image';
        $heading_tag = 'none';
        $contain = false;
        $content = wp_get_attachment_image( get_sub_field('image'), 'full', false, [ 'class' => 'w-100', 'loading' => 'lazy' ] );
      }

      printf( '<section id="%s" class="page-section %s">', $id, $classes );
      if ( $contain ) {
        echo '<div class="container">';
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
  else {
    the_content();
  }

@endphp

{!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
