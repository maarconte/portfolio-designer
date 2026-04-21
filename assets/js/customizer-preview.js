/**
 * Jeanne Theme — Customizer live-preview bindings.
 */
(function ($) {
	'use strict';

	wp.customize('blogname', function (value) {
		value.bind(function (newVal) {
			$('.site-title').text(newVal);
		});
	});

	wp.customize('blogdescription', function (value) {
		value.bind(function (newVal) {
			$('.site-description').text(newVal);
		});
	});

	wp.customize('jeanne_footer_text', function (value) {
		value.bind(function (newVal) {
			$('.site-footer__left').html(newVal || '');
		});
	});

}(jQuery));
