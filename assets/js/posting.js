/**	
 * For posting module
 */
(function($) {

	// On Ready
	$(document).ready(function() {

		var $form = $('#poststuff').parent('form');

		// Permalink
		var is_permalink_saved = false;

		if ($('#permalink').text() != '@title') {
			is_permalink_saved = true;
		}

		$('#title').keyup(function() {
			if (is_permalink_saved) return;

			var title = $(this).val();
			if (!title) title = '@title';

			title = title.toLowerCase().replace(/\s/g, '-').replace(/[,:]*/g, '');

			$('#permalink').text(title);
		});
		$('#edit-permalink').click(function () {
			$('#permalink, #edit-permalink').hide();
			$('#save-permalink, #cancel-permalink').show();

			$('<input/>').attr({
				type: 'text',
				'id': 'new-permalink'
			}).val($('#permalink').text()).insertAfter('#permalink');
		});
		$('#save-permalink').click(function () {
			is_permalink_saved = true;
			var permalink = $('#new-permalink').val();

			$('#permalink').text(permalink);
			$('input[name="permalink"]').val(permalink);

			$('#cancel-permalink').click();
		});
		$('#cancel-permalink').click(function () {
			$('#permalink, #edit-permalink').show();
			$('#new-permalink').remove();
			$('#save-permalink, #cancel-permalink').hide();
		});

		// Tags
		$('#add-tags').click(function () {
			var value = $('input[name="tags"]').val();
			var tags = value ? value.split(',') : [];
			var newTags = $('#tagsinput').val().split(',');

			if (!$('#tagsinput').val()) return;

			_.each(newTags, function (tag) {
				tag = $.trim(tag);

				if (_.indexOf(tags, tag) == -1) {
					tags.push(tag);

					var $tag = $('<span>\
						<a class="ntdelbutton" data-tag="'+ tag + '">X</a>\
						' + tag + '\
					</span>');

					$tag.find('a').click(function () {
						$tag.remove();

						tags = _.without(tags, $(this).data('tag'));
						$('input[name="tags"]').val(tags.join(','));
					});

					$tag.appendTo('#tags');
				}
			});

			$('input[name="tags"]').val(tags.join(','));
			$('#tagsinput').val('');
		});

		if ($('#tagsinput').val() != '') {
			$('#add-tags').click();
		}
		
		// On-Page SEO
		$('#on-page-seo').change(function() {
			if ($(this).is(':checked')) {
				$('#on-page-seo-wrap').show();
			} else {
				$('#on-page-seo-wrap').hide();
			}
		});

		// Schema
		$('#schema').change(function() {
			if ($(this).is(':checked')) {
				$('#schema-wrap').show();
			} else {
				$('#schema-wrap').hide();
			}
		});

		// Dripfeed
		$('#dripfeed-enabler').change(function() {
			if ($(this).is(':checked')) {
				$('#dripfeed-wrap').show();
			} else {
				$('#dripfeed-wrap').hide();
			}
		});

		// Image EXIF
		$('#exif-enabler').change(function() {
			if ($(this).is(':checked')) {
				$('#exif-wrap').show();
			} else {
				$('#exif-wrap').hide();
			}
		});

		// Local SEO
		$('#local-seo-enabler').change(function() {
			if ($(this).is(':checked')) {
				$('#local-seo-wrap').show();
			} else {
				$('#local-seo-wrap').hide();
			}
		});

		$('#local-country').change(function() {
			$('#jstree').jstree(true).refresh();
		});

		var geoRepeated = {};
		var allowedLocalSEO = {country: 1, state: 0, stateshort: 0, city: 0, zip: 0};

		$('#jstree')
			.on('select_node.jstree', function (e, data) {
				var path = data.node.id.split('/');

				if (path.length == 1) {
					allowedLocalSEO['state']++;
					allowedLocalSEO['stateshort']++;
				}
				else if (path.length == 2) {
					allowedLocalSEO['state']++;
					allowedLocalSEO['stateshort']++;
					allowedLocalSEO['city']++;

					if (!geoRepeated[path[0]]) geoRepeated[path[0]] = 0;

					geoRepeated[ path[0] ]++;
				}
				else if (path.length == 3) {
					allowedLocalSEO['state']++;
					allowedLocalSEO['stateshort']++;
					allowedLocalSEO['city']++;
					allowedLocalSEO['zip']++;

					if (!geoRepeated[path[0]]) geoRepeated[path[0]] = 0;
					if (!geoRepeated[path[0] + '/' + path[1]]) geoRepeated[path[0] + '/' + path[1]] = 0;

					geoRepeated[ path[0] ]++;
					geoRepeated[ path[0] + '/' + path[1] ]++;
				}
			})
			.on('deselect_node.jstree', function (e, data) {
				var path = data.node.id.split('/');

				if (path.length == 1) {
					allowedLocalSEO['state']--;
					allowedLocalSEO['stateshort']--;
				}
				else if (path.length == 2) {
					allowedLocalSEO['state']--;
					allowedLocalSEO['stateshort']--;
					allowedLocalSEO['city']--;

					geoRepeated[ path[0] ]--;
				}
				else if (path.length == 3) {
					allowedLocalSEO['state']--;
					allowedLocalSEO['stateshort']--;
					allowedLocalSEO['city']--;
					allowedLocalSEO['zip']--;

					geoRepeated[ path[0] ]--;
					geoRepeated[ path[0] + '/' + path[1] ]--;
				}
			})
			.jstree({
				core: {
					'data': {
						url: function(node) {
							return '/index.php?api=workhorse&action=geo-tree&country='+ $('#local-country').val();
						},
						data: function(node) {
							return {id: node.id};
						}
					}
				},
				checkbox: {
					//three_state: false
				},
				plugins: ['checkbox', 'changed']
			});

		// Word AI
		if ($('#word-ai-key').length && $('#word-ai-email').length) {
			$('#wp-content-media-buttons').append('\
				<button type="button" id="word-ai" class="button" data-editor="content">\
					Word AI\
				</button>\
			');
			$('#word-ai').click(function(e) {
				var text = $.trim(tinymce.get('content').getContent());
				if (!text) return false;

				var email = $('#word-ai-email').val();
				var hash = $('#word-ai-key').val();

				$(this).text('Processing..').attr('disabled', true);
				$.post('/index.php?api=workhorse&action=word-ai', {
					text: text,
					quality: 'Readable',
					email: email,
					hash: hash,
					output: 'json'
				}, 'json')
				.done(function(response) {
					if (response.status == 'Failure') {
						alert(response.error);
					} else {
						tinymce.get('content').setContent(response.text);
					}
				})
				.always(function() {
					$(this).text('Word AI').attr('disabled', false);
				}.bind(this));
			});
		}

		// Form submission
		$form.submit(function(e) {
			// Check local seo tags
			var localSEOFields = ['title', 'content'];
			var localSEOHasError = false;
			var localSEOErrors = {};

			if ($form.find('#on-page-seo').is(':checked')) {
				localSEOFields = localSEOFields.concat(['custom_title', 'custom_description', 'custom_keywords']);
			}

			if ($form.find('#local-seo-enabler').is(':checked')) {
				var locations = $form.find('#jstree').jstree(true).get_checked();
				var local_country = $form.find('#local-country').val();

				if (parseInt($form.find('#max-posts').val()) > 0) {
					if (!confirm('There maybe more combinations in the local SEO area than you typed in max posts. Continue?')) {
						e.preventDefault();
					}
				}
			}

			$form.find('[local-seo-error]').remove();
			
			if (localSEOHasError) {
				$.each(localSEOErrors, function(field, errors) {
					var html = [];
					$.each(errors, function(i, error) {
						html.push('<span class="PostForm__error" local-seo-error>'+ error + '</span>');
					});

					$el = $('[name="'+ field + '"]');
					if (field == 'content') $el = $el.parent().parent();
					
					$el.after(html.join(''));
				});

				e.preventDefault();
				return;
			}

			$form.find('#local-geo-locations').remove();
			$form.find('#local-geo-data').remove();

			if ($form.find('#local-seo-enabler').is(':checked')) {
				var locations = $form.find('#jstree').jstree(true).get_checked();
				var uniqueLocations = [];

				_.each(locations, function(location, i) {
					if (!geoRepeated[location]) uniqueLocations.push(location);
				});

				$form.prepend('<input type="hidden" id="local-geo-locations" name="local_geo_locations" value=\''+ JSON.stringify(uniqueLocations) +'\'>');
			}

			$form.find('#exif-locations').remove();

			if ($form.find('#exif-enabler').is(':checked')) {
				var exifLocations = [];

				_.each(ImageEXIF.selected, function(location) {
					exifLocations.push({
						address: location.address,
						location: location.location
					});
				});

				$form.prepend('<input type="hidden" id="exif-locations" name="exif_locations" value=\''+ JSON.stringify(exifLocations) + '\'>');
			}
		});

	});
})(jQuery);