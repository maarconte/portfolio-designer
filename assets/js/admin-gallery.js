/**
 * Jeanne Theme — Admin Gallery Meta Box
 *
 * Each click on "Add images" opens a fresh wp.media frame with no pre-selection,
 * which is the only reliable way to get wp.media multi-select to work.
 * New picks are appended to the existing gallery; duplicates are skipped.
 */
(function ($) {
	'use strict';

	var $preview  = $('#jeanne-gallery-preview');
	var $idsField = $('#jeanne-gallery-ids');

	/** Return the current ordered list of attachment IDs from the DOM. */
	function getCurrentIds() {
		var ids = [];
		$preview.find('.jeanne-gallery-item').each(function () {
			ids.push(parseInt($(this).data('id'), 10));
		});
		return ids;
	}

	/** Sync the hidden input to the current DOM order. */
	function syncIds() {
		$idsField.val(getCurrentIds().join(','));
	}

	/** Build a thumbnail tile. */
	function renderItem(id, thumbUrl) {
		return $(
			'<div class="jeanne-gallery-item" data-id="' + id + '">' +
				'<img src="' + thumbUrl + '" alt="">' +
				'<button type="button" class="jeanne-gallery-remove" title="Retirer">&#x2715;</button>' +
			'</div>'
		);
	}

	/** Open a fresh media frame. Pre-selection is intentionally omitted —
	 *  it is what prevents wp.media from allowing multi-select. */
	$('#jeanne-gallery-button').on('click', function () {
		var frame = wp.media({
			title:   jeanneAdmin.selectImages,
			button:  { text: jeanneAdmin.useImages },
			multiple: true,
			library: { type: 'image' },
		});

		frame.on('select', function () {
			var attachments = frame.state().get('selection').toJSON();
			var existingIds = getCurrentIds();

			attachments.forEach(function (att) {
				// Skip images already present in the gallery.
				if (existingIds.indexOf(att.id) !== -1) return;

				var thumbUrl = att.sizes && att.sizes.thumbnail
					? att.sizes.thumbnail.url
					: att.url;

				$preview.append(renderItem(att.id, thumbUrl));
				existingIds.push(att.id);
			});

			syncIds();
			initSortable();
		});

		frame.open();
	});

	// ── Remove a single image ──────────────────────────────────────────────────
	$preview.on('click', '.jeanne-gallery-remove', function () {
		$(this).closest('.jeanne-gallery-item').remove();
		syncIds();
	});

	// ── Clear the entire gallery ───────────────────────────────────────────────
	$('#jeanne-gallery-clear').on('click', function () {
		$preview.empty();
		$idsField.val('');
	});

	// ── Drag-to-reorder ────────────────────────────────────────────────────────
	function initSortable() {
		if (!$.fn.sortable) return;

		if ($preview.hasClass('ui-sortable')) {
			$preview.sortable('refresh');
		} else {
			$preview.sortable({
				items:  '.jeanne-gallery-item',
				cursor: 'grab',
				update: syncIds,
			});
		}
	}

	initSortable();

}(jQuery));
