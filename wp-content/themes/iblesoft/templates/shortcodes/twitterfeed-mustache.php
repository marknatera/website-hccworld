<!-- ========================== TWITTER FEED ==========================-->

  <div class="twitter_feed_wrapper row">

    <div class="col-md-2">
      <div class="twitter_feed_icon wow slideInLeft"><a href="https://twitter.com/{{ twitter_screen_name }}" target="_blank"><i class="fa fa-twitter"></i></a></div>
    </div>

    <div class="col-md-8">
      <div id="twitter_slider" class="twitter_slider" style="display:none;">
        {{# twitter_feed }}
          <blockquote>
            <p>{{ twitter_screen_name }} / <a target="_blank" href="https://twitter.com/{{ twitter_screen_name }}">@{{ twitter_screen_name }}</a> {{# twitter_date }} &bull; {{ twitter_date }} {{/ twitter_date }}</p>
            <p><a href="{{ twitter_link }}" target="_blank" >{{ twitter_text }}</a></p>
          </blockquote>
        {{/ twitter_feed }}
      </div>
    </div>

  </div>

<!-- END======================= TWITTER FEED ==========================-->

<script type="text/javascript">

jQuery(function($){

  $("#twitter_slider").owlCarousel({

    items              : 1,
    loop               : true,
    autoplay           : true,
    autoplayTimeout    : 3000,
    autoplayHoverPause : true,
    onInitialized      : function(){  this.$element.css('display','block');  }

  });

});


</script>