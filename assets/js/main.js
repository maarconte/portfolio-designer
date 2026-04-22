/**
 * Jeanne Theme — Main JS
 * Handles: custom cursor, horizontal slider, project modal
 */
(function () {
	'use strict';

	/* (hover: hover) and (pointer: fine) = vrai souris/trackpad précis ; faux sur écran tactile */
	var isTouch = !window.matchMedia('(hover: hover) and (pointer: fine)').matches;

	/* ==========================================================================
	   CUSTOM CURSOR
	   ========================================================================== */
	var Cursor = {
		el: null,
		arrowEl: null,
		state: null,

		init: function () {
			if (isTouch) return;

			this.el = document.getElementById('custom-cursor');
			if (!this.el) return;

			this.arrowEl = this.el.querySelector('.custom-cursor__arrow');

			document.addEventListener('mousemove', function (e) {
				if (!Cursor.el) return;
				Cursor.el.style.left = e.clientX + 'px';
				Cursor.el.style.top  = e.clientY + 'px';
			});
		},

		setState: function (state) {
			if (!this.el) return;
			if (this.state === state) return;
			this.state = state;

			this.el.className = 'custom-cursor';

			if (!state) return;

			this.el.classList.add('is-visible', 'state-' + state);

			if (state === 'arrow-left' && this.arrowEl) {
				this.arrowEl.textContent = '\u2190'; // ←
			} else if (state === 'arrow-right' && this.arrowEl) {
				this.arrowEl.textContent = '\u2192'; // →
			}
		}
	};

	/* ==========================================================================
	   SLIDER
	   ========================================================================== */
	var Slider = {
		el: null,
		track: null,
		cards: [],
		current: 0,
		autoPlayTimer: null,
		isWrapping: false,

		init: function () {
			this.el    = document.getElementById('project-slider');
			this.track = document.getElementById('slider-track');
			if (!this.el || !this.track) return;

			this.cards = Array.from(this.track.querySelectorAll('.project-card'));
			if (this.cards.length === 0) return;

			this._setupNavZones();
			this._setupMobileNav();
			this._setupTouch();
			this._setupKeyboard();
			this._setupAutoPlay();

			// Pause autoplay while hovering the slider
			this.el.addEventListener('mouseenter', this.pauseAutoPlay.bind(this));
			this.el.addEventListener('mouseleave', this.resumeAutoPlay.bind(this));

			// Window resize: recalculate offset
			var self = this;
			window.addEventListener('resize', function () {
				self.goTo(self.current, true);
			});
		},

		_setupNavZones: function () {
			var prevZone = this.el.querySelector('.nav-zone--prev');
			var nextZone = this.el.querySelector('.nav-zone--next');
			var self     = this;

			if (prevZone) {
				prevZone.addEventListener('click', function () { self.prev(); });
				prevZone.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') self.prev(); });
				prevZone.addEventListener('mouseenter', function () { Cursor.setState('arrow-left'); });
				prevZone.addEventListener('mouseleave', function () { Cursor.setState(null); });
			}

			if (nextZone) {
				nextZone.addEventListener('click', function () { self.next(); });
				nextZone.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') self.next(); });
				nextZone.addEventListener('mouseenter', function () { Cursor.setState('arrow-right'); });
				nextZone.addEventListener('mouseleave', function () { Cursor.setState(null); });
			}
		},

		_setupMobileNav: function () {
			if (!isTouch && window.innerWidth > 768) return;

			var mobileNav = document.createElement('div');
			mobileNav.className = 'slider-mobile-nav';
			mobileNav.innerHTML =
				'<button class="slider-mobile-nav__btn" aria-label="Previous">\u2190</button>' +
				'<button class="slider-mobile-nav__btn" aria-label="Next">\u2192</button>';

			var self = this;
			var btns = mobileNav.querySelectorAll('.slider-mobile-nav__btn');
			btns[0].addEventListener('click', function () { self.prev(); });
			btns[1].addEventListener('click', function () { self.next(); });

			this.el.appendChild(mobileNav);
		},

		_setupTouch: function () {
			var startX   = 0;
			var startY   = 0;
			var isDragging = false;
			var self     = this;

			this.el.addEventListener('touchstart', function (e) {
				startX     = e.touches[0].clientX;
				startY     = e.touches[0].clientY;
				isDragging = true;
			}, { passive: true });

			this.el.addEventListener('touchend', function (e) {
				if (!isDragging) return;
				isDragging     = false;
				var diffX      = startX - e.changedTouches[0].clientX;
				var diffY      = startY - e.changedTouches[0].clientY;

				// Only trigger if horizontal swipe is dominant
				if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 45) {
					if (diffX > 0) {
						self.next();
					} else {
						self.prev();
					}
				}
			}, { passive: true });
		},

		_setupKeyboard: function () {
			var self = this;
			document.addEventListener('keydown', function (e) {
				if (Modal.isOpen()) return;
				if (e.key === 'ArrowLeft')  self.prev();
				if (e.key === 'ArrowRight') self.next();
			});
		},

		_setupAutoPlay: function () {
			var delay = (typeof jeanneConfig !== 'undefined') ? jeanneConfig.autoplayDelay : 4000;
			if (!delay || this.cards.length <= 1) return;

			var self = this;
			this.autoPlayTimer = setInterval(function () {
				self.next();
			}, delay);
		},

		pauseAutoPlay: function () {
			clearInterval(this.autoPlayTimer);
		},

		resumeAutoPlay: function () {
			if (Modal.isOpen()) return;
			this.pauseAutoPlay();
			this._setupAutoPlay();
		},

		_getCardStep: function () {
			if (!this.cards.length) return 0;
			var card  = this.cards[0];
			var style = window.getComputedStyle(this.track);
			var gap   = parseFloat(style.gap) || parseFloat(style.columnGap) || 0;
			return card.offsetWidth + gap;
		},

		goTo: function (index, instant) {
			if (this.cards.length === 0) return;
			this.current = index;
			var offset   = this.current * this._getCardStep();

			if (instant) {
				var orig = this.track.style.transition;
				this.track.style.transition = 'none';
				this.track.style.transform  = 'translateX(-' + offset + 'px)';
				// Force layout then restore transition
				void this.track.offsetHeight;
				this.track.style.transition = orig;
			} else {
				this.track.style.transform = 'translateX(-' + offset + 'px)';
			}
		},

		_wrapTo: function (index) {
			if (this.isWrapping) return;
			this.isWrapping = true;
			var self        = this;

			this.track.style.opacity = '0';

			setTimeout(function () {
				self.goTo(index, true);

				requestAnimationFrame(function () {
					requestAnimationFrame(function () {
						self.track.style.opacity   = '1';
						self.isWrapping            = false;
					});
				});
			}, 280);
		},

		next: function () {
			if (this.isWrapping) return;
			if (this.current >= this.cards.length - 1) {
				this._wrapTo(0);
			} else {
				this.goTo(this.current + 1);
			}
		},

		prev: function () {
			if (this.isWrapping) return;
			if (this.current <= 0) {
				this._wrapTo(this.cards.length - 1);
			} else {
				this.goTo(this.current - 1);
			}
		}
	};

	/* ==========================================================================
	   MODAL
	   ========================================================================== */
	var Modal = {
		el: null,
		imageContainer: null,
		titleEl: null,
		metaEl: null,
		descEl: null,
		counterEl: null,
		images: [],
		currentImage: 0,
		_open: false,

		init: function () {
			this.el             = document.getElementById('project-modal');
			this.imageContainer = document.getElementById('modal-image-container');
			this.titleEl        = document.getElementById('modal-title');
			this.metaEl         = document.getElementById('modal-meta');
			this.descEl         = document.getElementById('modal-description');
			this.counterEl      = document.getElementById('modal-counter');

			if (!this.el) return;

			// Remove [hidden] so CSS controls visibility
			this.el.removeAttribute('hidden');

			// Close button
			var closeBtn = document.getElementById('modal-close');
			if (closeBtn) closeBtn.addEventListener('click', this.close.bind(this));

			// Overlay click
			var overlay = document.getElementById('modal-overlay');
			if (overlay) overlay.addEventListener('click', this.close.bind(this));

			// Navigation
			var prevBtn = document.getElementById('modal-prev');
			var nextBtn = document.getElementById('modal-next');
			if (prevBtn) prevBtn.addEventListener('click', this.prevImage.bind(this));
			if (nextBtn) nextBtn.addEventListener('click', this.nextImage.bind(this));

			// Keyboard
			document.addEventListener('keydown', this._onKeydown.bind(this));

			// Touch swipe inside modal
			this._setupModalTouch();

			// Project card click handlers
			this._bindCards();
		},

		_bindCards: function () {
			var cards = document.querySelectorAll('.project-card');
			var self  = this;

			cards.forEach(function (card) {
				// Click to open modal
				card.addEventListener('click', function () {
					self.open(card);
				});

				// Keyboard: Enter / Space opens modal
				card.addEventListener('keydown', function (e) {
					if (e.key === 'Enter' || e.key === ' ') {
						e.preventDefault();
						self.open(card);
					}
				});

				// Custom cursor: "More" on image hover
				var imgEl = card.querySelector('.project-card__image');
				if (imgEl && !isTouch) {
					imgEl.addEventListener('mouseenter', function () { Cursor.setState('more'); });
					imgEl.addEventListener('mouseleave', function () { Cursor.setState(null); });
				}
			});
		},

		open: function (card) {
			var galleryRaw   = card.dataset.gallery  || '[]';
			var title        = card.dataset.title    || '';
			var description  = card.dataset.description || '';
			var year         = card.dataset.year     || '';
			var client       = card.dataset.client   || '';
			var category     = card.dataset.category || '';

			try {
				this.images = JSON.parse(galleryRaw);
			} catch (e) {
				this.images = [];
			}

			// Fallback to the card thumbnail
			if (!this.images.length) {
				var img = card.querySelector('.project-card__image img');
				if (img) {
					this.images = [{ url: img.src, alt: img.alt, thumb: img.src }];
				}
			}

			// Populate info
			if (this.titleEl) this.titleEl.textContent = title;

			if (this.metaEl) {
				var metaParts = [category, client, year].filter(Boolean);
				this.metaEl.textContent = metaParts.join('\u2002\u2022\u2002');
				this.metaEl.style.display = metaParts.length ? '' : 'none';
			}

			if (this.descEl) {
				this.descEl.textContent    = description;
				this.descEl.style.display  = description ? '' : 'none';
			}

			this.currentImage = 0;
			this._renderImages();
			this._updateCounter();
			this._updateNavVisibility();

			this.el.classList.add('is-open');
			this._open = true;
			document.body.classList.add('modal-open');
			Slider.pauseAutoPlay();

			// Focus management
			var closeBtn = document.getElementById('modal-close');
			if (closeBtn) closeBtn.focus();
		},

		close: function () {
			this.el.classList.remove('is-open');
			this._open = false;
			document.body.classList.remove('modal-open');
			Slider.resumeAutoPlay();

			var self = this;
			setTimeout(function () {
				if (self.imageContainer) self.imageContainer.innerHTML = '';
			}, 360);
		},

		isOpen: function () {
			return this._open;
		},

		_renderImages: function () {
			if (!this.imageContainer) return;
			this.imageContainer.innerHTML = '';

			var self = this;
			this.images.forEach(function (imgData, i) {
				var el    = document.createElement('img');
				el.src    = imgData.url;
				el.alt    = imgData.alt || '';
				el.className = 'modal__image' + (i === 0 ? ' is-active' : '');
				self.imageContainer.appendChild(el);
			});
		},

		_showImage: function (index) {
			var imgs = this.imageContainer.querySelectorAll('.modal__image');
			imgs.forEach(function (img, i) {
				img.classList.toggle('is-active', i === index);
			});
			this.currentImage = index;
			this._updateCounter();
		},

		nextImage: function () {
			var next = (this.currentImage + 1) % this.images.length;
			this._showImage(next);
		},

		prevImage: function () {
			var prev = (this.currentImage - 1 + this.images.length) % this.images.length;
			this._showImage(prev);
		},

		_updateCounter: function () {
			if (!this.counterEl) return;
			if (this.images.length > 1) {
				this.counterEl.textContent    = (this.currentImage + 1) + ' \u2014 ' + this.images.length;
				this.counterEl.style.display  = '';
			} else {
				this.counterEl.style.display  = 'none';
			}
		},

		_updateNavVisibility: function () {
			var show    = this.images.length > 1;
			var prevBtn = document.getElementById('modal-prev');
			var nextBtn = document.getElementById('modal-next');
			if (prevBtn) prevBtn.style.display = show ? '' : 'none';
			if (nextBtn) nextBtn.style.display = show ? '' : 'none';
		},

		_onKeydown: function (e) {
			if (!this._open) return;
			switch (e.key) {
				case 'Escape':     this.close();      break;
				case 'ArrowLeft':  this.prevImage();  break;
				case 'ArrowRight': this.nextImage();  break;
			}
		},

		_setupModalTouch: function () {
			if (!this.el) return;
			var startX = 0;
			var self   = this;

			this.el.addEventListener('touchstart', function (e) {
				startX = e.touches[0].clientX;
			}, { passive: true });

			this.el.addEventListener('touchend', function (e) {
				var diff = startX - e.changedTouches[0].clientX;
				if (Math.abs(diff) > 45) {
					if (diff > 0) {
						self.nextImage();
					} else {
						self.prevImage();
					}
				}
			}, { passive: true });
		}
	};

	/* ==========================================================================
	   INIT
	   ========================================================================== */
	document.addEventListener('DOMContentLoaded', function () {
		Cursor.init();
		Slider.init();
		Modal.init();
	});

})();
