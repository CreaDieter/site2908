/**
 * Class to show website Advertisers
 * 
 */

// Register namespace for this class
pimcore.registerNS("backend.editlist");

// Create class
backend.editlist = Class.create({
	
	filterField: null,
    preconfiguredFilter: "",
    params: null,
	
	/**
	 * Constructor-like function
	 */
    initialize: function (params) {
    	// Make params globaly accessable
    	this.params = params;
    	
    	// Build filter
        this.getFilterField();
        
        // Build Grid & Panel
        this.getTabPanel(params);
    },
    
    getFilterField: function() {
    	 this.filterField = new Ext.form.TextField({
             xtype: "textfield",
             width: 200,
             style: "margin: 0 10px 0 0;",
             enableKeyEvents: true,
             value: this.preconfiguredFilter,
             listeners: {
                 "keydown" : function (field, key) {
                     if (key.getKey() == key.ENTER) {
                         var input = field;
                         this.store.baseParams.filter = input.getValue();
                         this.store.load();
                     }
                 }.bind(this)
             }
         });
         var filter="";
         this.preconfiguredFilter = filter;
         this.filterField.setValue(filter);
    },

    getTabPanel: function (params) {
    	// Check if panel exists
        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "editlist_" + params.id,
                title: params.title,
                iconCls: params.iconCls,
                border: false,
                layout: "fit",
                closable:true,
                items: [this.getData(params)] // function to load to get items
            });
            
            // Find element to add panel
            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.activate("editlist_" + params.id);
            
            // Add listener for onClose/destroy action
            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("editlist_" + params.id);
            }.bind(this));
            
            // Refresh layout
            pimcore.layout.refresh();
        }
        // Return this panel
        return this.panel;
    },

    getData: function(params) {    	
        var proxy = new Ext.data.HttpProxy({
            url: params.listUrl,
            method: 'post'
        });
        
        var me = this;
        var readerFields = [];
        var typesColumns = [];

        for (var i = 0; i < params.fields.length; i++) {
            readerFields.push({name: params.fields[i].source});
            
            if (params.fields[i].editor) {
            	editor = params.fields[i].editor;
            } else {
            	editor = new Ext.form.TextField({});
            }
            if (params.fields[i].renderer == 'boolean') {
            	var renderer = this.booleanRender;
            } else if (params.fields[i].renderer == 'list') {
            	var renderer = this.listRender;
            } else if (params.fields[i].renderer == 'link') {
            	var renderer = this.linkRender;
            } else {
            	var renderer =  params.fields[i].renderer;
            }
            if (params.fields[i].source == 'id') {
            	var width = 20;
            } else {
            	var width = null;
            }
            typesColumns.push({
            					header: params.fields[i].label, 
            					sortable: true,
            					editable: params.fields[i].editable, 
            					dataIndex: params.fields[i].source, 
            					editor: editor,
            					renderer: renderer,
            					url: params.fields[i].url,
            					urlText: params.fields[i].text,
            					width: width
            				});
        }
        
        if (params.deleteRow == true) {
        	typesColumns.push({
                xtype: 'actioncolumn',
                width: 30,
                items: [{
                    tooltip: t('delete'),
                    icon: "/pimcore/static/img/icon/cross.png",
                    handler: function (grid, rowIndex) {
                    	grid.getStore().removeAt(rowIndex);
                    }.bind(this)
                }]
            });
        }

        var reader = new Ext.data.JsonReader({
            totalProperty: 'total',
            successProperty: 'success',
            root: 'data',
            idProperty: 'id'
        }, readerFields);

        var writer = new Ext.data.JsonWriter();

        var itemsPerPage = 20;
        this.store = new Ext.data.Store({
            id: 'editlist_store_' + params.id,
            restful: false,
            proxy: proxy,
            reader: reader,
            writer: writer,
            remoteSort: true,
            baseParams: {
                limit: itemsPerPage,
                filter: this.preconfiguredFilter
            },            
            listeners: {
                write : function(store, action, result, response, rs) {
                }
            }
        });
        this.store.load();

        this.editor = new Ext.ux.grid.RowEditor();

        this.pagingtoolbar = new Ext.PagingToolbar({
            pageSize: itemsPerPage,
            store: this.store,
            displayInfo: true,
            displayMsg: '{0} - {1} / {2}',
            emptyMsg: ts("no_objects_found")
        });

        // add per-page selection
        this.pagingtoolbar.add("-");

        this.pagingtoolbar.add(new Ext.Toolbar.TextItem({
            text: t("items_per_page")
        }));
        this.pagingtoolbar.add(new Ext.form.ComboBox({
            store: [
                [10, "10"],
                [20, "20"],
                [40, "40"],
                [60, "60"],
                [80, "80"],
                [100, "100"]
            ],
            mode: "local",
            width: 50,
            value: 20,
            triggerAction: "all",
            listeners: {
                select: function (box, rec, index) {
                    this.pagingtoolbar.pageSize = intval(rec.data.field1);
                    this.pagingtoolbar.moveFirst();
                }.bind(this)
            }
        }));        
        
        // Build grid toolbar
        var toolbar = [];
        if (params.addRow != false) {
        	toolbar.push({
					    text: t('add'),
					    handler: this.onAdd.bind(this),
					    iconCls: "pimcore_icon_add"
        	});
        	
        	toolbar.push('-');
        }
        toolbar.push({
	                    text: t('reload'),
	                    handler: function () {
	                        this.store.reload();
	                    }.bind(this),
	                    iconCls: "pimcore_icon_reload"
	                });
        if (params.exportUrl) {
        	toolbar.push({
					    text: ts('export_xls'),
					    handler: function() {
					    	window.open(me.params.exportUrl);
					    },
					    iconCls: "pimcore_icon_export"
        	});
        	
        	toolbar.push('-');
        }
        toolbar.push("->");
        toolbar.push({
            text: t("filter") + "/" + t("search"),
            xtype: "tbtext",
            style: "margin: 0 10px 0 0;"
          },this.filterField);
        
        this.grid = new Ext.grid.GridPanel({
            frame: false,
            autoScroll: true,
            store: this.store,
            plugins: [this.editor],
            columnLines: true,
            stripeRows: true,
            columns : typesColumns,
            bbar: this.pagingtoolbar,
            sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
            tbar: toolbar,           
            viewConfig: {
                forceFit: true
            },
        });

        return this.grid;
    },
    
    onAdd: function (btn, ev) {
        var u = new this.grid.store.recordType();
        this.editor.stopEditing();
        this.grid.store.insert(0, u);
        this.editor.startEditing(0);
    },
    
    /**
     * Helper function to show a boolean nice
     */
    booleanRender: function(value, id, r) {
    	if (value == 1) {
    		return ts('Ja');
    	} else {
    		return ts('Neen');
    	}
    },
    
    /**
     * Helper function to show the value of a list from the editor store
     */
    listRender: function(value, id, r) {
    	var s = this.editor.getStore();
    	for(var i=0;i<s.data.items.length;i++) {
    		if (s.data.items[i].data.id == value) {
    			return s.data.items[i].data[this.editor.displayField];
    		}
    	}
    	return ts('Onbestaand item');
    },
    
    /**
     * Helper function to show a link in the grid
     */
    linkRender: function(value,id,r) {
    	return '<a href="'+this.url+r.data.id+'" target="_blank">' + this.urlText + '</a>';
    },
    
    doExport:function(){
        window.open(this.exportUrl);
    },

});
