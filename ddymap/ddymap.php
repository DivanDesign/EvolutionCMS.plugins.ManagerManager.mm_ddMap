<?php
/**
 * mm_ddYMap
 * @version 1.3.1 (2013-06-08)
 * 
 * @desc A widget for ManagerManager plugin allowing Yandex Maps integration.
 * 
 * @uses ManagerManager plugin 0.5.
 * 
 * @param $tvs {comma separated string} - TV names to which the widget is applied. @required
 * @param $roles {comma separated string} - The roles that the widget is applied to (when this parameter is empty then widget is applied to the all roles). Default: ''.
 * @param $templates {comma separated string} - Id of the templates to which this widget is applied (when this parameter is empty then widget is applied to the all templates). Default: ''.
 * @param $w {'auto'; integer} - Width of the map container. Default: 'auto'.
 * @param $h {integer} - Height of the map container. Default: 400.
 * @param $hideField {boolean} - Original coordinates field hiding status (true — hide, false — show). Default: true.
 * 
 * @link http://code.divandesign.biz/modx/mm_ddymap/1.3.1
 * 
 * @copyright 2013, DivanDesign
 * http://www.DivanDesign.biz
 */

function mm_ddYMap($tvs, $roles = '', $templates = '', $w = 'auto', $h = '400', $hideField = true){
	global $modx, $mm_current_page, $mm_fields;
	$e = &$modx->Event;
	
	if ($e->name == 'OnDocFormRender' && useThisRule($roles, $templates)){
		$output = '';
		
		// if we've been supplied with a string, convert it into an array 
		$tvs = makeArray($tvs);
		
		$tvs = tplUseTvs($mm_current_page['template'], $tvs);
		if ($tvs == false){
			return;
		}
		
		// We always put a JS comment, which makes debugging much easier
		$output .= "//  -------------- mm_ddYMap :: Begin ------------- \n";
		
		//Подключаем основной js
		$output .= includeJs($modx->config['site_url'].'assets/plugins/managermanager/widgets/ddymap/jquery.ddManagerManager.mm_ddYMap-1.0.js', 'js', 'jquery.ddManagerManager.mm_ddYMap', '1.0');
		//Подключаем библиотеку карт
		$output .= includeJs('http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU&onload=mm_ddYMap_init', 'js', 'api-maps.yandex.ru', '2.0');
		
		foreach ($tvs as $tv){
			$output .= '
$j("#tv'.$tv['id'].'").mm_ddYMap({
	hideField: '.intval($hideField).',
	width: "'.$w.'",
	height: "'.$h.'"
});
			';
		}
		
		$output .= "//  -------------- mm_ddYMap :: End ------------- \n";
		
		$e->output($output . "\n");	// Send the output to the browser
	}
}
?>