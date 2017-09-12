<?php

class LiamW_Macros_ViewPublic_Edit extends XenForo_ViewPublic_Base
{
	public function prepareParams()
	{
		parent::prepareParams();

		$initialContent = '';

		if (isset($this->_params['macro']))
		{
			$initialContent = $this->_params['macro']['content'];
		}

		$this->_params['editorTemplate'] = XenForo_ViewPublic_Helper_Editor::getEditorTemplate($this, 'content',
			$initialContent);
	}
}