<?php
/**
 * Single Event Meta (Map) Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/modules/meta/details.php
 *
 * @package TribeEventsCalendar
 */

$address = '';
$location_parts = array( 'address', 'city', 'state', 'province', 'zip', 'country' );

// Form the address string for the map
foreach ( $location_parts as $val ) {
    $address_part = call_user_func( 'tribe_get_' . $val, $venue_id );
    if ( $address_part ) {
        $address .= $address_part . ' ';
    }
}
?>
<div id="tribe-events-ymap-<?php the_ID(); ?>" class="tribe-events-meta-group-map tribe-events-ymap">
</div>

<script type="text/javascript">
if (typeof(ymaps) !== 'undefined') {
    ymaps.ready(function() {
        var myGeocoder = ymaps.geocode("<?php echo $address;?>");
        myGeocoder.then(
            function (res) {
                // Выбираем первый результат геокодирования.
                var firstGeoObject = res.geoObjects.get(0);
                // Координаты геообъекта.
                var coords = firstGeoObject.geometry.getCoordinates()

                // Создание экземпляра карты и его привязка к контейнеру с заданным id ("map").
                var map = new ymaps.Map('tribe-events-ymap-<?php the_ID(); ?>', {
                    // При инициализации карты обязательно нужно указать её центр и коэффициент масштабирования.
                    center: coords, // калининград
                    zoom: 14
                });
                // Добавляем первый найденный геообъект на карту.
                map.geoObjects.add(firstGeoObject);
                // Масштабируем карту на область видимости геообъекта.
                
                // Область видимости геообъекта.
                var bounds = firstGeoObject.properties.get('boundedBy');
                map.setBounds(bounds, {
                    // Проверяем наличие тайлов на данном масштабе.
                    checkZoomRange: true
                });
                
            }
        );
    });
}
</script>