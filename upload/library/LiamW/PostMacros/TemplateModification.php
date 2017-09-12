<?php

class LiamW_PostMacros_TemplateModification
{
	public static function footer(array $matches)
	{
		return ' | <a href="https://xf-liam.com/products" target="_blank" class="concealed" title="XF Liam Products">Post Macros <span>&copy2015</span></a> ' . $matches[0];
	}
}