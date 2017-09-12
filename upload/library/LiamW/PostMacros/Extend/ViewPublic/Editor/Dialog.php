<?php

class LiamW_PostMacros_Extend_ViewPublic_Editor_Dialog extends XFCP_LiamW_PostMacros_Extend_ViewPublic_Editor_Dialog
{
	public function renderHtml()
	{
		if ($this->_templateName != 'editor_dialog_liam_postmacros')
		{
			return parent::renderHtml();
		}

		$this->_renderer->setNeedsContainer(false);

		$template = $this->createTemplateObject($this->_templateName, $this->_params);
		$output = $template->render();

		return $this->_renderer->replaceRequiredExternalPlaceholders($template, $output);
	}

}

if (false)
{
	class XFCP_LiamW_PostMacros_Extend_ViewPublic_Editor_Dialog extends XenForo_ViewPublic_Editor_Dialog
	{
	}
}