//pimcore.registerNS("pimcore.plugin.BackendConfig");
//pimcore.plugin.BackendConfig = Class.create(pimcore.plugin.admin, {
//    
//	getClassName: function() {
//        return "pimcore.plugin.BackendConfig";
//    },
//
//    initialize: function() {
//        pimcore.plugin.broker.registerPlugin(this);
//        
//        this.getKeyValue({url: '/backend/get-fields',id:'fields', fields: ['id','term_nl']});
//    },
//    keyValues: [],
//    
//    getConfig: function() {
//    	
//    	var actions = [];
//    	var me = this;
//    	
//    	/**
//    	 * Add Menu item for Fields
//    	 */
//    	actions.push({
//    		text: ts("Sectoren"),
//    		iconCls: 'icon-list',
//    		handler: function() {
//    			new backend.editlist({
//    				id: 'fields',
//    				title: ts("Sectoren beheren"),
//    				listUrl: '/backend/get-fields',
//    				iconCls: 'icon-list',
//    				deleteRow: true,
//    				fields: [
//    				         {source: 'id', label: ts('ID'), editable:false},
//    				         {source: 'term_nl', label: ts('Naam NL')},
//    				         {source: 'term_fr', label: ts('Naam FR')},
//    				         {source: 'active', label: ts('Actief'), renderer: 'boolean', editor: new Ext.form.ComboBox({
//																    				                triggerAction: 'all',
//																    				                editable: false,
//																    				                store: [['1',ts("Ja")],["0",ts("Neen")]]
//																    				            })
//    				         }
//    				]
//    			});
//    		},
//    	});
//    	 	
//    	var returnVal = {
//                text: ts("Overzichten"),
//                iconCls: "icon-report",
//                cls: "pimcore_main_menu",
//                menu: [actions]
//    	};
//    	
//    	return returnVal;
//    },
//    
//    /**
//     * Helper function to load a key/value list from server to fill a dropDown
//     * @param params
//     */
//    getKeyValue: function(params) {
//    	this.keyValues[params.id] = new Ext.data.JsonStore({
//    	    autoDestroy: false,
//    	    autoLoad: true,
//    	    url: params.url,
//    	    storeId: 'keyValue_' + params.id,
//    	    root: 'data',
//    	    idProperty: 'id',
//    	    fields: params.fields
//    	});
//    	this.keyValues[params.id].load();
//    },
//});