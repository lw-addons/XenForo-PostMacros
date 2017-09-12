<?php

class LiamW_Macros_ViewPublic_Preview extends XenForo_ViewPublic_Base
{
	public function renderHtml()
	{
		$bbCodeParser = XenForo_BbCode_Parser::create(XenForo_BbCode_Formatter_Base::create('Base',
			array('view' => $this)));
		$this->_params['contentParsed'] = new XenForo_BbCode_TextWrapper($this->_params['content'], $bbCodeParser);
	}
}