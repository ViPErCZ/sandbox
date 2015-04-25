/**
 * Created by viper on 7.3.14.
 */
/**
 * appForms jQuery plugin
 *
 * @author Martin Chudoba
 * @version 1.2.0
 * @returns appForms
 */
var appForms = function() {

    /** @var Form to submit */
    var form;
    var parentForm;
    var preloader;
    var submitButton;
    var panel;
    var customSuccess;
    var customError;
    var addLink;
    var refreshLink;
    this.grid = null;
    var tabfilterData;
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
            var addButton = {
                selector: $("#add"),
                click: function(event) {
                    event.preventDefault();
                    if(plugin) {
                        plugin.panel.html('<div class="well center panel"><img src="' + plugin.preloader + '" alt="loading..." /></div>');
                        plugin.panel.load(plugin.addLink);
                    }
                }
            };
            var removeButton = {
                selector: $("#marked, #dropdown-marked"),
                click: function(event) {
                    event.preventDefault();
                    if (plugin) {
                        var selectedArr = {};
                        plugin.grid.find('input[type="checkbox"]').each(function () {
                            if ($(this).prop('checked'))
                                selectedArr[$(this).data('primary')] = $(this).data('primary');
                        });
                        if (Object.keys(selectedArr).length != 0) {
                            $("#notselectedAlert").hide();
                            showConfirmDialog(that, JSON.stringify(selectedArr));
                            $('.confirm').modal('show');
                        } else {
                            $("#notselectedAlert").removeClass('hidden').show();
                        }
                    }
                }
            };
            this.buttons.push(addButton);
            this.buttons.push(removeButton);
            this.action();
            return this;
        },
        action: function() {
            $.each(this.buttons, function(index, value) {
                value.selector.off('click').on('click', function(event) {
                    if (value.click != undefined && typeof(value.click) == "function")
                    value.click(event);
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

    /** Znovunačtení datagridu
     * ************************************ */
    var refreshContent = function(obj) {
        obj.grid.html('<div class="well center"><img src="' + obj.preloader + '" alt="loading..." /></div>');
        obj.grid.load(obj.refreshLink, { tabfilter: obj.tabfilterData }, function() {
            $.nette.load();
            actions(obj);
        });
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
            that.panel.html('<div class="well center panel"><img src="' + that.preloader + '" alt="loading..." /></div>');
            that.panel.load($(this).attr('href'));
        });

        /** Odebrání řádku
         * ********************************************* */
        that.grid.find(".removable").off("click").on("click", function(event) {
            event.preventDefault();
			showConfirmDialogWithLink(that, $(this).attr('href'));
        });

        /** Submit form */
        /* ********************************************** */
        that.submitButton.off('click').on("click", function(event){
            event.preventDefault();
            for (var i = 0; i < document.forms.length; i++) {
                Nette.initForm(document.forms[i]);
            }
            that.form.ajaxSubmit(function(msg) {
                if (msg.result == "success") {
                    that.parentForm.modal('hide');
                    that.parentForm.on('hidden.bs.modal', function (param) {
                        if (event != null && event.target.id == "submit") {
                            event = null;
                            //refreshContent(obj);
                            that.customSuccess.find('p').text('Úspěšně uloženo...');
                            that.customSuccess.removeClass('hidden').show();
                            setTimeout(function(){ that.customSuccess.alert('close') }, 1500);
                        }
                        that.parentForm.find('.modal-body').html('<p>Načítám…</p>');
                    });
                } else if (msg.result == "error") {
                    that.parentForm.modal('hide');
                    that.customError.find('p').text(msg.message);
                    that.customError.removeClass('hidden').show();
                }
            });
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
	showConfirmDialog = function (plugin, data) {
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
					removeCallback(plugin, dialog, confirmButton.data('link'));
				});
			}
		});
	};

	/**
	 *
	 * @param plugin
	 * @param link
	 */
	showConfirmDialogWithLink = function (plugin, link) {
		var dialog = $('.confirm');
		dialog.modal('show');
		var confirmButton = $('.confirm .modal-footer .confirm-button');
		confirmButton.off('click').on('click', function (e) {
			e.preventDefault();
			removeCallback(plugin, dialog, link);
		});
	};

	/**
	 *
	 * @param plugin
	 * @param dialog
	 * @param link
	 */
	removeCallback = function (plugin, dialog, link) {
		var that = plugin;
		$.ajax({
			type: 'get',
			cache: false,
			url: link,
			success: function(msg) {
				dialog.modal('hide');
				dialog.on('hidden.bs.modal', function (e) {
					if (msg.result == "success") {
						that.customSuccess.find('p').text('Úspěšně odebráno...');
						that.customSuccess.removeClass('hidden').show();
						refreshContent(plugin);
						setTimeout(function(){ that.customSuccess.alert('close') }, 1500);
					} else {
						that.customError.find('p').text(msg.message);
						that.customError.removeClass('hidden').show();
					}
				});
			},
			error: function(e, stastus) {
				dialog.modal('hide');
				alert('Ajaj nepovedl se ajax dotaz na server: ' + stastus);
			}
		});
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
		dialog += '</div> </div><!-- /.modal-content --> </div><!-- /.modal-dialog --> </div><!-- /.modal -->';
		content.append(dialog);
	};

	/**
	 * Init plugin
	 * @param preloader
	 * @param form
	 * @param panel
	 * @param customSuccess
	 * @param customError
	 * @param addLink
	 * @param refreshLink
	 * @param grid
	 * @param tabfilterData
	 * @returns {*}
	 */
    this.init = function(preloader, form, panel, customSuccess, customError, addLink, refreshLink, grid, tabfilterData) {
        this.preloader = preloader;
        this.parentForm = form;
        this.form = form.find("form");
        this.submitButton = this.form.find("#submit");
        this.panel = panel;
        this.customSuccess = customSuccess;
        this.customError = customError;
        this.addLink = addLink;
        this.refreshLink = refreshLink;
        this.grid = grid;
        this.tabfilterData = tabfilterData;

        $(".alert").alert();
        $.nette.load();
        this.getActionBar().init(this);
        actions(this);
		initConfirmDialog(this.grid, this);

        return this;
    };

};
$.appForms = new ($.extend(appForms, $.appForms ? $.appForms : {}));