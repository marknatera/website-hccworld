 <!-- ===================== LATEST NEWS: MUSTACHE ========================-->

 {{{ before_widget }}}

 <div class="pl_latest_news_widget">
 {{# title }}
 <h4>{{ title }} </h4>
 {{/ title }}
 <ul class="media-list">
  {{# posts}}

    <li class="media">

     <a href="{{ permalink }}" class="media-photo" style="background-image:url(' {{ thumbnail_url }} ')"></a> 

     <h5 class="media-heading">
      <a href="{{ permalink }}">{{ title }}</a>  <small>{{ date }}</small> 
     </h5>
     
     <p>{{ content }}</p>

    </li>

  {{/ posts}}
 </ul>
 </div>  

 {{{ after_widget }}}

 <!-- END================== LATEST NEWS: MUSTACHE ========================-->