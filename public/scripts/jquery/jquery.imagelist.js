$.widget('ui.imageList', {
    // Default options
    'options': {
	'url': '',
	'ajaxOptions': null,
	'selected_id': null,
	'image_dir': '/images/',
	'onSelect': null,
	'onRender': null,
	'onItemClick': null,
	'left': 0,
	'top': 0
    },

    '_create': function() {
	    var widgetObject = this;
	    var containerElement = jQuery(this.element);
	    var config = this.options;

	    containerElement.addClass('ui-widget');
	    containerElement.addClass('ui-corner-all');
	    containerElement.addClass('ui-helper-reset');
	    containerElement.addClass('ui-image-list');
	    containerElement.hide();

	    this.load(null);
    },

    load: function () {

	var widgetObject = this;
	var containerElement = jQuery(this.element);
	var config = this.options;
	var url = config.url;

	$.getJSON(
	    url,
	    config.ajaxOptions,
	    function(reply){

		if (reply) {
		    // Call the specified function...
		    if (config.onLoad) {
			config.onLoad(reply);
		    } else {
			widgetObject._render(reply);
		    }
		} else {
			throw('No data returned.');
		}
	    }
	);
    },
    show: function() {
	
	var containerElement = jQuery(this.element);
	var config = this.options;
	var pos = containerElement.position();
	var offset = containerElement.offset();
	containerElement.height(1).show();
	containerElement.position({left: pos.left, top: pos.top + containerElement.height()});
	containerElement.slideDown('fast');
    },
    hide: function() {
	var containerElement = jQuery(this.element);
	containerElement.slideUp('fast');
    },
    selected: function(id) {
	var containerElement = jQuery(this.element);
	if (id) {
	    var last_id = this.options.selected_id;

	    // Setter
	    this.options.selected_id = id;

	    // @todo Select the specified element.
	    if (this.options.onSelect) this.options.onSelect(last_id, id);
	    return true;
	} else {
	    // Getter
	    return this.options.selected_id;
	}
    }
    ,
    _render: function (reply) {

	    var widgetObject = this;
	    var containerElement = jQuery(this.element);
	    var config = this.options;

	    if (reply.result == 'ok') {
		
		containerElement.position({left: config.left, top: config.top});

		if (reply.data) {

		    if (config.onRender) {
			config.onRender(widgetObject, reply.data);
		    } else {
			containerElement.html('<ul class="ui-image-list-container ui-helper-reset ui-helper-clearfix"></ul>');
			var ulElement = containerElement.find('.ui-image-list-container');
			jQuery.each(reply.data, function(i, row){
			    ulElement.append(
				'<li class="ui-corner-all ui-image-list-item ui-image-list-item-' + row.image_id  + '"><img class="ui-helper-reset ui-helper-clearfix" src="' + config.image_dir + row.file + '" alt="' + row.file + '" /></li>'
			    );
			});

			if (config.onItemClick) containerElement.find('.ui-image-list-item').click(config.onItemClick);
		    }

		} else {
		    alert('The "data" parameter is empty.');
		}
	    } else {
		throw('Invalid response.');
	    }
    }
});