/**	
 * For posting module
 */

(function($) {

	// On Ready
	$(document).ready(function() {

		var $form = $('#poststuff').parent('form');
		
		// On-Page SEO
		$('#on-page-seo').change(function() {
			if ($(this).is(':checked')) {
				$('#on-page-seo-wrap').show();
			} else {
				$('#on-page-seo-wrap').hide();
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
							return '/index.php?api=sourceflood&action=geo-tree&country='+ $('#local-country').val();
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
			}

			$.each(localSEOFields, function(i, field) {
				localSEOErrors[field] = [];
				var value = $('[name="'+ field + '"]').val();

				if (field == 'content') {
					value = tinymce.get('content').getContent();
				}

				var founded = value.match(/(@zip(?![a-z\-])|@city(?![a-z\-])|@stateshort(?![a-z\-])|@state(?![a-z\-])|@country(?![a-z\-]))/gi);
				if (founded) {
					$.each(founded, function(i, tag) {
						tag = tag.replace(/@/, '');

						if (allowedLocalSEO[tag] == 0) {
							localSEOHasError = true;
							localSEOErrors[field].push('You cannot use <strong>@' + tag + '</strong>. Please remove tag or select in locations.');
						}
					});
				}
			});

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
		});

	});
})(jQuery);