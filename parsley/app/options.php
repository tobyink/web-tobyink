<?php

namespace App;

function theme_get_option ( $id ) {
  static $json = null;
  $o = theme_get_options();
  if ( array_key_exists( $id, $o ) ) {
    return $o[$id];
  }
  if ( ! $json ) {
    $json = json_decode( file_get_contents( __FILE__ . '.json' ) );
  }
  foreach ( $json as $section ) {
    foreach ( $section->options as $opt ) {
      if ( $opt->key == $id ) {
        return $opt->default;
      }
    }
  }
  return null;
}

function theme_get_options () {
  static $options = null;
  if ( ! $options ) {
    $options = get_option( 'theme_options' );
  }
  return $options;
}

function theme_write_sass ( $options ) {
  $sass = [];
  $sass_early = [];
  $sass_late  = [];
  foreach ( $options as $k => $v ) {
    if ( $k == 'font-base' || $k == 'font-headings' ) {
      $sass_late []= sprintf('$wp-theme-%s: $wp-theme-font-%s;', $k, $v);
    }
    elseif ( $k == 'imports' ) {
      $lines = explode( "\n", $v );
      foreach ( $lines as $l ) {
        if ( ! preg_match( '/^http/', $l ) ) {
          continue;
        }
        $sass_early []= sprintf( '@import url(%s);', str_replace( ';', '%3B', trim($l) ) );
      }
    }
    elseif ( ! preg_match( '/^_/', $k ) ) {
      $sass []= sprintf('$wp-theme-%s: %s;', $k, $v);
    }
  }

  $final = "";
  foreach ( [ $sass_early, $sass, $sass_late ] as $arr ) {
    foreach ( $arr as $l ) {
      $final .= $l . "\n";
    }
    if ( count($arr) ) {
      $final .= "\n";
    }
  }

  $file = __DIR__ . '/../resources/assets/styles/common/_wp_theme.scss';
  file_put_contents( $file, $final );
  system( "chmod g+rwX '$file'" );
}

function theme_compile_sass () {
  $olddir = getcwd();
  chdir( __DIR__ . '/..'  );
  system( './yarn build' );
  system( 'chmod -R g+rwX ./dist' );
  chdir( $olddir );
}

if ( is_admin() ) {
  add_action( 'admin_enqueue_scripts', function () {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
  } );
  add_action( 'admin_init', function () {
    register_setting(
      'theme_options',
      'theme_options',
      function ( $options ) {
        theme_write_sass( $options );
        theme_compile_sass();
        return $options;
      }
    );
  } );
  add_action( 'admin_menu', function () {
    add_submenu_page(
      'themes.php',
      'Theme Options',
      'Theme Options',
      'manage_options',
      'theme-settings',
      function () {
        $html = '';
        $json = json_decode( file_get_contents( __FILE__ . '.json' ) );
        foreach ( $json as $section ) {
          $html .= '<h2>' . $section->label . '</h2>';
          foreach ( $section->options as $opt ) {
            $val = array_key_exists( 'theme_options', $_POST ) ? $_POST['theme_options'][$opt->key] : theme_get_option( $opt->key );
            if ( empty($val) ) {
              $val = $opt->default;
            }
            $hval = htmlspecialchars( $val );
            $html .= '<div style="margin-bottom:1rem">';
            $html .= '<label for="' . $opt->key . '">' . $opt->label . '</label><br>';
            if ( $opt->type == 'colour' ) {
              $html .= '<input type="text" class="colour" name="theme_options[' . $opt->key . ']" id="' . $opt->key . '" value="' . $hval . '" data-default-color="' . $opt->default . '" />';
            }
            elseif ( $opt->type == 'colourx') {
              $html .= '<input type="text" class="colourx" style="width:100%;max-width:30rem" name="theme_options[' . $opt->key . ']" id="' . $opt->key . '" value="' . $hval . '" />';
              $html .= '<button onclick="document.getElementById(\''.$opt->key.'\').value=\''.htmlspecialchars(addslashes($opt->default)).'\';return false">Default</button>';
            }
            elseif ( $opt->type == 'select' ) {
              $html .= '<select name="theme_options[' . $opt->key . ']" id="' . $opt->key . '">';
              foreach ( $opt->choices as $choice ) {
                $html .= sprintf(
                  '<option %s value="%s">%s%s</option>',
                  ( ( $choice == $val ) ? 'selected' : '' ),
                  htmlspecialchars($choice),
                  htmlspecialchars($choice),
                  ( ( $choice == $opt->default ) ? ' [default]' : '' ),
                );
              }
              $html .= '</select>';
            }
            elseif ( $opt->type == 'textarea' ) {
              $html .= '<textarea cols="40" rows="6" style="width:100%;max-width:40rem" name="theme_options[' . $opt->key . ']" id="' . $opt->key . '">' . $hval . '</textarea>';
            }
            else {
              $html .= '<input type="text" style="width:100%;max-width:40rem" name="theme_options[' . $opt->key . ']" id="' . $opt->key . '" value="' . $hval . '" />';
              $html .= '<button onclick="document.getElementById(\''.$opt->key.'\').value=\''.htmlspecialchars(addslashes($opt->default)).'\';return false">Default</button>';
            }
            $html .= '</div>';
          }
        }
        $html .= '<script>
          var palette_map = {};
          var palette     = [];
          jQuery( document ).ready( function ($) {
            $(".colour").wpColorPicker();

            $(".colourx").iris( {
              mode: "hsv",
              controls: { horiz: "h", vert: "v", strip: "s" },
              change: function ( e, ui ) {
                var $this = $( e.target );
                $this.iris( "hide" );
                var c = ui.color.toString();
                setTimeout( function () {
                  if ( palette_map[c] ) {
                    $this.val( "$wp-theme-" + palette_map[c] );
                  }
                }, 300 );
              },
            } ).click( function () {
              var $this = $(this);
              palette_map = {};
              palette     = [];
              $(".colour").each( function ( ix, e ) {
                var $e = jQuery(e);
                palette_map[ $e.val() ] = $e.attr( "id" );
                palette.push( $e.val() );
              } );
              $this.iris( "option", "palettes", palette );
              $this.iris( "show" );
            } );
          } );
        </script>';

        echo '<div class="wrap">';
        echo '<h1>Theme Options</h1>';
        echo '<form method="post" action="options.php">';
        echo settings_fields('theme_options');
        echo $html;
        echo submit_button();
        echo "<p>Saving changes may take a minute; the site's stylesheets will be recompiled after each change.</p>";
        echo '</form>';
        echo '</div>';
      }
    );
  } );
}
