pimcore.registerNS("pimcore.plugin.sebasic");
pimcore.plugin.sebasic = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.sebasic";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        var user = pimcore.globalmanager.get("user");
        if (user.admin) {
            var statusbar = pimcore.globalmanager.get("statusbar");
            statusbar.add('<div class=""><a href="#" style="color:#fff;" onclick="sebasicPlugin.installHelp()">Installation companion</a></div>');
            statusbar.add("-");
            statusbar.doLayout();
        }
    },

    installHelp: function () {
        var tabs = new Ext.TabPanel({
            renderTo: Ext.getBody(),
            activeTab: 0,
            items: [{
                title: 'Add features',
//                layout: 'fit',
                items: [this.addLanguage(), this.addUser(), this.addPages()]
            },{
                title: 'Edit Configuration',
                items: [this.addConfig()]
            }]
        });

        this.helpWindow = new Ext.Window({
            width: 800,
            height: 500,
            modal: true,
            title: 'Installation Companion',
            closeAction: "close",
            listeners: {
                "close": function () {
                    if (typeof this.failure == "function") {
                        this.failure();
                    }
                }.bind(this)
            },
            items: [tabs]
//            items: [this.addLanguage(), this.addPages()]
        });
        this.helpWindow.show();
    },

    addUser: function() {
        this.userPanel = new Ext.FormPanel({
            width: 780,
            url: '/plugin/SEBasic/index/add-user',
            items: [
                {
                    'xtype': 'textfield',
                    'value': '',
                    'width': 250,
                    'fieldLabel': 'Username',
                    'name': 'username'
                },
                {
                    'xtype': 'textfield',
                    'inputType': 'password',
                    'value': '',
                    'width': 250,
                    'fieldLabel': 'Password',
                    'name': 'password'
                },
                {
                    'xtype': 'textfield',
                    'vtype': 'email',
                    'value': '',
                    'width': 250,
                    'fieldLabel': 'Email',
                    'name': 'email'
                }
            ],
            title: "Add backoffice user",
            id: 'userAddFormPanel',
            labelWidth: 100,
            bodyStyle: 'padding:5px 5px 0',
            autoScroll: true,
            defaults: {width: 300},
            buttons: [
                {
                    text: ts('Add User'),
                    score: this,
                    formBind: true,
                    handler: function () {
                        // Get the form
                        var f = Ext.getCmp('userAddFormPanel');
                        f.getForm().submit({
                            params: {
                                // Send all form data as params
                                data: Ext.encode(f.getForm().getFieldValues())
                            },
                            success: function (result) {
                                Ext.MessageBox.alert('Succes!', ts('Yippee! You can now sign in using this user!'));
                            },
                            failure: function (j,t) {
                                if(t.response) {
                                   var popupText = t.response.responseText;
                                } else {
                                    var popupText = "Could not add the user! Are all form fields correct?";
                                }

                                var titles = ['Aiaiaiaaiai!','Oopsy daisy!', 'Whoops!','d\'oh!','Failure', 'Mayday mayday!','Big error'];
                                var popupTitle = titles[Math.floor(Math.random()*titles.length)];
                                Ext.MessageBox.alert(popupTitle, ts(popupText));

                            }
                        });
                    }
                }
            ]
        });

        return this.userPanel;
    },

    addLanguage: function () {
        var me = this;
        this.languagePanel = new Ext.FormPanel({
            width: 780,
            url: '/plugin/SEBasic/index/add-language',
            items: [
                {
                    'xtype': 'textfield',
                    'value': '',
                    'width': 250,
                    'fieldLabel': 'Language',
                    'name': 'language'
                }
            ],
            title: "Add Frontend Language",
            id: 'languageAddFormPanel',
            labelWidth: 100,
            bodyStyle: 'padding:5px 5px 0',
            autoScroll: true,
            defaults: {width: 300},
            buttons: [
                {
                    text: ts('Add Language'),
                    score: this,
                    formBind: true,
                    handler: function () {
                        // Get the form
                        var f = Ext.getCmp('languageAddFormPanel');
                        f.getForm().submit({
                            params: {
                                // Send all form data as params
                                data: Ext.encode(f.getForm().getFieldValues())
                            },
                            success: function (result) {
                                me.reloadDocumentTree();
                                Ext.MessageBox.alert('Status', ts('Language succesfully  added!'));
                            },
                            failure: function () {
                                Ext.MessageBox.alert('Status', ts("Whoops... something went wrong while adding the language!"));
                            }
                        });
                    }
                }
            ]
        });

        return this.languagePanel;
    },

    addPages: function () {
        var me = this;
        this.pagesPanel = new Ext.FormPanel({
            width: 780,
            items: [
                {
                    'xtype': 'button',
                    'width': 100,
                    'text':'Homepage',
                    handler: function() {
                        Ext.Ajax.request({
                            url: '/plugin/SEBasic/index/add-home-page',
                            method: 'GET',
                            success: function (responseObject, request) {
                                me.reloadDocumentTree();
                                Ext.MessageBox.alert('Status', ts('Document successfully  added!'));
                            }
                        });
                    }
                },
                {
                    'xtype': 'button',
                    'width': 100,
                    'text':'Contact Page',
                    handler: function() {
                        Ext.Ajax.request({
                            url: '/plugin/SEBasic/index/add-contact-page',
                            method: 'GET',
                            success: function (responseObject, request) {
                                me.reloadDocumentTree();
                                Ext.MessageBox.alert('Status', ts('Document successfully  added!'));
                            }
                        });
                    }
                },
                {
                    'xtype': 'button',
                    'width': 100,
                    'text':'Disclaimer Page',
                    handler: function() {
                        Ext.Ajax.request({
                            url: '/plugin/SEBasic/index/add-disclaimer-page',
                            method: 'GET',
                            success: function (responseObject, request) {
                                me.reloadDocumentTree();
                                Ext.MessageBox.alert('Status', ts('Document successfully  added!'));
                            }
                        });
                    }
                },
                {
                    'xtype': 'button',
                    'width': 100,
                    'text':'News Page',
                    handler: function() {
                        Ext.Ajax.request({
                            url: '/plugin/SEBasic/index/add-news-page',
                            method: 'GET',
                            success: function (responseObject, request) {
                                me.reloadDocumentTree();
                                Ext.MessageBox.alert('Status', ts('Document successfully  added!'));
                            }
                        });
                    }
                },
                {
                    'xtype': 'button',
                    'width': 100,
                    'text':'Vacancy Page',
                    handler: function() {
                        Ext.Ajax.request({
                            url: '/plugin/SEBasic/index/add-vacancy-page',
                            method: 'GET',
                            success: function (responseObject, request) {
                                me.reloadDocumentTree();
                                Ext.MessageBox.alert('Status', ts('Document successfully  added!'));
                            }
                        });
                    }
                },
                {
                    'xtype': 'button',
                    'width': 100,
                    'text':'Events Page',
                    handler: function() {
                        Ext.Ajax.request({
                            url: '/plugin/SEBasic/index/add-events-page',
                            method: 'GET',
                            success: function (responseObject, request) {
                                me.reloadDocumentTree();
                                Ext.MessageBox.alert('Status', ts('Document successfully  added!'));
                            }
                        });
                    }
                },
                {
                    'xtype': 'button',
                    'width': 100,
                    'text':'Search Page',
                    handler: function() {
                        Ext.Ajax.request({
                            url: '/plugin/SEBasic/index/add-search-page',
                            method: 'GET',
                            success: function (responseObject, request) {
                                me.reloadDocumentTree();
                                Ext.MessageBox.alert('Status', ts('Document successfully  added!'));
                            }
                        });
                    }
                }
            ],
            title: "Add Pages & Modules",
            id: 'pagesAddFormPanel',
            labelWidth: 0,
            bodyStyle: 'padding:5px 5px 0',
            autoScroll: true
        });

        return this.pagesPanel;
    },

    addConfig: function () {
        this.configPanel = new Ext.FormPanel({
            width: 780,
            url: '/plugin/SEBasic/index/add-config',
            items: [],
            title: "Edit Config",
            id: 'configAddFormPanel',
            labelWidth: 150,
            bodyStyle: 'padding:5px 5px 0',
            autoScroll: true,
            defaults: {width: 300},
            buttons: [
                {
                    text: ts('Save Config'),
                    score: this,
                    formBind: true,
                    handler: function () {
                        // Get the form
                        var f = Ext.getCmp('configAddFormPanel');
                        f.getForm().submit({
                            params: {
                                // Send all form data as params
                                data: Ext.encode(f.getForm().getFieldValues())
                            },
                            success: function (result) {
                                Ext.MessageBox.alert('Status', ts('Config succesfully  saved!'));
                            },
                            failure: function () {
                                Ext.MessageBox.alert('Status', ts("Whoops... something went wrong while saving the config!"));
                            }
                        });
                    }
                }
            ]
        });

        Ext.Ajax.request({
            url: '/plugin/SEBasic/index/get-config-form',
            method: 'GET',
            success: function (responseObject, request) {
                var data = Ext.util.JSON.decode(responseObject.responseText);
                var itemList = [];
                var items = data.items;
                Ext.each(items, function (object, index) {
                    itemList.push(object);
                });

                Ext.getCmp('configAddFormPanel').removeAll();
                Ext.getCmp('configAddFormPanel').add(itemList);
                Ext.getCmp('configAddFormPanel').doLayout();
            }
        });

        return this.configPanel;
    },

    reloadDocumentTree: function() {
        pimcore.globalmanager.get("layout_document_tree").tree.getLoader().load(pimcore.globalmanager.get("layout_document_tree").tree.root, function () {
            pimcore.globalmanager.get("layout_document_tree").tree.root.expand();
        });
    }
});

var sebasicPlugin = new pimcore.plugin.sebasic();

