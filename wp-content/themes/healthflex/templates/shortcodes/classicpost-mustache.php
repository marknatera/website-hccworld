<!-- ========================== CLASSIC POST SHORTCODE ELEMENT ==========================-->

<article class="post pl_classic_post {{ layout_styling }} {{ css }}">

    <h2 class="post_title">
    	<a href="{{ link_url }}" alt="{{ link_title }}" target="{{ link_target }}" class="{{ ajax }}">{{{ title }}}</a>
    </h2>

    <p class="post_subtitle">{{ subtitle }}</p>

    <div class="post_figure_and_info">
      <div class="post_sub">
      		<span class="post_info post_author">{{ post_meta }}</span>
      </div>
      <figure class="{{ media_ratio }}">
      	<a style="background-image: url('{{ image }}'); {{ style }}" title="{{ title }}" href="{{ link_url }}" class="{{ ajax }} {{ image_valign }}"></a>
      </figure>
    </div>

    <p>{{{ description }}}</p>

    <!-- <a class="btn {button-color} {{ ajax }}" href="{{ link_url }}">{ button_title }(check class also)</a> -->

    <a href="{{ link_url }}" class="btn {{ button_size }} {{ button_style }} {{ button_with_icon }} {{ button_icon_align }} {{ ajax }}" title="{{ button_title }}"  target="{{ button_target }}">
        {{# button_icon_align_left }} <i class="{{ button_icon }}"></i> {{/ button_icon_align_left }}
        {{{ button_text }}}
        {{# button_icon_align_right }} <i class="{{ button_icon }}"></i> {{/ button_icon_align_right }}
    </a>

</article>


<!-- END======================= CLASSIC POST SHORTCODE ELEMENT ==========================-->


