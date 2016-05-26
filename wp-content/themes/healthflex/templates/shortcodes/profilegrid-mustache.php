<div class="team_members_grid row">

  {{# profiles }}

    <div class="{{ col_class }}">
      <div class="team_member teaser_box centered same_height_col {{ color_set }}">
        
        <a href="{{ permalink }}" style="background-image: url('{{ image }}')" data-colorset="{{ color_set }}" class="{{ link_to }} figure stretchy_wrapper ratio_1-1"></a>
        
        <div class="content boxed {{# link_button }} with_button {{/ link_button }}">

          <div class="hgroup">
            <h4>{{ name }}</h4>
            {{# subtitle_text }} <p>{{ subtitle_text }}</p> {{/ subtitle_text }}
          </div>

          <div class="team_social">
          {{# social_profiles }}
            <a href="{{ social_url }}"><i class="fa {{ social_icon }}"></i></a>
          {{/ social_profiles }}
          </div>

          <div class="desc">       
            <p>{{{ content }}}</p>
          </div>

          {{# link_button }}
          <div class="link">
            <a href="{{ permalink }}" data-colorset="{{ color_set }}" class="{{ link_to }} btn btn-xs {{ button_style }}">{{ button_text }}</a>
          </div>
          {{/ link_button }}

        </div>

      </div>
    </div>

  {{/ profiles }}
                 
</div>