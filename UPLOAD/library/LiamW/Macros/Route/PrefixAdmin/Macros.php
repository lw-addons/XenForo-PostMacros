<?php

class LiamW_Macros_Route_PrefixAdmin_Macros implements XenForo_Route_Interface
{

	/**
	 * Match a specific route for an already matched prefix.
	 *
	 * @see XenForo_Route_Interface::match()
	 */
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		// Please, discover what action the user wants to call!
		$action = $router->resolveActionWithIntegerParam($routePath, $request, 'id');
		
		// Call the action in the controller SimpleText_ControllerPublic_SimpleText!
		return $router->getRouteMatch('LiamW_Macros_ControllerAdmin_Controller', $action, 'macros');
	}

	/**
	 * Method to build a link to the specified page/action with the provided
	 * data and params.
	 *
	 * @see XenForo_Route_BuilderInterface
	 */
	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'id', 'name');
	}
}