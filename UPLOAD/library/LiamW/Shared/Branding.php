<?php

abstract class LiamW_Shared_Branding
{

	private static $_brandingShown = false;
	private static $_helperAdded = false;

	public static function helperRenderBranding()
	{
		if (self::$_brandingShown)
		{
			return '';
		}

		self::$_brandingShown = true;

		return '<br><a href="https://xf-liam.com" target="_blank" style="float: left;" class="concealed">Certain addons by Liam W are installed here.</a>';
	}

	public static function addHelper()
	{
		if (self::$_helperAdded)
		{
			return;
		}

		XenForo_Template_Helper_Core::$helperCallbacks += array(
			'liam_branding' => array(
				'LiamW_Shared_Branding',
				'helperRenderBranding'
			)
		);

		self::$_helperAdded = true;
	}

}