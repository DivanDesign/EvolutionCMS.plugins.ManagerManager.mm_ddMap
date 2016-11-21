/**
 * jQuery.ddMM.mm_ddYMap
 * @version 1.1 (2015-05-07)
 * 
 * @uses Yandex.Maps 2.1.
 * @uses jQuery 1.10.2.
 * @uses jQuery.ddMM 1.0.
 * @uses jQuery.ddYMap 1.3.1.
 * 
 * @copyright 2013–2015 [DivanDesign]{@link http://www.DivanDesign.biz }
 */

(function($){
$.ddMM.mm_ddYMap = {
	//Параметры по умолчанию
	defaults: {
		//Нужно ли скрывать оригинальное поле
		hideField: true,
		//Ширина контейнера с картой
		width: 'auto',
		//Высота контейнера с картой
		height: 400
	},
	
	/**
	 * @method init
	 * @version 1.0 (2016-11-25)
	 * 
	 * @desc Инициализация карты.
	 * 
	 * @param elem {object_plain} — The parameters.
	 * @param elem.id {integer} — TV ID.
	 * @param elem.LngLat {array} — Position.
	 * @param elem.LngLat[0] {float} — Lng.
	 * @param elem.LngLat[1] {float} — Lat.
	 * @param elem.$elem {jQuery} — Coordinates field.
	 * 
	 * @returns {void}
	 */
	init: function(elem){
		var $mapElement = $('#ddYMap' + elem.id);
		
		//После инициализации карты
		$mapElement.on('ddAfterInit', function(){
			//Объект карты
			var map = $mapElement.data('ddYMap').map,
				//Контрол поиска
				serachControl = new ymaps.control.SearchControl({
					options: {
						useMapBounds: true,
						noPlacemark: true,
						maxWidth: 400
					}
				}),
				//Метка.
				//TODO: Это очень странно, но похоже, что «map.geoObjects.get(0)» возвращает «GeoObjectCollection» вместо «Placemark», потому приходится ещё раз делать «get(0)».
				placemark = map.geoObjects.get(0).get(0);
			
			//При выборе результата поиска
			serachControl.events.add('resultselect', function(event){
				var coords = event.originalEvent.target.getResultsArray()[0].geometry.getCoordinates();
				
				//Переместим куда надо маркер
				placemark.geometry.setCoordinates(coords);
				
				//Запишем значение в оригинальное поле
				elem.$elem.val(coords[0] + ',' + coords[1]);
			});
			
			map.controls.add(serachControl);
			
			//При клике по карте меняем координаты метки
			map.events.add('click', function(event){
				var coords = event.get('coords');
				
				placemark.geometry.setCoordinates([coords[0], coords[1]]);
				
				elem.$elem.val(coords[0] + ',' + coords[1]);
			});
			
			//Перетаскивание метки
			placemark.events.add('dragend', function(event){
				var coords = placemark.geometry.getCoordinates();
				
				elem.$elem.val(coords[0] + ',' + coords[1]);
			});
		}).ddYMap({
			placemarks: elem.LngLat,
			placemarkOptions: {draggable: true}
		});
	}
};

/**
 * jQuery.fn.mm_ddYMap
 * @version 1.0.3 (2015-09-16)
 * 
 * @desc Делает карту.
 * 
 * @uses $.ddMM.mm_ddYMap.
 * 
 * @param [params] {object_plain} — Параметры передаются в виде plain object.
 * @param [params.hideField=true] {boolean} — Нужно ли скрывать оригинальное поле.
 * @param [params.width='auto'] {integer|'auto'} — Ширина контейнера с картой.
 * @param [params.height=400] {integer} — Высота контейнера с картой.
 * 
 * @copyright 2013–2015 [DivanDesign]{@link http://www.DivanDesign.biz }
 */
$.fn.mm_ddYMap = function(params){
	//Обрабатываем параметры
	params = $.extend({}, $.ddMM.mm_ddYMap.defaults, params || {});
	
	//Если ширина является числом
	if ($.isNumeric(params.width)){
		//Допишем пиксели
		params.width += 'px';
	}
	
	return $(this).each(function(){
		var elem = {};
		
		//TV с координатами
		elem.$elem = $(this);
		//ID оригинальной TV
		elem.id = elem.$elem.attr('id');
		//Координаты
		elem.LngLat = elem.$elem.val();
		
		//Родитель
		var	$elemParent = elem.$elem.parents('tr:first'),
			//Запоминаем название поля
			sectionName = $elemParent.find('.warning').text(),
			//Контейнер для карты
			$sectionContainer = $('<div class="sectionHeader">' + sectionName + '</div><div class="sectionBody"><div id="ddYMap' + elem.id + '" style="width: ' + params.width + '; height: ' + params.height + 'px; position: relative; border: 1px solid #c3c3c3;"></div></div>'),
			$YMap = $sectionContainer.find('#ddYMap' + elem.id);
		
		//Добавляем контейнер
		elem.$elem.parents('.tab-page:first').append($sectionContainer);
		
		//Скрываем родителя и разделитель
		$elemParent.hide().prev('tr').hide();
		
		//Если скрывать не надо, засовываем перед картой
		if (!params.hideField){
		 	elem.$elem.insertBefore($YMap);
		}
		
		//Если координаты не заданны, то задаём дефолт
		if ($.trim(elem.LngLat) == ''){
			elem.LngLat = '55.20432131317031,61.28999948501182';
		}
		
		//Разбиваем координаты
		elem.LngLat = elem.LngLat.split(',');
		
		//Инициализируем
		$.ddMM.mm_ddYMap.init(elem);
	});
};
})(jQuery);