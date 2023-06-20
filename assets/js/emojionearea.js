window.addEventListener( 'elementor/init', () => {
	var emojioneareaItemView = elementor.modules.controls.BaseData.extend({
		onReady() {
			var self = this,
				options = _.extend({
					events: {
						change: () => self.saveValue(),
						emojibtn_click: () => self.saveValue(),
						keyup: () => self.saveValue()
					},
					pickerPosition: 'bottom',
					filtersPosition: 'top',
					searchPosition: 'bottom',
					saveEmojisAs: 'unicode',
					inline: false,
				}, this.model.get( 'emojionearea_options' ) );

			this.ui.textarea.emojioneArea( options );
		},
		saveValue() {
			this.setValue(this.ui.textarea[0].emojioneArea.getText());
		},
		onBeforeDestroy() {
			this.saveValue();
			this.ui.textarea[0].emojioneArea.off();
		}
	});

	elementor.addControlView( 'emojionearea', emojioneareaItemView );

} );