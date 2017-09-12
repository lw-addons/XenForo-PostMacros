<?php

class LiamW_PostMacros_ViewPublic_Index extends XenForo_ViewPublic_Base
{
	public function renderHtml()
	{
		if (XenForo_Application::getOptions()->postMacros_collapsable)
		{
			return;
		}

		$bbCodeParser = XenForo_BbCode_Parser::create(XenForo_BbCode_Formatter_Base::create('Base',
			array('view' => $this)));

		$macros = $this->_params['macros'];

		foreach ($macros['user'] as $key => $macro)
		{
			$macros['user'][$key]['content'] = new XenForo_BbCode_TextWrapper($macro['content'], $bbCodeParser);
		}

		foreach ($macros['admin'] as $key => $macro)
		{
			$macros['admin'][$key]['content'] = new XenForo_BbCode_TextWrapper($macro['content'], $bbCodeParser);
		}

		$this->_params['macros'] = $macros;
	}
}