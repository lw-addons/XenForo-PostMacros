/**
 * @param {jQuery} $
 *            jQuery Object
 */
!function ($, window, document, _undefined)
{
	XenForo.PostMacros = function ($macros)
	{
		this.__construct($macros);
	};
	XenForo.PostMacros.prototype =
	{
		__construct: function ($macros)
		{
			this.$macros = $macros;
			this.$macros.bind('change', $.context(this, 'macroSelected'));

			this.updateVars();

			this.titleField = $('#ctrl_title_thread_create');
			this.noForm = false;
		},

		updateVars: function ()
		{
			$forms = $('form');

			var thisClass = this;

			jQuery.each($forms, function (index, form)
			{
				$editor = XenForo.getEditorInForm(form);
				if ($editor)
				{
					thisClass.$editor = $editor;
					thisClass.$form = form;
					return false;
				}
			});

			delete(thisClass);

			if (!this.$editor)
			{
				console.warn("Unable to find form to insert array into!");
				this.noForm = true;
			}

			//console.log(this.$editor);

			this.type = typeof (this.$editor.val) == "undefined" ? 'html' : 'plain';
		},

		macroSelected: function ()
		{
			if (this.noForm)
			{
				return;
			}

			var macroId = this.$macros.val();

			if (macroId == 0)
			{
				return;
			}

			this.updateVars();

			XenForo.ajax("post-macros/use", {
				'macro_id': macroId,
				'render': this.type == 'html' ? 1 : 0,
				'type': this.$macros.find(':selected').data('type'),
				'formAction': this.$macros.closest('form').attr('action')
			}, $.context(this, 'successCallback'));
		},

		successCallback: function (ajaxData, textStatus)
		{
			if (this.noForm)
			{
				return;
			}

			if (ajaxData.templateHtml)
			{
				this.$macros.val(0);
				XenForo.createOverlay(null, ajaxData.templateHtml, {'title': XenForo.phrases['error']}).xfShow();
				return;
			}

			if (ajaxData.macroContent)
			{
				switch (this.type)
				{
					case "html":
						try
						{
							this.$editor
								.insertHtml(ajaxData.macroContent.toString());
						} catch (TypeError)
						{
							// TinyMce Support
							this.editor.execCommand('mceInsertContent', false, ajaxData.macroContent.toString());
						}
						break;
					case "plain":
						this.$editor
							.val(this.$editor.val()
							+ ajaxData.macroContent.toString()
								.replace(
								/<br \/>/g,
								''));
						break;
				}
			}


			if (ajaxData.threadTitle && this.titleField)
			{
				this.titleField.val(ajaxData.threadTitle);
			}

			if (ajaxData.lockThread)
			{
				console.log('Locking thread');
				$('<input type="hidden" name="_set[discussion_open]" value="1" />').appendTo(this.$form);
				$('<input type="hidden" name="discussion_open" value="0" />').appendTo(this.$form);
			}

			if (ajaxData.threadPrefix)
			{
				console.log('Setting prefix...');
				$('<input type="hidden" name="set_prefix" value="' + ajaxData.threadPrefix + '" />').appendTo(this.$form);
			}

			this.$macros.val(0);
		}
	}

	XenForo.register('#MacroSelect', 'XenForo.PostMacros');
}
(jQuery, this, document);