pimcore.registerNS("pimcore.plugin.SENewsletter");

pimcore.plugin.SENewsletter = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.SENewsletter";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params,broker){
        var action = new Ext.Action({
            id: "sitemap_setting_button",
            text: t('Nieuwsbrief versturen'),
            iconCls: "pimcore_icon_newsletter",
            handler: function () {
                new newsletter.send;
            }
        });
        layoutToolbar.extrasMenu.add(action);
        // Reload layout
        pimcore.layout.refresh();
    }
});

var senewsletterPlugin = new pimcore.plugin.SENewsletter();