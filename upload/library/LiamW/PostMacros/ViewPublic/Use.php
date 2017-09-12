<?php

class LiamW_PostMacros_ViewPublic_Use extends XenForo_ViewPublic_Base
{
	public function renderJson()
	{
		$macro = $this->_compileVariables($this->_params['macro'], $this->_params['thread'], $this->_params['forum']);

		$macroContent = $macro['content'];

		$options = array(
			'bbCode' => array(
				'bbCodes' => array(),
			),
			'view' => $this
		);

		if ($this->_params['render'])
		{
			$bbCodeParser = new XenForo_BbCode_Parser(XenForo_BbCode_Formatter_Base::create('LiamW_PostMacros_BbCode_Formatter_Reversible',
				$options));
			$macroContent = new XenForo_BbCode_TextWrapper($macroContent, $bbCodeParser);
		}

		$output = array(
			'macroContent' => $macroContent,
			'threadTitle' => $macro['thread_title'],
			'threadPrefix' => $macro['thread_prefix'],
			'lockThread' => $macro['lock_thread']
		);

		return XenForo_ViewRenderer_Json::jsonEncodeForOutput($output, false);
	}

	protected function _compileVariables($macro, array $thread, array $forum)
	{
		$threadUser = isset($thread['username']) ? $thread['username'] : '';
		$threadName = isset($thread['title']) ? $thread['title'] : '';
		$forumName = isset($forum['title']) ? $forum['title'] : '';

		$macro['content'] = str_replace(array(
			"{threadcreator}",
			"{threadtitle}",
			"{forumtitle}"
		), array(
			$threadUser,
			$threadName,
			$forumName
		), $macro['content']);

		return $macro;
	}

}