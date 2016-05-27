<!-- ========================== TESTIMONIALS ==========================-->

    <div class="testimonial testimonial-slider">
      <ul class="slides" id="{{ id }}">
        {{# testimonials }}
          <li>
			   {{{ content }}}
			{{# person_name }}
			<div class="name">
	          	<strong>{{{ person_name }}}</strong> 
	          	{{{ person_role }}}
          	</div>
			{{/ person_name }}

          </li>
        {{/ testimonials }}
      </ul>
    </div>

<!-- END======================= TESTIMONIALS ==========================-->
