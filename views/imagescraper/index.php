<div class="PixabayWrap">
	<!--<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active">Search Images</a>
	</h2>-->

	<div class="PixabaySearch">
		<input type="text" id="pixabay-query" placeholder="Search images" onkeypress="if (event.keyCode == 13 || event.which == 13) ImageScraper.find()">
		<button id="pixabay-search-btn" class="button-primary" onclick="ImageScraper.find()">Search</button>
	</div>

	<div id="pixabay-search-images">
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

		<div id="pixabay-images" class="clearfix">
			<div class="Pixabay--empty">Type something into search field</div>
		</div>
	</div>

	<div class="Pixabay__footer">
		<div class="fl_l Pixabay__status"><strong>0</strong> selected</div>
		<div class="fl_r Pixabay__buttons">
			<div class="Pixabay__shortcode-wrap" onclick="event.stopPropagation()">
				<input type="text" id="shortcode-name" placeholder="Shortcode">
				<button id="shortcode-btn" class="button-primary" onclick="ImageScraper.shortcode()">Create</button>
			</div>

			<button id="create-shortcode-btn" class="button-primary" onclick="ImageScraper.showShortcode(event)">Create shortcode</button>
			<!--<button class="button-primary" onclick="ImageScraper.post()">Add to post/page</button>-->
		</div>
	</div>
</div>