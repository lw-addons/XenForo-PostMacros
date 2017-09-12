<?php

class LiamW_PostMacros_ViewPublic_Expand extends XenForo_ViewPublic_Base
{
	public function renderHtml()
	{
		$bbCodeParser = XenForo_BbCode_Parser::create(XenForo_BbCode_Formatter_Base::create('Base',
			array('view' => $this)));

		$this->_params['macro']['content'] = new XenForo_BbCode_TextWrapper($this->_params['macro']['content'], $bbCodeParser);
	}
}