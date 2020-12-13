<?php

add_shortcode( 'wa_link', function ( $atts, $content='' ) {
  $atts = shortcode_atts( [
    'icon'   => 'whatsapp',
    'class'  => '',
    'text'   => '',
    'number' => '123456890',
  ], $atts, 'wa_link');

  if ( empty($content) ) {
    $content = 'WhatsApp';
  }

  $realnumber = preg_replace( '/[^0-9]/', '', $atts['number'] );

  $url = 'https://api.whatsapp.com/send';
  $url .= sprintf( '?phone=%s', rawurlencode($realnumber) );
  if ( ! empty($atts['text']) ) {
    $url .= sprintf( '&text=%s', rawurlencode($atts['text']) );
  }

  $htmlicon = '';
  if ( ! empty($atts['icon']) ) {
    $htmlicon = sprintf( '<i class="fa fa-%s"></i> ', htmlspecialchars($atts['icon']) );
  }

  return sprintf(
    '<a href="%s" class="wa-link %s">%s%s</a>',
    htmlspecialchars($url),
    htmlspecialchars($atts['class']),
    $htmlicon,
    htmlspecialchars($content)
  );
} );
