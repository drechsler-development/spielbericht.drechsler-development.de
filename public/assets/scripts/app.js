let App = function () {

    let Init = function () {

        let ShowGritter = function (title, message, delay = 5) {

            let image = '';
            let messageType = 'info';

            if (message != '') {

                switch (title) {
                    case 'Information':
                    case 'Erfolg':
                    case 'Success':
                        image = '/assets/img/gritter_success.png';
                        messageType = 'success';
                        break;
                    case 'Warnung':
                    case 'Warning':
                        image = '/assets/img/gritter_warning.png';
                        messageType = 'warning';
                        break;
                    case 'Fehler':
                    case 'Error':
                        image = '/assets/img/gritter_warning.png';
                        messageType = 'error';
                        break;
                    case 'Hinweis':
                    case 'Hint':
                        image = '/assets/img/gritter_hint.png';
                        messageType = 'info';
                        break;
                }

                delay = Number(delay) * 1000;

                toastr.options = {
                    "closeButton": true,
                    "debug": true,
                    "positionClass": "toast-top-center",
                    "showDuration": "1000",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }

                toastr[messageType](message, title);

                console.log({message});

            }

        }

    }

    let InitDatePicker = function () {
        /** THANKS TO //HELP AT: https://uxsolutions.github.io/bootstrap-datepicker **/
        $('#date, #dateFrom, #dateTo, .dateFrom, .dateTo, .date').datepicker({
            language: "de",
            keyboardNavigation: false,
            todayHighlight: true,
            autoclose: true,
        });
    }

    let InitTimePicker = function () {

        $('#startTime,#endTime,#time').timepicker({
            showMeridian: false,
            stepping: 15,
        });
    }

    return {
        init: Init,
        initDatePicker: InitDatePicker,
        initTimePicker: InitTimePicker
    }

}();
