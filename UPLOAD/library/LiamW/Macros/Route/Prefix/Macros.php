<?php

/**
 * Route prefix class.
 *
 * @author Liam W
 * @package Post Macros
 *
 */
class LiamW_Macros_Route_Prefix_Macros implements XenForo_Route_Interface
{

	/**
	 * Match a specific route for an already matched prefix.
	 *
	 * @see XenForo_Route_Interface::match()
	 */
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		$action = $router->resolveActionWithIntegerParam($routePath, $request, 'macro_id');
		
		// Set to the account tab, as that's where the link is...
		return $router->getRouteMatch('LiamW_Macros_ControllerPublic_Macros', $action, 'account');
	}

	/**
	 * Method to build a link to the specified page/action with the provided
	 * data and params.
	 *
	 * @see XenForo_Route_BuilderInterface
	 */
	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'macro_id', 'name');
	}

}