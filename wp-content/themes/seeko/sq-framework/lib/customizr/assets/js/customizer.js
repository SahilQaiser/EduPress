'use strict';

(function ($, wp) {

  var api = wp.customize;

  /**
   * Seeko main object.
   */
  var Seeko = api.Seeko || {};

  api.Seeko = Seeko;

  var Components = {

    /**
     * Image control component.
     */
    'seeko-image': {
      /**
       * Initialize behaviors.
       *
       * @since 1.0.0
       *
       * @returns {void}
       */
      ready: function ready() {
        var controls = this.container.find('.seeko-image-upload-control');

        _.each(controls, function (control) {
          var container = $(control),
              field = container.find('input[type=hidden]'),
              previewer = container.find('.seeko-image-upload-control-preview'),
              remover = container.find('.seeko-image-upload-control-remove'),
              frame = void 0;

          container.on('click', function () {
            if (frame) {
              frame.open();
              return;
            }

            frame = wp.media({
              title: 'Insert Media',
              multiple: false
            });

            frame.on('select', function () {
              var attachment = frame.state().get('selection').first().toJSON();

              container.addClass('has-image');
              previewer.prop('src', attachment.url);
              field.val(attachment.url);
              field.trigger('change');
            });

            frame.open();
          });

          remover.on('click', function (e) {
            e.stopPropagation();
            container.removeClass('has-image');
            previewer.prop('src', '');
            field.val('');
            field.trigger('change');
          });
        });
      }
    },

    /**
     * Multicheck control component.
     */
    'seeko-multicheck': {
      /**
       * Initialize behaviors.
       *
       * @since 1.0.0
       *
       * @returns {void}
       */
      ready: function ready() {
        var controls = this.container.find('.seeko-multicheck-control');

        _.each(controls, function (control) {
          var container = $(control),
              inputElements = container.find('input[type=checkbox]'),
              hiddenField = container.find('input[type=hidden]');

          container.find('input[type=checkbox]').on('change', function () {
            var value = [];

            // Create array values.
            inputElements.filter(':checked').each(function (i, field) {
              value[i] = $(field).val();
            });

            // Store to hidden data container.
            hiddenField.val(value).trigger('change');
          });
        });
      },
      /**
       * Filter the data before save.
       *
       * @since 1.0.0
       *
       * @returns {void}
       */
      filterData: function filterData(value) {
        if (!_.isString(value)) {
          return value;
        }

        return value.split(',');
      }
    },

    /**
     * Choose control component.
     */
    'seeko-choose': {
      /**
       * Initialize behaviors.
       *
       * @since 1.0.0
       *
       * @returns {void}
       */
      ready: function ready() {
        if (!this.params.multiple) {
          return;
        }

        var controls = this.container.find('.seeko-choose-control');

        _.each(controls, function (control) {
          var container = $(control),
              inputElements = container.find('input[type=checkbox]'),
              hiddenField = container.find('input[type=hidden]');

          container.find('input[type=checkbox]').on('change', function () {
            var value = [];

            // Create array values.
            inputElements.filter(':checked').each(function (i, field) {
              value[i] = $(field).val();
            });

            // Store to hidden data container.
            hiddenField.val(value).trigger('change');
          });
        });
      },
      /**
       * Filter the data before save.
       *
       * @since 1.0.0
       *
       * @returns {void}
       */
      filterData: function filterData(value) {
        return this.params.multiple && _.isString(value) ? value.split(',') : value;
      }
    },

    /**
     * Input control component.
     */
    'seeko-input': {
      /**
       * Initialize behaviors.
       *
       * @since 1.0.0
       *
       * @returns {void}
       */
      ready: function ready() {
        var controls = this.container.find('.seeko-input-control');

        _.each(controls, function (control) {
          var container = $(control),
              input = container.find('input.seeko-input-control-input'),
              hidden = container.find('input[type=hidden]');

          input.on('keyup blur change', function () {
            hidden.trigger('change');
          });
        });
      }
    },
  };

  Seeko.components = Components;

  /**
   * Get control components.
   *
   * @since 1.0.0
   *
   * @param {string} component
   * @returns {mixed}
   */
  var getControlComponents = function getControlComponents(component) {
    if (_.isUndefined(Components[component])) {
      return;
    }

    return Components[component];
  };


  /**
   * Class for control's base.
   *
   * @memberOf wp.customize
   * @alias wp.customize.Seeko.Control
   *
   * @constructor
   */
  Seeko.Control = api.Control.extend({
    defaultActiveArguments: { duration: 0, completeCallback: $.noop },

    /**
     * Initialize.
     *
     * @since 1.0.0
     *
     * @param {string} id      Unique identifier for the control instance.
     * @param {object} options Options hash for the control instance.
     * @returns {void}
     */
    initialize: function initialize(id, options) {
      var control = this;

      api.Control.prototype.initialize.call(control, id, options);

      // After the control is embedded on the page, invoke this method.
      control.deferred.embedded.done(function () {
        control.responsiveEvents();
        control.previewRedirectionEvents();
        control.unitsEvents();
        control.unitInputValidate();
        control.stepizeInputs();
        control.actuallyReady();
      });
    },

    /**
     * Embed the control into the container document.
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    embed: function embed() {
      var control = this,
          sectionId = control.section();

      if (!sectionId) {
        return;
      }

      wp.customize.section(sectionId, function (section) {
        section.expanded.bind(function (expanded) {
          if (expanded) {
            control.actuallyEmbed();
          }
        });
      });
    },

    /**
     * Actually embed to delay control render.
     *
     * This function is called in Section.onChangeExpanded() so the control
     * will only get embedded when the Section is expanded.
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    actuallyEmbed: function actuallyEmbed() {
      if ('resolved' === this.deferred.embedded.state()) {
        return;
      }

      this.renderContent();
      this.deferred.embedded.resolve();
    },

    /**
     * Link elements between settings and inputs.
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    linkElements: function linkElements() {
      var control = this,
          nodes = void 0,
          radios = {},
          element = void 0;

      nodes = control.container.find('[data-customize-setting-link], [data-setting-property-link]');

      nodes.each(function () {
        var node = $(this),
            property = void 0,
            viewport = void 0,
            name = void 0;

        if (node.data('customizeSettingLinked')) {
          return;
        }

        node.data('customizeSettingLinked', true);

        if (node.is(':radio')) {
          name = node.prop('name');

          if (radios[name]) {
            return;
          }

          radios[name] = true;
          node = nodes.filter('[name="' + name + '"]');
        }

        property = node.data('settingPropertyLink');

        if (property) {
          element = new api.Element(node);
          element.bind(function (to, from) {
            return control.savePropertyValue(to, from, node);
          });
          control.elements[property] = [];
          control.elements[property].push(element);
          return;
        }

        element = new api.Element(node);
        element.bind(function (to, from) {
          return control.saveValue(to, from, node);
        });
        control.elements.push(element);
      });
    },

    /**
     * Attach responsive events.
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    responsiveEvents: function responsiveEvents() {
      var control = this;

      control.container.find('.seeko-responsive-switcher a').on('click', function (event) {
        api.previewedDevice.set($(event.currentTarget).data('device'));
      });
    },

    /**
     * Events to trigger preview redirection.
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    previewRedirectionEvents: function previewRedirectionEvents() {
      var control = this;
      control.container.find('.seeko-renew-preview').on('change', function (e) {
        control.redirectOptionPreview(control.params.section, e.target.getAttribute('data-customize-setting-link'), e.target.value);
      });
    },

    /**
     * Change preview URL to a new one based on option.
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    redirectOptionPreview: function redirectOptionPreview(sectionId, optionId, optionValue) {
      var url = new URL(api.previewer.previewUrl.get());
      url.searchParams.set('seeko', sectionId); // Need this for checks in back-end.
      url.searchParams.set(optionId, optionValue);
      api.previewer.previewUrl.set(url.toString());
    },

    /**
     * Attach unit selector events.
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    unitsEvents: function unitsEvents() {
      var controls = this.container.find('.seeko-control-units-container');

      $(document.body).on('click', function (e) {
        if (!e.target.closest('.seeko-control-unit-selector')) {
          controls.find('.seeko-control-unit-selector').removeClass('open');
        }
      });

      _.each(controls, function (control) {
        var container = $(control),
            field = container.find('input[type=hidden]'),
            unitSelector = container.find('.seeko-control-unit-selector'),
            selectedUnit = unitSelector.find('.selected-unit');

        if (selectedUnit[0].classList.contains('disabled')) {
          return;
        }

        unitSelector.on('click', 'li', function (e) {
          unitSelector.toggleClass('open');

          if (e.target.classList.contains('selected-unit')) {
            return;
          }

          var unit = e.target.innerText.toLowerCase();
          selectedUnit.text(unit);
          field.val(unit).trigger('change');
        });
      });
    },

    /**
     * Change unit to none if input is not a numeric value.
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    unitInputValidate: function unitInputValidate() {
      var controls = this.container.find('.seeko-input-control:not(.seeko-text-control)');

      _.each(controls, function (control) {
        var container = $(control),
            field = container.find('input[type=text]'),
            unitsContainer = container.find('.seeko-control-units-container'),
            selectedUnit = unitsContainer.find('.selected-unit'),
            initialUnit = selectedUnit.text();

        field.on('keyup focus blur', function () {
          var val = field.val();
          if (!isNaN(parseFloat(val)) && isFinite(val) || _.isEmpty(val)) {
            if ('-' === selectedUnit.text()) {
              selectedUnit.text(initialUnit);
            }
            return;
          }
          selectedUnit.text('-');
        });
      });
    },

    /**
     * Add Stepper to inputs
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    stepizeInputs: function stepizeInputs() {
      var controls = [];
      controls.push(this.container.find('input.seeko-input-control-input'));
      controls.push(this.container.filter('.customize-control-seeko-box-model').find('input.seeko-box-model-control-input'));

      _.each(controls, function (control) {
        control = $(control), control.stepper({
          decimals: 1,
          min: 0,
          max: 1000,
          step: 0.1
        });
      });
    },

    /**
     * Save value generically.
     *
     * @since 1.0.0
     *
     * @param {mixed} to    New value of the control.
     * @param {mixed} from  Old value of the control.
     * @param {object} node Element that holds the value.
     * @returns {void}
     */
    saveValue: function saveValue(to, from, node) {
      var control = this,
          viewport = void 0,
          value = void 0,
          setting = void 0;

      if (to === from) {
        return;
      }

      if (!_.isUndefined(control.filterData)) {
        to = control.filterData(to);
      }

      viewport = node.data('settingViewportLink');
      value = to;

      if (viewport) {
        setting = control.setting.get();
        value = _.extend({}, setting);
        value[viewport] = to;
        control.setting.set(value);
        return;
      }

      control.setting.set(value);
    },

    /**
     * Save value as property from object.
     *
     * @since 1.0.0
     *
     * @param {mixed} to    New value of the control.
     * @param {mixed} from  Old value of the control.
     * @param {object} node Element that holds the value.
     * @returns {void}
     */
    savePropertyValue: function savePropertyValue(to, from, node) {
      var control = this,
          viewport = void 0,
          property = void 0,
          setting = void 0,
          value = void 0;

      if (to === from) {
        return;
      }

      if (!_.isUndefined(control.filterData)) {
        to = control.filterData(to);
      }

      viewport = node.data('settingViewportLink');
      property = node.data('settingPropertyLink');
      setting = control.setting.get();
      value = _.extend({}, setting);

      if (viewport) {
        value[viewport] = _.extend({}, setting[viewport]);
        value[viewport][property] = to;
        control.setting.set(value);
        return;
      }

      value[property] = to;
      control.setting.set(value);
    },

    /**
     * Additional behaviors.
     *
     * Runs after the controls is embedded.
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    actuallyReady: function actuallyReady() {}
  });

  Seeko.TemplateControl = Seeko.Control.extend({
    /**
     * Add control template.
     *
     * The field param must have property `id` and `property` to work properly.
     *
     * @since 1.0.0
     *
     * @param {object} field     Field control arguments.
     * @param {jquery} container Container of the control where to append.
     * @returns {void}
     */
    addControl: function addControl(field, container) {
      var control = this,
          templateId = void 0,
          template = void 0,
          classes = void 0,
          content = void 0,
          component = void 0;

      // Format the template name.
      templateId = 'customize-control-' + field.type + '-content';

      // Nothing to do since template is not found.
      if (!document.getElementById('tmpl-' + templateId)) {
        return;
      }

      // Set control container.
      container = container || control.container.find('.seeko-group-controls');

      // Create field defaults args.
      field = _.defaults(field, {
        column: 12,
        cssClass: ''
      });

      // Create data link.
      field.link = 'data-customize-setting-link="' + field.id + '" ' + (field.link ? field.link : '');

      // Create string of input attributes.
      if (_.isObject(field.inputAttrs)) {
        field.inputAttrs = _.map(field.inputAttrs, function (value, attr) {
          return attr + '="' + value + '"';
        }).join(' ');
      }

      // Create string of control attributes.
      if (_.isObject(field.controlAttrs)) {
        field.controlAttrs = _.map(field.controlAttrs, function (value, attr) {
          return attr + '="' + value + '"';
        }).join(' ');
      }

      // Control classes.
      classes = _.compact(['seeko-col-' + field.column, 'customize-control customize-control-' + field.type, field.cssClass, field.property ? control.params.type + '-control-' + field.property.replace(/_/g, '-') : '', field.responsive ? 'customize-control-responsive' : '']);

      // Create template wrapper.
      content = $('<li></li>', {
        class: classes.join(' ')
      });

      // Append template to control container.
      template = wp.template(templateId);
      content.append(template(field));
      container.append(content);

      // Link elements by content.
      control.linkElements(field, content);

      // Create control events.
      component = Components[field.type];
      if (component && component.ready) {
        component.ready.call({ container: content, params: field });
      }
    },

    /**
     * Link elements between settings and inputs.
     *
     * @since 1.0.0
     *
     * @param {object} params  Field parameters.
     * @param {jquery} content Holds the control elements.
     * @returns {void}
     */
    linkElements: function linkElements(params, content) {
      if (_.isEmpty(params) || _.isEmpty(content)) {
        return;
      }

      var control = this,
          nodes = void 0,
          radios = {},
          element = void 0,
          property = void 0;

      nodes = content.find('[data-customize-setting-link], [data-setting-property-link]');
      property = params.property;
      control.elements[property] = [];

      nodes.each(function () {
        var node = $(this),
            name = void 0;

        if (node.data('customizeSettingLinked')) {
          return;
        }

        node.data('customizeSettingLinked', true);

        if (node.is(':radio')) {
          name = node.prop('name');

          if (radios[name]) {
            return;
          }

          radios[name] = true;
          node = nodes.filter('[name="' + name + '"]');
        }

        element = new api.Element(node);
        control.elements[property].push(element);
        element.bind(function (to, from) {
          return control.saveValue(to, from, property, node, params);
        });
      });
    },

    /**
     * Save value behavior.
     *
     * @since 1.0.0
     *
     * @param {mixed}  to       New value of the control.
     * @param {mixed}  from     Old value of the control.
     * @param {string} property Base property name.
     * @param {object} node     Element that holds the value.
     * @param {object} params   Field params.
     * @returns {void}
     */
    saveValue: function saveValue(to, from, property, node, params) {
      var control = this,
          component = Components[params.type],
          trail = property,
          responsive = control.params.responsive,
          propertyLink = void 0,
          viewportLink = void 0,
          value = void 0;

      /**
       * Recursively search keys and apply value at the last key name.
       *
       * @param {string} path  String keys path format.
       * @param {mixed}  value New value.
       * @param {object} ref   Object reference from existing value.
       */
      var getObj = function getObj(path, value, ref) {
        var keys = path.split('.');
        ref = _.extend({}, ref);

        if (keys.length === 1) {
          ref[keys[0]] = value;
          return ref;
        } else {
          var current = keys.shift();
          ref[current] = getObj(keys.join('.'), value, ref[current]);
          return ref;
        }
      };

      // Call component data filter before save.
      if (component && component.filterData) {
        to = component.filterData.call({ params: params }, to);
      }

      propertyLink = node.data('settingPropertyLink');

      if (propertyLink) {
        trail = trail + '.' + propertyLink;
      }

      viewportLink = node.data('settingViewportLink');

      // If control is responsive and viewport is empty then set it to desktop.
      viewportLink = responsive && !viewportLink ? 'desktop' : viewportLink;

      if (viewportLink) {
        trail = viewportLink + '.' + trail;
      }

      value = _.extend({}, control.setting.get());
      value = getObj(trail, to, value);
      control.setting.set(value);
    }
  });

  /**
   * Class for Group control.
   *
   * @memberOf wp.customize
   * @alias wp.customize.Seeko.GroupControl
   *
   * @constructor
   */
  Seeko.GroupControl = Seeko.TemplateControl.extend({
    /**
     * Initialize.
     *
     * @since 1.0.0
     *
     * @param {string} id      Unique identifier for the control instance.
     * @param {object} options Options hash for the control instance.
     * @returns {void}
     */
    initialize: function initialize(id, options) {
      Seeko.Control.prototype.initialize.call(this, id, options);
    },

    /**
     * Initialize behaviors.
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    ready: function ready() {
      var control = this,
          params = control.params;

      // Append control.
      _.each(params.fields, function (field) {
        control.addControl(field);
      });
    }
  });

  /**
   * Class for Image control.
   *
   * @memberOf wp.customize
   * @alias wp.customize.Seeko.ImageControl
   *
   * @constructor
   */
  Seeko.ImageControl = Seeko.Control.extend(getControlComponents('seeko-image'));

  /**
   * Class for Multicheck control.
   *
   * @memberOf wp.customize
   * @alias wp.customize.Seeko.MulticheckControl
   *
   * @constructor
   */
  Seeko.MulticheckControl = Seeko.Control.extend(getControlComponents('seeko-multicheck'));

  /**
   * Class for Choose control.
   *
   * @memberOf wp.customize
   * @alias wp.customize.Seeko.ChooseControl
   *
   * @constructor
   */
  Seeko.ChooseControl = Seeko.Control.extend(getControlComponents('seeko-choose'));

  /**
   * Class for Input control.
   *
   * @memberOf wp.customize
   * @alias wp.customize.Seeko.InputControl
   *
   * @constructor
   */
  Seeko.InputControl = Seeko.Control.extend(getControlComponents('seeko-input'));


  /**
   * Class for Background control.
   *
   * @memberOf wp.customize
   * @alias wp.customize.Seeko.BackgroundGroupControl
   *
   * @constructor
   */
  Seeko.BackgroundGroupControl = Seeko.GroupControl.extend({
    /**
     * Additional behaviors.
     *
     * Runs after the controls is embedded.
     *
     * @since 1.0.0
     *
     * @returns {void}
     */
    actuallyReady: function actuallyReady() {
      var control = this,
          setting = control.setting.get(),
          classicFields = control.container.find('.for-classic'),
          gradientFields = control.container.find('.for-gradient'),
          videoFields = control.container.find('.for-video'),
          initialType = !_.isUndefined(setting.type) ? setting.type : 'classic',
          toggleFields = void 0;

      if (control.params.responsive) {
        initialType = !_.isUndefined(setting.desktop) && !_.isUndefined(setting.desktop.type) ? setting.desktop.type : 'classic';
      }

      toggleFields = function toggleFields(type) {
        if (type) {
          classicFields.toggleClass('hidden', type !== 'classic' ? true : false);
          gradientFields.toggleClass('hidden', type !== 'gradient' ? true : false);
          videoFields.toggleClass('hidden', type !== 'video' ? true : false);
        }
      };

      _.each(control.elements.type, function (element) {
        element.bind(toggleFields);
      });

      toggleFields(initialType);
    }
  });


  // Add new controls to wp.customize.controlConstructor
  $.extend(api.controlConstructor, {
    'seeko-text': Seeko.Control,
    'seeko-input': Seeko.InputControl,
    'seeko-textarea': Seeko.Control,
    'seeko-select': Seeko.Control,
    'seeko-toggle': Seeko.Control,
    'seeko-choose': Seeko.ChooseControl,
    'seeko-divider': Seeko.Control,
    'seeko-position': Seeko.Control,
    'seeko-radio-image': Seeko.Control,
    'seeko-multicheck': Seeko.MulticheckControl,
    'seeko-image': Seeko.ImageControl,
    'seeko-box-model': Seeko.Control,
  });

  // Add new group controls to wp.customize.controlConstructor
  $.extend(api.controlConstructor, {
    'seeko-box-shadow': Seeko.GroupControl,
    'seeko-typography': Seeko.GroupControl,
    'seeko-border': Seeko.GroupControl,
    'seeko-background': Seeko.BackgroundGroupControl
  });

})(jQuery, wp);
