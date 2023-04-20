let Session = function () {

    const SESSION_TIME_OUT = 10 * 60 * 1000; //represent 10 minutes
    const CHECK_IDLE_INTERVAL = 10 * 1000; //represent 10 seconds
    const TIME_BETWEEN_WARNING_AND_LOGOUT = 2 * 60 * 1000; //represent 2 minutes

    let remainingTimer, idleIntervalID;
    let isTimedOut = false;
    let isTimerTillLogoutActivated = false;
    let locatStorageSessionName = 'idleSessionStatus';
    let localStorageSessionNameIdleTimeCounter = 'idleSessionTimeCounter';

    localStorage.setItem(locatStorageSessionName, 'isStarted');

    let btnSessionExpiredCancelled = $("#btnSessionExpiredCancelled");
    let modalBackdrop = $('.modal-backdrop');
    let sessionExpireWarningModal = $("#session-expire-warning-modal");
    let sessionExpiredModal = $("#session-expired-modal");
    let btnExpiredOk = $('#btnExpiredOk');
    let btnOk = $("#btnOk");
    let btnLogoutNow = $("#btnLogoutNow");

    let InitSessionMonitor = function () {

        //Check if the user is on the Login or Logout page
        if (window.location.pathname === '/Login' || window.location.pathname === '/Logout') {
            //console.log('Login or Logout page');
            return;
        }

        console.log('InitSessionMonitor');

        /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
         * Handle re-initialze the idle timer on users action
         *+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

        //Handle clicks and enter key press on Modals
        $(document).bind('keypress.session', function (ed, e) {
            ModalKeyPressed(ed, e);
        });

        $(document).bind('mousedown keydown', function (ed, e) {
            ModalKeyPressed(ed, e);
        });

        //Handle Scrolling
        $(window).scroll(function () {
            localStorage.setItem(locatStorageSessionName, 'isStarted');
            StartCheckIdleTimeout();
        });

        //Handle clicks and enter key press on Modals

        btnSessionExpiredCancelled.click(function () {
            modalBackdrop.css("z-index", parseInt(modalBackdrop.css('z-index')) - 500);
        });

        btnOk.click(function () {
            sessionExpireWarningModal.modal('hide');
            modalBackdrop.css("z-index", parseInt(modalBackdrop.css('z-index')) - 500);
            StartCheckIdleTimeout();
            clearInterval(remainingTimer);
            localStorage.setItem(locatStorageSessionName, 'isStarted');
        });

        btnLogoutNow.click(function () {
            localStorage.setItem(locatStorageSessionName, 'loggedOut');
            window.location = "Logout.html";
            LogOut();
            sessionExpiredModal.modal('hide');

        });

        sessionExpiredModal.on('shown.bs.modal', function () {
            sessionExpireWarningModal.modal('hide');
            $(this).before(modalBackdrop);
            $(this).css("z-index", parseInt(modalBackdrop.css('z-index')) + 1);
        });

        sessionExpiredModal.on("hidden.bs.modal", function () {
            window.location = "Logout.html";
        });

        sessionExpireWarningModal.on('shown.bs.modal', function () {
            sessionExpireWarningModal.modal('show');
            $(this).before(modalBackdrop);
            $(this).css("z-index", parseInt(modalBackdrop.css('z-index')) + 1);
        });

        sessionExpiredModal.on("click", RedirectToLogout, false);

        StartCheckIdleTimeout();
    }

    let ModalKeyPressed = function (ed, e) {

        let target = ed ? ed.target : e.target;
        let sessionTarget = $(target).parents("#session-expire-warning-modal").length;

        if (sessionTarget != null) {

            if (
                ed.target.id !== "btnSessionExpiredCancelled" &&
                ed.target.id !== "btnSessionModal" &&
                ed.target.id !== "btnExpiredOk" &&
                ed.target.id !== "session-expire-warning-modal" &&
                ed.currentTarget.activeElement.id !== "session-expire-warning-modal" &&
                ed.currentTarget.activeElement.className !== "modal fade modal-overflow in" &&
                ed.currentTarget.activeElement.className !== 'modal-header' &&
                sessionTarget !== 1

            ) {
                localStorage.setItem(locatStorageSessionName, 'isStarted');
                StartCheckIdleTimeout();
            }

        }

    }

    let StartCheckIdleTimeout = function () {

        ClearAllIntervals();
        localStorage.setItem(localStorageSessionNameIdleTimeCounter, $.now());
        idleIntervalID = setInterval(CheckIdleTimeout, CHECK_IDLE_INTERVAL);
        isTimerTillLogoutActivated = false;

    }

    let RedirectToLogout = function (evt) {
        console.log(evt);
        window.location = "/Logout";
    }

    let ClearAllIntervals = function () {
        clearInterval(idleIntervalID);
        clearInterval(remainingTimer);
    }

    let CheckIdleTimeout = function () {

        let idleTime = parseInt(localStorage.getItem(localStorageSessionNameIdleTimeCounter)) + SESSION_TIME_OUT;

        //If the user is idle for more than the session time out, then redirect to the logout page
        if ($.now() > idleTime + TIME_BETWEEN_WARNING_AND_LOGOUT) {

            sessionExpireWarningModal.modal('hide');
            sessionExpiredModal.modal('show');
            modalBackdrop.css("z-index", parseInt(modalBackdrop.css('z-index')) + 100);
            sessionExpiredModal.css('z-index', 2000);
            btnExpiredOk.css('background-color', '#428bca');
            btnExpiredOk.css('color', '#fff');

            isTimedOut = true;

            clearInterval(idleIntervalID);

            LogOut();
            //If the user is idle for more than the session time out minus the time between the warning and logout, then show the warning modal
        } else if ($.now() > idleTime) {

            if (!isTimerTillLogoutActivated) {

                localStorage.setItem(locatStorageSessionName, '');
                StartCountdownOnModal();

                modalBackdrop.css("z-index", parseInt(modalBackdrop.css('z-index')) + 500);
                $('#session-expire-warning-modal').css('z-index', 1500);
                $('#btnOk').css('background-color', '#428bca').css('color', '#fff');
                $('#btnSessionExpiredCancelled').css('background-color', '#428bca').css('color', '#fff');
                $('#btnLogoutNow').css('background-color', '#428bca').css('color', '#fff');

                $("#seconds-timer").html('');
                sessionExpireWarningModal.modal('show');

                isTimerTillLogoutActivated = true;
            }
        }
    }

    let StartCountdownOnModal = function () {

        let dialogDisplaySeconds = 60;

        remainingTimer = setInterval(function () {
                if (localStorage.getItem('sessionSlide') === "isStarted") {
                    sessionExpireWarningModal.modal('hide');
                    StartCheckIdleTimeout();
                    clearInterval(remainingTimer);
                } else if (localStorage.getItem('sessionSlide') === "loggedOut") {
                    sessionExpireWarningModal.modal('hide');
                    sessionExpiredModal.modal('show');
                } else {

                    $('#seconds-timer').html(dialogDisplaySeconds);
                    dialogDisplaySeconds -= 1;
                }
            }
            , 1000);
    };

    let LogOut = function () {
        window.location = "/Logout";
    }

    return {

        init: InitSessionMonitor

    }

}();
