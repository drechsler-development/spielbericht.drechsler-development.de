let Game = function () {

    let url = '/Game';

    let pageContent = $('body');

    let Init = function () {

        pageContent.on("click", ".btnReload", LoadList);

        $('#filterTeamId, #filterGroupId').change(function () {
            LoadList();
        });

    }

    let LoadList = function () {

        let data = {
            filterTeamId: $('#filterTeamId').val(),
            filterGroupId: $('#filterGroupId').val(),
        }

        CMS.LoadList(url, data);

        pageContent.off('click', '.btnGroupTrigger');

        pageContent.on('click', '.btnGroupTrigger', function () {
            let id = $(this).data('id');
            $('#filterGroupId').val(id).trigger('change');
        });


        pageContent.off('click', '.btnTeamTrigger');

        pageContent.on('click', '.btnTeamTrigger', function () {
            let id = $(this).data('id');
            $('#filterTeamId').val(id).trigger('change');
        });

    }

    return {

        init: Init,

        //initLoad: LoadList
        initLoad: LoadList

    }

}();
