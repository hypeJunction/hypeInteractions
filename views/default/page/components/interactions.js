/**
 * @module page/components/interactions
 */
define(function (require) {

	var elgg = require('elgg');
	var $ = require('jquery');
	require('jquery.form');
	var spinner = require('elgg/spinner');
	
	var interactions = {
		ready: false,
		init: function () {
			// Strip core comments functionality
			elgg.comments.init = elgg.nullFunction;
			
			if (interactions.ready) {
				return;
			}
			$(document).on('click', '.interactions-form:not(.elgg-state-expanded)', interactions.expandForm);
			$(document).on('click', '.interactions-state-toggler', interactions.toggleState);

			$(document).on('click', '.elgg-menu-interactions > .interactions-tab > a', interactions.triggerTabSwitch);
			$(document).on('click', '.elgg-menu-interactions > .elgg-menu-item-comments > a', interactions.triggerTabSwitch);

			$(document).off('click', '.interactions-edit > a'); // disable core js events
			$(document).on('click', '.interactions-edit > a', interactions.loadEditForm);
			$(document).on('submit', '.interactions-form', interactions.saveComment);

			$(document).on('change', '.interactions-comments-list,.interactions-likes-list', interactions.listChanged);

			interactions.ready = true;
		},
		require: function () {
			if (!require.defined('hypeList')) {
				return false;
			}
			return require(['hypeList', 'framework/lists/init']);
		},
		preloader: function ($elem) {
			return {
				beforeSend: function () {
					spinner.start();
					$elem.find('[type="submit"]').prop('disabled', true).addClass('elgg-state-disabled');
				},
				complete: function () {
					spinner.stop();
					$elem.find('[type="submit"]').prop('disabled', false).removeClass('elgg-state-disabled');
				}
			};
		},
		buildSelector: function (prefix, obj, suffix) {
			var selector = prefix || '';
			if (typeof obj === 'object') {
				$.each(obj, function (key, val) {
					selector += ['[', key, '=', val, ']'].join('');
				});
			}
			selector += suffix || '';
			return selector;
		},
		updateStats: function (guid, stats) {
			$.each(stats, function (trait, stat) {
				if (typeof stat.count !== 'undefined') {
					interactions.updateBadge(guid, trait, stat.count);
				}
				if (typeof stat.state !== 'undefined') {
					interactions.updateStateToggler(guid, trait, stat.state);
				}
			});
		},
		updateBadge: function (guid, trait, count) {
			if (count >= 0 && trait) {
				$(interactions.buildSelector('.interactions-badge', {
					'data-guid': guid,
					'data-trait': trait
				}, ' .interactions-badge-indicator')).text(count);
			}
		},
		updateStateToggler: function (guid, trait, state) {
			var $toggler = $(interactions.buildSelector('.interactions-state-toggler', {
				'data-guid': guid,
				'data-trait': trait
			}));
			$toggler.data({
				'state': state
			}).attr({
				'data-state': state
			}).text(elgg.echo(['interactions', trait, state].join(':')));
		},
		toggleState: function (e) {
			e.preventDefault();
			var options = interactions.preloader($(this));
			options.success = function(response) {
				var data = response.output;
				interactions.updateStats(data.guid, data.stats);
			};
			elgg.action($(this).attr('href'), options);
		},
		expandForm: function (e) {
			$(this).addClass('elgg-state-expanded');
			$(this).find('[name="generic_comment"]').focus().trigger('click');
		},
		saveComment: function (e) {
			e.preventDefault();
			var $form = $(this);
			$form.ajaxSubmit($.extend({}, interactions.preloader($form), {
				headers: {
					'X-Requested-With': 'XMLHttpRequest', // simulate XHR
				},
				dataType: 'json',
				success: function (response) {
					if (response.status >= 0) {
						$form.siblings().find('.elgg-list').first().trigger('addFetchedItems', [response.output.view, null, true]).trigger('refresh');
						$form.resetForm();
						$form.trigger('reset');
					}
					// Hide edit form
					if ($form.is('.elgg-form-edit')) {
						if (response.status >= 0) {
							$form.siblings().replaceWith(response.output.view);
						}
						$form.siblings().show();
						$form.remove();
					}
					if (response.system_messages) {
						elgg.register_error(response.system_messages.error);
						elgg.system_message(response.system_messages.success);
					}
				}
			}));
		},
		loadEditForm: function (e) {
			e.preventDefault();
			var $elem = $(this);
			// If the menu was displayed in a popup module,
			// we need to connect it properly to the original element
			var $menu = $elem.closest('.elgg-menu');
			var $item;
			if ($menu.is('.elgg-state-popped')) {
				var $trigger = $menu.data('trigger');
				if ($trigger.length) {
					$item = $trigger.closest('.elgg-list > li');
				}
			}
			if (!$item) {
				var $item = $menu.closest('.elgg-list > li');
			}
			elgg.ajax($elem.attr('href'), $.extend({}, interactions.preloader($elem), {
				success: function (data) {
					var $form = $(data);
					$item.append($form);
					$form.trigger('initialize')
					$form.siblings().hide();
					$form.find('textrea,input[type="text"]').first().focus().trigger('click');
					$form.addClass('elgg-form-edit')
					$form.find('.elgg-button-cancel').on('click', function () {
						$form.siblings().show();
						$form.remove();
					});
				}
			}));
		},
		triggerTabSwitch: function (e) {
			e.preventDefault();

			var $elem = $(this);

			var trait = $elem.data('trait');

			if ($elem.closest('.interactions-controls').find('.elgg-menu-interactions').find('.interactions-tab > a[data-trait="' + trait + '"]').length) {
				$elem = $elem.closest('.interactions-controls').find('.elgg-menu-interactions').find('.interactions-tab > a[data-trait="' + trait + '"]');
			}

			$elem.parent().addClass('elgg-state-selected').siblings().removeClass('elgg-state-selected');

			var $controls = $(this).closest('.interactions-controls');
			$controls.parent().addClass('interactions-has-active-tab');

			var $components = $controls.nextAll('.interactions-component');
			$components.removeClass('elgg-state-selected');

			var $traitComponent = $components.filter(interactions.buildSelector('.interactions-component', {
				'data-trait': trait
			}));

			if ($traitComponent.length) {
				$traitComponent.addClass('elgg-state-selected');
				$traitComponent.children('.interactions-form').show().find('.elgg-input-comment').focus().trigger('click');
			} else {
				$traitComponent = $('<div></div>').addClass('interactions-component elgg-state-selected elgg-ajax-loader').data('trait', trait).attr('data-trait', trait);
				$controls.after($traitComponent);
				elgg.ajax($elem.attr('href'), {
					success: function (data) {
						$traitComponent.removeClass('elgg-ajax-loader').html(data);
						$traitComponent.find('.elgg-list').trigger('refresh');
						$traitComponent.children('.interactions-form').show().find('.elgg-input-comment').focus().trigger('click');
					}
				});
			}
		},
		listChanged: function (e, params) {
			if (params && params.guid && params.trait) {
				interactions.updateBadge(params.guid, params.trait, params.count || 0);
			}
		}
	};

	interactions.init();
	
	return interactions;
});