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
											var macrotexthtml = $(
													'#macroSelect option:selected')
													.attr('data-valuehtml');

											macrotext = $(
													'#macroSelect option:selected')
													.val();

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
											if (typeof (editor.val) == "undefined"
													&& macrotext != '-') {

												editor
														.insertHtml(macrotexthtml);

												var titleText = $(
														'#macroSelect option:selected')
														.attr('data-valuetitle');
												var threadTitleBox = $('#ctrl_title_thread_create');
												threadTitleBox.val(titleText);
											} else if (macrotext != '-') {
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