<?php

/**
 * Plugin Name: Sidebar links
 * Version: 1.0
 * Author: Toby Inkster
 * Author URI: http://toby.ink/
 */

class TobyInk_Links extends WP_Widget {

  function __construct() {
    parent::__construct( 'tobyink-links', 'TobyInk Links' );
    add_action( 'widgets_init', function() {
      register_widget( 'TobyInk_Links' );
    } );
  }

  public $args = [
    'before_title'  => '<h3 class="widgettitle">',
    'after_title'   => '</h3>',
    'before_widget' => '<div class="widget-wrap">',
    'after_widget'  => '</div>',
    'before_section_title' => '<h4>',
    'after_section_title'  => '</h4>',
  ];

  public function linkdata () {
    return get_field( 'link_sections' );
  }

  public function widget( $args, $instance ) {
    $linkdata = $this->linkdata();
    if ( ! is_array($linkdata) ) {
      return;
    }
    if ( ! count($linkdata) ) {
      return;
    }
    if ( is_archive() ) {
      return;
    }

    foreach ( [ 'before_widget', 'after_widget', 'before_section_title', 'after_section_title' ] as $k ) {
      isset( $args[$k] ) || ( $args[$k] = '' );
    }

    echo $args['before_widget'] . "\n";
    echo $args['before_title'] . apply_filters( 'widget_title', "Links" ) . $args['after_title'] . "\n";
    foreach ( $linkdata as $ix => $section ) {
      echo $args['before_section_title'] . esc_html( $section['section_title'] ) . $args['after_section_title'] . "\n";
      echo '<ul class="list-group mb-3">' . "\n";
      foreach ( $section['links'] as $link ) {
        printf( '<li class="list-group-item"><a href="%s" rel="%s" target="%s">%s</a></li>' . "\n",
          esc_html( $link['url'] ),
          esc_html( $link['rel'] ? $link['rel'] : 'related' ),
          esc_html( $link['target'] ? $link['target'] : '_self' ),
          esc_html( $link['text'] ) );
      }
      echo '</ul>' . "\n";
    }
    echo $args['after_widget'] . "\n";
  }

  public function form ($instance) {
    echo '';
  }

  public function update ($new_instance, $old_instance) {
    echo '';
  }
}

add_action( 'widgets_init', function () {
  register_widget( 'TobyInk_Links' );
} );
