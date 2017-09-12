<?php

class LiamW_PostMacros_Option_SelectorMode
{
	public static function renderOption(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
	{
		$activeAddons = XenForo_Application::get('addOns');

		$canUseNew = XenForo_Application::$versionId >= 1030070 && !isset($activeAddons['sedo_tinymce_quattro']);

		return XenForo_ViewAdmin_Helper_Option::renderOptionTemplateInternal('liam_postMacros_option_selectormode',
			$view, $fieldPrefix, $preparedOption, $canEdit, array('canUseNew' => $canUseNew)
		);
	}

	public static function verifyOption(&$optionValue, XenForo_DataWriter $dw)
	{
		if (XenForo_Application::$versionId <= 1030070 || isset($activeAddons['sedo_tinymce_quattro']))
		{
			$optionValue = 'classic';
		}

		return true;
	}
}