<div class="newsletter_form {{ css }}">
  <form id="newsletter" action="{{ action }}" method="POST" class="form-inline {{ alignment }}">
    <input id="email" placeholder="{{ email_placeholder }}" name="email" type="text" class="form-control">
    <input type="hidden" name="nonce" id="nonce" value="{{ nonce }}">
    <button type="submit" class="btn btn-secondary form-control">
        <span class="fa fa-refresh fa-refresh-animate hidden"></span>
        {{{ icon }}} {{ button_text }}
        <span id="newsletterResponse" class="btn btn-primary hidden">MESSAGE</span>
    </button>
  </form>
</div>