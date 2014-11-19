(function($) {
  "use strict";
  var timeout = null;
  $(function() {
    $('.fullscreen_image').hover(function(){
      $('.fullOverlay').show();
    }, function(){
      timeout = setTimeout(function(){
        $('.fullOverlay').hide();
      }, 250);
    });
    $('.fullOverlay').hover(function(){
      clearTimeout(timeout);
    }, function(){
      $('.fullOverlay').hide();  
    });
    $('.fullOverlay').hide();
    $('#selectImage').click(function inputClicked(){
      $('.fullOverlay').hide();
      var media = new THB_MediaSelector({
        select: function(selected_images) {
          console.log(selected_images);
          $('#fullscreen_image').val(selected_images.id);
          if($('.fullscreen_image').length > 0)
            $('.fullscreen_image').attr('src', selected_images.sizes.thumbnail.url);
          else
            $('<img/>',{
              'src': selected_images.sizes.thumbnail.url,
              'width':'150px',
              'height':'150px',
            }).appendTo('#fullscreen-image .inside')
        }
      });
      media.open();
    });
  });

}(jQuery));