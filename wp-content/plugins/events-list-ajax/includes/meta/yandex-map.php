<?php
/**
 * Single Event Meta (Map) Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/modules/meta/details.php
 *
 * @package TribeEventsCalendar
 */
if ( empty( $map ) ) {
	//return;
}

?>

<div id="events-list-yandex-map" class="tribe-events-venue-map">
</div>

<script type="text/javascript">
ymaps.ready(function() {
	// Создание экземпляра карты и его привязка к контейнеру с заданным id ("map").
    var myMap = new ymaps.Map('events-list-yandex-map', {
        // При инициализации карты обязательно нужно указать её центр и коэффициент масштабирования.
        center: [54.70453942733338,20.473791500000015], // калининград
        zoom: 13
    });
	myMap.geoObjects.add(
		new ymaps.Placemark([54.70453942733338,20.473791500000015], { hintContent: 'Калининград!'})
	);
});
</script>