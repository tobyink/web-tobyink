<header class="banner">
  <div class="container">
    <div class="navbar navbar-expand-md">
      <a class="navbar-brand" href="{{ home_url('/') }}">{{ get_bloginfo('name', 'display') }}</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-primary" aria-controls="navbar-primary" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"><i class="fa fa-bars"></i></span>
      </button>
      @if (has_nav_menu('primary_navigation'))
        {!! wp_nav_menu($primarymenu) !!}
      @endif
    </div>
  </div>
  @php
    $bannerimg = get_field( 'fullwidth_banner', \App\acf_page_id() );
    if ( ! empty($bannerimg) ) {
      printf( '<div class="fullwidth-banner"><img src="%s" alt="" class="fullwidth"></div>', htmlspecialchars($bannerimg['url']) );
    }
  @endphp
</header>

@php
  if( function_exists('bcn_display') && ! get_field( 'hide_breadcrumbs', \App\acf_page_id() ) ) {
    echo '<div class="container breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">';
    bcn_display();
    echo '</div>';
  }
@endphp
