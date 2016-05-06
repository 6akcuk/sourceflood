var WordAI = {
	loaded: false,

	init: function () {
		Dialog.init('WordAI');
	},

	start: function (el) {
		Dialog.title('WordAI', 'Word AI Console');

		if (!WordAI.loaded) {
			Dialog.body('WordAI').load(jQuery(el).attr('href'), function () {

			});
		}

		Dialog.open('WordAI');

		return false;
	},

	spin: function (event) {
		var $spun = jQuery('#spun-text');
		var text = jQuery.trim($spun.val());
		if (!text) {
			$spun.focus();
			return false;
		}

		var $quality = jQuery('#word-ai-quality');
		var quality = parseInt($quality.val());
		if (quality < 0 || quality > 100) {
			$quality.focus();
			return false;
		}

		var nesting = jQuery('#WordAI').find('input[name="nesting"]:checked').val();
		var paragraph = jQuery('#WordAI').find('input[name="paragraph_spinning"]:checked').val();
		var no_original = jQuery('#WordAI').find('input[name="no_original_words"]:checked').val();
		var protected_words = jQuery.trim(jQuery('#word-ai-protected-words').val());
		
		if (protected_words.split(' ;').length > 1 || protected_words.split('; ').length > 1) {
			jQuery('#word-ai-protected-words').focus();
			return false;
		}

		Dialog.showProgress(event, 'WordAI');
		jQuery('#word-ai-result-wrap').hide();
		jQuery('#word-ai-post-btn').attr('disabled', true);

		jQuery.post('/index.php?api=workhorse&action=word-ai', {
			text: text,
			quality: quality,
			nonested: nesting == 'no' ? 'on' : null,
			paragraph: paragraph == 'yes' ? 'on' : null,
			nooriginal: no_original == 'yes' ? 'on' : null,
			protected: protected_words ? protected_words : null,
			output: 'json'
		}, 'json')
		.done(function(response) {
			if (response.status == 'Failure') {
				alert(response.error);
			} else {
				jQuery('#word-ai-result-wrap').show();
				jQuery('#word-ai-result').val(response.text);
				jQuery('#word-ai-post-btn').attr('disabled', false);

				//tinymce.get('content').setContent(response.text);
			}
		})
		.always(function() {
			Dialog.hideProgress(event, 'WordAI');
		}.bind(this));
	},
	
	post: function (event) {
		tinymce.get('content').setContent(jQuery('#word-ai-result').val());
	}
};

jQuery(document.body).ready(function () {
	WordAI.init();
})