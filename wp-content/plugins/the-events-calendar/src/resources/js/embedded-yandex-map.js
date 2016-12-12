/**
 * Sets up one or more embedded maps.
 */
if ( "function" === typeof jQuery ) jQuery( document ).ready( function( $ ) {
	var mapHolder,
	    position,
		bounds,
	    venueObject,
	    venueAddress,
	    venueCoords,
		venueBounds,
	    venueTitle;

	// The tribeEventsSingleMap object must be accessible (as it contains the venue address data etc)
	if ( "undefined" === typeof tribeEventsSingleMap ) return;

	/**
	 * Determine whether to use long/lat coordinates (these are preferred) or the venue's street
	 * address.
	 */
	function prepare() {
		if ( false !== venueCoords ) useCoords();
		else useAddress();
	}

	/**
	 * Use long/lat coordinates to position the pin marker.
	 */
	function useCoords() {
		position = [venueCoords[0], venueCoords[1]];
		if (false !== venueBounds) {
			// venueBounds: top, right, bot, left
			bounds = [
				venueBounds[3], venueBounds[2],//lower left
				venueBounds[1], venueBounds[0] //upper right
			];
			//bounds=54.715223,20.493096,54.719976,20.501306
		}
		initialize();
	}

	/**
	 * Use a street address and Google's geocoder to position the pin marker.
	 */
	function useAddress() {
		var myGeocoder = ymaps.geocode(venueAddress);
		myGeocoder.then(function (res) {
			var firstGeoObject = res.geoObjects.get(0);
			position = firstGeoObject.geometry.getCoordinates();
			bounds = firstGeoObject.properties.get('boundedBy');
			initialize();
		});
	}

	/**
	 * Setup the map and apply a marker.
	 *
	 * Note that for each individual map, the actual map object can be accessed via the
	 * tribeEventsSingleMap object. In simple cases (ie, where there is only one map on
	 * the page) that means you can access it and change its properties via:
	 *
	 *     tribeEventsSingleMap.addresses[0].map
	 *
	 * Where there are multiple maps - such as in a custom list view with a map per
	 * event - tribeEventsSingleMap.addresses can be iterated through and changes made
	 * on an map-by-map basis.
	 */
	function initialize() {
		
		// Создание экземпляра карты и его привязка к контейнеру с заданным id ("map").
		var map = new ymaps.Map(mapHolder, {
			// При инициализации карты обязательно нужно указать её центр и коэффициент масштабирования.
			center: position, 
			zoom: 14 // Игнорируется, так как дальше выставляется масштаб
		});

		var myGeoObject = new ymaps.GeoObject({
			// Описываем геометрию типа "Точка".
			geometry: {
				type: "Point",
				coordinates: position
			},
			// Описываем данные геообъекта.
			properties: {
				hintContent: venueTitle,
				balloonContentHeader: venueTitle,
				balloonContentBody: venueAddress
			}
		});

		// Добавляем первый найденный геообъект на карту.
		map.geoObjects.add(myGeoObject);
		// Масштабируем карту на область видимости геообъекта.
		
		console.debug('bounds=' + bounds);
		if ( "undefined" !== typeof bounds ) {
			// Область видимости геообъекта.
			map.setBounds(bounds, {
				// Проверяем наличие тайлов на данном масштабе.
				checkZoomRange: true
			});
		}
	}

	ymaps.ready(function() {
		// Iterate through available addresses and set up the map for each
		$.each( tribeEventsSingleMap.addresses, function( index, venue ) {
			mapHolder = document.getElementById( "tribe-events-map-" + index );
			if ( null !== mapHolder ) {
				venueObject  = "undefined" !== typeof venue ? venue: {};
				venueAddress = "undefined" !== typeof venue.address ? venue.address : false;
				venueCoords  = "undefined" !== typeof venue.coords  ? venue.coords  : false;
				venueBounds  = "undefined" !== typeof venue.bounds  ? venue.bounds  : false;
				venueTitle   = venue.title;
				prepare();
			}
		});
	});
});