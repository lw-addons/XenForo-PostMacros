<?php

class LiamW_PostMacros_Route_PrefixAdmin_PostMacros implements XenForo_Route_Interface
{
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		$action = $router->resolveActionWithIntegerParam($routePath, $request, 'admin_macro_id');

		return $router->getRouteMatch('LiamW_PostMacros_ControllerAdmin_AdminMacros', $action);
	}

	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'admin_macro_id',
			'title');
	}
}