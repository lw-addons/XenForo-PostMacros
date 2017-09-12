<?php

class LiamW_PostMacros_TemplateModification
{
	public static function footer(array $matches)
	{
		return ' | <a href="https://xf-liam.com/products" target="_blank" class="concealed" title="XF Liam Products">Post Macros by Liam W <span>&copy2013-2015 Liam W</span></a> ' . $matches[0];
	}
}