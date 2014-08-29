(function($){
    if (typeof window.Storme == "undefined"){
        var Storme = window.Storme = function(){

            // We have a dependency on jQuery:
            if (typeof jQuery == "undefined"){
                alert("Please load jQuery library first");
            }

            this.fn = {};

            this.extend = function(namespace,obj){
                if (typeof this[namespace] == "undefined"){
                    if (typeof this.fn[namespace] === "undefined"){
                        // extend each namespace with core functionality
                        $.extend(obj,this.fn.extFN);

                        // If there are no settings in the class, create them
                        if (typeof obj.settings == "undefined") {
                            obj.settings = {};
                        }
                        // Merge settings
                        $.extend(obj.settings,this.settings);

                        // load the new libary in the namespaces:
                        this.fn[namespace] = obj;
                        //this.fn[namespace] = tmp;
                        this[namespace] = this.fn[namespace];

                        // initialize the new library
                        if (typeof this[namespace].init === "function"){
                            this[namespace].init();
                        }
                    } else {
                        alert("The namespace '" + namespace + "' is already taken...");
                    }
                }
            };

            this.setObjectData = function(obj1, obj2) {
                for (var p in obj2) {
                    try {
                        // Property in destination object set; update its value.
                        if ( obj2[p].constructor == Object ) {
                            obj1[p] = window.Storme.setObjectData(obj1[p], obj2[p]);
                        } else {
                            obj1[p] = obj2[p];
                        }
                    } catch(e) {
                        // Property in destination object not set; create it and set its value.
                        obj1[p] = obj2[p];
                    }
                }

                return obj1;
            };

            /**
             * @var object settings - The configuration of this library
             */
            this.settings = {},

            /**
             * @var array - List of loaded JS scripts
             */
            this.libraries = [],

            /**
             * Loads a JS library via Ajax
             *
             * @param string url - The location of the script
             * @param function callback - Optional callback to execute after script was loaded
             * @return core for chainability
             */
             this.loadJS = function(url,callback){
                if (typeof callback === "undefined"){
                    callback = function(){};
                }

                // load it via Ajax
                $.getScript(url,callback);
                return this;
            },

            /**
             * Loads a JS library once via Ajax
             *
             * @param string url - The location of the script
             * @param function callback - Optional callback to execute after script was loaded
             * @return core for chainability
             */
            this.loadOnce = function(url,callback){
                if (typeof callback === "undefined"){
                    callback = function(){};
                }

                if ($.inArray(url,this.libraries) == -1){
                    // load JS:
                    this.loadJS(url, callback);

                    // append to list:
                    this.libraries.push(url);
                } else {
                    callback();
                }
                return this;
            },

            this.loadClass = function(className, callback) {
                $.ajaxSetup({
                    cache: true
                });
                var me =this;
                if (typeof callback != 'function') {
                    var name = callback;
                    callback = function() { me.callAction(className,name); }
                }

                return this.loadOnce('/js/storme/classes/' + className + '.js', callback);
            },

            this.callAction = function(namespace,actionName) {
                if (typeof this.fn[namespace] != "undefined") {
                    if (typeof this.fn[namespace][actionName] === "function") {
                        return this.fn[namespace][actionName]();
                    }
                }
            },

            this.setConfig = function(settings) {
                this.setObjectData(this.settings, settings);
                return this;
            }

            /**
             * Log some text or data to console
             */
            this.log = function(txt) {
                if (typeof console == "undefined") {
                    alert(txt);
                } else {
                    console.log(txt);
                }
            }
        };

        // Create instance
        window.Storme = new Storme();

//        window.Storme.extFN = window.Storme.fn.extFN = {
//            setConfig:function(settings) {
//                window.Storme.setObjectData(this.settings, settings);
//                return this;
//            }
//        };
    }
})(jQuery);