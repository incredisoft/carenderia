<?php
import('services/common/ResourceService.php');

class MenuService{
	function getMenus($location){
		$resourceSvc = new ResourceService();
		$infos = $resourceSvc->getResources($location, '/info/');
		
		$menus = array();
		
		foreach($infos as $info){
			$content = $resourceSvc->getContents($info['location']);
			$info_ini = $resourceSvc->parseConfigString($content);
			array_push($menus, $info_ini);
		}
		
		return $menus;
	}
}
?>