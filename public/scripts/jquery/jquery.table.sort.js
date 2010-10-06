(function($){
	$.fn.tableSort = function(settings) {
		var config = {
			'pageIndex'			: 1, // What page to request
			'rowCount'			: 30, // How may results per page
			'sort'				: null, // Sort by this column
			'order'				: 'ASC', // Order ASC or DESC
			'tableDataTag'		: '.table-data',
			'tableBodyTag'		: '.table-body',
			'tableSortOrderTag'	: '.table-sort-order',
			'tableRowTag'		: '.table-row',
			'tableHeaderTag'	: '.table-header',
			'allowRebind'		: true, // Should we allow table rows to be re-binded to event handlers after updating them?
			'onJsonSuccess'		: null,
			'onHeaderLinkClick'	: null,
			'onSubmit'			: 
							function(ev){
								// Dont allow the form to be submitted, by default.
								ev.preventDefault();
							},
			'onTableRowClick'	: 
							function(ev){
								// Do nothing by default
							},
			'onTableBodyClick'	: 
							function(ev){
								// Collapse/Expand sub rows by default
								$(this).parent().find('.table-sub-row').toggle();
							}
		};
		
		if (settings) $.extend(config, settings);
		
		this.each(function(index, obj) {
			var formElement = $(obj);
			var orderElement = formElement.find(config.tableSortOrderTag);
			var url = new String(formElement.attr('action'));
			var tableElement = formElement.find(config.tableDataTag);
			var headerElement = tableElement.find(config.tableHeaderTag);
			var rowElements = tableElement.find(config.tableRowTag);
			
			var headerLinks = headerElement.find('a');
			
			formElement.submit(config.onSubmit);
			
			rowElements.each(function(){
				var row = $(this);
				
				if ('.' + row.parent().attr('class') == config.tableBodyTag) {
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
						var thisLink = $(this);
						thisLink.addClass('loading');
						
						$.getJSON(
							url,
							{
								sort: columnName,
								order: orderElement.val(),
								pageIndex: config.pageIndex,
								rowCount: config.rowCount
							},
							function(response){
								thisLink.removeClass('loading');
								if (response) {
									// Update the sort order...
									if (orderElement.val() == 'ASC') {
										orderElement.val('DESC');
									} else {
										orderElement.val('ASC');
									}
									
									// Call the specified function...
									if (config.onJsonSuccess) config.onJsonSuccess(response);
									
									if (config.allowRebind == true) {
										// Re-bind the row elements...
										rowElements = tableElement.find(config.tableRowTag);
										rowElements.each(function(){
											var row = $(this);
											
											if ('.' + row.parent().attr('class') == config.tableBodyTag) {
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
					});
				}
			});
		});
		
		return this;
   };
})(jQuery);