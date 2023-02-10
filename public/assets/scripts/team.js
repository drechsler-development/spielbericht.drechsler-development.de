let Team = function () {

    let url = '/team';
    var cookieDontShowHint;

    let Init = function () {

        cookieDontShowHint = $.cookie("DontShowHint") !== undefined ? $.cookie("DontShowHint") : null;
        console.log(cookieDontShowHint);
        if (cookieDontShowHint === null || cookieDontShowHint === undefined) {
            $('#modalShowHint').modal('show');
            $('#modalShowHint').on('shown.bs.modal', function () {
                $('#myInput').trigger('focus')
            });
        }

        $('#btnGenerate').click(function () {
            $.ajax({

                url: "createReportSheet.php",
                type: "POST",
                data: {

                    //
                    groupName: $('#groupName').val(),
                    t1Name: $('#t1Name').val(),
                    t2Name: $('#t2Name').val(),
                    address: $('#address').val(),
                    date: $('#date').val(),
                    startTime: $('#startTime').val(),
                    endTime: $('#endTime').val(),
                    //TEAM1
                    t1p1Number: $('#t1p1Number').val(),
                    t1p1Name: $('#t1p1Name').val(),
                    t1p2Number: $('#t1p2Number').val(),
                    t1p2Name: $('#t1p2Name').val(),
                    t1p3Number: $('#t1p3Number').val(),
                    t1p3Name: $('#t1p3Name').val(),
                    t1p4Number: $('#t1p4Number').val(),
                    t1p4Name: $('#t1p4Name').val(),
                    t1p5Number: $('#t1p5Number').val(),
                    t1p5Name: $('#t1p5Name').val(),
                    t1p6Number: $('#t1p6Number').val(),
                    t1p6Name: $('#t1p6Name').val(),
                    t1p7Number: $('#t1p7Number').val(),
                    t1p7Name: $('#t1p7Name').val(),
                    t1p8Number: $('#t1p8Number').val(),
                    t1p8Name: $('#t1p8Name').val(),
                    t1p9Number: $('#t1p9Number').val(),
                    t1p9Name: $('#t1p9Name').val(),
                    t1p10Number: $('#t1p10Number').val(),
                    t1p10Name: $('#t1p10Name').val(),
                    t1p11Number: $('#t1p11Number').val(),
                    t1p11Name: $('#t1p11Name').val(),
                    t1p12Number: $('#t1p12Number').val(),
                    t1p12Name: $('#t1p12Name').val(),
                    //TEAM2
                    t2p1Number: $('#t2p1Number').val(),
                    t2p1Name: $('#t2p1Name').val(),
                    t2p2Number: $('#t2p2Number').val(),
                    t2p2Name: $('#t2p2Name').val(),
                    t2p3Number: $('#t2p3Number').val(),
                    t2p3Name: $('#t2p3Name').val(),
                    t2p4Number: $('#t2p4Number').val(),
                    t2p4Name: $('#t2p4Name').val(),
                    t2p5Number: $('#t2p5Number').val(),
                    t2p5Name: $('#t2p5Name').val(),
                    t2p6Number: $('#t2p6Number').val(),
                    t2p6Name: $('#t2p6Name').val(),
                    t2p7Number: $('#t2p7Number').val(),
                    t2p7Name: $('#t2p7Name').val(),
                    t2p8Number: $('#t2p8Number').val(),
                    t2p8Name: $('#t2p8Name').val(),
                    t2p9Number: $('#t2p9Number').val(),
                    t2p9Name: $('#t2p9Name').val(),
                    t2p10Number: $('#t2p10Number').val(),
                    t2p10Name: $('#t2p10Name').val(),
                    t2p11Number: $('#t2p11Number').val(),
                    t2p11Name: $('#t2p11Name').val(),
                    t2p12Number: $('#t2p12Number').val(),
                    t2p12Name: $('#t2p12Name').val(),
                },
                dataType: "json",

            }).done(function (response) {

                console.log(response);

                let error = response.error;
                if (error === "") {

                    window.location = 'createReportSheet.php';

                } else {
                    console.log(error);
                }

            }).fail(function (error) {
                console.log(error);
            });
        });

        $('#btnSave').click(function () {

            $.ajax({

                url: "save.php",
                type: "POST",
                data: {

                    //
                    groupName: $('#groupName').val(),
                    t1Name: $('#t1Name').val(),
                    address: $('#address').val(),
                    startTime: $('#startTime').val(),
                    endTime: $('#endTime').val(),
                    //TEAM1
                    t1p1Number: $('#t1p1Number').val(),
                    t1p1Name: $('#t1p1Name').val(),
                    t1p2Number: $('#t1p2Number').val(),
                    t1p2Name: $('#t1p2Name').val(),
                    t1p3Number: $('#t1p3Number').val(),
                    t1p3Name: $('#t1p3Name').val(),
                    t1p4Number: $('#t1p4Number').val(),
                    t1p4Name: $('#t1p4Name').val(),
                    t1p5Number: $('#t1p5Number').val(),
                    t1p5Name: $('#t1p5Name').val(),
                    t1p6Number: $('#t1p6Number').val(),
                    t1p6Name: $('#t1p6Name').val(),
                    t1p7Number: $('#t1p7Number').val(),
                    t1p7Name: $('#t1p7Name').val(),
                    t1p8Number: $('#t1p8Number').val(),
                    t1p8Name: $('#t1p8Name').val(),
                    t1p9Number: $('#t1p9Number').val(),
                    t1p9Name: $('#t1p9Name').val(),
                    t1p10Number: $('#t1p10Number').val(),
                    t1p10Name: $('#t1p10Name').val(),
                    t1p11Number: $('#t1p11Number').val(),
                    t1p11Name: $('#t1p11Name').val(),
                    t1p12Number: $('#t1p12Number').val(),
                    t1p12Name: $('#t1p12Name').val(),
                },
                dataType: "json",

            }).done(function (response) {

                console.log(response);

                let error = response.error;
                if (error === "") {

                    ShowGritter("Erfolg", "Daten gespeichert");

                } else {
                    console.log(error);
                }

            }).fail(function (error) {
                console.log(error);
            });
        });

        $('#btnDeleteSession').click(function () {
            $.ajax({

                url: "deleteSession.php",
                type: "POST",
                dataType: "json",

            }).done(function (response) {

                console.log(response);

                let error = response.error;
                if (error === "") {

                    window.location = '/?normal=2';

                } else {
                    console.log(error);
                }

            }).fail(function (error) {
                console.log(error);
            });
        });

        $('.btnSwitchMode').click(function () {
            if ($(this).attr('data-targetMode') == 'berichtMode') {
                $('.berichtMode').show(200);
                $('.safeMode').hide(200);
                $('#subTitle').html('Spielberichtmodus').removeClass('badge-info').addClass('badge-danger');
                ;
            } else {
                $('.berichtMode').hide(200);
                $('.safeMode').show(200);
                $('#subTitle').html('Team-Bearbeitungsmodus').addClass('badge-info').removeClass('badge-danger');
            }
        });

        $('#btnDontShowHint').click(function () {
            $.cookie("DontShowHint", 1);
            console.log($.cookie("DontShowHint"));
        });

        $('body').on('change', '#t1Id,#t2Id', function () {

            let pos = $(this).attr('data-pos');
            let id = $(this).val();

            if (id != 0) {

                $('#t' + pos + 'NameSection').hide(200);

                $.ajax({

                    url: url + "/load",
                    type: "POST",
                    data: {
                        id: id,
                    },
                    dataType: "json",

                }).done(function (response) {

                    let error = response.error;

                    if (error == "") {
                        let data = response.data;

                        console.log('#t' + pos + 'Name');
                        console.log(data.name);
                        $('#t' + pos + 'Name').val(data.name);

                        if (pos == 1) {
                            $('#address').val(data.address);
                            $('#startTime').val(data.startTime);
                            $('#endTime').val(data.endTime);
                            $('#groupName').val(data.groupName);
                        }
                        let players = data.players;
                        let i = 0;
                        players.forEach(function (player) {
                            $('#t' + pos + 'p' + i + 'Number').val(player.number);
                            $('#t' + pos + 'p' + i + 'Name').val(player.name);
                            i++;
                        });

                    } else {
                        console.log(error);
                    }

                }).fail(function (error) {
                    console.log(error);
                });

            } else {

                $('#address').val('');
                $('#startTime').val('');
                $('#endTime').val('');
                $('#groupName').val('test');
                for (let i = 0; i < 12; i++) {
                    $('#t' + pos + 'p' + i + 'Number').val('');
                    $('#t' + pos + 'p' + i + 'Name').val('');
                }

                $('#t' + pos + 'NameSection').show(200);
                $('#t' + pos + 'Name').val('');

            }

        });

    }

    return {
        init: Init
    }

}();
