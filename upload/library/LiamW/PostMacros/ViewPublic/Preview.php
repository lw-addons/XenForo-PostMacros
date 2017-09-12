<?php

class LiamW_PostMacros_ViewPublic_Preview extends XenForo_ViewPublic_Base
{
	public function renderHtml()
	{
		$formatter = XenForo_BbCode_Formatter_Base::create('XenForo_BbCode_Formatter_Base');
		$parser = XenForo_BbCode_Parser::create($formatter);

		$this->_params['contentParsed'] = $parser->render($this->_params['content']);
	}
}