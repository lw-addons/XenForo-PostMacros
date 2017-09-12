<?php

class LiamW_PostMacros_ViewPublic_Edit extends XenForo_ViewPublic_Base
{
	public function prepareParams()
	{
		parent::prepareParams();

		$this->_params['editorTemplate'] = XenForo_ViewPublic_Helper_Editor::getEditorTemplate($this, 'content',
			$this->_params['macro']['content'], array('noMacros' => true));
	}
}