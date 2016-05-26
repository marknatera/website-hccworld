//================== APPOINTMENT FORM ========================================

jQuery(function($){

  var currentForm      = "APPOINTMENT_FORM";

  var contactForm      = $("#" + themeConfig[currentForm].elementId);                              
  var contactScript    = themeConfig[currentForm].scriptFile;      
  var captcha_code_src = themeConfig[currentForm].captchaFile;    
  var requiredFields   = themeConfig[currentForm].requiredFields;
  var successStyle     = {"border-color":"green"};
  var failureStyle     = {"border-color":"red"};
  var neutralStyle     = {"border-color":"transparent"};
  var warningClass     = "alert alert-warning alert-dismissable";
  var warningFadeSpeed = themeConfig[currentForm].warningFadeSpeed;
  var enableCaptcha    = themeConfig[currentForm].captchaEnable;

  var contactType      = contactForm.find("#contact_type"); 
  var submitButton     = contactForm.find("#submit_btn");       
  var user_name        = contactForm.find('#name');             
  var user_phone       = contactForm.find('#phone');            
  var user_email       = contactForm.find('#email');            
  var user_subject     = contactForm.find('#subject');          
  var user_message     = contactForm.find('#message');          
  var user_captcha     = contactForm.find('#captcha');
  var captcha_code     = contactForm.find(".captcha-code");
  var notice           = contactForm.find("#notice");   
  var department       = contactForm.find('#department');             
  var birthdate        = contactForm.find('#birthdate');             
  var sex              = contactForm.find('#sex');             
  var appointment_date = contactForm.find('#appointment_date');             

  var default_values = {
    captcha          : user_captcha.val(),
    email            : user_email.val(),
    message          : user_message.val(),
    name             : user_name.val(), 
    phone            : user_phone.val(),
    department       : department.val(),
    appointment_date : appointment_date.val(),
    birthdate        : birthdate.val(),
    sex              : sex.val()
  };

  contactForm.find("input, textarea").on( "focus", function(event){
    event.currentTarget.value = ( event.currentTarget.value === default_values[event.currentTarget.id] ) ? "" : event.currentTarget.value;
  }).on("blur", function(event){
    event.currentTarget.value = ( event.currentTarget.value === "" ) ? default_values[event.currentTarget.id] : event.currentTarget.value;
  });

  submitButton.click(function(event){ 

      event.preventDefault();

      var user_name_val        = user_name.val(); 
      var user_phone_val       = user_phone.val();
      var user_email_val       = user_email.val();
      var user_message_val     = user_message.val();
      var user_captcha_val     = user_captcha.val();

      var appointment_date_val = appointment_date.val();
      var birthdate_val        = birthdate.val(); 
      var department_val       = department.val(); 
      var sex_val              = sex.val(); 

      _p.debugLog( "[OK] SUBMIT BUTTON CLICKED" );
      if ( user_name_val === undefined || user_email_val === undefined || user_message_val === undefined ) { _p.debugLog( "[ERROR] INPUT FIELDS NOT FOUND" ); return false; }

      var output;

      // SIMPLE VALIDATION AT CLIENT'S END 

      var proceed         = true;
      
      if ( notice.is(":visible") ) notice.hide();
      notice.stop(true);

      if ( 

        user_name_val === "" || 
        user_name_val === default_values.name ||

        user_email_val === "" || 
        user_email_val === default_values.email ||

        user_message_val === "" || 
        user_message_val === default_values.message ||

        ( enableCaptcha && user_captcha_val.length < 1 || user_captcha_val === default_values.captcha ) ||

        ( user_phone.length > 0 && "" === user_phone_val ) || 
        ( user_phone.length > 0 && default_values.phone === user_phone_val  ) ||

        appointment_date_val === "" || 
        appointment_date_val === default_values.appointment_date ||

        department_val === "" || 
        department_val === default_values.department ||

        birthdate_val === "" || 
        birthdate_val === default_values.birthdate ||

        sex_val === "" || 
        sex_val === default_values.sex

        ){
             _p.debugLog( "[WARN] FIELDS ARE EMPTY" );
             notice.removeClass().html( requiredFields ).addClass( warningClass ).fadeIn( warningFadeSpeed ).delay(5000).fadeOut();
             proceed = false;
        }

      if ( user_name_val    === "" || default_values.name == user_name_val ) { user_name.css( failureStyle ); proceed = false; } else { user_name.css( successStyle ); }
      if ( user_phone.length > 0 ) {
        if ( user_phone_val === "" || default_values.phone == user_phone_val || user_phone_val.match(/^[\d- ]{6,}$/) === null ) { user_phone.css( failureStyle ); proceed = false; } else { user_phone.css( successStyle ); }
      }
      if ( user_email_val   === "" || user_email_val.match(/^\S+@\S+\.\S{2,}$/) === null ) { user_email.css( failureStyle ); proceed = false; } else { user_email.css( successStyle ); }
      if ( user_message_val === "" || default_values.message == user_message_val || user_message_val.length < 5 ) { user_message.css( failureStyle ); proceed = false; } else { user_message.css( successStyle ); }
      if ( enableCaptcha ){ if( user_captcha_val === "" || user_captcha_val === default_values.captcha ){ user_captcha.css( failureStyle ); proceed = false; } else { user_captcha.css( successStyle ); } }

      if ( department_val    === "" || default_values.department == department_val ) { department.css( failureStyle ); proceed = false; } else { department.css( successStyle ); }
      if ( birthdate_val    === "" || default_values.birthdate == birthdate_val ) { birthdate.css( failureStyle ); proceed = false; } else { birthdate.css( successStyle ); }
      if ( sex_val    === "" || default_values.sex == sex_val ) { sex.css( failureStyle ); proceed = false; } else { sex.css( successStyle ); }
      if ( appointment_date_val === "" || default_values.appointment_date == appointment_date_val ) { appointment_date.css( failureStyle ); proceed = false; } else { appointment_date.css( successStyle ); }

      // LOOKS GOOD! PROCEED
      if( proceed ) 
      {
          _p.debugLog( "[OK] PROCEED WITH AJAX POST" );
          // DATA TO BE SENT TO SERVER SCRIPT
          var post_data = {
              'contactType'         : contactType.val(),
              'userName'            : user_name_val, 
              'userPhone'           : user_phone_val,
              'userEmail'           : user_email_val, 
              'userMessage'         : user_message_val,
              'department'          : department_val, 
              'birthdate'           : birthdate_val, 
              'sex'                 : sex_val, 
              'appointment_date'    : appointment_date_val
          };

          if ( enableCaptcha ){ post_data.userCaptcha = user_captcha_val; }

          submitButton.addClass('loading');

          // AJAX POST TO SERVER
          $.post( contactScript, post_data, function(response, status, xhr){  

              submitButton.removeClass('loading');
              _p.debugLog( "STATUS: ", status );
              _p.debugLog( "RESPONSE: ", response );

              // LOAD JSON DATA FROM SERVER AND OUTPUT MESSAGE
              if ( response === null ) {
                  _p.debugLog( "[ERROR] RESPONSE TYPE: null" );
                  throw new Error("Response null");
              }
              if ( response.type == 'error' )
              {
                  output = response.text;
                  _p.debugLog( "[ERROR] RESPONSE TYPE: error. OUTPUT: ", output );
                  notice.removeClass().html(output).addClass( warningClass ).fadeIn( warningFadeSpeed ).delay(5000).fadeOut();

              } else {

                  output = response.text;
                  _p.debugLog( "[OK] RESPONSE TYPE: success. OUTPUT: ", output );
                  // RESET ALL INPUT FIELD VALUES
                  contactForm.find("input[type!='submit'], textarea").val("").css( neutralStyle );
                  notice.removeClass().html( output ).addClass( warningClass ).fadeIn( warningFadeSpeed ).delay(5000).fadeOut();
                  user_name.val( default_values.name ); 
                  user_phone.val( default_values.phone );
                  user_email.val( default_values.email ); 
                  user_message.val( default_values.message );
                  captcha_code.css( "backgroundImage", "url('" +  captcha_code_src + "')" );
                  user_captcha.css( neutralStyle );

                  department.val( default_values.department ); 
                  birthdate.val( default_values.birthdate ); 
                  sex.val( default_values.sex ); 
                  appointment_date.val( default_values.appointment_date ); 


              }
          }, 'json').fail(function(data){ submitButton.removeClass('loading'); _p.debugLog( "Response Text: ", data.responseText, "Status:", data.status, data.statusText ); });
          
      } else {
          _p.debugLog( "[ERROR] COULD NOT PROCEED WITH AJAX POST" );
      }
  });

  // RESET PREVIOUSLY SET BORDER COLORS AND HIDE MESSAGE ON .keyup()
  contactForm.find("input[type!='submit'], textarea").keyup(function(e) { 
      $(this).css( neutralStyle ); 
  });

});

//END=============== APPOINTMENT FORM ============================================
