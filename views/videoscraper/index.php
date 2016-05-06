<div class="PixabayWrap">
	<!--<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active">Search Images</a>
	</h2>-->

	<div class="PixabaySearch">
		<input type="text" id="youtube-query" placeholder="Search videos" onkeypress="if (event.keyCode == 13 || event.which == 13) VideoScraper.find()">
		<button id="youtube-search-btn" class="button-primary" onclick="VideoScraper.find()">Search</button>
	</div>

	<div id="youtube-search-videos">
		<div class="ispinner ispinner--gray ispinner--animating">
		  <div class="ispinner__blade"></div>
		  <div class="ispinner__blade"></div>
		  <div class="ispinner__blade"></div>
		  <div class="ispinner__blade"></div>
		  <div class="ispinner__blade"></div>
		  <div class="ispinner__blade"></div>
		  <div class="ispinner__blade"></div>
		  <div class="ispinner__blade"></div>
		  <div class="ispinner__blade"></div>
		  <div class="ispinner__blade"></div>
		  <div class="ispinner__blade"></div>
		  <div class="ispinner__blade"></div>
		</div>

		<div id="youtube-videos" class="clearfix">
			<div class="Pixabay--empty">Type something into search field</div>
		</div>
	</div>

	<div class="Pixabay__footer">
		<div class="fl_l Pixabay__status"><strong>0</strong> selected</div>
		<div class="fl_r Pixabay__buttons">
			<div class="Pixabay__shortcode-wrap" onclick="event.stopPropagation()">
				<input type="text" id="video-shortcode-name" placeholder="Shortcode">
				<button id="video-shortcode-btn" class="button-primary" onclick="VideoScraper.shortcode()">Create</button>
			</div>

			<button id="create-video-shortcode-btn" class="button-primary" onclick="VideoScraper.showShortcode(event)">Create shortcode</button>
			<!--<button class="button-primary" onclick="ImageScraper.post()">Add to post/page</button>-->
		</div>
	</div>
</div>