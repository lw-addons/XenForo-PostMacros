<?php

class LiamW_PostMacros_Route_PrefixAdmin_PostMacros implements XenForo_Route_Interface
{
	protected $_subComponents = array(
		'admin' => array(
			'controller' => 'LiamW_PostMacros_ControllerAdmin_AdminMacros',
			'intId' => 'admin_macro_id',
			'title' => 'title'
		),
		'user' => array(
			'controller' => 'LiamW_PostMacros_ControllerAdmin_UserMacros',
			'intId' => 'macro_id',
			'title' => 'title'
		)
	);

	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		$action = $router->getSubComponentAction($this->_subComponents, $routePath, $request, $controllerName);
		$action = $router->resolveActionAsPageNumber($action, $request);

		return $router->getRouteMatch($controllerName, $action, 'applications');
	}

	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		if (isset($extraParams['page']) && !$extraParams['page'])
		{
			unset($extraParams['page']);
		}

		return XenForo_Link::buildSubComponentLink($this->_subComponents, $outputPrefix, $action, $extension, $data);
	}
}