//As contains() isn't supported in Chrome
if (!('contains' in String.prototype)) {
    String.prototype.contains = function (str, startIndex) {
        return -1 !== String.prototype.indexOf.call(this, str, startIndex);
    };
}

function CreateDateFromString(dateString, timeString) {

    return CreateDateFromStringWithSourceFormat(dateString, timeString, 'DD.MM.YYYY');

} // END function ParseDateFromString(date){

function CreateDateFromStringWithSourceFormat(dateString, timeString, sourceFormat) {

    let dateObject = null;
    let years, months, days, hours, minutes, seconds;

    if (timeString === null) {
        hours = 0;
        minutes = 0;
        seconds = 0;
    } else {
        hours = timeString.substr(0, 2);
        minutes = timeString.substr(3, 2);
        seconds = timeString.substr(6, 2);
    }

    if (sourceFormat.contains('-')) {

        years = Number(dateString.substr(0, 4));
        months = Number(dateString.substr(5, 2)) - 1;
        days = Number(dateString.substr(8, 2));

        dateObject = new Date(years, months, days, hours, minutes, seconds);

    } else if (sourceFormat.contains('.')) {

        days = Number(dateString.substr(0, 2));
        months = Number(dateString.substr(3, 2)) - 1;
        years = Number(dateString.substr(6, 4));

        dateObject = new Date(years, months, days, hours, minutes, seconds);

    } // END if(sourceFormat.contains('-')){

    return dateObject;

} // END function CreateDateFromStringWithSourceFormat(dateString,sourceFormat){

function GetURLGetParameter(parameterName) {
    let result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
            tmp = item.split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

function CurrentDate() {

    let date = new Date();
    let day = date.getDate();
    let month = date.getMonth() + 1;
    let year = date.getFullYear();

    if (day < 10)
        day = "0" + day;

    if (month < 10)
        month = "0" + month;

    return day + "." + month + "." + year;

} // END function CurrentDate(){

function ShowModalWarning(title, body) {

    $('#modal .modal-title').html(title);
    $('#modal .modal-body').html(body);
    $('#modal').modal('show');

}

let DateDiff = {

    inDays: function (d1, d2) {
        let t2 = d2.getTime();
        let t1 = d1.getTime();

        return parseInt((t2 - t1) / (24 * 3600 * 1000));
    },

    inWeeks: function (d1, d2) {
        let t2 = d2.getTime();
        let t1 = d1.getTime();

        return parseInt((t2 - t1) / (24 * 3600 * 1000 * 7));
    },

    inMonths: function (d1, d2) {
        let d1Y = d1.getFullYear();
        let d2Y = d2.getFullYear();
        let d1M = d1.getMonth();
        let d2M = d2.getMonth();

        return (d2M + 12 * d2Y) - (d1M + 12 * d1Y);
    },

    inYears: function (d1, d2) {
        return d2.getFullYear() - d1.getFullYear();
    }
}

function ShowGritter(title, message, delay = 5) {

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
            "hideMethod": "fadeOut",
            "autohide": "false"
        }

        toastr[messageType](message, title);

        console.log({message});

    }

}

function HideElement(element, delay = 200) {
    element.hide(delay);
}

function ShowElement(element, delay = 200) {
    element.show(delay);
}

function SetOrReplaceURLParam(key, value) {

    let keyRegex = new RegExp('([\?&])' + key + '[^&]*');
    let baseUrl = [location.protocol, '//', location.host, location.pathname].join('');
    let urlQueryString = document.location.search;
    let newParam = key + '=' + value;
    let params;

    // If the "search" string exists, then build params from it
    if (urlQueryString) {

        // If param exists already, update it
        if (urlQueryString.match(keyRegex) !== null) {
            params = urlQueryString.replace(keyRegex, "$1" + newParam);
            // Otherwise, add it to end of query string
        } else {
            params = urlQueryString + '&' + newParam;
        }

    } else {

        params = '?' + newParam;

    }

    return baseUrl + params;

}

function PopulateAndShowModal(response) {

    let responseJson = JSON.parse(response);

    if (responseJson["error"] == "") {

        $('#modal .modal-title').html(responseJson["title"]);
        $('#modal .modal-body').html(responseJson["content"]);
        $('#modal').modal('show');

    } else {
        ShowGritter("Error", responseJson["error"]);
    }

}

Date.prototype.ddmmyyyy = function () {
    let mm = this.getMonth() + 1; // getMonth() is zero-based
    let dd = this.getDate();

    return [
        (dd > 9 ? '' : '0') + dd,
        (mm > 9 ? '' : '0') + mm,
        this.getFullYear(),
    ].join('.');
};

Date.prototype.yyyymmdd = function () {
    let mm = this.getMonth() + 1; // getMonth() is zero-based
    let dd = this.getDate();

    return [
        this.getFullYear(),
        (mm > 9 ? '' : '0') + mm,
        (dd > 9 ? '' : '0') + dd,
    ].join('-');
};

function Message(title, message) {

    let cssclass;

    switch (title.toLowerCase()) {
        case 'info':
            cssclass = "alert-info";
            break;
        case 'warning':
            cssclass = "alert-warning";
            break;
        case 'error':
            cssclass = "alert-error";
            break;
        default:
            cssclass = "alert-success";
            break;
    }

    return '<div class="alert ' + cssclass + '">' +
        '<div style="display:inline-block; min-width: 70px; height: auto; text-align:left; margin-right:-5px; vertical-align:top;"><strong>' + title + ':</strong></div>' +
        '<div style=\"display:inline-block; vertical-align:top; width:auto;\">' + message + '</div>' +
        '</div>';

}

function FormatToPrice(total) {
    return total.toFixed(2).replace('.', ',');
}

function CopyToClipBoard(copyText) {

    copyText.select();
    copyText.setSelectionRange(0, 99999); /*For mobile devices*/

    /* Copy the text inside the text field */
    document.execCommand("copy");

    /* Alert the copied text */
    ShowGritter('Information', 'Der Text \'' + copyText + '\' wurde in die Zwischenablage copiert');

}

