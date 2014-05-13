var Ooi = (function() {

    // content url base
    var _CONTENT_BASE_URL = './content.php';

    // jQuery selection
    var $content = $("#content");
    var $menu = $("#menu");

    // Bootstrap popover options
    var popoverOptions = { selector : "i[rel='popover']",
        trigger : "hover",
        container : "body"
    };

    var _POLYGON_OPTIONS = {className : 'polygon'};

    var _MAP;
    var _SITE_LOCATIONS = {};
    var _SITE_LAYERS = {};

    var _DEFAULT_CENTER = [20.25, -104.75];

    // Leaflet.MousePosition options
    var mousePositionOptions = { latFormatter : _lat2dm,
        lngFormatter : _lon2dm
    };

    function _init() {

        // Start the clock
        ClockUtc.init('#utc-clock');

        var $body = $('body');

        // Delegate Bootstrap popovers
        $body.popover(popoverOptions);

        // Fetch PlatformSite data products table
        $menu.on('click', 'a.platform', function(evt) {

            evt.preventDefault();

            // Remove the 'active' class from all a.platforms
            $('a.platform').removeClass('active');
            $(this).addClass('active');

            _fetchDataProductStatus(this.href);

            // Update the map view
            var site = $(this).parent().attr('id');
            if (_SITE_LOCATIONS[site].type == 'Polygon') {
                _MAP.fitBounds(_SITE_LAYERS[site].getBounds());
            }
            else if (_SITE_LOCATIONS[site].type == 'Point') {
                _MAP.setView(_SITE_LAYERS[site].getLatLng(), 8);
            }

        });

        // Fetch Data Product parameters
        $content.on('click', 'a.data-product-params', function(evt) {

            evt.preventDefault();

            _fetchDataProductParameters(this);

        });

        // Add the map
        _MAP = L.map('map').setView(_DEFAULT_CENTER, 1);
        L.tileLayer('http://services.arcgisonline.com/ArcGIS/rest/services/Ocean_Basemap/MapServer/tile/{z}/{y}/{x}.png').addTo(_MAP);

        // Add the Leaflet.MousePosition plugin
        L.control.mousePosition(mousePositionOptions).addTo(_MAP);

        $('#ooi-sites').on('click', 'a', function(evt) {

            evt.preventDefault();

            // Reset map view
            _MAP.setView(_DEFAULT_CENTER, 1);

            var $target = $(this);
/*
            $target.siblings().removeClass('active');
*/

            $target.parent().siblings().find('i.glyphicon-ok').remove();
            $target.append('&nbsp;<i class="glyphicon glyphicon-ok"></i>');
/*
            if ($target.hasClass('active')) {
                $target.removeClass('active');
                $content.html('');
                $menu.html('');
            }
            else {
                $target.addClass('active');
                $content.html('');
            }
*/

            var urlTokens = Url.decomposeUrl(this.href);
            var siteUrl = _CONTENT_BASE_URL + '/' + Url.composeQuery(urlTokens.vars);

            var xhr = $.ajax({url : siteUrl,
                dataType : 'html',
                type : 'GET'
            });

            xhr.done(function(response) {
                $menu.html(response);
            });

        });

        // Fetch the array of geoJSON features for all Observatories
        var xhr = $.ajax({url : _CONTENT_BASE_URL + '/?type=sites',
            dataType : 'json',
            type : 'GET'});
        xhr.done(function(response) {

            for (var site in response) {

                if (response[site].type == 'Point') {
                    _SITE_LAYERS[site] = L.marker([response[site].coordinates[1], response[site].coordinates[0]]).bindPopup(site).addTo(_MAP);
                }
                else if (response[site].type == 'Polygon') {
                    // Reverse the elments of each of the arrays in
                    // coordinates
                    var latLons = [];
                    response[site].coordinates.forEach(function(elm) {
                        latLons.push([elm[1], elm[0]]);
                    });
                    _SITE_LAYERS[site] = L.polygon(latLons, _POLYGON_OPTIONS).bindPopup(site).addTo(_MAP);
                }

            }

            _SITE_LOCATIONS = response;
            
        });

    }

    function _fetchDataProductStatus(url) {

        var urlTokens = Url.decomposeUrl(url);
        var platformUrl = _CONTENT_BASE_URL + '/' + Url.composeQuery(urlTokens.vars);

        var xhr = $.ajax({url : platformUrl,
            dataType : 'html',
            type : 'GET'
        });

        xhr.done(function(response) {
            $content.html(response);
        });

    }

    function _fetchDataProductParameters(link) {

        var urlTokens = Url.decomposeUrl(link.href);

        // Find the row number of the table and add it as the pnum
        // variable
        urlTokens.vars['pnum'] = $(link).closest('tr').index();

        paramsUrl = _CONTENT_BASE_URL + '/' + Url.composeQuery(urlTokens.vars);

        var xhr = $.ajax({url : paramsUrl,
            dataType : 'html',
            type : 'GET'
        });

        xhr.done(function(response) {
            $content.html(response);
        });

    }

    /*
     * _lat2dm(coordinate)
     * Formats a latitude, given in decimal degrees (as returned by Leaflet)
     * to NMEA units (native glider GPS units) and appends the direction
     * (N/S).  Used as the latFormatter for Leaflet.MousePosition.
     */
    function _lat2dm(coordinate) {

        var formatted = _dd2dm(coordinate);

        return coordinate < 0 ? formatted + ' S' : formatted + ' N';

    }

    /*
     * _lat2dm(coordinate)
     * Formats a longitude, given in decimal degrees (as returned by Leaflet)
     * to NMEA units (native glider GPS units) and appends the direction 
     * (E/W).  Used as the lngFormatter for Leaflet.MousePosition.
     */
    function _lon2dm(coordinate) {

        var formatted = _dd2dm(coordinate);

        // Dateline handling
        if (coordinate < -180) {
            coordinate += 360;
        }

        return coordinate < 0 ? formatted + ' W' : formatted + ' E';

    }

    /*
     * _dd2dm(coordinate)
     * Converts a gps coordinate from decimal degrees to NMEA coordinates.
     * Called by _lat2dm and _lon2dm.
     */
    function _dd2dm(coordinate) {

        // Dateline handling
        if (coordinate < -180) {
            coordinate += 360;
        }

        var absCoord = Math.abs(coordinate);
        var degrees = Math.floor(absCoord);
        var mm = (absCoord - degrees) * 60;
        var minutes = Math.floor(mm);
        var s = (mm - minutes) * 60;
        var mm = Math.floor(mm);
        var minutes = mm < 10 ? "0" + mm : mm;
        var s = Math.floor(s);
        var seconds = s < 10 ? "0" + s : s;

        return Math.abs(degrees) + "&deg; " + minutes + "&#39; " + seconds + "&#34";

/*
        // Remember if we're negative or not
        var isNegative = coordinate < 0 ? true : false;

        var absCoordinate = Math.abs(coordinate);

        var gpsDegrees = Math.floor(absCoordinate);
        var gpsMinutes = (absCoordinate - Math.abs(gpsDegrees)) * 60;

        var minutesString = gpsMinutes < 10 ?
            '0' + gpsMinutes.toString() :
            gpsMinutes.toString();

        var dmString = parseFloat(gpsDegrees.toString() + minutesString).toFixed(3);

        return isNegative ? '-' + dmString : dmString;
*/

    }

    return {
        init : _init,
        getMap : function() { return _MAP },
        getSiteLocations : function() { return _SITE_LOCATIONS },
        getSiteLayers : function() { return _SITE_LAYERS }
    }

})();

Ooi.init();
