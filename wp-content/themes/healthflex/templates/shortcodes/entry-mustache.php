<!-- ========================== ENTRY SHORTCODE ELEMENT ==========================-->
{{# image }}
<div class="entry {{ class }}">
{{/ image }}

{{# icon }}
<div class="entry entry_icon_wrapper {{ class }}">
{{/ icon }}

	{{# link }}
		<a href="{{ url }}" target="{{ target }}" title="{{ title }}">  
	{{/ link }}

		{{# image }}
	      <div class="entry_photo {{ media_ratio }} {{ image_valign }}" style="background-image:url('{{ image }}')"></div>
		{{/ image }}

		{{# icon }}
	      <div class="entry_icon"><i class="{{ icon }}"></i></div>
		{{/ icon }}

		{{# content }}
	      <div class="entry_text">{{{ content }}}</div>
		{{/ content }}

	{{# link }}
		</a>
	{{/ link }}

</div>

<!-- END======================= ENTRY SHORTCODE ELEMENT ==========================-->


