<div class="section_header {{ subtitle_position }} {{ extra_class }} {{ css }} {{{ type }}} {{{ align }}}">
    {{# subtitle_top }}
        <p>{{{ subtitle }}}</p>
    {{/ subtitle_top }}

    {{{ title }}}

    {{^ subtitle_top }}
        <p>{{{ subtitle }}}</p>
    {{/ subtitle_top }}
</div>