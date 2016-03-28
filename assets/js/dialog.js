var Dialog = {
	overlays: {},

	init: function(id, overlays) {
		jQuery('<div id="'+ id + '-overlay" class="Dialog__overlay" onclick="Dialog.close(\''+ id +'\')"></div>\
			<div id="'+ id + '" class="Dialog" onclick="Dialog.hideOverlays(\''+ id +'\')">\
				<div class="Dialog__title-wrap">\
					<div class="Dialog__title"></div>\
					<a class="Dialog__close" onclick="Dialog.close(\''+ id +'\')"><div class="tb-close-icon"></div></a>\
				</div>\
				<div class="Dialog__body"></div>\
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
	}
};