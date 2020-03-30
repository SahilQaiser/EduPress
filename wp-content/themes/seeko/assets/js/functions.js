/*-----------------------------------------------------------------------------------

 Theme Name: SEEKO
 Theme URI: http://themeforest.net/user/SeventhQueen
 Description: Community Site Builder with BuddyPress SuperPowers
 Author: SeventhQueen
 Author URI: http://themeforest.net/user/SeventhQueen
 Javascript theme logic

 == Table of contents ==
 1. Init logic
 2. Responsive classes
 3. Resize functions
 4. Shadow functionality
 5. Lazy image load
 6. Slick Carousel

 -----------------------------------------------------------------------------------*/

var SQ = SQ || {};
(function ($) {

	// USE STRICT
	"use strict";

	// Debounce
	var $event = $.event,
		$special,
		resizeTimeout;

	$special = $event.special.debouncedresize = {
		setup: function () {
			$(this).on("resize", $special.handler);
		},
		teardown: function () {
			$(this).off("resize", $special.handler);
		},
		handler: function (event, execAsap) {
			// Save the context
			var context = this,
				args = arguments,
				dispatch = function () {
					// set correct event type
					event.type = "debouncedresize";
					$event.dispatch.apply(context, args);
				};

			if (resizeTimeout) {
				clearTimeout(resizeTimeout);
			}

			resizeTimeout = setTimeout(dispatch, $special.threshold);
		},
		threshold: 400
	};

	SQ.responsiveClassesAdded = false;

	SQ.main = {
		lazyInstance: null,
		lazyFirstScroll: false,
		startShadow: false,
		ie10Verify: 0,
		ie11Verify: 0,
		onLoad: function () {
			SQ.main.imageResize();
		},

		/* 1. Init logic */
		init: function () {

			SQ.main.ie11Verify = navigator.userAgent.indexOf("rv:11.0");
			SQ.main.ie10Verify = navigator.userAgent.indexOf("MSIE 10.0");

			// bp members min height
			SQ.main.bpMinHeight();


			// verify ie 10
			if (SQ.main.ie10Verify !== -1) {
				$("body").addClass("ie-10");
			}

			// verify ie 11
			if (SQ.main.ie11Verify !== -1) {
				$("body").addClass("ie-11");
			}

			//responsive classes
			SQ.main.responsiveClasses();

			//accordion
			$(".collapse").on('show.bs.collapse', function () {
				$(this).closest(".accordion-card").addClass("show");
			});

			$(".collapse").on('hide.bs.collapse', function () {
				$(this).closest(".accordion-card").removeClass("show");
			});


			/* Navbar multilevel dropdown - mobile */
			$( '#seekoMainMenu .dropdown-menu a.dropdown-toggle' ).on( 'click touchstart', function ( e ) {
				var $el = $( this );
				var $parent = $( this ).offsetParent( ".dropdown-menu" );
				if ( !$( this ).next().hasClass( 'show' ) ) {
					$( this ).parents( '.dropdown-menu' ).first().find( '.show' ).removeClass( "show" );
				}
				var $subMenu = $( this ).next( ".dropdown-menu" );
				$subMenu.toggleClass( 'show' );

				$( this ).parent( "li" ).toggleClass( 'show' );

				$( this ).parents( 'li.nav-item.dropdown.show' ).on( 'hidden.bs.dropdown', function ( e ) {
					$( '.dropdown-menu .show' ).removeClass( "show" );
				} );
				return false;
			} );


			//tooltip
			$('[data-toggle="tooltip"]').tooltip({
				container: 'body'
			});

			// image shadow
			SQ.main.imageShadow();

			//img-progressive
			SQ.main.registerLazy();

			//slick
			SQ.main.slickCarousel();

			SQ.main.slickInitPrevNext();

			$('label > input[type="checkbox"], label > input[type="radio"]').each(function () {
				$(this).after('<span class="check-icon"></span>');
				$(this).parent('label').addClass('form-check-wrapper label-' + $(this).attr('type'));
			});

			//nav post hover
			$(".nav2-post .entry-title, .nav2-post .quote-post-card .entry-content blockquote").on('mouseenter', function () {
				if (!$body.hasClass("device-xs") && !$body.hasClass("device-sm") && !$body.hasClass("device-md")) {

					$(this).closest(".nav2-post").addClass("is-hover");
					$(this).closest(".standard-post").addClass("overlay");


					setTimeout(function () {
						SQ.main.imageResize();
					}, 250);
				}
			});
			$(".nav2-post").on('mouseleave', function () {
				var $this = $(this);
				if (!$body.hasClass("device-xs") && !$body.hasClass("device-sm") && !$body.hasClass("device-md")) {
					$this.removeClass("is-hover");
					$this.closest(".standard-post").removeClass("overlay");
				}
			});


			// Fit Vids
			if ($.fn.fitVids) {
				$(".entry-content, .activity-content").fitVids();
			}
			// textarea autoresize
			$('textarea[data-autoresize]').each(function () {
				var offset = this.offsetHeight - this.clientHeight;

				var resizeTextarea = function (el) {
					$(el).css('height', 'auto').css('height', el.scrollHeight + offset);
				};
				$(this).on('keyup input', function () {
					resizeTextarea(this);
				}).removeAttr('data-autoresize');
			});

			$('body').on('bp-data-update', function () {
				setTimeout(function () {
					$(window).trigger('resize');
					SQ.main.registerLazy();
				}, 100);
			});

			// object nav menu .last
			if ($("#subnav ul li").hasClass("last")) {
				$("#subnav").addClass("last-but-one");
			}


			// sidebar widget_nav_menu
			$(".widget_nav_menu .menu-item-has-children > a").on('click', function () {
				if ($(this).hasClass("active")) {
					$(this).removeClass("active");
					$(this).siblings(".sub-menu").find(".menu-item-has-children a").removeClass("active");
					$(this).siblings(".sub-menu").removeClass("open");
					$(this).siblings(".sub-menu").find(".sub-menu").removeClass("open");
				} else {
					$(this).addClass("active");
					$(this).siblings(".sub-menu").addClass("open");
				}
				return false;
			});

			$('body').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {

				SQ.main.registerLazy( false );

				/* Only if plugin exists */
				if ($.fn.slick) {

					$('.svq-slick').each(function() {
						if ($(this).hasClass('slick-initialized')) {
							$(this).slick('setPosition');
						}
					});

					if ($(this).hasClass('svq-slick-filter')) {
						var tab = $(this).attr('href');
						var carousel = $(tab).find('.svq-slick').data('carousel');
						$('.svq-slick-prev, .svq-slick-next', $(this).closest('.svq-sh-carousel')).attr('data-carousel', carousel);
					}
				}
			});

		},

		onResize: function () {
			SQ.main.bpMinHeight();
		},

		onClassicResize: function () {
			SQ.main.imageResize();
		},

		onScroll: function () {

			/* Enable Lazy also for non visible elements */
			if (SQ.main.lazyFirstScroll === false) {
				SQ.main.registerLazy( false );
				SQ.main.lazyFirstScroll = true;
			}

		},

		/* 2. Responsive classes */
		responsiveClasses: function () {

			if (SQ.responsiveClassesAdded === true) {
				return;
			}
			var jRes = jRespond([
				{
					label: 'xs',
					enter: 0,
					exit: 575.99
				}, {
					label: 'sm',
					enter: 576,
					exit: 767.99
				}, {
					label: 'md',
					enter: 768,
					exit: 1149.99
				}, {
					label: 'lg',
					enter: 1150,
					exit: 1439.99
				}, {
					label: 'xl',
					enter: 1440,
					exit: 10000
				}
			]);
			jRes.addFunc([
				{
					breakpoint: 'xl',
					enter: function () {
						$body.addClass('device-xl');
					},
					exit: function () {
						$body.removeClass('device-xl');
					}
				}, {
					breakpoint: 'lg',
					enter: function () {
						$body.addClass('device-lg');
					},
					exit: function () {
						$body.removeClass('device-lg');
					}
				}, {
					breakpoint: 'md',
					enter: function () {
						$body.addClass('device-md');
					},
					exit: function () {
						$body.removeClass('device-md');
					}
				}, {
					breakpoint: 'sm',
					enter: function () {
						$body.addClass('device-sm');
					},
					exit: function () {
						$body.removeClass('device-sm');
					}
				}, {
					breakpoint: 'xs',
					enter: function () {
						$body.addClass('device-xs');
					},
					exit: function () {
						$body.removeClass('device-xs');
					}
				}
			]);
		},

		/* 3. Resize functions */
		imageResize: function () {

			/* Only for IE11 */
			if (navigator.userAgent.indexOf("rv:11.0") === -1) {
				return;
			}

			var photoContainer = $(".aspect-ratio .img-card");

			photoContainer.each(function () {
				var wrapperWidth = $(this).width();
				var wrapperHeight = $(this).height();
				var wrapperRatio = wrapperWidth / wrapperHeight;

				console.log("Ww: " + wrapperWidth + "Wh: " + wrapperHeight);

				var imageWidth = $(this).find("img").width();
				var imageHeight = $(this).find("img").height();
				var imageRatio = imageWidth / imageHeight;

				/*if (wrapperWidth === 0 || wrapperHeight === 0) {
				 return false;
				 }*/

				if (imageRatio <= wrapperRatio) {
					var newImageHeight = wrapperWidth / imageRatio;
					var newImageWidth = wrapperWidth;
					var semiImageHeight = newImageHeight / 2;

					$(this).find("img").css({
						width: newImageWidth + 1,
						height: newImageHeight + 1,
						marginTop: -semiImageHeight,
						marginLeft: 0,
						top: "50%",
						left: "0"
					});

				} else {
					var newImageHeight = wrapperHeight;
					var newImageWidth = wrapperHeight * imageRatio;
					var semiImageWidth = newImageWidth / 2;

					$(this).find("img").css({
						width: newImageWidth + 1,
						height: newImageHeight + 1,
						marginTop: 0,
						marginLeft: -semiImageWidth,
						top: "0",
						left: "50%"
					});
				}

				$(this).css("opacity", "1");

			});
		},

		/* 4. Shadow functionality */
		imageShadow: function () {
			if ($body.hasClass("enable-shadow") && !($body.hasClass("ie-11") || $body.hasClass("ie-10"))) {

				$(".img-dynamic").each(function () {
					if ($(this).hasClass("have-shadow")) {
						var imgSource = $(this).find("img").attr("src");
						$(this).append("<div class='img-shadow'><img src='" + imgSource + "'></div>");
					}
				});

			}
		},

		/* 5. Lazy image load */
		registerLazy: function ( visible ) {

			var lazySelector = '.img-progressive.progressive--not-loaded';

			/* Only if plugin exists */
			if (!$.fn.Lazy) {
				return false;
			}

			if (SQ.main.lazyInstance) {
				SQ.main.lazyInstance.destroy();
			}

			var attributes = {
				chainable: false,
				enableThrottle: true,
				throttle: 250,
				visibleOnly: true,
				afterLoad: function (element) {
					$(element).removeClass('progressive--not-loaded').addClass('progressive--is-loaded');
				},
				onError: function (element) {
					$(element).removeClass('progressive--not-loaded').addClass('progressive--is-loaded');
				}
			};
			if( visible !== undefined ) {
				attributes.visibleOnly = visible;
			}

			SQ.main.lazyInstance = $(lazySelector).Lazy(attributes);

			return false;
		},

		/* 6. Slick Carousel */
		slickCarousel: function () {

			/* Only if plugin exists */
			if (!$.fn.slick) {
				return false;
			}

			var slicks = $(".svq-slick");

			if (slicks.length === 0) {
				return false;
			}

			slicks.each(function () {

				if ($(this).hasClass('slick-initialized')) {
					$(this).slick('setPosition');
					return false;
				}

				var $this = $(this);
				var $responsive = [];
				var breakpoints = ['xs', 'sm', 'md', 'lg'];
				var breakpointsValues = {
					'xs': '576',
					'sm': '768',
					'md': '1170',
					'lg': '1440'
				};

				$.each(breakpoints, function (index, value) {

					var $settings = {};

					if ($this.attr('data-' + value + '-infinite')) {
						$settings.infinite = $this.data(value + '-infinite');
					}
					if ($this.attr('data-' + value + '-show-slides')) {
						$settings.slidesToShow = $this.data(value + '-show-slides');
					}
					if ($this.attr('data-' + value + '-scroll-slides')) {
						$settings.slidesToScroll = $this.data(value + '-scroll-slides');
					}
					if ($this.attr('data-' + value + '-dots')) {
						$settings.dots = $this.data(value + '-dots');
					}
					if ($this.attr('data-' + value + '-arrows')) {
						$settings.arrows = $this.data(value + '-arrows');
					}
					if ($this.attr('data-' + value + '-fade')) {
						$settings.fade = $this.data(value + '-fade');
					}
					if ($this.attr('data-' + value + '-center-mode')) {
						$settings.centerMode = $this.data(value + '-center-mode');
					}
					if ($this.attr('data-' + value + '-center-padding')) {
						$settings.centerPadding = $this.data(value + '-center-padding');
					}
					if ($this.attr('data-' + value + '-variable-width')) {
						$settings.variableWidth = $this.data(value + '-variable-width');
					}
					if ($this.attr('data-' + value + '-speed')) {
						$settings.speed = $this.data(value + '-speed');
					}
					if ($this.attr('data-' + value + '-ease')) {
						$settings.ease = $this.data(value + '-ease');
					}


					if (!jQuery.isEmptyObject($settings)) {
						var data = {
							breakpoint: breakpointsValues[value],
							settings: $settings
						};

						$responsive.push(data);
					}

				});

				var $infinite = true,
					$slidesToShow = 1,
					$slidesToScroll = 1,
					$dots = false,
					$arrows = true,
					$fade = false,
					$speed = 300,
					$ease = "ease",
					$centerMode = false,
					$centerPadding = "0px",
					$variableWidth = false;

				if ($(this).attr('data-infinite')) {
					$infinite = $(this).data('infinite');
				}
				if ($(this).attr('data-show-slides')) {
					$slidesToShow = $(this).data('show-slides');
				}
				if ($(this).attr('data-scroll-slides')) {
					$slidesToScroll = $(this).data('scroll-slides');
				}
				if ($(this).attr('data-dots')) {
					$dots = $(this).data('dots');
				}
				if ($(this).attr('data-arrows')) {
					$arrows = $(this).data('arrows');
				}
				if ($(this).attr('data-fade')) {
					$fade = $(this).data('fade');
				}
				if ($(this).attr('data-speed')) {
					$speed = $(this).data('speed');
				}
				if ($(this).attr('data-ease')) {
					$ease = $(this).data('ease');
				}
				if ($(this).attr('data-center-mode')) {
					$centerMode = $(this).data('center-mode');
				}
				if ($(this).attr('data-center-padding')) {
					$centerPadding = $(this).data('center-padding');
				}
				if ($(this).attr('data-variable-width')) {
					$variableWidth = $(this).data('variable-width');
				}

				$(this).slick({
					infinite: $infinite,
					slidesToShow: $slidesToShow,
					slidesToScroll: $slidesToScroll,
					dots: $dots,
					arrows: $arrows,
					fade: $fade,
					responsive: $responsive,
					speed: $speed,
					cssEase: $ease,
					centerMode: $centerMode,
					centerPadding: $centerPadding,
					variableWidth: $variableWidth,
					prevArrow: '<button class="slick-prev btn btn-xs" aria-label="Previous" type="button"><i class="icon icon-arrow-left"></i></button>',
					nextArrow: '<button class="slick-next btn btn-xs" aria-label="Next" type="button"><i class="icon icon-arrow-right"></i></button>'
				});

				$(this).on('afterChange', function (event, slick, currentSlide, nextSlide) {
					SQ.main.registerLazy();
				});

			});


		},

		slickInitPrevNext: function () {
			/* Only if plugin exists */
			if (!$.fn.slick) {
				return false;
			}

			$('.svq-slick-next').on('click', function (e) {
				var dataCarousel = $(this).attr("data-carousel");
				$('.svq-slick[data-carousel="' + dataCarousel + '"]').slick("slickNext");

				return false;

			});

			$('.svq-slick-prev').on('click', function (e) {
				var dataCarousel = $(this).attr("data-carousel");
				$('.svq-slick[data-carousel="' + dataCarousel + '"]').slick("slickPrev");
				e.preventDefault();

				return false;
			});
		},

		bpMinHeight: function () {

			var admHeight = $("#wpadminbar").height();
			var total = $("#header").height();

			if ($("body").hasClass("adminbar-enable")) {
				total += admHeight;
			}

			$(".bp-member-layout").css("min-height", "calc(100vh - " + total + "px)");
		}

	};

	var $window = $(window),
		$body = $('body');

	$(document).ready(SQ.main.init);
	$window.on('load', SQ.main.onLoad);
	$window.on('debouncedresize', SQ.main.onResize);
	$window.on('resize', SQ.main.onClassicResize);
	$window.on('scroll', SQ.main.onScroll);

})(jQuery);
