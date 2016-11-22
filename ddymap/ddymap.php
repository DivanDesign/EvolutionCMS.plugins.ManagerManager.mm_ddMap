<?php
/**
 * mm_ddYMap
 * @version 1.5b (2015-05-08)
 * 
 * @desc A widget for ManagerManager plugin allowing Yandex Maps integration.
 * 
 * @uses PHP >= 5.4.
 * @uses MODXEvo.plugin.ManagerManager >= 0.7.
 * 
 * @param $params {array_associative|stdClass} — The object of params. @required
 * @param $params['fields'] {string_commaSeparated} — TV names to which the widget is applied. @required
 * @param $params['mapWidth'] {integer|'auto'} — Width of the map container. Default: 'auto'.
 * @param $params['mapHeight'] {integer} — Height of the map container. Default: 400.
 * @param $params['hideOriginalInput'] {boolean} — Original coordinates field hiding status (true — hide, false — show). Default: true.
 * @param $params['roles'] {string_commaSeparated} — The roles that the widget is applied to (when this parameter is empty then widget is applied to the all roles). Default: ''.
 * @param $params['templates'] {string_commaSeparated} — Id of the templates to which this widget is applied (when this parameter is empty then widget is applied to the all templates). Default: ''.
 * 
 * @event OnDocFormPrerender
 * @event OnDocFormRender
 * 
 * @link http://code.divandesign.biz/modx/mm_ddymap/1.5b
 * 
 * @copyright 2012–2015 DivanDesign {@link http://www.DivanDesign.biz }
 */

function mm_ddYMap($params){
	//For backward compatibility
	if (
		!is_array($params) &&
		!is_object($params)
	){
		//Convert ordered list of params to named
		$params = ddTools::orderedParamsToNamed([
			'paramsList' => func_get_args(),
			'compliance' => [
				'fields',
				'roles',
				'templates',
				'mapWidth',
				'mapHeight',
				'hideOriginalInput'
			]
		]);
	}
	
	//Defaults
	$params = (object) array_merge([
// 		'fields' => '',
		'mapWidth' => 'auto',
		'mapHeight' => 400,
		'hideOriginalInput' => true,
		'roles' => '',
		'templates' => ''
	], (array) $params);
	
	if (!useThisRule($params->roles, $params->templates)){return;}
	
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
		$params->fields = makeArray($params->fields);
		
		$usedTvs = tplUseTvs($mm_current_page['template'], $params->fields, '', 'id', 'name');
		if ($usedTvs == false){return;}
		
		$output .= '//---------- mm_ddYMap :: Begin -----'.PHP_EOL;
		
		//Iterate over supplied TVs instead of doing so to the result of tplUseTvs() to maintain rendering order.
		foreach ($params->fields as $field){
			//If this $field is used in a current template
			if (isset($usedTvs[$field])){
				$output .= 
'
$j("#tv'.$usedTvs[$field]['id'].'").mm_ddYMap({
	hideField: '.intval($params->hideOriginalInput).',
	width: "'.$params->mapWidth.'",
	height: "'.$params->mapHeight.'"
});
';
			}
		}
		
		$output .= '//---------- mm_ddYMap :: End -----'.PHP_EOL;
		
		$e->output($output);
	}
}
?>