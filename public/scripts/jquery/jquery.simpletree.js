$.widget('ui.simpleTree', {
    // Default options
    'options': {
	'url': '',
	'parent_id': 1,
	'parent_label': '(Root)',
	'onLoad': null,
	'onSelect': null, // function(last_id, selected_id)
	'icon_open': 'ui-icon-triangle-1-s',
	'icon_close': 'ui-icon-triangle-1-e',
	'icon_empty': 'ui-icon-folder-collapsed'
    },

    '_create': function() {
	    var widgetObject = this;
	    var containerElement = jQuery(this.element);
	    var config = this.options;

	    containerElement.addClass('ui-widget');
	    containerElement.addClass('ui-corner-all');
	    containerElement.addClass('ui-helper-reset');

	    this.load(null);
    },

    load: function (id) {

	var widgetObject = this;
	var containerElement = jQuery(this.element);
	var config = this.options;
	var url = config.url;


	if (!id) {id = config.parent_id;}

	$.getJSON(
	    url,
	    {
		parent_id: id
	    },
	    function(reply){
		
		if (reply) {
		    // Call the specified function...
		    if (config.onLoad) {
			config.onLoad(id, reply);
		    } else {
			widgetObject._render(id, reply);
		    }
		} else {
			throw('No data returned.');
		}
	    }
	);
    },
    selected: function(id) {
	var containerElement = jQuery(this.element);
	if (id) {
	    var last_id = this.options.parent_id;
	    var text = '';
	    // Setter
	    containerElement.find('li.ui-state-focus').removeClass('ui-state-focus');

	    rootElement = containerElement.find('.node-child-' + id);
	    if (rootElement.hasClass('node-child')) {
		rootElement.addClass('ui-state-focus');
		text = rootElement.find('.node-title-text-' + id).html();
	    }
	    this.options.parent_id = id;


	    if (this.options.onSelect) this.options.onSelect(last_id, id, text);
	    return true;
	} else {
	    // Getter
	    return this.options.parent_id;
	}
    }
    ,
    _render: function (id, reply) {
	    
	    var widgetObject = this;
	    var containerElement = jQuery(this.element);
	    var config = this.options;

	    var rootElement = containerElement.find('.node-child-' + id);
	    
	    if (!rootElement.hasClass('node-child')) {
		rootElement = containerElement;
	    }

	    if (reply.result == 'ok') {

		if (reply.data) {
		    
		    if (rootElement.hasClass('node-child')) {
			var node_id = rootElement.attr('title');
			var icon = rootElement.find('.node-title-' + node_id + ' .ui-icon');
			
			if (icon.hasClass(config.icon_close)) icon.removeClass(config.icon_close);
			if (icon.hasClass(config.icon_empty)) icon.removeClass(config.icon_empty);

			if (!icon.hasClass(config.icon_open)) {
			    icon.addClass(config.icon_open);
			}
		    }
		    rootElement.find('.node-children-' + id).html();
		    rootElement.append(
			'<ul class="node-children node-children-' + id + '"></ul>'
		    );

		    var rootNode = rootElement.find('.node-children-' + id);

		    jQuery.each(reply.data, function(i, row){
			var sub_node_id = row.article_container_id;

			rootNode.append(
			    '<li class="ui-helper-reset node-child node-child-' + sub_node_id + ' ui-helper-clearfix ui-state-default" style="padding-left: 1em;" title="' + sub_node_id + '">' +
				'<div class="node-title node-title-' + sub_node_id + '">' +
				    '<span class="ui-icon ' + config.icon_close + '" style="float: left; margin-right: 0.3em;"></span>' +
				    '<span class="node-title-text node-title-text-' + sub_node_id + '">' + row.title + '</span>' +
				'</div>' +
			    '</li>'
			);
		    });

		    rootElement.find('.node-child').mouseover(function(ev){jQuery(this).addClass('ui-state-highlight'); return false;});
		    rootElement.find('.node-child').mouseout(function(ev){jQuery(this).removeClass('ui-state-highlight'); return false;});

		    rootElement.find('.node-child').click(function(ev){
			var node_id = jQuery(this).attr('title');
			var nodeChildren = jQuery(this).find('.node-children-' + node_id);

			ev.preventDefault();

			widgetObject.selected(node_id);

			if (nodeChildren != null && nodeChildren.is(':visible')) {
			    // visible
			    if (jQuery(this).hasClass('node-child')) {
				var node_id = jQuery(this).attr('title');
				var icon = jQuery(this).find('.node-title-' + node_id + ' .ui-icon');

				if (icon.hasClass(config.icon_open)) icon.removeClass(config.icon_open);
				if (icon.hasClass(config.icon_empty)) icon.removeClass(config.icon_empty);

				if (!icon.hasClass(config.icon_clos)) {
				    icon.addClass(config.icon_close);
				}
			    }
			    nodeChildren.slideUp();
			} else {
			    // hidden
			    var len = nodeChildren.length;
			    
			    if (jQuery(this).hasClass('node-child')) {
				var node_id = jQuery(this).attr('title');
				var icon = jQuery(this).find('.node-title-' + node_id + ' .ui-icon');

				if (icon.hasClass(config.icon_close)) icon.removeClass(config.icon_close);
				if (icon.hasClass(config.icon_empty)) icon.removeClass(config.icon_empty);

				if (!icon.hasClass(config.icon_open)) {
				    icon.addClass(config.icon_open);
				}
			    }
			    if (nodeChildren == null || len == 0) {
				nodeChildren.slideUp().remove();
				widgetObject.load(node_id);
			    } else {
				nodeChildren.slideDown();
			    }
			}
			return false;
		    });
		} else {
		    
		    if (rootElement.hasClass('node-child')) {
			var node_id = rootElement.attr('title');
			var icon = rootElement.find('.node-title-' + node_id + ' .ui-icon');
			
			if (icon.hasClass(config.icon_close)) icon.removeClass(config.icon_close);
			if (icon.hasClass(config.icon_open)) icon.removeClass(config.icon_open);

			if (!icon.hasClass(config.icon_empty)) {
			    icon.addClass(config.icon_empty);
			}
		    }
		}
	    } else {
		throw('Invalid response.');
	    }
    }
});