<div class="PixabayWrap">
	<div class="Pixabay__body">
		<div class="form-group">
			<label for="spun-text" class="control-label">Text to Spun</label>
			<textarea id="spun-text" class="form-control" style="height: 150px;"></textarea>
		</div>

		<div class="form-group">
			<label for="word-ai-quality" class="control-label">Quality</label>
			<input type="number" min="1" max="100" id="word-ai-quality" value="50" class="form-control">
			<span class="help-block">
				Higher quality = less uniqueness.
			</span>
		</div>

		<div class="form-group">
			<div class="form-column-4 text-center">
				<label class="control-label">Nesting</label>
				<label>
					<input type="radio" name="nesting" value="yes">
					- Yes
				</label>
				<label>
					<input type="radio" name="nesting" value="no" checked>
					- No
				</label>
				<span class="help-block">
					Turning it off [setting it to Yes] will help readability but hurt uniqueness.
				</span>
			</div>

			<div class="form-column-4 text-center">
				<label class="control-label">Paragraph spinning</label>
				<label>
					<input type="radio" name="paragraph_spinning" value="yes" checked>
					- Yes
				</label>
				<label>
					<input type="radio" name="paragraph_spinning" value="no">
					- No
				</label>
				<span class="help-block">
					Keeps articles unique and great for large builds.
				</span>
			</div>

			<div class="form-column-4 text-center">
				<label class="control-label">No original words</label>
				<label>
					<input type="radio" name="no_original_words" value="yes">
					- Yes
				</label>
				<label>
					<input type="radio" name="no_original_words" value="no" checked>
					- No
				</label>
				<span class="help-block">
					Keeps no unique words from the original spin which results in a super unique spin. Only recommend for very large builds.
				</span>
			</div>
		</div>

		<div class="form-group">
			<label for="word-ai-protected-words" class="control-label">Protected words</label>
			<input type="text" id="word-ai-protected-words" class="form-control">
			<span class="help-block">
				Comma separated words to not spin; no spaces between words.
			</span>
		</div>

		<!-- Spinned text -->
		<div id="word-ai-result-wrap" class="form-group" style="display: none;">
			<label for="word-ai-protected-words" class="control-label">Spintax</label>
			<textarea id="word-ai-result" class="form-control" style="height: 150px"></textarea>
		</div>
	</div>

	<div class="Pixabay__footer">
		<div class="fl_r Pixabay__buttons">
			<button id="word-ai-spin-btn" class="button-primary" onclick="WordAI.spin(event)">Spin</button>
			<button id="word-ai-post-btn" class="button" onclick="WordAI.post(event)" disabled>Insert to post/page</button>
		</div>
	</div>
</div>