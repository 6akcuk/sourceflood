var VideoScraper = {
	selected: [],
	currentPage: 1,
	formLoaded: false,
	moreBusy: false,

	init: function() {
		Dialog.init('VideoScraper', ['div.Pixabay__shortcode-wrap']);
	},

	start: function(el) {
		Dialog.title('VideoScraper', 'Video Scraper');

		if (!VideoScraper.formLoaded) {
			Dialog.body('VideoScraper').load(jQuery(el).attr('href'), function() {
				VideoScraper.formLoaded = true;
			});
		}

		Dialog.open('VideoScraper');

		return false;
	},

	find: function() {
		var query = jQuery('#youtube-query').val();
		if (!jQuery.trim(query)) {
			jQuery('#youtube-query').focus();
			return;
		}

		VideoScraper.deselect();

		jQuery('#youtube-videos').html('');

		VideoScraper.getVideos();
	},

	getVideos: function() {
		var query = jQuery('#youtube-query').val();
		var key = jQuery('#youtube-api-key').val();

		VideoScraper.toggleProgress(true);
		jQuery.getJSON('https://www.googleapis.com/youtube/v3/search?key=' + key + '&q=' + query + '&part=snippet&maxResults=40&page='+ VideoScraper.currentPage, function(response) {
			_.each(response.items, function(video, i) {
				var $elle = jQuery('<div class="fl_l Youtube__video">\
						<img src="'+ video.snippet.thumbnails.medium.url + '">\
						<i class="fa fa-check"></i>\
						<div class="Youtube__title">'+ video.snippet.title +'</div>\
					</div>');
				
				$elle.click(function() {
					VideoScraper.handleSelect(this, video);
				});

				$elle.appendTo('#youtube-videos');
			});
		}).always(function(error) {
			VideoScraper.toggleProgress(false);
		});
	},

	status: function() {
		Dialog.body('VideoScraper').find('div.Pixabay__status').html('<strong>' + VideoScraper.selected.length + '</strong> selected.' + (VideoScraper.selected.length > 0 ? ' <a href="#" onclick="VideoScraper.deselect()">Deselect all</a>' : ''));
	},

	handleSelect: function(el, video) {
		var jQueryel = jQuery(el);

		// Video selected
		if (jQueryel.hasClass('Youtube__video--selected')) {
			jQueryel.removeClass('Youtube__video--selected');

			VideoScraper.selected = _.without(VideoScraper.selected, _.findWhere(VideoScraper.selected, {etag: video.etag}));
		} else {
			jQueryel.addClass('Youtube__video--selected');

			VideoScraper.selected.push(video);
		}

		VideoScraper.status();
	},

	deselect: function() {
		jQuery('#youtube-videos').find('div.Youtube__video--selected').click();
	},

	toggleProgress: function(busy) {
		var jQuerybtn = jQuery('#youtube-search-btn');
		var jQueryvideos = jQuery('#youtube-search-videos');
		var jQueryspin = jQueryvideos.find('div.ispinner');

		if (busy) {
			jQuerybtn.attr('disabled', true);
			jQueryspin.show();
		} else {
			jQuerybtn.attr('disabled', false);
			jQueryspin.hide();

			VideoScraper.moreBusy = false;
		}
	},

	post: function() {
		_.each(VideoScraper.selected, function(image, i) {
			tinyMCE.execCommand('mceInsertContent', false, '<img alt="'+ image.tags + '" height="'+ image.webformatHeight + '" width="'+ image.webformatWidth + '" src="' + image.webformatURL + '"/>');
		});
	},

	showShortcode: function(e) {
		e.stopPropagation();

		if (VideoScraper.selected.length > 0) {
			jQuery('div.Pixabay__shortcode-wrap').show();
			jQuery('#video-shortcode-name').focus();
		} else {
			jQuery(e.target).notify('Select at least one video', 'error');
		}
	},

	shortcode: function() {
		var name = jQuery('#video-shortcode-name').val();
		if (!jQuery.trim(name)) {
			jQuery('#video-shortcode-name').focus();
			return;
		}

		var videos = [];
		_.each(VideoScraper.selected, function(video, i) {
			videos.push({
				videoId: video.id.videoId,
				thumbnails: video.snippet.thumbnails,
				title: video.snippet.title
			});
		});

		jQuery('#video-shortcode-btn').attr('disabled', true);

		jQuery.post('/index.php?api=workhorse&action=shortcode', {
			shortcode: name,
			media: videos
		}).done(function(response) {
			if (response.success) {
				Dialog.hideOverlays('VideoScraper');
				VideoScraper.deselect();

				jQuery('#create-video-shortcode-btn').notify('Successfully created', 'success');
			} else {
				jQuery('#video-shortcode-btn').notify('Error', 'error');
			}
		}).fail(function() {
			jQuery('#video-shortcode-btn').notify('Error', 'error');
		}).always(function() {
			jQuery('#video-shortcode-btn').attr('disabled', false);
		});
	}
};

jQuery(document.body).ready(function() {

	VideoScraper.init();	

});