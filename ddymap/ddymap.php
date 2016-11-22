<?php
/**
 * mm_ddYMap
 * @version 1.5b (2015-05-08)
 * 
 * @desc A widget for ManagerManager plugin allowing Yandex Maps integration.
 * 
 * @uses MODXEvo.plugin.ManagerManager >= 0.6.2.
 * 
 * @param $fields {string_commaSeparated} — TV names to which the widget is applied. @required
 * @param $roles {string_commaSeparated} — The roles that the widget is applied to (when this parameter is empty then widget is applied to the all roles). Default: ''.
 * @param $templates {string_commaSeparated} — Id of the templates to which this widget is applied (when this parameter is empty then widget is applied to the all templates). Default: ''.
 * @param $mapWidth {integer|'auto'} — Width of the map container. Default: 'auto'.
 * @param $mapHeight {integer} — Height of the map container. Default: 400.
 * @param $hideOriginalInput {boolean} — Original coordinates field hiding status (true — hide, false — show). Default: true.
 * 
 * @event OnDocFormPrerender
 * @event OnDocFormRender
 * 
 * @link http://code.divandesign.biz/modx/mm_ddymap/1.5b
 * 
 * @copyright 2012–2015 DivanDesign {@link http://www.DivanDesign.biz }
 */

function mm_ddYMap(
	$fields,
	$roles = '',
	$templates = '',
	$mapWidth = 'auto',
	$mapHeight = '400',
	$hideOriginalInput = true
){
	if (!useThisRule($roles, $templates)){return;}
	
	global $modx;
	$e = &$modx->Event;
	
	if ($e->name == 'OnDocFormPrerender'){
		//The Yandex.Maps library including
		$output = includeJsCss('//api-maps.yandex.ru/2.1/?lang=ru_RU', 'html', 'api-maps.yandex.ru', '2.1');
		//The jQuery.ddYMap library including
		$output .= includeJsCss($modx->config['site_url'].'assets/plugins/managermanager/widgets/ddymap/jQuery.ddYMap-1.4.min.js', 'html', 'jQuery.ddYMap', '1.4');
		//The main js file including
		$output .= includeJsCss($modx->config['site_url'].'assets/plugins/managermanager/widgets/ddymap/jQuery.ddMM.mm_ddYMap.js', 'html', 'jQuery.ddMM.mm_ddYMap', '1.1');
		
		$e->output($output);
	}else if ($e->name == 'OnDocFormRender'){
		global $mm_current_page;
		
		$output = '';
		
		//if we've been supplied with a string, convert it into an array
		$fields = makeArray($fields);
		
		$usedTvs = tplUseTvs($mm_current_page['template'], $fields, '', 'id', 'name');
		if ($usedTvs == false){return;}
		
		$output .= '//---------- mm_ddYMap :: Begin -----'.PHP_EOL;
		
		//Iterate over supplied TVs instead of doing so to the result of tplUseTvs() to maintain rendering order.
		foreach ($fields as $field){
			//If this $field is used in a current template
			if (isset($usedTvs[$field])){
				$output .= 
'
$j("#tv'.$usedTvs[$field]['id'].'").mm_ddYMap({
	hideField: '.intval($hideOriginalInput).',
	width: "'.$mapWidth.'",
	height: "'.$mapHeight.'"
});
';
			}
		}
		
		$output .= '//---------- mm_ddYMap :: End -----'.PHP_EOL;
		
		$e->output($output);
	}
}
?>