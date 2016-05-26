  <!-- ========================== TIMETABLE =============================-->

  {{{ before_widget }}}

  <div style="background-image:url('{{ image_uri }}')" class="widget pl_timetable_widget secondary_section boxed transparent_film">

    {{# title }}
      <h4 class="centered"> {{{ title }}} </h4>
    {{/ title }}

    <div class="timetable">
 
      <table class="timetable_hours">

      {{# rowData }}
        <tr>
          <td>{{ day }}</td>
          <td>{{ time }}</td>
        </tr>
      {{/ rowData }}

      </table>
    </div>
 
    {{# button }}
      {{{ button }}}
    {{/ button }}
 
  </div>

  {{{ after_widget }}}

 <!-- END======================= TIMETABLE =============================-->
 

