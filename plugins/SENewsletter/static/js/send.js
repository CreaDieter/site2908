// register namespace
pimcore.registerNS("newsletter.send");

// Create class
newsletter.send = Class.create({

    /**
     * Constructor-like function
     */
    initialize: function () {
        // open panel
        this.getTabPanel();
        this.getForm();
    },

    getTabPanel: function () {
        // Check if panel exists
        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "importerPanel",
                title: ts('Nieuwsbrief versturen'),
                iconCls: "pimcore_icon_newsletter",
                border: false,
                layout: "fit",
                closable:true,
                items: []
            });

            // Find element to add panel
            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.activate("importerPanel");

            // Add listener for onClose/destroy action
            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("importerPanel");
            }.bind(this));

            // Refresh layout
            pimcore.layout.refresh();
        }
        // Return this panel
        return this.panel;
    },

    getForm: function() {
        var me = this;
        Ext.Ajax.request({
            url: '/plugin/SENewsletter/admin/get-lists/',
            method: 'GET',
            success: function (responseObject, request) {
                var data = Ext.util.JSON.decode(responseObject.responseText);
                var fields = data.fields;
                me.getNewsletterForm(fields);
            },
            failure: function() {
                alert('Could not get enews lists');
            }
        });
        return {};
    },

    getNewsletterForm: function(fields) {
        var me = this;
        var data = {};
        var fieldConfigDragdrop = {
            title: ts('Sleep de nieuwsbrief naar dit veld'),
            name: 'newsletterDrgDrp',
            required: true,
            documentsAllowed: true,
//            documentTypes : [{documentTypes: ['nieuwsbrief']}],
            width: 300
        };

        var fieldConfigStatus = {
            title: ts('Vink dit aan als de nieuwsbrief direct verstuurd mag worden'),
            name: 'statusCheckbox',
            required: true,
            width: 300
        };

        this.dragdrop = new pimcore.object.tags.href(data,fieldConfigDragdrop);
        this.campaignStatus = new pimcore.object.tags.checkbox(data,fieldConfigStatus);
        this.campaignLists = new Ext.form.CheckboxGroup({
            id:'listGroup',
            xtype: 'checkboxgroup',
            fieldLabel: ts('Naar welke lijst moet dit verstuurd worden?'),
            itemCls: 'x-check-group-alt',
            columns: 1,
            items: [fields]
        });

        var sendForm = new Ext.FormPanel({
            width: 500,
            autoHeight: true,
            bodyStyle: 'padding: 10px 10px 10px 10px;',
            labelWidth: 200,
            defaults: {
                anchor: '95%',
                allowBlank: false,
                msgTarget: 'side'
            },
            items:[
                me.dragdrop.getLayoutEdit(),
                me.campaignStatus.getLayoutEdit(),
                me.campaignLists
            ],
            buttons: [{
                text: ts('Verstuur'),
                handler: function(){
                    var lists = [];
                    var options = me.campaignLists.getValue();
                    for (index = 0; index < options.length; ++index) {
                        if (!isNaN(options[index].inputValue)) {
                            lists.push(options[index].inputValue);
                        }
                    }
                    if(sendForm.getForm().isValid()){
                        sendForm.getForm().submit({
                            url: '/plugin/SENewsletter/admin/send-newsletter/id/' + me.dragdrop.getValue().id+'/lists/' + lists.join('-') + '/sendNow/' + (me.campaignStatus.getValue() == true ? 1 : 0) + '/',
                            waitMsg: ts('Nieuwsbrief versturen...'),
                            success: function(form,action){
//                                me.panel.destroy();
                                Ext.MessageBox.alert('Voltooid!', 'Voltooid! De nieuwsbrief werd verstuurd naar het enews systeem.');
                            },
                            failure: function(form, action) {
                                var response = JSON.parse(action.response.responseText);
                                Ext.MessageBox.alert('Fout', 'fout: '+ response.msg);
                            }
                        });
                    }
                }
            }]
        });

        Ext.getCmp('importerPanel').items.add(sendForm);
        pimcore.layout.refresh();
    }
});