<?php

abstract class LiamW_Shared_Branding
{

	static $brandingShown = false;

	public static function helperRenderBranding()
	{
		if (self::$brandingShown)
			return false;
		
		return '<br><a href="https://xf-liam.com" target="_blank" style="float: left;" class="concealed">Certain addons by Liam W are installed here.</a>';
	}

}