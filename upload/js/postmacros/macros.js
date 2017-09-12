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
			console.log($macros);
			this.$macros = $macros;

			if (!this.$macros.data('editorid'))
			{
				console.error('Post Macros: There is no editor ID on the macro select. Aborting setup & hiding select.');

                this.$macros.xfRemove('xfHide');

				return;
			}

			this.$macros.bind('change', $.context(this, 'macroSelected'));

			this.updateVars();

			this.titleField = $('#ctrl_title_thread_create');
			this.noForm = false;
		},

		updateVars: function ()
		{
			var $forms = $('form');
			var thisClass = this;

			jQuery.each($forms, function (index, form)
			{
				$editor = XenForo.getEditorInForm(form, '#' + thisClass.escape(thisClass.$macros.data('editorid')) + '_html');
				if ($editor)
				{
					thisClass.$editor = $editor;
					thisClass.$form = form;
					return false;
				}
			});

			if (!this.$editor)
			{
				jQuery.each($forms, function (index, form)
				{
					$editor = XenForo.getEditorInForm(form, '#' + thisClass.escape(thisClass.$macros.data('editorid')));
					if ($editor)
					{
						thisClass.$editor = $editor;
						thisClass.$form = form;
						return false;
					}
				});
			}

			if (!this.$editor)
			{
				console.warn("Post Macros: Unable to find editor! Post macros isn't going to work :( Getting rid of select.");

                this.$macros.xfRemove('xfHide');

                return;
			}

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

			XenForo.ajax("index.php?post-macros/use", {
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
				XenForo.ajaxError(ajaxData);
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
							this.$editor.execCommand('mceInsertContent', false, ajaxData.macroContent.toString());
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

            console.log(this.titleField);

			if (ajaxData.threadTitle && this.titleField && this.titleField.val() == "")
			{
				this.titleField.val(ajaxData.threadTitle);
			}

            if (ajaxData.lockThread)
            {
                console.log('Locking thread');
                var existingLockInput = $('#ctrl_macros_set_locked');
                if (existingLockInput) existingLockInput.remove();
                $('<input id="ctrl_macros_set_locked" type="hidden" name="macros_set_locked" value="1" />').appendTo(this.$form);
            }

            if (ajaxData.threadPrefix)
            {
                console.log('Setting prefix...');
                var existingPrefixInput = $('#ctrl_macros_set_prefix');
                if (existingPrefixInput) existingPrefixInput.remove();

                $('<input id="ctrl_macros_set_prefix" type="hidden" name="macros_set_prefix" value="' + ajaxData.threadPrefix + '" />').appendTo(this.$form);
            }

			this.$macros.val(0);
		},

		escape: function (str)
		{
			return str.replace(/(:|\.|\[|\]|,)/g, "\\$1");
		}
	};

	XenForo.PostMacrosExpand = function ($ol)
	{
		this.__construct($ol);
	};
	XenForo.PostMacrosExpand.prototype =
	{
		__construct: function ($ol)
		{
			this.$macroId = $ol.data('macroid');

			this.$expandLink = $ol.find('.ExpandLink');
			this.$expandLink.bind('click', $.context(this, 'expandCollapse'));

			this.$type = $ol.data('type') || 'user';

			this.$ol = $ol;
		},

		expandCollapse: function ($e)
		{
			$e.preventDefault();

			if (this.$expandLink.text() == XenForo.phrases['postMacros_expand'])
			{
				this.expand();
			}
			else
			{
				this.collapse();
			}
		},

		expand: function ()
		{
			if (typeof this.$templateHtml != 'undefined')
			{
				this.$templateHtml.xfFadeDown();
			}
			else
			{
				XenForo.ajax(
					'post-macros/expand',
					{
						'macro_id': this.$macroId,
						'type': this.$type
					},
					$.context(this, 'expandTemplateReceived')
				);
			}

			this.$expandLink.text(XenForo.phrases['postMacros_collapse']);
		},

		collapse: function ()
		{
			this.$templateHtml.xfFadeUp();
			this.$expandLink.text(XenForo.phrases['postMacros_expand']);
		},

		expandTemplateReceived: function (ajaxData)
		{
			if (ajaxData.templateHtml)
			{
				this.$templateHtml = $(ajaxData.templateHtml);
				this.$templateHtml.xfInsert('appendTo', this.$ol);
			}
		}
	};

	XenForo.register('select.MacroSelect', 'XenForo.PostMacros');
	XenForo.register('.MacroExpand', 'XenForo.PostMacrosExpand');
}(jQuery, this, document);