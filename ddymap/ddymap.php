<?php
/**
 * mm_ddYMap
 * @version 1.6 (2016-11-25)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/mm_ddymap/1.6
 * 
 * @copyright 2012–2016 DivanDesign {@link https://DivanDesign.biz }
 */

function mm_ddYMap($params){
	//For backward compatibility
	if (
		!is_array($params) &&
		!is_object($params)
	){
		//Convert ordered list of params to named
		$params = \ddTools::orderedParamsToNamed([
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
	$params = \DDTools\ObjectTools::extend([
		'objects' => [
			(object) [
//	 			'fields' => '',
				'mapWidth' => 'auto',
				'mapHeight' => 400,
				'hideOriginalInput' => true,
				'defaultZoom' => '',
				'defaultPosition' => '',
				'roles' => '',
				'templates' => '',
			],
			$params
		]
	]);
	
	if (
		!useThisRule(
			$params->roles,
			$params->templates
		)
	){
		return;
	}
	
	global $modx;
	
	if ($modx->Event->name == 'OnDocFormPrerender'){
		//The Yandex.Maps library including
		$output = includeJsCss(
			'//api-maps.yandex.ru/2.1/?lang=ru_RU',
			'html',
			'api-maps.yandex.ru',
			'2.1'
		);
		//The jQuery.ddYMap library including
		$output .= includeJsCss(
			$modx->config['site_url'] . 'assets/plugins/managermanager/widgets/ddymap/jQuery.ddYMap-1.4.min.js',
			'html',
			'jQuery.ddYMap',
			'1.4'
		);
		//The main js file including
		$output .= includeJsCss(
			$modx->config['site_url'] . 'assets/plugins/managermanager/widgets/ddymap/jQuery.ddMM.mm_ddYMap.js',
			'html',
			'jQuery.ddMM.mm_ddYMap',
			'1.1.6'
		);
		
		$modx->Event->output($output);
	}elseif ($modx->Event->name == 'OnDocFormRender'){
		$output = 
'
//---------- mm_ddYMap :: Begin -----
$j.ddMM.getFieldElems({fields: "' . $params->fields . '"}).mm_ddYMap({
	hideField: ' . intval($params->hideOriginalInput) . ',
	width: "' . $params->mapWidth . '",
	height: "' . $params->mapHeight . '"' . (
		!empty($params->defaultZoom) ?
		', defaultZoom: ' . intval($params->defaultZoom) :
		''
	) . (
		!empty($params->defaultPosition) ?
		', defaultPosition: "' . $params->defaultPosition . '"' :
		''
	) . '
});
//---------- mm_ddYMap :: End -----
';
		
		$modx->Event->output($output);
	}
}
?>