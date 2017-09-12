<?php

class LiamW_Macros_ViewPublic_View extends XenForo_ViewPublic_Base
{

	public function prepareParams()
	{
		parent::prepareParams();

		$viewParams = $this->getParams();

		$initial = @$viewParams['macro']['macro'];

		$this->_params['editorTemplate'] = XenForo_ViewPublic_Helper_Editor::getEditorTemplate($this, 'macro',
			$initial);
	}

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
			$macros[$key]['macro'] = new XenForo_BbCode_TextWrapper($macro['macro'], $bbCodeParser);
		}

		$this->_params['macros'] = $macros;

		if (!array_key_exists('adminmacros', $this->_params))
		{
			$this->_params['adminmacros'] = array();
		}

		$adminMacros = $this->_params['adminmacros'];

		foreach ($adminMacros as $key => $adminMacro)
		{

			$adminMacros[$key]['content'] = new XenForo_BbCode_TextWrapper($adminMacro['content'], $bbCodeParser);
		}

		$this->_params['adminmacros'] = $adminMacros;
	}

}