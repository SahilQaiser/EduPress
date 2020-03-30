var sqImporter = sqImporter || {};
(function ($) {

	"use strict";

	var sqLoader = '<div class="sk-folding-cube"> <div class="sk-cube1 sk-cube"></div> <div class="sk-cube2 sk-cube"></div> <div class="sk-cube4 sk-cube"></div> <div class="sk-cube3 sk-cube"></div> </div>';

	var SQModal = function () {
	};
	$.extend(SQModal.prototype, {
		modal: '',
		closeButton: '<a href="#" class="cd-popup-close img-replace"></a>',
		progressBar: ' <div class="progress"><div class="progress-bar"></div></div>',
		getStructure: function (options) {

			var out = '',
				loader = '',
				classes = 'cd-popup',
				containerClasses = 'cd-popup-container';

			if (options.loader) {
				loader = sqLoader;
			}

			if (options.nobg) {
				containerClasses += ' no-bg';
			}
			out += '<div class="' + classes + '" role="alert">' +
				'<div class="' + containerClasses + '">' +
				'<div class="cd-loader">' + loader + '</div>' +
				'<div class="cd-text">' + options.text + '</div>';
			if (options.buttons) {
				out += '<ul class="cd-buttons">' +
					'<li><a class="yes-modal" href="#">GO!</a></li>' +
					'<li><a class="no-modal" href="#">Back</a></li>' +
					'</ul>';
			}
			if (options.progress) {
				out += this.progressBar;
			}

			if (options.closeBtn) {
				out += this.closeButton;
			}
			out += '</div> <!-- cd-popup-container -->' +
				'</div> <!-- cd-popup -->';

			return out;
		},
		open: function (data) {

			var options = $.extend({
				text: "",
				nobg: false,
				loader: false,
				closeBtn: false,
				progress: false,
				progressData: '',
				buttons: true
			}, data);


			this.modal = $(this.getStructure(options));
			this.modal.appendTo('body');

			$('.cd-popup').addClass('is-visible');

			if (options.progressData) {
				this.update({progressData: options.progressData});
			}

			var $this = this;
			$('.yes-modal').on('click', this.modal, function (e) {
				//$this.hide();
				$this.modal.trigger('modal-confirmed');
				e.preventDefault();
				return false;
			});
			$('.no-modal').on('click', this.modal, function (e) {
				$this.close();
				$this.modal.trigger('modal-not-confirmed');
				e.preventDefault();
				return false;
			});

			this.modal.on('click', '.cd-popup-close', function (e) {
				$this.close();
				e.preventDefault();
				return false;
			});

		},

		close: function () {
			this.modal.trigger('modal-closed');
			this.modal.remove();
		},

		hide: function () {
			this.modal.removeClass('is-visible');
		},

		update: function (data) {
			if (data.hasOwnProperty("text") && data.text != '') {
				this.modal.find('.cd-text').html(data.text);
			}

			if (data.hasOwnProperty("loader")) {
				if (data.loader) {
					this.modal.find('.cd-loader').html(sqLoader);
				} else {
					this.modal.find('.cd-loader').html('');
				}
			}

			if (data.hasOwnProperty("closeBtn")) {
				if (data.closeBtn) {
					this.modal.find('.cd-popup-close').remove();
					this.modal.find('.cd-popup-container').append(this.closeButton);
				} else {
					this.modal.find('.cd-popup-close').remove();
				}
			}

			if (data.hasOwnProperty("nobg")) {
				if (data.nobg) {
					this.modal.find('.cd-popup-container').addClass('no-bg');
				} else {
					this.modal.find('.cd-popup-container').removeClass('no-bg');
				}
			}

			if (data.hasOwnProperty("progress")) {
				if (data.progress) {
					this.modal.find('.progress').remove();
					this.modal.find('.cd-popup-container').append(this.progressBar);
				} else {
					this.modal.find('.progress').remove();
				}
			}

			if (data.hasOwnProperty("progressData")) {
				if (data.progressData) {
					this.modal.find('.progress').remove();
					this.modal.find('.cd-popup-container').append(this.progressBar);
					this.modal.find('.progress-bar').attr('style', 'width: ' + data.progressData + '%;');
				}
			}
		}
	});

	var importerAction = function () {
	};
	$.extend(importerAction.prototype, {
		pendingProgress: null,
		updatingProgress: false,
		updatePeriod: 1500,
		progressInterval: null,
		failedCount: 0,
		failedMax: 10,
		pendingRequest: null,
		ajaxModal: null,
		importModal: null,
		messageModal: null,
		pid: Math.round(new Date().getTime() + (Math.random() * 100)),
		data: null,

		init: function (el) {
			this.pendingProgress = null;
			this.pendingRequest = null;
			this.updatingProgress = false;
			this.progressInterval = null;
			this.failedCount = 0;

			this.ajaxModal = new SQModal();
			this.importModal = new SQModal();
			this.messageModal = new SQModal();
			this.importModal.open({text: $(el).siblings('.to-left').html(), buttons: true, nobg: false});
			var self = this;

			$('.cd-popup .tooltip-me').tooltip({
				position: {my: 'center bottom', at: 'center top-10'}
			});

			self.importModal.modal.on('modal-confirmed', function () {

				var importData = self.importModal.modal.find('input[name]:checked');
				if (!importData.length) {
					self.messageModal.open({text: 'The selection is empty!', buttons: false, closeBtn: true});
					return false;
				}

				var data = {
					action: 'sq_single_import',
					options: importData.serialize(),
					security: $('.kleo-import-form').find('[name="kleo_import_nonce"]').val(),
					pid: self.pid
				};
				data.options += "&import_demo=" + $(el).val();

				self.data = data;

				self.pendingRequest = self.ajaxRequest();

			});
		},

		ajaxRequest: function (e) {
			var self = null;
			var hasFailed = false;

			if (e !== undefined) {
				self = e;
				hasFailed = true;
			} else {
				self = this;
			}

			//console.log('starting ajax request');
			delete self.data.check_progress;

			return $.ajax({
				url: ajaxurl,
				method: 'POST',
				data: self.data,
				beforeSend: function (xhr) {
					//console.log('doing main ajax...');
					if (!hasFailed) {
						self.importModal.close();
						self.ajaxModal.open({
							text: "Starting import...",
							buttons: false,
							nobg: true,
							loader: true,
							progressData: '1'
						});
					}
				},
				statusCode: {
					500: function () {
						self.onFail(self);
					}
				},
				error: function () {
					self.onFail(self);
				}
			})
				.done(function (response) {

					response = self.getValidResponse(response);
					if (typeof(response) === 'object' && response.hasOwnProperty('success')) {

						var dataText = '';
						if (response.hasOwnProperty('data') && response.data.hasOwnProperty('message')) {
							dataText = response.data.message;
						}

						if (response.hasOwnProperty('data') && response.data.hasOwnProperty('message')) {
							dataText = response.data.message;
						}

						if (response.success == true) {
							if (response.data.hasOwnProperty('process')) {
								//ongoing process. make the request again
								self.ajaxModal.update({
									progressData: response.data.progress,
									text: response.data.message,
									progress: true,
									buttons: false,
									nobg: true,

								});
								setTimeout(function () {
									self.pendingRequest = self.ajaxRequest(self);
								}, 1000);

							} else {
								//the process is complete
								if (dataText === '') {
									dataText = 'Import is now complete!!!';
								}

								self.ajaxModal.update({
									text: dataText,
									loader: false,
									closeBtn: true,
									nobg: false,
									progress: false
								});
							}

						} else if (response.success == false) {

							if (dataText === '') {
								dataText = 'Import failed!!!';
							}
							dataText = '<small>' + dataText + '<br>Debug Data:' + JSON.stringify(response.data.debug) + '</small>';

							self.onFail(self, dataText);
						}

					} else {
						self.onFail(self);
					}

				})
				.fail(function () {
					self.onFail(self);
				});
		},
		onFail: function (self, message) {

			if (message === undefined) {
				message = 'Failed to import all data. Please try again!';
			}

			self.failedCount++;
			if (self.failedCount < self.failedMax) {
				//console.log('fail start new ajax');
				setTimeout(function () {
					self.pendingRequest = self.ajaxRequest(self);
				}, 1000);
			} else {
				self.pendingRequest = null;
				self.ajaxModal.close();
				self.ajaxModal.open({
					text: /*'<div class="bg-msg fail-msg"><span class="dashicons dashicons-warning"></span></div>' + */message,
					loader: false,
					closeBtn: true,
					nobg: false,
					progress: false,
					buttons: false
				});
			}

		},
		getValidResponse: function(str) {
			if ( typeof(str) === 'object' ) {
				return str;
			}

			var valid = true;
			try {
				JSON.parse(str);
			} catch (e) {
				valid = false;
			}

			if (valid) {
				return str;
			}

			var res = str.match(/{.*}/gi);
			if (res) {
				try {
					JSON.parse(res[0]);
				} catch (e) {
					return false;
				}
				return JSON.parse(res[0]);
			}

			return false;
		}
	});

	sqImporter.action = new importerAction();

	$(document).ready(function () {

		var $body = $('body');
		sqImporter.generalModal = new SQModal();

		//new ajax functionality
		$('.import-demo-btn').on('click', function (e) {
			sqImporter.action.init(this);

			e.preventDefault();
			return false;

		});

		//set as home
		$body.on('click', '.sq-set-as-home', function () {
			var self = $(this);
			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'sq_set_as_home', pid: self.data('pid'),
					security: $('.kleo-import-form').find('[name="kleo_import_nonce"]').val()
				},
				beforeSend: function (xhr) {
					//console.log('doing progress ajax...');
				}
			}).done(function (response) {
				if (response.hasOwnProperty('success')) {
					if (response.hasOwnProperty('data') && response.data.hasOwnProperty('message')) {
						sqImporter.generalModal.open({text: response.data.message, buttons: false, closeBtn: true});
						self.remove();
					}
				} else {
					sqImporter.generalModal.open({text: JSON.stringify(response), buttons: false, closeBtn: true});
				}
			});
			return false;
		});

		// namespace
		var importer = $('.kleo-import');

		// reset select
		$('select.import', importer).val('');

		// disable submit button
		$('.button.advanced', importer).attr('disabled', 'disabled');

		// select.import change
		$('select.import', importer).change(function () {

			var val = $(this).val();

			// submit button
			if (val) {
				$('.button.advanced', importer).removeAttr('disabled');
			} else {
				$('.button.advanced', importer).attr('disabled', 'disabled');
			}

			// content
			if (val == 'content') {
				$('.row-content', importer).show();
			} else {
				$('.row-content', importer).hide();
			}

			// homepage
			if (val == 'page') {
				$('.row-homepage', importer).show();
			} else {
				$('.row-homepage', importer).hide();
			}

		});

		$('select[name=page], select[name=content], select.import', importer).change(function () {
			var attach = $(this).find('option:selected').attr('data-attach');
			if (typeof attach !== typeof undefined && attach !== false) {
				$('.row-attachments', importer).show();
			} else {
				$('.row-attachments', importer).hide();
			}

		});

		/*$("input.check-attachment").on("change", function() {
			if( $(this).is(":checked")) {
				$(this).closest('.to-left').find("input.check-page").prop('checked', true);
			}
		});*/

		/* Un-check the Attachments checkbox if no page is checked */
		$body.on("change", "input.check-page", function () {
			var inputContainer = $(this).closest('.demo-checkboxes');
			if (inputContainer.find("input.check-page:checked").length == 0) {

				/* Set initial state before un-checking */
				if (inputContainer.find("input.check-attachment").prop('checked') == true) {
					inputContainer.find("input.check-attachment").data('initial-state', 'checked');
				} else {
					inputContainer.find("input.check-attachment").data('initial-state', 'unchecked');
				}

				inputContainer.find("input.check-attachment").prop('disabled', true);
				inputContainer.find("input.check-attachment").prop('checked', false);

			} else {
				inputContainer.find("input.check-attachment").prop('disabled', false);

				if (inputContainer.find("input.check-page:checked").length == 1 && $(this).is(":checked") && inputContainer.find("input.check-attachment").data('initial-state')) {
					var initialState = inputContainer.find("input.check-attachment").data('initial-state');

					if (initialState == 'checked') {
						inputContainer.find("input.check-attachment").prop('checked', true);
					}
				}
			}
		});

		$body.on("change", '.demo-checkboxes input[type=checkbox]', function () {
			var $isChecked = false;
			$(this).closest('.demo-checkboxes').find('input[type=checkbox]').each(function (index, element) {
				if ($(this).is(":checked")) {
					$isChecked = true;
				}
			});

			if ($isChecked === false) {
				$(this).closest(".demo-options").find('.import-demo-btn').prop('disabled', true);
			} else {
				$(this).closest(".demo-options").find('.import-demo-btn').prop('disabled', false);
			}
		});
	});

})(jQuery);
