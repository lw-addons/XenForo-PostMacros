/**
 * @param {jQuery} $
 *            jQuery Object
 */
!function ($, window, document, _undefined)
{
	$('document')
		.ready(
		function ()
		{
			$("#macroSelect")
				.change(
				function (e)
				{
					var selectedOption = $(
						'#macroSelect option:selected');

					var htmlContent = selectedOption.attr('data-content-parsed');
					var plainContent = selectedOption.attr('data-content');
					var titleText = selectedOption
						.attr('data-title');

					var editor = XenForo
						.getEditorInForm($('#ThreadCreate'));
					if (!editor)
					{
						editor = XenForo
							.getEditorInForm($('#QuickReply'));
					}
					if (!editor)
					{
						editor = XenForo
							.getEditorInForm($('#main_form'));
					}
					if (typeof (editor.val) == "undefined"
						&& plainContent != '-')
					{
						editor
							.insertHtml(htmlContent);
						$('#ctrl_title_thread_create').val(titleText);
					} else if (macrotext != '-')
					{
						editor
							.val(editor.val()
							+ macrotext
								.replace(
								/<br \/>/g,
								''));
						$('#ctrl_title_thread_create').val(titleText);
					}
					$('#macroSelect').val(0);
				});
		});
}(jQuery, this, document);