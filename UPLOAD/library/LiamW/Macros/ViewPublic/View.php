<?php

class LiamW_Macros_ViewPublic_View extends XenForo_ViewPublic_Base
{
	public function renderHtml()
	{
		$bbCodeParser = new XenForo_BbCode_Parser(XenForo_BbCode_Formatter_Base::create('Base',
			array('bbCode' => false)));

		if (!array_key_exists('macros', $this->_params))
		{
			$this->_params['macros'] = array();
		}

		$macros = $this->_params['macros'];

		foreach ($macros as $key => $macro)
		{
			$macros[$key]['content'] = new XenForo_BbCode_TextWrapper($macro['content'], $bbCodeParser);
		}

		$this->_params['macros'] = $macros;

		if (!array_key_exists('adminMacros', $this->_params))
		{
			$this->_params['adminMacros'] = array();
		}

		$adminMacros = $this->_params['adminMacros'];

		foreach ($adminMacros as $key => $adminMacro)
		{

			$adminMacros[$key]['content'] = new XenForo_BbCode_TextWrapper($adminMacro['content'], $bbCodeParser);
		}

		$this->_params['adminMacros'] = $adminMacros;
	}
}