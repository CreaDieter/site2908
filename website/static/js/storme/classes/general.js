$(function(){
    var general = {
        /**
         * Is loaded on every page
         */
        init: function() {
            // placeholder fallback when not supported
            if (!('placeholder' in document.createElement('input'))) {
                $('[placeholder]').each(function(){
                    if($(this).val() === "" && $(this).attr("placeholder") != ""){
                        $(this).val($(this).attr("placeholder"));

                        $(this).on('focus', function() {
                            if ($(this).val() === $(this).attr("placeholder")) {
                                $(this).val("");
                            }
                        });

                        $(this).on('focusout', function() {
                            if ($(this).val() === "") {
                                $(this).val($(this).attr("placeholder"));
                            }
                        });
                    }
                });
            }

            /**
             * Show warning when using old browser
             */
            var $buoop = {}
            $buoop.ol = window.onload;
            window.onload=function(){
                try {if ($buoop.ol) $buoop.ol();}catch (e) {}
                var e = document.createElement("script");
                e.setAttribute("type", "text/javascript");
                e.setAttribute("src", "http://browser-update.org/update.js");
                document.body.appendChild(e);
            }
        }

    }
    Storme.extend("general",general);
});