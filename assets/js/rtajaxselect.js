(function ($) {
	$(document).on('rt_select2_event', function (event, obj) {
		var rtSelect = '#elementor-control-default-' + obj.data._cid;

		setTimeout(function () {
			var IDSelect2 = $(rtSelect).select2({
				minimumInputLength: obj.data.minimum_input_length,
				ajax: {
					url: rtSelect2Obj.ajaxurl,
					dataType: 'json',
					delay: 250,
					method: "POST",
					data: function (params) {
						return {
							action:'rt_select2_object_search',
							post_type: obj.data.source_type,
							source_name: obj.data.source_name,
							search: params.term,
							page: params.page || 1,
						};
					},
				},
				initSelection: function (element, callback) {
					// console.log(element)
					if (!obj.multiple) {
						callback({id: '', text: rtSelect2Obj.search_text});
					} else {
						callback({id: '', text: ''});
					}
					var ids = [];
					if (!Array.isArray(obj.currentID) && obj.currentID != '') {
						ids = [obj.currentID];
					} else if (Array.isArray(obj.currentID)) {
						ids = obj.currentID.filter(function (el) {
							return el != null;
						})
					}

					if (ids.length > 0) {
						var label = $("label[for='elementor-control-default-" + obj.data._cid + "']");
						label.after('<span class="elementor-control-spinner">&nbsp;<i class="eicon-spinner eicon-animation-spin"></i>&nbsp;</span>');
						$.ajax({
							method: "POST",
							url: rtSelect2Obj.ajaxurl + "?action=rt_select2_get_title",
							data: {post_type: obj.data.source_type, source_name: obj.data.source_name, id: ids}
						}).done(function (response) {
							if (response.success && typeof response.data.results != 'undefined') {
								let rtSelect2Options = '';
								ids.forEach(function (item, index) {
									if (typeof response.data.results[item] != 'undefined') {
										const key = item;
										const value = response.data.results[item];
										rtSelect2Options += `<option selected="selected" value="${key}">${value}</option>`;
									}
								})

								element.append(rtSelect2Options);
							}
							label.siblings('.elementor-control-spinner').remove();
						});
					}
				}
			});

			//Select2 drag and drop : starts
			setTimeout(function () {
				IDSelect2.next().children().children().children().sortable({
					containment: 'parent',
					stop: function (event, ui) {
						ui.item.parent().children('[title]').each(function () {
							var title = $(this).attr('title');
							var original = $('option:contains(' + title + ')', IDSelect2).first();
							original.detach();
							IDSelect2.append(original)
						});
						IDSelect2.change();
					}
				});

				$(rtSelect).on("select2:select", function (evt) {
					var element = evt.params.data.element;
					var $element = $(element);

					$element.detach();
					$(this).append($element);
					$(this).trigger("change");
				});
			}, 200);


			//Manual Sorting : Select2 drag and drop : ends

		}, 100);

	});
}(jQuery));