
$(function() {

    $('[name="'+AUTOCOMPLETE_INPUT_NAME+'"]').autocomplete({
      source: function( request, response ) {
         $.ajax({
           url: AUTOCOMPLETE_URL_AJAX,
           dataType: "jsonp",
           data: {
              q: request.term
           },
           success: function( data ) {
              // console.log(data);
              response( data );
           }
         });
      },

      minLength: 3,

      select: function( event, ui ) {
         console.log('id: '+ ui.item.value + '\n'+'label: '+ ui.item.label);

         $('[name="'+AUTOCOMPLETE_INPUT_NAME+'"]').val(ui.item.label);
         $('[name="'+AUTOCOMPLETE_INPUT_ID+'"]').val(ui.item.value);
         return false;
      },
      open: function() {
         $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
      },
      close: function() {
         $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
      }
    });

});
