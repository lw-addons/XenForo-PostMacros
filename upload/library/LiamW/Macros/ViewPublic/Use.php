<?php

class LiamW_Macros_ViewPublic_Use extends XenForo_ViewPublic_Base
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
			$bbCodeParser = new XenForo_BbCode_Parser(XenForo_BbCode_Formatter_Base::create('Base', $options));
			$macroContent = new XenForo_BbCode_TextWrapper($macroContent, $bbCodeParser);
		}

		return XenForo_ViewRenderer_Json::jsonEncodeForOutput(array(
			'macroContent' => $macroContent,
			'threadTitle' => $macro['thread_title'],
			'lockThread' => $macro['lock_thread'],
			'applyPrefix' => $macro['apply_prefix']
		), false);
	}

	protected function _compileVariables($macro, array $thread, array $forum)
	{
		$threadUser = isset($thread['username']) ? $thread['username'] : '';
		$threadName = isset($thread['title']) ? $thread['title'] : '';
		$forumName = isset($forum['title']) ? $forum['title'] : '';

		$macro = str_replace(array(
			"{threaduser}",
			"{threadname}",
			"{forumname}"
		), array(
			$threadUser,
			$threadName,
			$forumName
		), $macro);

		return $macro;
	}

}