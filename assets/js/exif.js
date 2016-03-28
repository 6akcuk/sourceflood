var ImageEXIF = {
	selected: [],
	formLoaded: false,
	map: null,
	geocoder: null,
	
	init: function() {
		Dialog.init('ImageEXIF');
	},

	initMap: function() {
		ImageEXIF.geocoder = new google.maps.Geocoder();

		ImageEXIF.map = new google.maps.Map(document.getElementById('google-map'), {
			center: {lat: 34.0522342, lng: -118.2436849},
			zoom: 8
        });

		ImageEXIF.map.addListener('click', function(event) {
			ImageEXIF.geocoder.geocode({'location': event.latLng}, function(results, status) {
				if (status === google.maps.GeocoderStatus.OK) {
					if (results[1]) {
						ImageEXIF.select(results[1].formatted_address, event.latLng);
					} else {
						window.alert('No results found');
					}
				} else {
					window.alert('Geocoder failed due to: ' + status);
				}
			});
		});
	},

	start: function(el) {
		Dialog.title('ImageEXIF', 'Image EXIF');

		if (!ImageEXIF.formLoaded) {
			Dialog.body('ImageEXIF').load(jQuery(el).attr('href'), function() {
				ImageEXIF.formLoaded = true;
			});
		}

		Dialog.open('ImageEXIF');

		return false;
	},

	find: function() {
		var query = jQuery('#exif-query').val();
		if (!jQuery.trim(query)) {
			jQuery('#exif-query').focus();
			return;
		}

		jQuery('#exif-search-btn').attr('disabled', true);

		ImageEXIF.geocoder.geocode({'address': query}, function(results, status) {
			jQuery('#exif-search-btn').attr('disabled', false);
			
			if (status == google.maps.GeocoderStatus.OK) {
				var location = {
					lat: results[0].geometry.location.lat(),
					lng: results[0].geometry.location.lng()
				};

				ImageEXIF.map.setCenter(location);
				ImageEXIF.select(query, location);
			} else {
				alert("Geocode was not successful for the following reason: " + status);
			}
		});
	},

	select: function(address, location) {
		var marker = new google.maps.Marker({
			map: ImageEXIF.map,
			position: location
		});

		ImageEXIF.add(address, location, marker);
	},

	add: function(address, location, marker) {
		if (typeof _.findWhere(ImageEXIF.selected, {address: address}) == 'undefined') {
			ImageEXIF.selected.push({
				location: location,
				address: address,
				marker: marker
			});

			jQuery('<div class="ExifAddress" data-address="'+ address + '">\
					<span class="ExifAddress__name">'+ address +'</span>\
					<span class="ExifAddress__delete tb-close-icon" onclick="return ImageEXIF.delete(this)"></span>\
				</div>').appendTo('#exif-addresses');

			ImageEXIF.status();
		}
	},

	delete: function(el) {
		var $el = jQuery(el).parent();
		var founded = _.findWhere(ImageEXIF.selected, {address: $el.data('address')});
		founded.marker.setMap(null);

		ImageEXIF.selected = _.without(ImageEXIF.selected, founded);
		$el.remove();

		ImageEXIF.status();
	},

	deselect: function() {
		jQuery('#exif-addresses').html('');
		ImageEXIF.selected = [];

		ImageEXIF.status();
	},

	status: function() {
		Dialog.body('ImageEXIF').find('div.Pixabay__status').html('<strong>' + ImageEXIF.selected.length + '</strong> selected.' + (ImageEXIF.selected.length > 0 ? ' <a href="#" onclick="ImageEXIF.deselect()">Deselect all</a>' : ''));
	}

};

jQuery(document.body).ready(function() {

	ImageEXIF.init();	

});