<div class="pricing_plan {{ features_colorset }}">  

  {{# image }}

    <div class="{{ image_ratio }}">
      <div style="background-image:url('{{ image_url }}');" class="pricing_plan_photo"></div>
      {{# special }}<div class="heart beating"><div class="heart-inner"><div class="heart-label"></div></div></div>{{/ special }}
    </div>

  {{/ image }}

  <div {{{ heading_vectorbcg }}} class="plan_title {{ heading_colorset }}">
    <h3>{{{ title }}}</h3>
    <p>{{{ subtitle }}}</p>
  </div>
  <div class="the_price {{ pricing_colorset }}">{{ amount }}<small>{{ cycle }}</small></div>
  <div class="the_offerings {{ the_offerings }}">
  {{# features }} {{{ . }}}   <br /> {{/ features }}

  {{# button }}

  <a href="{{ button_link_url }}" class="{{ button_size }} {{ button_style }} {{ button_with_icon }} {{ button_icon_align }}" title="{{ button_link_title }}"  target="{{ button_link_target }}">

    {{ button_text }}

    {{# button_with_icon }} <i class="{{ button_icon }}"></i>  {{/ button_with_icon }}

  </a>

  {{/ button }}

  </div>

</div>
