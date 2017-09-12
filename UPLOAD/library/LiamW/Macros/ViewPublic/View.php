<?php

class LiamW_Macros_ViewPublic_View extends XenForo_ViewPublic_Base
{

	public function prepareParams()
	{
		$initial = '';
		
		parent::prepareParams();
		
		$viewparams = $this->getParams();
		
		$initial = @$viewparams['macro']['macro'];
		
		$this->_params['editorTemplate'] = XenForo_ViewPublic_Helper_Editor::getEditorTemplate($this, 'macro', $initial);
	}

	public function renderHtml()
	{
		$bbCodeParser = new XenForo_BbCode_Parser(XenForo_BbCode_Formatter_Base::create('Base'));
		
		if (! array_key_exists('macros', $this->_params))
		{
			$this->_params['macros'] = array();
		}
		
		$macros = $this->_params['macros'];
		
		foreach ($macros as $key => $macro)
		{
			$macros[$key]['macro'] = new XenForo_BbCode_TextWrapper($macro['macro'], $bbCodeParser);
		}
		
		$this->_params['macros'] = $macros;
		
		if (! array_key_exists('adminmacros', $this->_params))
		{
			$this->_params['adminmacros'] = array();
		}
		
		$adminmacros = $this->_params['adminmacros'];
		
		foreach ($adminmacros as $key => $adminmacro)
		{
			
			$adminmacros[$key]['content'] = new XenForo_BbCode_TextWrapper($adminmacro['content'], $bbCodeParser);
		}
		
		$this->_params['adminmacros'] = $adminmacros;
	}

}