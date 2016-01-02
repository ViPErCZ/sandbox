/**
 * Created by viper on 7.3.14.
 */
/**
 * appForms jQuery plugin
 *
 * @author Martin Chudoba
 * @version 2.0.0
 * @returns appForms
 */
var appForms = function() {

    /** @var Form to submit */
    var loader;
	var refreshLink;
    this.customSuccess = $();
    this.customError = $();
    this.grid = null;
	this.confirmDialogTitle = '';
	this.confirmDialogMessage = '';
	this.confirmDialogGeneratorLinkUrl = null;

    /**
     *
     * @type {{buttons: Array, init: Function, action: Function, addButton: Function}}
     */
    var actionBar = {
        buttons: [],
        init: function(plugin) {
			var bar = this;
            var addButton = {
                selector: $('#add'),
                click: function(ui, event) {
                    event.preventDefault();
					bar.disablePanel();
                    if(plugin) {
						plugin.customError.alert('close');
						snippetCallback(plugin, ui.attr('href'), this, event);
                    }
                }
            };
            var removeButton = {
                selector: $("#marked, #dropdown-marked"),
                click: function(ui, event) {
                    event.preventDefault();
                    if (plugin) {
                        var selectedArr = {};
                        plugin.grid.find('input[type="checkbox"]').each(function () {
                            if ($(this).prop('checked'))
                                selectedArr[$(this).data('primary')] = $(this).data('primary');
                        });
                        if (Object.keys(selectedArr).length != 0) {
							plugin.customError.alert('close');
                            showConfirmDialog(plugin, JSON.stringify(selectedArr));
                            $('.confirm').modal('show');
                        } else {
                            $(".notSelectedAlert").removeClass('hidden').show();
                        }
                    }
                }
            };
            this.addButton(addButton);
            this.addButton(removeButton);
            this.action();
            return this;
        },
		disablePanel: function() {
			$.each(this.buttons, function(index, value) {
				value.selector.addClass('disabled');
				value.selector.closest('.btn-toolbar').find('.dropdown-toggle').addClass('disabled');
			});
		},
		enablePanel: function() {
			$.each(this.buttons, function(index, value) {
				value.selector.removeClass('disabled');
				value.selector.closest('.btn-toolbar').find('.dropdown-toggle').removeClass('disabled');
			});
		},
        action: function() {
            $.each(this.buttons, function(index, value) {
                value.selector.off('click').on('click', function(event) {
                    if (value.click != undefined && typeof(value.click) == "function")
                    value.click(value.selector, event);
                });
            });
        },
        addButton: function(btn) {
            this.buttons.push(btn);
            btn.selector.off('click').on('click', function(event) {
                if (btn.click != undefined && typeof(btn.click) == "function")
                    btn.click(event);
            });
        },
        getButton: function(index) {
            if (index >= 0 && index < this.buttons.length) {
                return this.buttons[index];
            } else {
                return null;
            }
        }
    };

	/**
	 * snippet request callback
	 * @param plugin
	 * @param link
	 * @param ui
	 * @param e
	 * @returns {jqXHR|null}
	 */
	var snippetCallback = function(plugin, link, ui, e) {
		plugin.grid.find("span:first").html('<div class="well center"><img src="' + loader + '" alt="loading..." /></div>');
		return $.nette.ajax({url: link }, ui, e);
	};

    /** Actions
     * ************************************* */
    var actions = function(obj) {
        var that = obj;
        that.getActionBar().action();

        /** Nastavení close u alert zpráv
         * ********************************************* */
        $('.alert').bind('close.bs.alert', function () {
            $(".alert").fadeOut('fast');
            return false;
        });

        /** Editace řádku
         * ********************************************* */
         that.grid.find(".editable").off("click").on("click", function(event) {
			 event.preventDefault();
			 that.customError.alert('close');
			 snippetCallback(that, $(this).attr('href'), this, event);
        });

        /** Odebrání řádku
         * ********************************************* */
        that.grid.find(".removable").off("click").on("click", function(event) {
            event.preventDefault();
			that.customError.alert('close');
			showConfirmDialogWithLink(that, $(this).attr('href'));
        });
    };

	/**
	 *
	 */
	this.refresh = function() {
		actions(this);
	};

	/**
	 *
	 * @param plugin
	 * @param data
	 */
	var showConfirmDialog = function (plugin, data) {
		var dialog = $('.confirm');
		dialog.modal('show');
		var confirmButton = $('.confirm .modal-footer .confirm-button');
		confirmButton.addClass('disabled').off('click');
		$.ajax({
			type: 'get',
			cache: false,
			url: plugin.confirmDialogGeneratorLinkUrl,
			data: { data: data },
			success: function(msg) {
				confirmButton.data('link', msg);
				confirmButton.removeClass('disabled');
				confirmButton.on('click', function (e) {
					e.preventDefault();
					removeCallback(plugin, dialog, confirmButton.data('link'), this, e);
				});
			}
		});
	};

	/**
	 *
	 * @param plugin
	 * @param link
	 */
	var showConfirmDialogWithLink = function (plugin, link) {
		var dialog = $('.confirm');
		dialog.modal('show');
		var confirmButton = $('.confirm .modal-footer .confirm-button');
		confirmButton.off('click').on('click', function (e) {
			e.preventDefault();
			removeCallback(plugin, dialog, link, this, e);
		});
	};

	/**
	 *
	 * @param plugin
	 * @param dialog
	 * @param link
	 * @param ui
	 * @param e
	 */
	var removeCallback = function (plugin, dialog, link, ui, e) {
		dialog.modal('hide');
		var xhr = $.nette.ajax({url: link }, ui, e);
		if (xhr) {
			xhr.success(function() {
				plugin.customError.each(function() {
					var container = $(this);
					if (container.find('ul').length > 0) {
						container.removeClass('hidden').show();
					}
				});
				plugin.customSuccess.each(function() {
					var container = $(this);
					if (container.find('ul').length > 0) {
						container.removeClass('hidden').show();
						setTimeout(function(){ container.addClass('hidden') }, 1500);
					}
				});
			});
			xhr.error(function() {
				alert('Ajaj nepovedl se ajax dotaz na server.');
			});
		} else {
			alert('Ajaj nepovedl se ajax dotaz na server.');
		}
	};

    this.getActionBar = function() {
        return actionBar;
    };

    /**
     *
     * @param content
     * @param plugin
     */
	var initConfirmDialog = function (content, plugin) {
		var dialog = '<div class="modal fade confirm"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		dialog += '<h4 class="modal-title">' + plugin.confirmDialogTitle + '</h4> </div>';
		dialog += '<div class="modal-body"> <p class="message">' + plugin.confirmDialogMessage + '</p> </div>';
		dialog += '<div class="modal-footer"> <button type="button" class="btn btn-default" data-dismiss="modal">Storno</button>';
		dialog += '<button type="button" class="btn btn-primary confirm-button">OK</button>';
		dialog += '</div></div><!-- /.modal-content --> </div><!-- /.modal-dialog --> </div><!-- /.modal -->';
		content.find("span:first").append(dialog);
	};

	/**
	 * Init plugin
	 * @param preloader
	 * @param form
	 * @param grid
	 * @returns {*}
	 */
    this.init = function(preloader, grid) {
        loader = preloader;
        this.grid = grid;
		this.customSuccess = $(".customSuccess");
		this.customError = $(".customError");
		refreshLink = grid.data('link');

        $(".alert").alert();
        $.nette.load();
        this.getActionBar().init(this);
        actions(this);
		initConfirmDialog(this.grid, this);

        return this;
    };

};
$.appForms = new ($.extend(appForms, $.appForms ? $.appForms : {}));