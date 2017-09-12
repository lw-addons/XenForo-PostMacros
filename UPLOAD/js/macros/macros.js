/**
 * @param {jQuery} $
 *            jQuoery Object
 */
!function($, window, document, _undefined) {
	$('document')
			.ready(
					function() {
						$("#macroSelect")
								.change(
										function(e) {
											var editor = XenForo
													.getEditorInForm($('#ThreadCreate'));
											if (!editor) {
												editor = XenForo
														.getEditorInForm($('#QuickReply'));
											}
											if (!editor) {
												editor = XenForo
														.getEditorInForm($('#main_form'));
											}
											if (typeof (editor.val) == "undefined") {
												var macrotext = $(
														'#macroSelect option:selected')
														.attr('data-valuehtml');
												editor.insertHtml(macrotext);

												var titleText = $(
														'#macroSelect option:selected')
														.attr('data-valuetitle');
												var threadTitleBox = $('#ctrl_title_thread_create');
												threadTitleBox.val(titleText);
											} else {
												macrotext = $(
														'#macroSelect option:selected')
														.val();
												editor
														.val(editor.val()
																+ macrotext
																		.replace(
																				/<br \/>/g,
																				''));

												var titleText = $(
														'#macroSelect option:selected')
														.attr('data-valuetitle');
												var threadTitleBox = $('#ctrl_title_thread_create');
												threadTitle.val(titleText);
											}
											$('#macroSelect').val('-');
										});
					});
}(jQuery, this, document);