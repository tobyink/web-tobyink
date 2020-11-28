@if ( ! get_field( 'hide_footer', \App\acf_page_id() ) )
<footer class="content-info">
  <div class="container">
    @php dynamic_sidebar('sidebar-footer') @endphp
  </div>
</footer>
@endif
