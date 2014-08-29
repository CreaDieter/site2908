pimcore.registerNS("pimcore.plugin.backend");

pimcore.plugin.backend = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.backend";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
//        var action = new Ext.Action({
//            id: "sitemap_setting_button",
//            text: t('Demo custom button'),
//            iconCls: "sitemap_icon_info",
//            handler: function () {
//                new backend.editlist({
//                    id: 'demo',
//                    title: ts("Demo overview"),
//                    listUrl: '/plugin/Backend/index/get-data-for-this-action',
//                    deleteRow: true,
//                    fields: [
//                        {source: 'id', label: ts('ID'), editable: false},
//                        {source: 'name', label: ts('Name')} // Default editable
//                    ]
//                });
//            }
//        });
//        layoutToolbar.extrasMenu.add(action);
//        // Reload layout
//        pimcore.layout.refresh();
    }
});

var backendPlugin = new pimcore.plugin.backend();

