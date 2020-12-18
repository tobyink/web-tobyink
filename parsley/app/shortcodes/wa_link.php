<?php

global $parsley_help_shortcodes;

$parsley_help_shortcodes['wa_link'] = <<<'HELP'
<p>Inserts a link to a WhatsApp chat.</p>
<pre>
[wa_link icon="whatsapp" class="classes" number="44785533657" text="Hi Toby"]Link text[/wa_link]
</pre>
<p><code>number</code> is the phone number including international prefix. It is required.</p>
<p><code>class</code> is a list of classes, separated by spaces.</p>
<p><code>text</code> is text to send as chat.</p>
<p><code>icon</code> is fontawesome icon.</p>
HELP;

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
