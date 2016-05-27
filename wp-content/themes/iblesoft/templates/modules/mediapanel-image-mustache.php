<div class="head_panel">
    <div style="background-image: url({{ media_url }})" class="full_width_photo {{ imgvalign }} {{ parallax }} {{ full_height }}">

    {{# title_subtitle }}

      <div class="hgroup">

      {{# title }}
        <div class="title {{ title_diagonal_class }} {{ title_colorset }} {{ title_backgroundtype }} {{ text_align }}">
          <div class="container">
            <h1>{{{ title_text }}}</h1>
          </div>
        </div>
      {{/ title }}

        <div class="subtitle {{ subtitle_diagonal_class }} {{ subtitle_colorset}} {{ subtitle_backgroundtype }} {{ text_align }}">
          <div class="container">   
            <p>{{{ subtitle_text }}}</p>
          </div>
        </div>

      </div>

    {{/ title_subtitle }}

    </div>
</div>
