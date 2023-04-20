<?php

session_start();

use Dompdf\Dompdf;
use Dompdf\Options;

require __DIR__ . '/../config.php';

if (!empty($_SESSION['report'])) {

    $responseArray['error'] = '';

    // instantiate and use the dompdf class
    $options = new Options();
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $pattern = "/[^a-zA-Z0-9_\-]/";
    $t1FileName = preg_replace($pattern, "_", $_SESSION['report']['t1Name']);
    $t2FileName = preg_replace($pattern, "_", $_SESSION['report']['t2Name']);
    $data = file_get_contents('assets/img/spielbericht.jpg');
    $base64 = 'data:image/jpeg;base64,' . base64_encode($data);

    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Title</title>
        <style>

            @page {
                size: A4 landscape;
            }

            * {
                font-family: 'Verdana', sans-serif;
            }

            body {
                margin: 0;
            }

            .starting-player {
                text-align: center;
                border-collapse: collapse;
                border-spacing: 0;
            }

            .starting-player td {
                border: 1px solid black;
                margin: 0;
                padding: 0;
                height: 26px;
                border-collapse: collapse;
                border-spacing: 0;
            }

            .points td {
                font-size: 9px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
            }

            .border {
                border: 1px solid black;
            }

            .border-bottom {
                border-bottom: 1px solid black;
            }

            .team td {
                font-size: 12px;
            }

            .team .first-cell {
                border-right: 1px solid black;
                border-bottom: 1px solid black;
                height: 18px;
            }

            .team .second-cell {
                border-bottom: 1px solid black;
                height: 18px;
            }

            .text-center {
                text-align: center;
            }

            .text-right {
                text-align: right;
            }

            .text-bottom {
                vertical-align: bottom !important;
            }

            .circle {
                background: #fff;
                border: 1px solid black;
                border-radius: 50%;
                /*box-shadow: 0.375em 0.375em 0 0 rgba(15, 28, 63, 0.125);*/
                height: 2.5em;
                width: 2.5em;
            }

            .line-middle {
                background: linear-gradient(180deg,
                rgba(0, 0, 0, 0) calc(50% - 1px),
                rgba(0, 0, 0, 100) calc(50%),
                rgba(0, 0, 0, 0) calc(50% + 1px)
                );
            }

            .underline {
                border-bottom: 1px solid black;
            }

            .headline {
                font-size: 20px !important;
                font-weight: bold !important;
                padding-bottom: 15px;
            }

            #header-left {
                margin-bottom: 15px;
            }

            #header-left td {
                font-size: 0.9em;
            }
        </style>
    </head>
    <body>
    <!-- create a volleyball report -->

    <table id="header-left">
        <tr>
            <td style="width: 75%">
                <table>
                    <tr>
                        <td class="text-center headline">
                            Hobbyliga der Stadt Leipzig
                        </td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td style="width: 40%">Spielberichtsbogen Volleyball</td>
                        <td colspan="3">
                            <div class="inline">Spielort: <span
                                    class="underline"><?php echo $_SESSION['report']['address']; ?></span></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 40%">Staffel:
                            <b><?php echo "Staffel " . $_SESSION['report']['groupName']; ?></b></td>
                        <td style="width: 20%">Datum: <span
                                class="underline"><?php echo $_SESSION['report']['date']; ?></span></td>
                        <td style="width: 20%">Beginn: <span
                                class="underline"><?php echo $_SESSION['report']['startTime']; ?></span></td>
                        <td style="width: 20%">Ende: <span
                                class="underline"><?php echo $_SESSION['report']['endTime']; ?></span></td>
                    </tr>
                </table>
            </td>
            <td style="line-height: 55px;">LOGO</td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="width: 75%">

                <table>
                    <tr>
                        <td class="border" style="width: 40%;">Heim: <b><?php echo $_SESSION['report']['t1Name']; ?></b>
                        </td>
                        <td style="text-align: center">Manschaften</td>
                        <td class="border" style="width: 40%;">Gast: <b><?php echo $_SESSION['report']['t2Name']; ?></b>
                        </td>
                    </tr>
                </table>
                <table>

                    <?php
                    for ($i = 1; $i < 6; $i++) {
                        ?>

                        <tr>
                            <td class="border" style="width: 40%;">
                                <table class="points">
                                    <tr>
                                        <td>1</td>
                                        <td>2</td>
                                        <td>3</td>
                                        <td>4</td>
                                        <td>5</td>
                                        <td>6</td>
                                        <td>7</td>
                                        <td>8</td>
                                        <td>9</td>
                                        <td>10</td>
                                        <td>11</td>
                                        <td>12</td>
                                        <td>13</td>
                                        <td>14</td>
                                        <td>15</td>
                                        <td>16</td>
                                        <td>17</td>
                                        <td>18</td>
                                        <td>19</td>
                                        <td>20</td>
                                        <td>21</td>
                                    </tr>
                                </table>
                                <table class="points">
                                    <tr>
                                        <td>22</td>
                                        <td>23</td>
                                        <td>24</td>
                                        <td>25</td>
                                        <td>26</td>
                                        <td>27</td>
                                        <td>28</td>
                                        <td>29</td>
                                        <td>30</td>
                                        <td>31</td>
                                        <td>32</td>
                                        <td>33</td>
                                        <td>34</td>
                                        <td>35</td>
                                        <td>36</td>
                                        <td>37</td>
                                        <td>38</td>
                                        <td>39</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="text-align: center">
                                <?php echo $i; ?>. Satz
                            </td>
                            <td style="border: 1px solid black; width: 40%;">
                                <table class="points">
                                    <tr>
                                        <td>1</td>
                                        <td>2</td>
                                        <td>3</td>
                                        <td>4</td>
                                        <td>5</td>
                                        <td>6</td>
                                        <td>7</td>
                                        <td>8</td>
                                        <td>9</td>
                                        <td>10</td>
                                        <td>11</td>
                                        <td>12</td>
                                        <td>13</td>
                                        <td>14</td>
                                        <td>15</td>
                                        <td>16</td>
                                        <td>17</td>
                                        <td>18</td>
                                        <td>19</td>
                                        <td>20</td>
                                        <td>21</td>
                                    </tr>
                                </table>
                                <table class="points">
                                    <tr>
                                        <td>22</td>
                                        <td>23</td>
                                        <td>24</td>
                                        <td>25</td>
                                        <td>26</td>
                                        <td>27</td>
                                        <td>28</td>
                                        <td>29</td>
                                        <td>30</td>
                                        <td>31</td>
                                        <td>32</td>
                                        <td>33</td>
                                        <td>34</td>
                                        <td>35</td>
                                        <td>36</td>
                                        <td>37</td>
                                        <td>38</td>
                                        <td>39</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td class="border">
                                <table class="starting-player" style="border: 0;">
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>:</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>:</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="text-align: center !important;">
                                <table style="width: 60%;margin:auto;">
                                    <tr>
                                        <td style="width: 33%;">
                                            <div class="circle"></div>
                                        </td>
                                        <td style="width: 34%">
                                            <div>:</div>
                                        </td>
                                        <td style="width: 33%">
                                            <div class="circle"></div>
                                        </td>

                                    </tr>
                                </table>
                            </td>
                            <td class="border">
                                <table class="starting-player" style="border: 0;">
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>:</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>:</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>

                </table>
                <table>
                    <tr style="line-height: 30px;">
                        <td class="text-bottom" style="width: 10%">Ergebnis:</td>
                        <td class="text-bottom border-bottom" style="width: 9%;"></td>
                        <td class="text-bottom text-center" style="width: 2%;">:</td>
                        <td class="text-bottom border-bottom" style="width: 9%;"></td>
                        <td class="text-bottom text-right" style="width: 10%;">für:</td>
                        <td class="text-bottom border-bottom" style="width: 20%;"></td>
                        <td class="text-bottom text-right" style="width: 15%;">Schiedsgericht:</td>
                        <td class="text-bottom border-bottom"></td>

                    </tr>
                </table>

                <table style="border: 2px solid black; margin-top: 10px;">
                    <tr>
                        <td style="width: 10%">Bemerkungen:</td>
                        <td class="border-bottom" style="width: 90%;"></td>
                    </tr>
                    <tr>
                        <td class="border-bottom" colspan="2" style="height: 20px;"></td>
                    </tr>
                    <tr>
                        <td class="border-bottom" colspan="2" style="height: 20px;"></td>
                    </tr>
                    <tr>
                        <td class="border-bottom" colspan="2" style="height: 20px;"></td>
                    </tr>
                </table>
            </td>
            <td style="padding-left: 15px; vertical-align: top">
                <!-- HEIM -->
                <table class="border">
                    <tr>
                        <td colspan="2"><b><?php echo $_SESSION['report']['t1Name']; ?></b></td>
                    </tr>
                </table>
                <table class="team border">
                    <tr>
                        <td class="first-cell" style="width: 10%;">Nr.</td>
                        <td class="second-cell">Name</td>
                    </tr>
                    <?php
                    $totalCount = !empty($_SESSION['report']['playersTeam1']) ? count($_SESSION['report']['playersTeam1']) : 0;
                    foreach ($_SESSION['report']['playersTeam1'] as $player) {
                        ?>
                        <tr>
                            <td class="first-cell"><?php echo $player['number']; ?></td>
                            <td class="second-cell"><?php echo $player['name']; ?></td>
                        </tr>
                        <?php
                    }
                    for ($i = 0; $i < 11 - $totalCount; $i++) {
                        ?>
                        <tr>
                            <td class="first-cell">&nbsp;</td>
                            <td class="second-cell">&nbsp;</td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="2">
                            <table>
                                <tr>
                                    <td style="width: 50%">Kapitän:</td>
                                    <td>Trainer:</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!-- GAST -->
                <table class="border" style="margin-top: 15px;">
                    <tr>
                        <td colspan="2"><b><?php echo $_SESSION['report']['t2Name']; ?></b></td>
                    </tr>
                </table>
                <table class="team border">
                    <tr>
                        <td class="first-cell" style="width: 10%;">Nr.</td>
                        <td class="second-cell">Name</td>
                    </tr>
                    <?php
                    $totalCount = !empty($_SESSION['report']['playersTeam2']) ? count($_SESSION['report']['playersTeam2']) : 0;
                    foreach ($_SESSION['report']['playersTeam2'] as $player) {
                        ?>
                        <tr>
                            <td class="first-cell"><?php echo $player['number']; ?></td>
                            <td class="second-cell"><?php echo $player['name']; ?></td>
                        </tr>
                        <?php
                    }
                    for ($i = 0; $i < 11 - $totalCount; $i++) {
                        ?>
                        <tr>
                            <td class="first-cell">&nbsp;</td>
                            <td class="second-cell">&nbsp;</td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="2">
                            <table>
                                <tr>
                                    <td style="width: 50%">Kapitän:</td>
                                    <td>Trainer:</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </body>
    </html>

    <?php
    $content = ob_get_contents();
    ob_end_clean();

    if (!empty($_SESSION['report']['preview'])) {
        echo $content;
        exit;
    }

    $dompdf->loadHtml($content);

    //echo $content;

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream();
}




