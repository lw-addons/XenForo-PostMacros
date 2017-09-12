<?php

class LiamW_PostMacros_ViewPublic_Create extends XenForo_ViewPublic_Base
{
	public function prepareParams()
	{
		parent::prepareParams();

		$this->_params['editorTemplate'] = XenForo_ViewPublic_Helper_Editor::getEditorTemplate($this, 'content', '',
			array('noMacros' => true));
	}
}