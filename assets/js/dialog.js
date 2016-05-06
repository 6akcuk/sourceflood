var Dialog = {
	overlays: {},

	init: function(id, overlays) {
		jQuery('<div id="'+ id + '-overlay" class="Dialog__overlay" onclick="Dialog.close(\''+ id +'\')"></div>\
			<div id="'+ id + '" class="Dialog" onclick="Dialog.close(\''+ id +'\')">\
				<div class="Dialog__container" onclick="Dialog.hideOverlays(\''+ id +'\'); event.cancelBubble = true;">\
					<div class="Dialog__title-wrap">\
						<div class="Dialog__title"></div>\
						<a class="Dialog__close" onclick="Dialog.close(\''+ id +'\')"><div class="tb-close-icon"></div></a>\
					</div>\
					<div class="Dialog__body">\
						<div class="ispinner ispinner--gray ispinner--animating">\
						  <div class="ispinner__blade"></div>\
						  <div class="ispinner__blade"></div>\
						  <div class="ispinner__blade"></div>\
						  <div class="ispinner__blade"></div>\
						  <div class="ispinner__blade"></div>\
						  <div class="ispinner__blade"></div>\
						  <div class="ispinner__blade"></div>\
						  <div class="ispinner__blade"></div>\
						  <div class="ispinner__blade"></div>\
						  <div class="ispinner__blade"></div>\
						  <div class="ispinner__blade"></div>\
						  <div class="ispinner__blade"></div>\
						</div>\
					</div>\
				</div>\
			</div>').appendTo('body');

		Dialog.overlays[id] = overlays;
	},

	open: function(id) {
		jQuery('#'+ id + '-overlay').show();
		jQuery('#'+ id).show();

		jQuery('body').css({
			overflow: 'hidden'
		});
	},

	close: function(id) {
		jQuery('#'+ id + '-overlay').hide();
		jQuery('#'+ id).hide();

		jQuery('body').css({
			overflow: 'auto'
		});

		Dialog.hideOverlays(id);
	},

	hideOverlays: function(id) {
		_.each(Dialog.overlays[id], function(overlay) {
			jQuery(overlay).hide();
		});
	},

	title: function(id, title) {
		var $title = jQuery('#'+ id).find('div.Dialog__title');

		if (title) $title.html(title);
		return $title;
	},
	
	body: function(id, body) {
		var $body = jQuery('#'+ id).find('div.Dialog__body');

		if (body) $body.html(body);
		return $body;
	},

	showProgress: function (event, id) {
		jQuery(event.target).attr('disabled', true);

		var $wrap = jQuery('#'+ id).find('div.Pixabay__body');
		var $wrapper = jQuery('<div class="Pixabay__wrapper">\
			<div class="ispinner ispinner--gray ispinner--animating">\
			  <div class="ispinner__blade"></div>\
			  <div class="ispinner__blade"></div>\
			  <div class="ispinner__blade"></div>\
			  <div class="ispinner__blade"></div>\
			  <div class="ispinner__blade"></div>\
			  <div class="ispinner__blade"></div>\
			  <div class="ispinner__blade"></div>\
			  <div class="ispinner__blade"></div>\
			  <div class="ispinner__blade"></div>\
			  <div class="ispinner__blade"></div>\
			  <div class="ispinner__blade"></div>\
			  <div class="ispinner__blade"></div>\
			</div>\
		</div>');

		$wrapper.css({
			width: $wrap.outerWidth(),
			height: $wrap.outerHeight()
		}).insertAfter($wrap);
	},

	hideProgress: function (event, id) {
		jQuery(event.target).attr('disabled', false);
		jQuery('#'+ id).find('div.Pixabay__wrapper').remove();
	}
};