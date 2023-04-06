let CMS = function () {

    //Variables

    let loadingImage = '<img src="/assets/img/loading.gif" alt="loading" />';

    //Define Modals
    let modal = $('#modal');
    let modalTitle = $('#modal #myModalLabel');
    let modalBody = $('#modal .modal-body');
    let modalFooter = $('#modal .modal-footer');
    let modalDialog = $('#modal .modal-dialog');
    let modalCloseButton = $('#modal .modal-header button');

    let toggleSpeed = 400;
    let delaySpeed = 1000;

    //Methods

    let ToggleFilterArea = function () {

        let _this = $(this);
        let target = _this.attr('data-target');
        $('#' + target).toggle('slow');

    }

    let ShowFilter = function () {

        let target = $(this).attr('data-target');
        $('#' + target).toggle(toggleSpeed);

    }

    let ShowModal = function (title, body, footer = '', showCloseButton = true, sizeClass = 'modal-lg', saveButtonObject = null) {

        let saveButtonId, saveButtonText;

        modalTitle.html(title);
        modalBody.html(body);
        if (saveButtonObject === null) {
            saveButtonId = "btnSave";
            saveButtonText = 'Speichern';
        } else if (typeof saveButtonObject == 'string') {
            saveButtonId = saveButtonObject;
            saveButtonText = 'Speichern';
        } else if (typeof saveButtonObject == 'object') {
            saveButtonId = saveButtonObject.id || 'btnSave';
            saveButtonText = saveButtonObject.text || 'Speichern';

        }

        if (footer.length > 0) {
            if (footer == 'save-cancel') {
                footer = '<button id="' + saveButtonId + '" class="btn btn-success"><i class="fa fa-save"></i> ' + saveButtonText + '</button>' + '<button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><i class="fa fa-ban"></i> Abbrechen</button>';
            }
            if (footer == 'cancel') {
                footer = '<button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><i class="fa fa-ban"></i> Abbrechen</button>';
            }
            modalFooter.html(footer).show();
        } else {
            modalFooter.hide();
        }

        if (showCloseButton) {
            modalCloseButton.show();
        } else {
            modalCloseButton.hide();
        }

        if (sizeClass != 'modal-lg' && sizeClass != 'modal-full' && sizeClass != 'modal-sm' && sizeClass != '') {
            console.log('Modal Size has been adjusted to be normal');
            sizeClass = '';
        }
        modalDialog.removeClass().addClass('modal-dialog').addClass(sizeClass).addClass('modal-scroll animated fadeInDown');
        modal.modal('show').removeClass('hide');


    }

    let ToggleTarget = function () {
        let target = $(this).attr("data-target");
        console.log({target});
        $('#' + target).toggle(toggleSpeed);

    }

    let HandleBtnCleanInput = function () {

        $('body').on("click", '.btnCleanInput', function () {
            $(this).prev().val('');
        });

    }


    let HandlePulsate = function () {
        $('.pulsateCrazy').pulsate({
            color: "#c00",
            reach: 50,
            repeat: 5,
            speed: 300,
            glow: true
        });
    }

    return {

        init: function () {

            $('.btnFilter').click(ToggleFilterArea);

            $('body').on('click', '#btnShowFilter', ShowFilter);
            $('body').on('click', '.toggle', ToggleTarget);

            this.HandleTootips();

            this.initHandleSelect2();

            HandleBtnCleanInput();

            HandlePulsate();

        },

        ShowModal: function (title, body, footer, showCloseButton, size, saveButtonId) {
            ShowModal(title, body, footer, showCloseButton, size, saveButtonId);
        },

        ClearAndHideModal: function () {
            modalTitle.html('');
            modalBody.html('');
            modal.modal('hide').removeClass('show');
            //modalDialog.addClass('modal-lg');
        },

        HandleTootips: function () {
            $('.tooltips').tooltip();
        },

        initHandleSelect2: function () {
            $('select.select2').select2('destroy');
            //console.log('select2 destroyed');
            $('select.select2').select2();
            //console.log('select2 initiated');
        },

        LoadList: function (url, data, target = null) {

            if (target) {
                target.html(loadingImage);
            } else {
                $('#content').html(loadingImage);
            }

            $.ajax({

                url: url + '/LoadLines',

                type: "POST",

                data: data,

                dataType: "json",

                async: true,

                success: function (response) {

                    let error = response.error;
                    let data = response.data;

                    if (error === "") {

                        if (target) {
                            target.html(data);
                        } else {
                            $('#content').html(data);
                        }

                    } else {
                        ShowGritter('Fehler', error);
                        if (target) {
                            target.html('Fehler');
                        } else {
                            $('#content').html('Fehler');
                        }
                    }

                },
                error: function (error) {
                    ShowGritter('Fehler', error);
                }

            });
        },

        Delete: function (id, name, url, data = {}, rowToDelete = null, additionalText = '', useDeleteSuffixInURL = true) {

            try {

                if (confirm('Willst Du den Eintrag \'' + name + '\' wirklich löschen? ' + additionalText)) {

                    data.id = id;
                    url = useDeleteSuffixInURL ? url + '/Delete' : url;

                    $.ajax({

                        url: url,
                        type: "POST",
                        data: data,
                        dataType: "json",

                    }).done(function (response) {

                        let error = response.error;
                        let data = response.data;
                        let info = response.info;

                        if (error === "") {

                            let message = info ? info : '\'Eintrag erfolgreich gelöscht\'';

                            ShowGritter('Erfolg', message);
                            if (rowToDelete) {
                                $(rowToDelete).hide(toggleSpeed).delay(delaySpeed).queue(function () {
                                    $(this).remove()
                                });
                            } else {

                                $('#line_' + id).hide(toggleSpeed).delay(delaySpeed).queue(function () {
                                    $(this).remove()
                                });
                                $('#tr_' + id).hide(toggleSpeed).delay(delaySpeed).queue(function () {
                                    $(this).remove()
                                });

                            }

                        } else {
                            ShowGritter('Fehler', error);
                        }

                    }).error(function (error) {
                        ShowGritter('Fehler', error);
                    });

                }

            } catch (e) {
                ShowGritter('Fehler', e.message);
            }

        },

        Duplicate: function (id, url) {

            try {

                $.ajax({

                    url: url + '/Duplicate',
                    type: "POST",
                    data: {id: id},
                    dataType: "json",

                }).done(function (response) {

                    let error = response.error;
                    let data = response.data;
                    let info = response.info;

                    if (error === "") {

                        let message = info ? info : '\'Eintrag erfolgreich dupliziert\'';

                        ShowGritter('Erfolg', message);
                        CMS.LoadList(url);

                    } else {
                        ShowGritter('Fehler', error);
                    }

                }).error(function (error) {
                    ShowGritter('Fehler', error);
                });

            } catch (e) {
                ShowGritter('Fehler', e.message);
            }

        },

        LoadingImage: function () {
            return loadingImage;
        },

    }

}();
