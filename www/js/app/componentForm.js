/**
 * Created by viper on 8.3.14.
 */
/**
 * componentForms jQuery plugin
 *
 * @author Martin Chudoba
 * !
 * @version 1.0.0
 * @returns componentForms
 */
var componentForms = function() {
    var form;
    var content;
    var link;
    var preloader;

    /**
     * Actions
     * @param form
     * @param content
     * @param link
     * @param preloader
     */
    var actions = function(form, content, link, preloader) {

		/** Checkbox bolder */
		form.find("input[type='checkbox']").each(function() {
			$(this).on("click", function() {
				if ($(this).prop("checked")) {
					$(this).closest("label").css("font-weight", "bold");
				} else {
					$(this).closest("label").css("font-weight", "normal");
				}
			});
		});

        /** Cancel */
        form.find("#cancel").off("click").on("click", function(event) {
            event.preventDefault();
            content.html('<div class="well center loading"><img src="'+preloader+'" alt="loading..." /></div>');
            content.load(link, function() {
                $.nette.load();
            });
        });

        /** Submit form */
        form.find("input[type=submit]").off("click").on("click", function(event) {
            event.preventDefault();
			if (tinyMCE) {
				$(tinymce.get()).each(function(i, el){
					document.getElementById(el.id).value = el.getContent();
				});
			}
			form.ajaxSubmit(function(msg) {
                if (msg.result == "success") {
					if (tinyMCE) {
						$(tinymce.get()).each(function(i, el){
							el.destroy();
						});
					}
                    content.html('<div class="well center loading"><img src="'+preloader+'" alt="loading..." /></div>');
                    content.load(link, function() {
                    	$.nette.load();
                	});
                	$("#customSuccess p").text('Úspěšně uloženo...');
                	$("#customSuccess").removeClass('hidden').show();
                	setTimeout(function(){ $("#customSuccess").alert('close') }, 1500);
                } else if (msg.result == "error") {
                    $("#customError p").html(msg.message);
                    $("#customError").removeClass('hidden').show();
                }
            });
        });

        /** Nastavení strong u labelu aktivniho checkboxu */
        form.find(".checkbox input[type='checkbox']").off('change').on("change", function(event) {
            if ($(this).prop("checked")) {
                $(this).closest("td").find("label").css("font-weight", "bold");
            } else {
                $(this).closest("td").find("label").css("font-weight", "normal");
            }
        });
    }

    /**
     * Init plugin
     * @param form
     * @param content
     * @param link
     */
    this.init = function(form, content, link, preloader) {
        this.form = form;
        this.content = content;
        this.link = link;
        this.preloader = preloader;

        form.find('input[type=text]:first').focus();
        form.on("submit", function(e) {
            e.preventDefault();
            form.find('input[type=submit]').trigger("click");
        });
        form.find("input[type='checkbox']").each(function() {
            if ($(this).prop("checked"))
                $(this).closest("label").css("font-weight", "bold");
        });
        actions(form, content, link, preloader);
		$(".chosen-select").chosen();
		if (tinymce) {
			tinymce.init({
				selector:'.mce',
				plugins: [
					"advlist autolink lists link image charmap print preview anchor",
					"searchreplace visualblocks code fullscreen",
					"insertdatetime media table contextmenu paste"
				]
			});
		}
	}
}
$.componentForms = new ($.extend(componentForms, $.componentForms ? $.componentForms : {}));