/**
 * Created by viper on 8.3.14.
 */
/**
 * componentForms jQuery plugin
 *
 * @author Martin Chudoba
 * !
 * @version 2.0.0
 * @returns componentForms
 */
var componentForms = function () {
	var form;
	var content;
	var link;
	var loader;
	this.tinymceInitCustomCallback = null;

	/**
	 *
	 */
	var actions = function () {
		var that = this;

		/** Checkbox bolder */
		form.find("input[type='checkbox']").each(function () {
			$(this).on("click", function () {
				if ($(this).prop("checked")) {
					$(this).closest("label").css("font-weight", "bold");
				} else {
					$(this).closest("label").css("font-weight", "normal");
				}
			});
		});

		/** Cancel */
		form.find("#cancel").off("click").on("click", function (event) {
			event.preventDefault();
			$(".customError").alert('close');
			var xhr = snippetCallback(that, link, this, event);
			if (xhr) {
				xhr.success(function () {
					var bar = $.appForms.getActionBar();
					if (bar) {
						bar.enablePanel();
					}
				});
			}
		});

		/** Submit form */
		form.find("input[type=submit], button[type=submit]").off("click").on("click", function (event) {
			event.preventDefault();
			$(".customError").alert('close');
			if (tinyMCE) {
				$(tinymce.get()).each(function (i, el) {
					document.getElementById(el.id).value = el.getContent();
				});
			}

			var xhr = $.nette.ajax({url: form.attr('action')}, this, event);
			if (xhr) {
				xhr.success(function (payload) {
					if (payload.result == "success") {
						if (tinyMCE) {
							$(tinymce.get()).each(function (i, el) {
								el.destroy();
							});
						}
						var xhr = snippetCallback(that, link, form.find("#cancel"), event, {message: 'Úspěšně uloženo...'});
						if (xhr) {
							xhr.success(function () {
								$(".customSuccess:first").removeClass('hidden').show();
								setTimeout(function () {
									$(".customSuccess:first").addClass('hidden')
								}, 1500);
								var bar = $.appForms.getActionBar();
								if (bar) {
									bar.enablePanel();
								}
							});
						}
					} else if (payload.result == "error") {
						$(".customError:first div:first").html(payload.message);
						$(".customError:first").removeClass('hidden').show();
					}
				});
			}
		});
	};

	/**
	 *
	 * @param plugin
	 * @param link
	 * @param ui
	 * @param e
	 * @param data
	 * @returns {jqXHR|null}
	 */
	var snippetCallback = function (plugin, link, ui, e, data) {
		content.find("span:first").html('<div class="well center"><img src="' + loader + '" alt="loading..." /></div>');
		return $.nette.ajax({url: link, data: data}, ui, e);
	};

	/**
	 * Init plugin
	 * @param ui
	 * @param grid
	 * @param preloader
	 */
	this.init = function (ui, grid, preloader) {
		form = ui;
		content = grid;
		link = content.data('link');
		loader = preloader;

		$(".alert").alert();
		$('.customError').off('close.bs.alert').on('close.bs.alert', function () {
			$(".customError:first").addClass('hidden');
			return false;
		});
		$.nette.load();
		form.find('input[type=text]:first').focus();
		form.find("input[type='checkbox']").each(function () {
			if ($(this).prop("checked"))
				$(this).closest("label").css("font-weight", "bold");
		});
		actions(form, content, link, preloader);
		$(".chosen-select").chosen();
		if (tinymce) {
			tinyMCE.editors = []; // remove any existing references
			tinymce.init({
				selector: '.mce',
				language: 'cs',
				init_instance_callback: typeof this.tinymceInitCustomCallback == "function" ? this.tinymceInitCustomCallback : '',
				toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | macros",
				plugins: [
					"fullpage, advlist autolink lists link image charmap print preview anchor",
					"searchreplace visualblocks code fullscreen",
					"insertdatetime media table contextmenu paste"
				]
			});
		}
	}
};
$.componentForms = new ($.extend(componentForms, $.componentForms ? $.componentForms : {}));