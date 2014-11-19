(function ( $ ) {
	"use strict";

	$(function () {

		// Place your public-facing JavaScript here
    if (typeof fullscreen_image !== 'undefined' && fullscreen_image.url !== undefined)
      $.backstretch(fullscreen_image.url);

	});

}(jQuery));