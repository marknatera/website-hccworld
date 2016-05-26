<!-- ========================== TEASER BOX ==========================-->

<div class="teaser_box wpb_content_element {{ text_align }} {{ image_hover }} {{ text_colorset }} {{ boxed_styling }} {{ same_height }} {{# button }} with_button {{/ button }}">

  {{# media_type_image }}
  <div class="figure {{ media_colorset }}">
  {{/ media_type_image }}

  {{# media_type_icon }}
  <div class="figure {{ media_colorset }}">
  {{/ media_type_icon }}

    {{# teaser_link_url }}
      <a href="{{ teaser_link_url }}" title="{{ teaser_link_title }}" target="{{ teaser_link_target}}"> 
    {{/ teaser_link_url }}

      {{# media_type_image }}
        
        {{# aplied_media_ratio }} <div class="{{ figure_classes }}" style="background-image:url('{{ image }}')"></div> {{/ aplied_media_ratio }}
        
        {{# no_media_ratio }} <img src="{{ image }}" alt="{{ title }}"> {{/ no_media_ratio }}  

      {{/ media_type_image }}
        
      {{# media_type_icon }} 

        {{# aplied_media_ratio }}<div class="{{ figure_classes }}"><i class="{{ icon }}"></i></div>{{/ aplied_media_ratio }}

        {{# no_media_ratio }}<i class="{{ icon }}"></i>{{/ no_media_ratio }}

      {{/ media_type_icon }}

    {{# teaser_link_url }}
    </a>
    {{/ teaser_link_url }}

  {{# media_type_image }}  
  </div>
  {{/ media_type_image }}  

  {{# media_type_icon }}  
  </div>
  {{/ media_type_icon }} 

  {{# title }}
  <div class="content {{ text_align }} {{ text_boxed_styling }} {{# button }} with_button {{/ button }}">
    
    <div class="hgroup">
       <h4>{{{ title }}}</h4>
      {{# subtitle }} <p>{{{ subtitle }}}</p> {{/ subtitle }}
    </div>

    {{# content }} <div class="desc"><p>{{{ content }}}</p></div> {{/ content }}

    {{# button }}
    <div class="link centered">
        <a href="{{ teaser_link_url }}" {{ teaser_link_title }} {{ teaser_link_target }} class="btn btn-xs {{ button_style }}"><strong>{{{ button_text }}}</strong></a>
    </div>
    {{/ button }}

  </div>
  {{/ title }}

</div>

<!-- END======================= TEASER BOX ==========================-->
