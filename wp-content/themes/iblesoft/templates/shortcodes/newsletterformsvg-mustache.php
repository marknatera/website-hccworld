    <div class="newsletter_form svg_newsletter {{ css }}">
      <div class="row">
        <div class="col-md-6 left_area secondary_section transparent">
          <h3>{{ title }}</h3>
          <p>{{ subtitle }}</p>
        </div>
        <div class="col-md-6 right_area">
          <form id="newsletter" action="{{ action }}" method="POST" class="form-inline">
            <input id="email" placeholder="{{ email_placeholder }}" name="email" type="text" class="form-control newsletter_input">
            <input type="hidden" name="nonce" id="nonce" value="{{ nonce }}">
            <button type="submit" class="form-control btn btn-primary">
              <span class="fa fa-refresh fa-refresh-animate hidden"></span>
              {{ button_text }}
            <span id="newsletterResponse" class="btn btn-primary hidden btn-success">MESSAGE</span>
            </button>
          </form>
        </div>
      </div>
    </div>