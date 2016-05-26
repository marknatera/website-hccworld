<div class="knowledgebase {{ css }}">
  <div class="row">
  {{# items }}

    <div class="col-md-3 col-sm-6 same_height_col">
      <h5>{{ letter }}</h5>
      <ul>
      {{# terms }}
          <li><a href="{{ term_permalink }}" target="{{ term_link_target }}" title="{{{ term_title_attr }}}" data-tip="{{ term_title }}">{{{ term_title }}}</a></li>   
      {{/ terms }}
      </ul>
    </div>

  {{/ items }}
  </div>
</div>