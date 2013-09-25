/*jshint browser: true*/
/*global jQuery, wp*/
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */
(function() {
  "use strict";
  var $ = jQuery;

	// add color picker to the background setting
	$('.js-persona__color-picker').wpColorPicker();

  // Add a filepicker where it is needed
  var mediaUploaderFrames = {};
  $('.js-persona__file-picker').click(function(event) {
    event.preventDefault();

    var target = $(event.target);
    var id = target.attr('for');
    var mediaUploaderFrame = mediaUploaderFrames[id];
    if (mediaUploaderFrame) {
      return mediaUploaderFrame.open();
    }

    var mediaUploaderConfig = {
      className: 'media-frame js-persona__media-frame',
      frame: 'select',
      multiple: false,
      title: target.attr('data-title') || '',
      input: $('#' + target.attr('for'))
    };

    var mediaType = target.attr('data-type');
    if (mediaType) {
      mediaUploaderConfig.library = {
        type: mediaType
      };
    }

    mediaUploaderFrame = mediaUploaderFrames[id] =
            wp.media(mediaUploaderConfig);

    mediaUploaderFrame.on('select', function() {
      var attachment =
          mediaUploaderFrame.state().get('selection').first().toJSON();

      var url = attachment.url;
      if (mediaType === "image")
        url = getBase64ImageIfHttpSite(url);

      mediaUploaderConfig.input.val(url);
    });

    mediaUploaderFrame.open();
  });

  function getBase64ImageIfHttpSite(imgURL) {
      // based on
      // http://stackoverflow.com/questions/5420384/convert-an-image-into-binary-data-in-javascript
      // Create an empty canvas element
      if (document.location.protocol === "https://") return imgURL;

      var canvas = document.createElement("canvas");
      // if canvas could not be created, abort.
      if (!canvas) return imgURL;

      var img = document.createElement("img");
      img.src = imgURL;
      document.body.appendChild(img);


      canvas.width = img.width;
      canvas.height = img.height;

      // Copy the image contents to the canvas
      var ctx = canvas.getContext("2d");
      ctx.drawImage(img, 0, 0);

      // Get the data-URL formatted image
      // Firefox supports PNG and JPEG. You could check img.src to guess the
      // original format, but be aware the using "image/jpg" will re-encode the image.
      var dataURL = canvas.toDataURL("image/png");

      document.body.removeChild(img);

      return dataURL;
  }
}());

