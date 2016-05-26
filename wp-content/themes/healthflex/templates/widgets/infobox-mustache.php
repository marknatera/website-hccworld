<!-- ===================== INFOBOX ========================-->

<div class="widget pl_html_widget boxed black_section transparent_film {{# animation }}wow {{ animation }} {{/ animation }}">

   {{# title }}
   <h4 class="{{ title_align }}">{{ title }}</h4>
   {{/ title }}

    <p>
    	{{# image_uri}}
    	<img src="{{ image_uri }}" width="90" alt="image-desc" class="pull-left">
    	{{/ image_uri}}
		{{ textarea }}
    </p>

    {{# button }}
    	{{{ button }}}
    {{/ button }}

</div>

<!-- END================== INFOBOX ========================-->
