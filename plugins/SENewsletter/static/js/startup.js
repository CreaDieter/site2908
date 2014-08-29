pimcore.registerNS("pimcore.plugin.senewsletter");

pimcore.plugin.senewsletter = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.senewsletter";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },
 
    pimcoreReady: function (params,broker){
        // alert("Example Ready!");
    }
});

var senewsletterPlugin = new pimcore.plugin.senewsletter();

