@if ( ! get_field( 'hide_header', \App\acf_page_id() ) )

@php
  echo \App\theme_get_option( 'header_html' );
@endphp

<header class="banner sticky-top">
  <div class="container">
    <div class="navbar navbar-expand-md">
      <a class="navbar-brand" href="{{ home_url('/') }}">
      @php
        $title = \App\theme_get_option( 'header_title' );
        if ( empty($title) ) {
          $title = get_bloginfo( 'name', 'display' );
        }
        $style = \App\theme_get_option( 'header_style' );
        if ( $style == 'image' ) {
          $img = \App\theme_get_option( 'header_image' );
          echo wp_get_attachment_image( $img, 'full', false, [ 'alt' => $title ] );
        }
        else {
          echo esc_html( $title );
        }
      @endphp
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-primary" aria-controls="navbar-primary" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"><i class="fa fa-bars"></i></span>
      </button>
      @if (has_nav_menu('primary_navigation'))
        {!! wp_nav_menu($primarymenu) !!}
      @endif
    </div>
  </div>
</header>

@endif

@php
  $bannerimg = get_field( 'fullwidth_banner', \App\acf_page_id() );
  if ( ! empty($bannerimg) ) {
    printf( '<div class="fullwidth-banner"><img src="%s" alt="" class="fullwidth"></div>', htmlspecialchars($bannerimg['url']) );
  }
@endphp

@php
  if( function_exists('bcn_display') && ! get_field( 'hide_breadcrumbs', \App\acf_page_id() ) ) {
    echo '<div class="container breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">';
    bcn_display();
    echo '</div>';
  }
@endphp
