let Team = function () {

    let url = '/Teams';

    let table = $('#teamsTable');
    let pageContent = $('.page-content');

    let Init = function () {

        console.log('Team.init()');

        table.on("click", ".btnEdit", ShowForm);
        table.on("click", ".btnDelete", function () {
            let id = $(this).attr('data-id');
            let name = $(this).attr('data-name');
            CMS.Delete(id, name, url);
            //CMS.Delete(id, name, url, data);
        });
        pageContent.on("click", ".btnReload", LoadList);

    }

    let InitModalButtons = function () {

        let modal = $('#modal');

        //OFF
        modal.off("click", "#btnSave");

        //ON
        modal.on("click", "#btnSave", Save);
    }

    let LoadList = function () {

        CMS.LoadList(url, {});

    }

    let ShowForm = function () {

        let id = $(this).attr('data-id');

        try {

            $.ajax({

                url: url + "/LoadSingle",
                type: "POST",
                data: {
                    id: id
                },
                dataType: "json",

                success: function (response) {

                    let error = response.error;
                    let data = response.data;

                    if (error === "") {

                        let title = id ? 'Eintrag bearbeiten' : 'Neuen Eintrag anlegen';
                        CMS.ShowModal(title, data, "save-cancel", true, 'modal-lg');


                        InitModalButtons();

                    } else {
                        ShowGritter('Fehler', error);
                    }

                },
                error: function (error) {
                    ShowGritter('Fehler', error);
                }
            });

        } catch (e) {
            ShowGritter('Fehler', e.message);
        }

    }

    let Save = function () {

        try {

            let id = $('#id').val();

            $.ajax({

                url: url + "/Save",
                type: "POST",
                data: {
                    id: id,
                    teamName: $('#teamName').val(),
                    address: $('#address').val(),
                    additionalAddress: $('#additionalAddress').val(),
                    postCode: $('#postCode').val(),
                    city: $('#city').val(),
                    day: $('#day').val(),
                    startTime: $('#startTime').val(),
                    endTime: $('#endTime').val(),
                    teamLeadName: $('#teamLeadName').val(),
                    teamLeadEmail: $('#teamLeadEmail').val(),
                    teamLeadTelephone: $('#teamLeadTelephone').val(),
                    teamLeadName2: $('#teamLeadName2').val(),
                    teamLeadEmail2: $('#teamLeadEmail2').val(),
                    teamLeadTelephone2: $('#teamLeadTelephone2').val(),

                },
                dataType: "json",

            }).done(function (response) {

                let error = response.error;

                if (error === "") {

                    let message = id ? 'Eintrag erfolgreich bearbeitet' : 'Eintrag erfolgreich erstellt';
                    ShowGritter('Info', message);
                    LoadList();
                    CMS.ClearAndHideModal();

                } else {
                    ShowGritter('Fehler', error);
                }

            }).error(function (error) {
                ShowGritter('Fehler', error);
            });

        } catch (e) {
            ShowGritter('Fehler', e.message);
        }

    }

    return {

        init: Init,

        initLoad: LoadList

    }

}();
