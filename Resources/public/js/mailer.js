var captches = {}
var captchaInitialized = false

$(document).ready(function(){

  $('input[data-form], button[data-form], form input[data-captcha], form button[data-captcha]').click(formSubmiter)

});

function initRecaptcha()
{
  if(!captchaInitialized)
  {
      captchaInitialized = true;
      var recaptchaScript = document.createElement('script');
      recaptchaScript.setAttribute('src','https://www.google.com/recaptcha/api.js?render=' + $('form input[data-captcha], form button[data-captcha]').data('captcha'));
      document.head.appendChild(recaptchaScript);  
  }
}

function waitingForCaptcha(callback)
{
  setTimeout(function(){
      if(typeof grecaptcha !== 'undefined'){
          callback()
      }
      else waitingForCaptcha(callback)
  }, 100)
    
}


function formSubmiter(e)
{
  var $button = $(this)
  var $form = $(this).data('form') ? $('#' + $(this).data('form')) :  $(this).closest('form');
  var formId = $form.attr('id')
  $form.removeClass('was-validated')

  e.preventDefault();
  e.stopPropagation();

  if ($form[0].checkValidity() === false) {
        // console.log('form errors')
        $form.addClass('was-validated')
        if(typeof grecaptcha !== 'undefined' && typeof captches[formId] === 'undefined') grecaptcha.reset(captches[formId]) 
    }
    else
    {

        var submit = function()
        {
          // console.log('submit?');
          if(typeof $(this).data('formAjax') === 'undefined' || $(this).data('formAjax') === 0)
          {
            // console.log('submit');
            $form.submit()
            if(typeof grecaptcha !== 'undefined' && typeof captches[formId] === 'undefined') grecaptcha.reset(captches[formId]) 
          }
          else
          {

          $form.addClass('sending');
          var $preloader = $form.find('.preloader')
          if($preloader.length) $preloader.show();
          $.post($form.attr('action'), $form.serialize(), function(data){
            $form.removeClass('sending');
            $form.addClass('sent');
            if($preloader.length) $preloader.hide();
            {
               var $parent = $form.parent()
               $parent.find('.form-message').html(data);
               $parent.find('input[data-form], button[data-form], form input[data-captcha], form button[data-captcha]').click(formSubmiter)
            }

            if(typeof grecaptcha !== 'undefined' && typeof captches[formId] === 'undefined') grecaptcha.reset(captches[formId]) 
          })
        }
        }


        if(typeof $(this).data('captcha') !== 'undefined')
        {

          initRecaptcha();

        
          if(typeof captches[formId] === 'undefined')
          {


            waitingForCaptcha(function(){

                var widgetId = grecaptcha.render($button[0], {
                  sitekey : $button.data('captcha'),
                  theme : 'light',
                  size : 'invisible',
                  callback : submit.bind($button)
              });
          
              captches[formId] = widgetId
              grecaptcha.execute(widgetId)  

            })


          }
          else if(typeof grecaptcha !== 'undefined') {
            grecaptcha.reset(captches[formId])
          } 
        }
        else
        {
          submit.call(this)
        }


        
    }

}


