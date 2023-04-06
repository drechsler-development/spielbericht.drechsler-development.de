let GameReport = function () {

    let url = '/Team';
    let cookieDontShowHint;
    let originalHomeTeamName, originalHomeTeamId;

    let Init = function () {

        originalHomeTeamName = $('#t1Name').val();
        originalHomeTeamId = $('#t1Id').val()
        console.log(originalHomeTeamName);
        console.log(originalHomeTeamId);

        cookieDontShowHint = $.cookie("DontShowHint") !== undefined ? $.cookie("DontShowHint") : null;
        if (cookieDontShowHint === null || cookieDontShowHint === undefined) {
            $('#modalShowHint').modal('show');
            $('#modalShowHint').on('shown.bs.modal', function () {
                $('#myInput').trigger('focus')
            });
        }

        $('#btnSave').click(function () {

            $.ajax({

                url: "/Team/UpdatePlayers",
                type: "POST",
                data: {

                    //TEAM1
                    p0Number: $('#p0Number').val(),
                    p0Name: $('#p0Name').val(),
                    p1Number: $('#p1Number').val(),
                    p1Name: $('#p1Name').val(),
                    p2Number: $('#p2Number').val(),
                    p2Name: $('#p2Name').val(),
                    p3Number: $('#p3Number').val(),
                    p3Name: $('#p3Name').val(),
                    p4Number: $('#p4Number').val(),
                    p4Name: $('#p4Name').val(),
                    p5Number: $('#p5Number').val(),
                    p5Name: $('#p5Name').val(),
                    p6Number: $('#p6Number').val(),
                    p6Name: $('#p6Name').val(),
                    p7Number: $('#p7Number').val(),
                    p7Name: $('#p7Name').val(),
                    p8Number: $('#p8Number').val(),
                    p8Name: $('#p8Name').val(),
                    p9Number: $('#p9Number').val(),
                    p9Name: $('#p9Name').val(),
                    p10Number: $('#p10Number').val(),
                    p10Name: $('#p10Name').val(),
                    p11Number: $('#p11Number').val(),
                    p11Name: $('#p11Name').val(),
                    p12Number: $('#p12Number').val(),
                    p12Name: $('#p12Name').val(),
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

        $('#btnGenerate, #btnPreview').click(function () {

            let preview = $(this).attr('data-preview') == "1" ? 1 : 0;

            $.ajax({

                url: "/GameReport/CreateReport",
                type: "POST",
                data: {

                    //
                    groupName: $('#groupName').val(),
                    t1Name: $('#t1Name').val(),
                    t2Name: $('#t2Name').val(),
                    address: $('#address').val(),
                    additionalAddress: $('#additionalAddress').val(),
                    date: $('#date').val(),
                    startTime: $('#startTime').val(),
                    endTime: $('#endTime').val(),
                    //TEAM1
                    p0Number: $('#p0Number').val(),
                    p0Name: $('#p0Name').val(),
                    p1Number: $('#p1Number').val(),
                    p1Name: $('#p1Name').val(),
                    p2Number: $('#p2Number').val(),
                    p2Name: $('#p2Name').val(),
                    p3Number: $('#p3Number').val(),
                    p3Name: $('#p3Name').val(),
                    p4Number: $('#p4Number').val(),
                    p4Name: $('#p4Name').val(),
                    p5Number: $('#p5Number').val(),
                    p5Name: $('#p5Name').val(),
                    p6Number: $('#p6Number').val(),
                    p6Name: $('#p6Name').val(),
                    p7Number: $('#p7Number').val(),
                    p7Name: $('#p7Name').val(),
                    p8Number: $('#p8Number').val(),
                    p8Name: $('#p8Name').val(),
                    p9Number: $('#p9Number').val(),
                    p9Name: $('#p9Name').val(),
                    p10Number: $('#p10Number').val(),
                    p10Name: $('#p10Name').val(),
                    p11Number: $('#p11Number').val(),
                    p11Name: $('#t1p11Name').val(),
                    p12Number: $('#t1p12Number').val(),
                    p12Name: $('#t1p12Name').val(),
                    //TEAM2
                    t2p0Number: $('#t2p0Number').val(),
                    t2p0Name: $('#t2p0Name').val(),
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
                    preview: preview
                },
                dataType: "json",

            }).done(function (response) {

                console.log(response);

                let error = response.error;
                if (error === "") {

                    window.open('createReportSheet.php', '_blank').focus();

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
            } else {
                $('.berichtMode').hide(200);
                $('.safeMode').show(200);
                $('#subTitle').html('Team-Bearbeitungsmodus').addClass('badge-info').removeClass('badge-danger');
            }

            $('#single, #multiple').toggle();

            let singleMode = $('#multiple').is(":hidden");

            if (singleMode) {
                $('#t1Name').val(originalHomeTeamName);
                $("#t1IdSelect").val(originalHomeTeamId).trigger('change');
            } else {
                $("#t1IdSelect").val(originalHomeTeamId);
            }

        });

        $('#btnDontShowHint').click(function () {
            $.cookie("DontShowHint", 1);
            console.log($.cookie("DontShowHint"));
        });

        $('body').on('change', '#t2Id, #t1IdSelect', function () {

            let pos = $(this).attr('data-pos');
            let id = $(this).val();
            let name1 = $("#t1IdSelect option:selected").text();
            let name2 = $("#t2Id option:selected").text();

            $('#t1Name').val(name1);
            $('#t2Name').val(name2);

            //Clear current data
            for (let i = 0; i < 12; i++) {
                if (pos == 1) {
                    $('#p' + i + 'Number').val('');
                    $('#p' + i + 'Name').val('');
                } else {
                    $('#t' + pos + 'p' + i + 'Number').val('');
                    $('#t' + pos + 'p' + i + 'Name').val('');
                }
            }

            if (pos == 1 && id != 0) {

                //Load Team Header data
                $.ajax({

                    url: "/Teams/LoadSingle",
                    type: "POST",
                    data: {
                        id: id,
                        json: 1
                    },
                    dataType: "json",

                }).done(function (response) {

                    $('#address').val(response['row'].address);
                    $('#startTime').val(response['row'].startTime);
                    $('#endTime').val(response['row'].endTime);
                    $('#groupName').val(response['row'].groupName);

                }).fail(function (error) {
                    console.log(error);
                });

            }

            if (id != 0) {

                $('#t' + pos + 'NameSection').hide(200);

                $.ajax({

                    url: "/GameReport/LoadPlayers/" + id,
                    type: "GET",
                    dataType: "json",

                }).done(function (response) {

                    let error = response.error;

                    if (error == "") {

                        let data = response.data;

                        let players = data.players;
                        let i = 0;
                        players.forEach(function (player) {


                            if (pos == 1) {
                                $('#p' + i + 'Number').val(player.number);
                                $('#p' + i + 'Name').val(player.name);
                            } else {
                                $('#t' + pos + 'p' + i + 'Number').val(player.number);
                                $('#t' + pos + 'p' + i + 'Name').val(player.name);
                            }

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
                $('#groupName').val('');
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
