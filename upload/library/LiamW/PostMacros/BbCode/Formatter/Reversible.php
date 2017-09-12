<?php

/**
 * Only renders BBCode that can be converted back into their BBCode varieties.
 */
class LiamW_PostMacros_BbCode_Formatter_Reversible extends XenForo_BbCode_Formatter_Base
{
	protected static $_removeTags = array(
		'quote',
		'code',
		'php',
		'html',
		'media',
		'spoiler',
		'user'
	);

	public function getTags()
	{
		$tags = parent::getTags();

		$return = array();

		foreach ($tags as $key => $tagInfo)
		{
			if (!in_array($key, self::$_removeTags))
			{
				$return[$key] = $tagInfo;
			}
		}

		return $return;
	}

	public function addCustomTags(array $tags)
	{
		return false; // Custom tags cannot be reversibly rendered.
	}

}