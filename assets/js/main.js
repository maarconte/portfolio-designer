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
		_cardStep: 0,

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
				self._cardStep = 0; // Invalidate cache
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
			if (this._cardStep) return this._cardStep;
			if (!this.cards.length) return 0;

			var card  = this.cards[0];
			var style = window.getComputedStyle(this.track);
			var gap   = parseFloat(style.gap) || parseFloat(style.columnGap) || 0;
			this._cardStep = card.offsetWidth + gap;
			return this._cardStep;
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
		images: [],
		_open: false,
		_galleryIndex: 0,
		_galleryTrack: null,
		_galleryCounter: null,

		init: function () {
			this.el             = document.getElementById('project-modal');
			this.imageContainer = document.getElementById('modal-image-container');
			this.titleEl        = document.getElementById('modal-title');
			this.metaEl         = document.getElementById('modal-meta');
			this.descEl         = document.getElementById('modal-description');

			if (!this.el) return;

			this.el.removeAttribute('hidden');

			var closeBtn = document.getElementById('modal-close');
			if (closeBtn) closeBtn.addEventListener('click', this.close.bind(this));

			var overlay = document.getElementById('modal-overlay');
			if (overlay) overlay.addEventListener('click', this.close.bind(this));

			document.addEventListener('keydown', this._onKeydown.bind(this));

			this._bindCards();
		},

		_bindCards: function () {
			var cards = document.querySelectorAll('.project-card');
			var self  = this;

			cards.forEach(function (card) {
				card.addEventListener('click', function () { self.open(card); });

				card.addEventListener('keydown', function (e) {
					if (e.key === 'Enter' || e.key === ' ') {
						e.preventDefault();
						self.open(card);
					}
				});

				var imgEl = card.querySelector('.project-card__image');
				if (imgEl && !isTouch) {
					imgEl.addEventListener('mouseenter', function () { Cursor.setState('more'); });
					imgEl.addEventListener('mouseleave', function () { Cursor.setState(null); });
				}
			});
		},

		open: function (card) {
			var projectId = card.dataset.projectId;
			var data      = (typeof jeanneProjects !== 'undefined') ? jeanneProjects[projectId] : null;

			if (!data) {
				console.error('Project data not found for ID:', projectId);
				return;
			}

			this.images      = data.gallery || [];
			var title        = data.title || '';
			var description  = data.description || '';
			var year         = data.year || '';
			var client       = data.client || '';
			var category     = data.category || '';

			if (this.titleEl) this.titleEl.textContent = title;

			if (this.metaEl) {
				var metaParts = [category, client, year].filter(Boolean);
				this.metaEl.textContent   = metaParts.join('\u2002\u2022\u2002');
				this.metaEl.style.display = metaParts.length ? '' : 'none';
			}

			if (this.descEl) {
				this.descEl.textContent   = description;
				this.descEl.style.display = description ? '' : 'none';
			}

			this._renderImages();

			// Reset scroll to top
			var body = this.el.querySelector('.drawer__body');
			if (body) body.scrollTop = 0;

			this.el.classList.add('is-open');
			this._open = true;
			document.body.classList.add('modal-open');
			Slider.pauseAutoPlay();

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
			}, 520);
		},

		isOpen: function () {
			return this._open;
		},

		// Build the gallery as a horizontal slider
		_renderImages: function () {
			if (!this.imageContainer) return;
			
			this._galleryIndex = 0;
			
			if (!this.images.length) {
				this.imageContainer.innerHTML = '';
				return;
			}

			var self = this;
			var fragment = document.createDocumentFragment();

			// Outer slider wrapper
			var sliderEl = document.createElement('div');
			sliderEl.className = 'drawer__gallery-slider';

			// Track
			var track = document.createElement('div');
			track.className = 'drawer__gallery';
			this._galleryTrack = track;

			this.images.forEach(function (imgData) {
				var el     = document.createElement('img');
				el.src     = imgData.url;
				el.alt     = imgData.alt || '';
				el.loading = 'lazy';
				track.appendChild(el);
			});

			sliderEl.appendChild(track);

			// Nav zones (only if more than one image)
			if (this.images.length > 1) {
				var prevZone = document.createElement('div');
				prevZone.className = 'drawer__gallery-nav drawer__gallery-nav--prev';
				prevZone.addEventListener('click', function () { self._galleryPrev(); });
				prevZone.addEventListener('mouseenter', function () { Cursor.setState('arrow-left'); });
				prevZone.addEventListener('mouseleave', function () { Cursor.setState(null); });

				var nextZone = document.createElement('div');
				nextZone.className = 'drawer__gallery-nav drawer__gallery-nav--next';
				nextZone.addEventListener('click', function () { self._galleryNext(); });
				nextZone.addEventListener('mouseenter', function () { Cursor.setState('arrow-right'); });
				nextZone.addEventListener('mouseleave', function () { Cursor.setState(null); });

				sliderEl.appendChild(prevZone);
				sliderEl.appendChild(nextZone);
			}

			fragment.appendChild(sliderEl);

			// Counter
			if (this.images.length > 1) {
				var counter = document.createElement('p');
				counter.className = 'drawer__gallery-counter';
				this._galleryCounter = counter;
				fragment.appendChild(counter);
				this._galleryUpdateCounter();
			}

			// Touch swipe
			this._setupGalleryTouch(sliderEl);

			// Single DOM update
			this.imageContainer.innerHTML = '';
			this.imageContainer.appendChild(fragment);
		},

		_galleryGoTo: function (index) {
			var total = this.images.length;
			this._galleryIndex = ((index % total) + total) % total;
			if (this._galleryTrack) {
				this._galleryTrack.style.transform = 'translateX(-' + (this._galleryIndex * 100) + '%)';
			}
			this._galleryUpdateCounter();
		},

		_galleryNext: function () { this._galleryGoTo(this._galleryIndex + 1); },
		_galleryPrev: function () { this._galleryGoTo(this._galleryIndex - 1); },

		_galleryUpdateCounter: function () {
			if (this._galleryCounter) {
				this._galleryCounter.textContent = (this._galleryIndex + 1) + ' \u2014 ' + this.images.length;
			}
		},

		_setupGalleryTouch: function (el) {
			var startX = 0;
			var self   = this;
			el.addEventListener('touchstart', function (e) {
				startX = e.touches[0].clientX;
			}, { passive: true });
			el.addEventListener('touchend', function (e) {
				var diff = startX - e.changedTouches[0].clientX;
				if (Math.abs(diff) > 45) {
					diff > 0 ? self._galleryNext() : self._galleryPrev();
				}
			}, { passive: true });
		},

		_onKeydown: function (e) {
			if (!this._open) return;
			if (e.key === 'Escape')      this.close();
			if (e.key === 'ArrowLeft')   this._galleryPrev();
			if (e.key === 'ArrowRight')  this._galleryNext();
		},

		_setupModalTouch: function () {
			// Touch géré par _setupGalleryTouch sur le slider.
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
