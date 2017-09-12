<?php

class LiamW_PostMacros_ViewPublic_Index extends XenForo_ViewPublic_Base
{
	public function renderHtml()
	{
		$bbCodeParser = new XenForo_BbCode_Parser(XenForo_BbCode_Formatter_Base::create('Base',
			array('bbCode' => false)));

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