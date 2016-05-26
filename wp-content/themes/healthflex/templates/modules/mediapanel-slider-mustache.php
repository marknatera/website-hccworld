<div class="head_panel">
  <div class="slider_wrapper">
    <div id="head_panel_slider" class="owl-carousel">

    {{# slides }}

      <!-- ============================ SLIDE ==========================-->

      {{^ full_height }} <div class="stretchy_wrapper ratio_slider"> {{/ full_height }}

        <div style="background-image: url({{ bg_image_url }});" aria-hidden="true" class="item {{ full_height }} {{ color_set }} {{ transparent_film }} {{ caption_neutralizetext }}">
          <div class="container">

          {{# captions }}

            <div class="caption {{ caption_align }} {{ caption_headingstyle }} {{ caption_size }} {{ caption_textalign }}">

              <div class="inner {{ caption_colorset }} {{ caption_transparentfilm }} {{ caption_animation }}">

                {{# caption_title }} <div class="t1">{{{ caption_title }}}</div> {{/ caption_title }}
                {{# caption_subtitle }} <div class="t2">{{{ caption_subtitle }}}</div> {{/ caption_subtitle }}
                {{# caption_secondarytitle }} <div class="t3">{{{ caption_secondarytitle }}} </div> {{/ caption_secondarytitle }}
                {{# caption_secondarytext }} <p class="desc hidden-xxs">{{{ caption_secondarytext }}}</p> {{/ caption_secondarytext }}

                {{# caption_button }} 
                  <div>
                  <a href="{{ caption_button_url }}" target="{{ caption_button_target }}" class="{{ caption_button_size }} {{ caption_button_style }}">
                    {{ caption_button_text }}
                  </a>
                  </div>
                {{/ caption_button }} 

              </div>
            </div>

          {{/ captions }}

          </div>
        </div>

      {{^ full_height }} </div> {{/ full_height }}

      <!-- END========================= SLIDE ==========================-->
    {{/ slides }}

    </div>
  </div>
</div>
