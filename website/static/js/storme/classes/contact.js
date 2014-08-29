$(function(){
    var contact = {
        /**
         * Is loaded on contact controller, every action
         */
        init: function() {

        },

        map: null,

        /**
         * Is loaded on contact controller, default action
         */
        default: function() {
            // Only run this if the map_canvas is available
            if (typeof $('map_canvas') != undefined) {
                this.createGoogleMap();

                if (this.settings.contact.geoAddress) {
                    this.addMarkerBasedOnAddress(this.settings.contact.geoAddress);
                } else {
                    if (this.settings.contact.geoLat == 0 || this.settings.contact.geoLng == 0) {
                        alert('No coordinates provided for this map! Please add "geo_lat" & "geo_long" OR "geo_address" to the properties of this document!');
                    }
                    this.addMarkerToMap(new google.maps.LatLng(this.settings.contact.geoLat, this.settings.contact.geoLng));
                }
            }
        },

        createGoogleMap: function() {
            // Define the map
            this.map = new google.maps.Map(document.getElementById("map_canvas"),{
                center: new google.maps.LatLng(0,0),
                zoom: 16
            });
        },


        addMarkerToMap: function(location) {
            var me = this;
            // Define the marker and add it to the map
            var marker = new google.maps.Marker({
                position: location,
                map: me.map
            });

            //
            this.map.setCenter(location);
        },

        addMarkerBasedOnAddress: function(address) {
            var me = this;
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
//                    console.log('returning ' + results[0].geometry.location);
                    var location = results[0].geometry.location;
                    if (location !== undefined) {
                        me.addMarkerToMap(location);
                    } else {
                        alert('No coordinates found for address: "' + address + '"!');
                    }
                }
            });
        }
    }
    Storme.extend("contact",contact);
});