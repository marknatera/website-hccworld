<!-- =============================  UNIGRID =============================-->

{{# strict }}

  <div class="portfolio_grid {{ double_helix }}">

    {{# category_filters }}
      <ul id="filt_{{ postsgrid_id }}" class="portfolio_filters">
        {{# showall }}
        <li><a href="#" data-filter="*" class="active">{{ showall }}</a></li>
        {{/ showall }}

        {{# category_filter }}
        <li><a href="#" data-filter=".{{ data-filter }}">{{ category_name }}</a></li>
        {{/ category_filter }}

      </ul>
    {{/ category_filters }}

    <div id="cont_{{ postsgrid_id }}" class="row isotope_portfolio_container isotope">

      {{# section_posts }}
      <div class="{{ post_cat_classes }} col-xs-12 col-sm-6 col-md-{{ col }} col-lg-{{ col }}">

        <div class="portfolio_item {{ media_ratio }}">

          <a href="{{ permalink }}" class="{{ linkify }} {{ lightbox }}">

            <div style="background-image:url('{{ background_image }}')" class="figure"></div>

            <div class="portfolio_title skincolored_section"> 
              <h3><!-- <i class="fa fa-image | fa-youtube | fa-vimeo-square | fa-youtube-play"></i> --> {{{ title }}} </h3>
            </div>
            
            <div class="portfolio_description skincolored_section portfolio_gradient">
              <p>{{{ excerpt }}}</p>

              {{# is_product }}
              <p>{{ product_categories }} {{ post_categories }}<br>

                {{# has_sale_price }}
                <del><span class="amount">{{{ product_price }}}</span></del>
                <ins><span class="amount">{{{ product_sale_price }}}</span></ins>
                {{/ has_sale_price }}

                {{^ has_sale_price }}
                <strong><span class="amount">{{{ product_price }}}</span></strong>
                {{/ has_sale_price }}

              </p>
              {{/ is_product }}

            </div>
          
          </a>

        </div>

      </div>
      {{/ section_posts }}

    </div>
  </div>

{{/ strict }}

{{# masonry }}

  <div>
  {{# category_filters }}
      <ul class="portfolio_filters" id="filt_{{ postsgrid_id }}">
          {{# showall }}
              <li><a href="#" data-filter="*">{{ showall }}</a></li>
          {{/ showall }}
          {{# category_filter }}
              <li><a href="#" data-filter=".{{ data-filter }}">{{ category_name }}</a></li>
          {{/ category_filter }}
      </ul>
  {{/ category_filters }}
  </div>

  <div class="post-grid masonry boxed_children {{ double_helix }}">

    <div class="row isotope_portfolio_container" id="cont_{{ postsgrid_id }}">

        {{# section_posts }}

          <!-- BLOG TYPE MASONRY LAYOUT -->

          {{# blog_masonry }}
    
          <div class="{{ post_cat_classes }} col-md-{{ col }}">
            <article class="post {{ masonry_blog_color_set }}">
              <h2 class="post_title"><a href="{{ permalink }}">{{{ title }}}</a></h2>
              <p class="post_subtitle">You can make a big difference with healthy lifestyle changes.  </p>
              <div class="post_figure_and_info">
                <div class="post_sub"><a href="#"><span class="post_info post_author">By Dr. Henrik Pleth</span></a></div>
                {{# background_image }} <figure class="{{ media_ratio }}"><a href="{{ permalink }}" title="Post" style="background-image: url('{{ background_image }}')"></a></figure> {{/ background_image }}
              </div>
              <p>{{{ excerpt }}}</p><a href="{{ permalink }}" class="btn {{ linkify }} {{ button_style }}">Read More</a>
            </article>
          </div>

          {{/ blog_masonry }}

          <!-- GALLERY TYPE MASONRY LAYOUT -->

          {{# gallery_masonry }}

          <div class="{{ post_cat_classes }} col-sm-{{ col }} col-md-{{ col }}">
             <div class="portfolio_item">
               <a href="{{ permalink }}" class="{{ linkify }} {{ lightbox }}">
                   <img src="{{ image }}" alt="{{{ image_title }}}" />
                   <div class="overlay">
                      <div class="desc">
                         <h3>{{{ title }}}</h3>
                         <p>{{ excerpt }}</p>
                      </div>
                   </div>
               </a>
             </div>
          </div>

          {{/ gallery_masonry }}

        {{/ section_posts }}

    </div>
  </div>

{{/ masonry }}

<!-- END==========================  UNIGRID =============================-->

<script>
jQuery(function($) {

  "use strict"; 

  // IMAGELIGHTBOX
  var activityIndicatorOn  = function(){  $( '<div id="imagelightbox-loading"><div></div></div>' ).appendTo( 'body' );  };
  var activityIndicatorOff = function(){  $( '#imagelightbox-loading' ).remove();  };
  var overlayOn            = function(){  $( '<div id="imagelightbox-overlay"></div>' ).appendTo( 'body' );  };
  var overlayOff           = function(){  $( '#imagelightbox-overlay' ).remove();  };
  var closeButtonOn        = function( instance ){  $( '<a href="#" id="imagelightbox-close">Close</a>' ).appendTo( 'body' ).on( 'click', function(){ $( this ).remove(); instance.quitImageLightbox(); return false; }); };
  var closeButtonOff       = function(){  $( '#imagelightbox-close' ).remove();  };
  var captionOn            = function(){
      var description = $( 'a[href="' + $( '#imagelightbox' ).attr( 'src' ) + '"] img' ).attr( 'alt' ) || "";
      if( description.length > 0 ) $( '<div id="imagelightbox-caption">' + description + '</div>' ).appendTo( 'body' );};
  var captionOnSingle      = function()
      {
          var description = $( 'a[href="' + $( '#imagelightbox' ).attr( 'src' ) + '"]' ).attr( 'title' ) || "";
          if( description.length > 0 )
              $( '<div id="imagelightbox-caption">' + description + '</div>' ).appendTo( 'body' );
      };
  var captionOnGallery     = function(){
          var description = $( 'a[href="' + $( '#imagelightbox' ).attr( 'src' ) + '"]' ) || "";
          if ( description.attr('data-description') !== "undefined" && description.attr('data-description') !== "" ){
              description = description.attr('data-description');
          } else if ( description.attr('datas-caption') !== "undefined" && description.attr('datas-caption') !== "" ) {
              description = description.attr('data-caption');
          }
          if( description && description.length > 0 )
              $( '<div id="imagelightbox-caption">' + description + '</div>' ).appendTo( 'body' );};
  var captionOff           = function(){  $( '#imagelightbox-caption' ).remove();  };
  var arrowsOn             = function( instance, selector ){
        if ( instance.length > 3 ){
          var $arrows = $( '<button type="button" class="imagelightbox-arrow imagelightbox-arrow-left"></button><button type="button" class="imagelightbox-arrow imagelightbox-arrow-right"></button>' );
              $arrows.appendTo( 'body' );
              $arrows.on( 'click touchend', function( e ){
                e.preventDefault();
                var $this   = $( this );
                var $target = $( selector + '[href="' + $( '#imagelightbox' ).attr( 'src' ) + '"]' );
                var index   = $target.index( selector );
                if( $this.hasClass( 'imagelightbox-arrow-left' ) ) {
                    index = index - 1;
                    if( !$( selector ).eq( index ).length ) index = $( selector ).length;
                } else {
                    index = index + 1;
                    if( !$( selector ).eq( index ).length ) index = 0;
                }
                instance.switchImageLightbox( index ); 
                return false;

          });
        }};
  var arrowsOff = function(){  $( '.imagelightbox-arrow' ).remove();  };
  var selectorGG = 'a.filterable_lightbox';                  // ENABLE ARROWS
  var imageLightboxOptions = {
      /* WITH ARROWS */
      onStart:        function() { arrowsOn( instanceGG, selectorGG ); overlayOn(); closeButtonOn( instanceGG ); }, 
      onEnd:          function() { arrowsOff(); overlayOff(); captionOff(); closeButtonOff(); activityIndicatorOff(); }, 
      onLoadEnd:      function() { $( '.imagelightbox-arrow' ).css( 'display', 'block' ); captionOnGallery(); activityIndicatorOff(); },
      onLoadStart:    function() { captionOff(); activityIndicatorOn(); }
  };

  // ISOTOPE

  var container = $('#cont_{{ postsgrid_id }}'); 
  var filt      = $('#filt_{{ postsgrid_id }} a'); 
      filt.eq(0).addClass('active'); 

  var selector = filt.eq(0).attr('data-filter'); 
      container.isotope({ filter: selector });

  var instanceGG;
      instanceGG = $( selector ).find( selectorGG ).imageLightbox( imageLightboxOptions );

  filt.click(function(){ 

    filt.removeClass('active'); 
    $(this).addClass('active'); 
    selector = $(this).attr('data-filter'); 
    container.isotope({ filter: selector }); 

    instanceGG.switchOff();
    instanceGG = $( selector ).find( selectorGG ).imageLightbox( imageLightboxOptions );

    return false; 

  }); 

  $(window).resize(function() {

        container.isotope({});

  });

}); 
</script>