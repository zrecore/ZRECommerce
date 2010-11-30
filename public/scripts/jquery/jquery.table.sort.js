$.widget('ui.tableSort', {
	// Default options
	'options': {
		'pageIndex'				: 1, // What page to request
		'rowCount'				: 30, // How may results per page
		'sort'					: null, // Sort by this column
		'order'					: 'ASC', // Order ASC or DESC
		'orderAscText'			: 'Asc.',
		'orderDescText'			: 'Desc.',
		'pageCount'				: 1,				
		'tableDataClass'		: '.table-data',
		'tableBodyClass'		: '.table-body',
		'tableSortOrderClass'	: '.table-sort-order',
		'tableRowClass'			: '.table-row',
		'tableHeaderClass'		: '.table-header',
		'paginatorClass' 		: '.paginator',
		'paginatorLinkClass'	: '.paginator-link',
		'paginatorThisLinkClass': '.paginator-this-link',
		'allowRebind'			: true, // Should we allow table rows to be re-binded to event handlers after updating them?
		'onJsonSuccess'			: null,
		'onHeaderLinkClick'		: null,
		'onSubmit'				: 
						function(ev){
							// Dont allow the form to be submitted, by default.
							ev.preventDefault();
						},
		'onTableRowClick'		: 
						function(ev){
							// Do nothing by default
						},
		'onTableBodyClick'		: 
						function(ev){
							// Collapse/Expand sub rows by default
							$(this).parent().find('.table-sub-row').toggle();
						}
	},
	
	'_create': function() {
		var widgetObject = this;
		var formElement = $(this.element);
		var config = this.options;
		var orderElement = formElement.find(config.tableSortOrderClass);
		var tableElement = formElement.find(config.tableDataClass);
		var headerElement = tableElement.find(config.tableHeaderClass);
		var rowElements = tableElement.find(config.tableRowClass);
		
		var headerLinks = headerElement.find('a');
		
		formElement.prepend('<div class="' + config.paginatorClass.replace('.', '') + '"></div>');
		
		// Set element values.
		if (orderElement.val() != config.order) orderElement.val(config.order);
		
		// Set the event handlers.
		formElement.submit(config.onSubmit);
		
		rowElements.each(function(){
			var row = $(this);
			
			if ('.' + row.parent().attr('class') == config.tableBodyClass) {
				row.click(config.onTableBodyClick);
			} else {
				row.click(config.onTableRowClick);
			}
		});
		
		headerLinks.each(function(i, obj){
			var headerLink = $(obj);
			var columnName = headerLink.attr('href').replace('#', '');
			
			if (config.onHeaderLinkClick) {
				headerLink.click(config.onHeaderLinkClick);
			} else {
				headerLink.click(function(ev){
					ev.preventDefault();
					widgetObject.load(columnName);
				});
			}
		});
		
		this._renderSort();
		this.load(null);
	},
	
	load: function (columnName) {
		
		var widgetObject = this;
		var formElement = $(this.element);
		var config = this.options;
		var url = new String(formElement.attr('action'));
		var tableElement = formElement.find(config.tableDataClass);
		var headerElement = tableElement.find(config.tableHeaderClass);
		var rowElements = tableElement.find(config.tableRowClass);
		var headerLink = null;
		
		
		if (!columnName) {
			headerLink = headerElement.find('a').first();
			columnName = headerLink.attr('href').replace('#', '');
		} else {
			headerLink = headerElement.find('a[href=#' + columnName + ']');
		}
		
		if (this.options.sort != columnName) this.options.sort = columnName;
		
		headerLink.addClass('loading');
		$.getJSON(
				url,
				{
					sort: columnName,
					order: config.order,
					pageIndex: config.pageIndex,
					rowCount: config.rowCount
				},
				function(response){
					headerLink.removeClass('loading');
					
					if (response) {
						// Call the specified function...
						if (config.onJsonSuccess) {
							// Render our paginator elements
							widgetObject.renderPaginator(response);
							
							config.onJsonSuccess(response);
						}
						
						if (config.allowRebind == true) {
							// Re-bind the row elements...
							rowElements = tableElement.find(config.tableRowClass);
							rowElements.each(function(){
								var row = $(this);
								
								if ('.' + row.parent().attr('class') == config.tableBodyClass) {
									row.click(config.onTableBodyClick);
								} else {
									row.click(config.onTableRowClick);
								}
							});
						}
					} else {
						throw('No data returned.');
					}
				}
			);
	},
	renderPaginator: function(response) {
		var widgetObject = this;
		var formElement = $(this.element);
		var config = this.options;
		
		var totalCount = response.totalRows;
		var totalPages = Math.ceil(totalCount / 5);

		var currentPage = config.pageIndex;

		var paginator = formElement.find(config.paginatorClass);
		
		// Generate the pagination links.
		paginator.html('');

		if (totalPages > 1) {
		    for(i=1; i <= totalPages; i++) {
			    if (i == currentPage) {
				    paginator.append('<span class="' + config.paginatorThisLinkClass.replace('.', '') + '">' + i + '</span>');
			    } else {
				    paginator.append('<a class="' + config.paginatorLinkClass.replace('.', '') + '" href="#' + i + '">' + i + '</a>');
			    }
		    }

		    paginator.find('a').each(function(i, lnk){
			    $(lnk).click(function (ev){
				    var pageIndex = $(this).attr('href').replace('#', '');
				    var currentColumn = config.sort;

				    widgetObject.options.pageIndex = pageIndex;

				    widgetObject.load(currentColumn ? currentColumn : null);

				    ev.preventDefault();
			    });
		    });
		}
	
	},
	_renderSort: function () {
		var widgetObject = this;
		var formElement = $(this.element);
		var config = this.options;
		var tableElement = formElement.find(config.tableDataClass);
		var headerElement = tableElement.find(config.tableHeaderClass);
		
		headerElement.find('th').last().append(
			'<select class="' + config.tableSortOrderClass.replace('.', '') + '">' +
				'<option value="ASC">' + config.orderAscText + '</option>' + 
				'<option value="DESC">' + config.orderDescText + '</option>' +
			'</select>'
		);
		
		headerElement.find(config.tableSortOrderClass).change(function(ev){
			widgetObject.options.order = $(this).val();
			formElement.tableSort('load', config.sort);
		});
	}
});