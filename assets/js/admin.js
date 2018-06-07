(function($){
    $(function(){
        let modal;
        let modal2;
        let descptionCheck;
        if ( $( "#modal1.modal" ).length ) {
            modal = $("#modal1.modal" );
            modal.modal({
                dismissable: false,
                opacity: 0.5
            });

        }
        if ( $( "#modal2.modal" ).length ) {
            modal2 = $("#modal2.modal" );
            modal2.modal({
                dismissable: false,
                opacity: 0.5,
                onOpenEnd: function() { checkModal(); },
                onCloseStart: function() { checkModal(true); }
            });

        }
        if ($( ".tooltipped" ).length ){
            $('.tooltipped').tooltip();
        }

    $('form#poster-twitter-wp-admin').submit(function(e){
       e.preventDefault();

       let consumerKey = $( "input[name='poster-twitter-wp-consumer-key-ptwp']" ).val();
       let consumerSecret = $( "input[name='poster-twitter-wp-consumer-secret-ptwp']" ).val();

       const pattern = /[0-9]+[a-zA-Z]/i

        if (!pattern.test(consumerKey)){
            modal.modal('open');
            modal.children('div').children('div.center').children('.material-icons').text('local_cafe');
            modal.children('div').children('h3').text(postertwitterwp.consumerKey);
            setTimeout(function(){ modal.modal('close'); }, 1000);
           return;
        }

        if (!pattern.test(consumerSecret)){
            modal.modal('open');
            modal.children('div').children('div.center').children('.material-icons').text('local_cafe');
            modal.children('div').children('h3').text(postertwitterwp.consumerSecret);
            setTimeout(function(){ modal.modal('close'); }, 1000);
            return;
        }

        $.ajax({
            type: 'POST',
            url:  ajaxurl,
            data: $(this).serialize() + '&action=poster_twitter_wp',
            beforeSend: function(){
                modal.modal('open');
                modal.children('div').children('h3').text(postertwitterwp.loadSave);
                modal.children('div').children('div.center').children('.material-icons').text('local_cafe');
            },
            success: function(r){
                modal.children('div').children('h3').text(postertwitterwp.successSave);
                modal.children('div').children('div.center').children('.material-icons').text('done');
                window.location.replace(r);
            },
            error: function(x, s, e){
                modal.children('div').children('h3').text(postertwitterwp.msgError + '\r\n\r\n' + x.responseText + s.status + e.error);
                modal.children('div').children('div.center').children('.material-icons').text('error');
            }
        });
    });

     $('#poster-twitter-wp-login-twitter').click(function() {
         let id = $(this).attr("data-id");
         $.ajax({
             type: 'POST',
             url: ajaxurl,
             data: 'iduser='+id+'&login=user&action=poster_twitter_wp',
             beforeSend: function(){
                 modal.modal('open');
                 modal.children('div').children('div.center').children('.material-icons').text('local_cafe');
             },
             success: function(r){
                 modal.children('div').children('h3').text(postertwitterwp.redirectTwitter);
                 window.location.replace(r);
             }
         });
     });


     $('form#poster-twitter-wp-tweet-ptwp').submit(function(e){
         e.preventDefault();
         let tweet = $('textarea[name="postertwitterwpmessage"]').val();

         if (!checkContent(tweet,true))
             return;

         $.ajax({
             type: 'POST',
             url: ajaxurl,
             data: $(this).serialize() + '&action=poster_twitter_wp',
             beforeSend: function(){
                 modal.modal('open');
                 modal.children('div').children('h3').text(postertwitterwp.loadSaveMsj);
                 modal.children('div').children('div.center').children('.material-icons').text('local_cafe');
             },
             success: function(r){
                 modal.children('div').children('h3').text(postertwitterwp.successSaveMsj);
                 modal.children('div').children('div.center').children('.material-icons').text('done');
                 setTimeout(function(){ modal.modal('close'); }, 3000);
             }
         });

     });


     $('#poster-twitter-wp-reset').click(function(){
         let reset = confirm(postertwitterwp.resetConfirm);
         if(reset){
             let id = $(this).attr("data-id");
             $.ajax({
                 type: 'POST',
                 url: ajaxurl,
                 data: 'iduser='+id+'&reset=tw&action=poster_twitter_wp',
                 beforeSend: function(){
                     modal.modal('open');
                     modal.children('div').children('h3').text(postertwitterwp.resetLoad);
                 },
                 success: function(r){
                     window.location.reload();
                 }
             });
         }
     });


        $.validator.setDefaults({
            errorClass: 'invalid',
            validClass: "valid",
            errorPlacement: function(error, element) {
                $(element)
                    .closest("form")
                    .find("label[for='" + element.attr("id") + "']")
                    .attr('data-error', error.text());
            }
        });


        $("#poster-twitter-wp-tweet-ptwp").validate({
            rules: {
                postertwitterwpmessage: {
                    required: true
                }
            }
        });


        function determineConcidence(msj){
            let pattern = /{+([a-zA-Záéíóúñ"'#$%&/()*-_¿?/¡!\s])+(\|)+([a-zA-Záéíóúñ"'#$%&/()*-_¿?/¡!\s])+(})+/i;
            let count = (msj.match(/{+([a-zA-Záéíóúñ"'#$%&/()*-_¿?/¡!\s])+(\|)+([a-zA-Záéíóúñ"'#$%&/()*-_¿?/¡!\s])+(})+/g) || []).length;
            if (pattern.test(msj) && count > 2)
                return true;
            return false;
        }

        function checkContent(message){

            if (message.length < 5){
                $('.messageText').show();
                $('.messageText .card-content p').text(postertwitterwp.msgPostLength);
                return false;
            }

            if (!determineConcidence(message)){

                $('.messageText').show();
                $('.messageText .card-content p').text(postertwitterwp.msgPostCoincidence);

                return false;
            }
            return true;
        }

        function checkModal(close = false){
            let duration = '?start=144&end=237';
            if($('div#modal2').is(":visible")){
                let videoSRC = $('div#modal2').attr("data-video"),
                    videoSRCauto = videoSRC + duration + "&modestbranding=1&rel=0&controls=0&showinfo=0&html5=1&autoplay=1";
                if (close){
                    $('div#modal2 iframe').attr('src', '');
                }else{
                    $('div#modal2 iframe').attr('src', videoSRCauto);
                }
            }
        }

        let URLactual = $(location).attr('href');
        if(URLactual.includes('config-postertwitterwp&oauth_token'))
            window.location.replace(postertwitterwp.urlAdmin);
        if(URLactual.includes('role-postertwitterwp&oauth_token'))
            window.location.replace(postertwitterwp.urlUser);
    });
})(jQuery);