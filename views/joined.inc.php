<?php
/** @var ViewService $this */

use PlaPok\Common\Common;
use PlaPok\Controllers\WebController;
use PlaPok\Controllers\XHRController;
use PlaPok\Enum\RoomStatus;
use PlaPok\Enum\StoryPoint;
use PlaPok\Services\ViewService;

$this->display('_head.inc.php');
?>
<!--suppress CssUnusedSymbol -->
<style>

    * {
  margin: 0;
}
html, body {
  height: 100%;
}
.page-wrap {
  min-height: 100%;
  /* equal to footer height */
  margin-bottom: -135px;
}
.page-wrap:after {
  content: "";
  display: block;
}
.site-footer, .page-wrap:after {
  height: 135px;
}
.site-footer {
  background: #f0f0f0;
}

    .lds-ripple {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
    }

    .lds-ripple div {
        position: absolute;
        border: 4px solid #22ebde;
        opacity: 1;
        border-radius: 50%;
        animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
    }

    .lds-ripple div:nth-child(2) {
        animation-delay: -0.5s;
    }

    @keyframes lds-ripple {
        0% {
            top: 36px;
            left: 36px;
            width: 0;
            height: 0;
            opacity: 1;
        }
        100% {
            top: 0;
            left: 0;
            width: 72px;
            height: 72px;
            opacity: 0;
        }
    }

    .backcover {
        background: url(/assets/card_back3.jpg) no-repeat center;
        border-radius: 12px;
        width: 100px;
        height: 160px;
    }

    .front1 {
        background: url(/assets/card1.jpg) no-repeat center;
        border-radius: 12px;
    }

    .front2 {
        background: url(/assets/card2.jpg) no-repeat center;
        border-radius: 12px;
    }

    .front3 {
        background: url(/assets/card3.jpg) no-repeat center;
        border-radius: 12px;
    }

    .front5 {
        background: url(/assets/card5.jpg) no-repeat center;
        border-radius: 12px;
    }

    .front8 {
        background: url(/assets/card8.jpg) no-repeat center;
        border-radius: 12px;
    }

    .frontq {
        background: url(/assets/card-q.jpg) no-repeat center;
        border-radius: 12px;
    }
</style>

<script>
function iSelected() {
        $('#selectStoryPoint').hide();
        $('#iDidIt').show();
    }

    let nextOkRoomFlip = false;

    function refreshRoom() {
        $.ajax({
            url: "<?=Common::link([XHRController::class, 'xhrRoomInfo'])?>",
        }).done(function (data) {
            let roomInfo = jQuery.parseJSON(data);
            $.each(roomInfo.participants, function (index, element) {
                addParticipant(element);
            });

            if (roomInfo.room_status === <?=RoomStatus::NOT_ALL_READY()->getValue()?>) {
                if (nextOkRoomFlip === true) {
                    flipAllCards(roomInfo.participants, false, 'q');
                    $('#wait2').hide();
                    $('#iDidIt').hide();
                    $('#selectStoryPoint').show();
                    nextOkRoomFlip = false;
                    chosenStoryPoint = 0;
                    sentStoryPoints = 0;

                }
            }



            if (roomInfo.room_status === <?=RoomStatus::IS_RESETTING()->getValue()?>) {
                ackReset();
                nextOkRoomFlip = true;
                $('#wait2').show();
                let allAck = 1;
                $.each(roomInfo.participants, function (index, element) {
                    if (element.ackReset === false) {
                        allAck = 0;
                    }
                });

                if (allAck === 1) {
                    <?php if ($this->variables['isHost']) { ?>
                    finishResetRoom();
                    <?php } ?>
                }

            }

            if ((roomInfo.room_status === <?=RoomStatus::ALL_READY()->getValue()?>) && (sentStoryPoints === 0)) {
                sendMyStoryPoints(chosenStoryPoint);
            }
            if (roomInfo.room_status === <?=RoomStatus::FINISHED()->getValue()?>) {
                flipAllCards(roomInfo.participants, true, false);
                $("#wait1").hide(500);
                $("#wait2").hide(500);
                $('#reset1').show(1000);
            }

            setTimeout(function () {
                refreshRoom();
            }, 2500);
        });
    }

    function flipAllCards(participants, flipValue, setback) {
        $.each(participants, function (index, element) {
            let exi = $('#' + element.id);
            if (setback === false) {
                exi.children('div:eq(1)').children('.back').removeClass('frontq').addClass('front' + element.number);
            }else{
                exi.children('div:eq(1)').children('.back').removeClass('frontq').addClass('front' + setback);
            }
            setTimeout(function () {
                $("#card_" + element.id).flip(flipValue);
            }, 250 + 250 * index);
        });
    }

    function iAmReady() {
        $.ajax({
            url: "<?=Common::link([XHRController::class, 'xhrParticipantReady'])?>",
        }).done(function () {
            iSelected();
        });
    }

    function sendMyStoryPoints(csp) {
        $.ajax({
            type: "POST",
            url: "<?=Common::link([XHRController::class, 'xhrSendStoryPoint'])?>",
            data: {"story_point": csp}
        }).done(function (data) {
            sentStoryPoints = 1;
        });
    }

    function startResetRoom() {
        $.ajax({
            url: "<?=Common::link([XHRController::class, 'xhrStartResetRoom'])?>",
        }).done(function () {
            $('#reset1').hide();
            $('#wait2').show();
        });
    }

    function finishResetRoom() {
        $.ajax({
            url: "<?=Common::link([XHRController::class, 'xhrFinishResetRoom'])?>",
        }).done(function () {

        });
    }

    function ackReset() {
        $.ajax({
            url: "<?=Common::link([XHRController::class, 'xhrAckReset'])?>",
        }).done(function () {

        });
    }


    function addParticipant(element) {

        let ss;
        var id = element.id;
        var name = element.name;
        var rdy = element.isReady;

        let ucard = $('#' + id);

        if (ucard.length === 0) {
            var part = $('#toClone').clone();
            part.prop('id', id);
            part.children('div:first').text(name);
            part.children('div:eq(1)').prop('id', 'card_' + id);
            if (rdy === 1) {
                var qwe = part.children('div:eq(1)').children('.front').children('ss' + id).length;
                ss = $('#lala_1').clone();
                ss.prop('id', 'ss' + id);
                part.children('div:eq(1)').children('.front').empty().append(ss);
            }
            part.show();
            $('#listParticipants').append(part);
            $("#card_" + id).flip({trigger: "manual"});
        } else {
            if (rdy === 1) {

                setReadOnParticipant(ucard, id);

                /*
                if (ucard.children('div:eq(1)').children('.front').children('#ss' + id).length === 0) {
                    ss = $('#lala_1').clone();
                    ss.prop('id', 'ss' + id);
                    ucard.children('div:eq(1)').children('.front').empty().append(ss);
                }
                 */
            }

        }

    }

    function setReadOnParticipant(ucard, id) {
        if (ucard.children('div:eq(1)').children('.front').children('#ss' + id).length === 0) {
                ss = $('#lala_1').clone();
                ss.prop('id', 'ss' + id);
                ucard.children('div:eq(1)').children('.front').empty().append(ss);
            }
    }



    function flashRed() {
        $('#keb').css('background', '#FFDDDC').fadeOut(200).promise().done(function () {
            $('#keb').css('background', '').fadeIn(100);

        });
    }


</script>

<body style="margin: 0;background: #f5f5f5;">
<script>
    let chosenStoryPoint = 0;
    let sentStoryPoints = 0;

    new ClipboardJS('.btn');


    $(document).ready(function () {
        refreshRoom();
    });

    $(document).keypress(function (e) {
        if (chosenStoryPoint === 0) {
            if (e.which === 49 || e.which === 50 || e.which === 51 || e.which === 53 || e.which === 56) {
                if (e.which === 49) {
                    chosenStoryPoint = <?=StoryPoint::ONE()?>
                }
                if (e.which === 50) {
                    chosenStoryPoint = <?=StoryPoint::TWO()?>;
                }
                if (e.which === 51) {
                    chosenStoryPoint = <?=StoryPoint::THREE()?>;
                }
                if (e.which === 53) {
                    chosenStoryPoint = <?=StoryPoint::FIVE()?>;
                }
                if (e.which === 56) {
                    chosenStoryPoint = <?=StoryPoint::EIGHT()?>;
                }
                iAmReady();
            } else {
                flashRed();
            }
        }
    });




</script>


<div class="page-wrap">
    <div class="container justify-content-center" style="border-bottom: 1px solid #c0c0c0;">
        <div class="row justify-content-center">
            <h3><img src="assets/card-1.1s-100px.svg">Planing Poker<img src="assets/card-flip-1.1s-100px.svg"></h3>
        </div>
    </div>
    <br>


    <div class="container" style="width: 90%;margin: 0 auto;position: relative;border-bottom: 1px solid #c0c0c0;">
        <div class="row justify-content-center" id="listParticipants">
        </div>
    </div>

    <div class="container text-center" id="selectStoryPoint">
        <div class="row justify-content-center">
            <div class="col w-100" style="vertical-align: middle;padding-top: 0;width: 65px;" id="keb">
                <img src="assets/keyboard-breathe.svg?123" alt="keyboard">
                <img src="assets/sp1-65px.svg" width="65" alt="sp1">
                <img src="assets/sp2-65px.svg" width="65" alt="sp2">
                <img src="assets/sp3-65px.svg" width="65" alt="sp3">
                <img src="assets/sp5-65px.svg" width="65" alt="sp5">
                <img src="assets/sp8-65px.svg" width="65" alt="sp8">
            </div>
        </div>
    </div>

    <div class="container text-center" id="iDidIt" style="display: none;">
        <div class="row justify-content-center">
            <div class="col w-100"
                 style="vertical-align: middle;padding-top: 0;width: 65px;font-family: Tahoma,serif;font-size: 20px;font-weight: bold;">
                <img src="assets/finish/party-1.1s-200px.svg" width="65" alt="party">
                <img src="assets/finish/cheers-1.1s-200px.svg" width="65" alt="cheers">
                <?= $this->variables['username'] ?> selected story points !
                <img src="assets/finish/drink-1.1s-200px.svg" width="65" alt="drink">
                <img src="assets/finish/beer-1.1s-200px.svg" width="65" alt="beer">
            </div>
        </div>
        <div class="row justify-content-center" id="wait1">
            <div class="col w-100"
                 style="vertical-align: middle;padding-top: 0;width: 65px;font-family: Tahoma,serif;font-size: 20px;font-weight: bold;">
                <img src="assets/finish/Equalizer-2.2s-100px.svg" alt="equalizer">
            </div>
        </div>
        <div class="row justify-content-center" id="wait2">
            <div class="col w-100"
                 style="vertical-align: middle;padding-top: 0;width: 65px;font-family: Tahoma,serif;font-size: 20px;font-weight: bold;color: #c0c0c0;">
                Waiting for others...
            </div>
        </div>
        <div class="row justify-content-center" id="reset1" style="display: none;">
            <div class="col w-100"
                 style="border: 0 solid red;vertical-align: middle;padding-top: 10px;width: 65px;font-family: Tahoma,serif;font-size: 20px;font-weight: bold;">
                <?php if ($this->variables['isHost']) { ?>
                    <button type="button" class="btn btn-success" onclick="startResetRoom();">Click to reset room and plan again</button>
                <?php } ?>
            </div>
        </div>

    </div>

    <!-- ------------------------------- -->
    <div style="display: none">
    <div class="lds-ripple" style="margin-top: 40px;margin-left: 12px;" id="toCloneThinking">
        <div></div>
        <div></div>
    </div>
    </div>


    <div class="col" id="toClone" style="display: none;height: 220px;border: 0px solid red;max-width: 130px;">
        <div style="width: 100px;border-bottom:1px solid #c0c0c0;margin-bottom: 10px;text-align: center;font-family: Tahoma,serif;font-size: 24px;color: #808080">
            .
        </div>
        <div id="toCloneCard" style="width: 100px;height: 160px;">
            <div class="front backcover">
                <div class="lds-ripple" style="margin-top: 40px;margin-left: 12px;">
                    <div></div>
                    <div></div>
                </div>
            </div>
            <div class="back frontq">
            </div>
        </div>
    </div>

    <div style="display: none;">
        <svg xml:space="preserve" viewBox="0 0 100 100" y="0" x="0" xmlns="http://www.w3.org/2000/svg" id="lala_1" version="1.1"
             style="height: 100%; width: 100%; background: none; shape-rendering: auto;" width="200px" height="200px"><g
                    class="ldl-scale"
                    style="transform-origin: 50% 50%; transform: rotate(0deg) scale(0.8, 0.8);">
                <g class="ldl-ani">
                    <g class="ldl-layer">
                        <g class="ldl-ani"
                           style="transform-origin: 50px 50px; transform: matrix(0.01, 0, 0, 0.01, 0, 0); animation: 1.11111s linear 0.277778s 1 normal forwards running bounce-in-fae8b6a9-3771-456b-af56-44ff8c6c526b;">
                            <path fill="#333"
                                  d="M45.5 8.2l-29.4 17c-2.8 1.6-4.5 4.6-4.5 7.8v34c0 3.2 1.7 6.2 4.5 7.8l29.4 17c2.8 1.6 6.2 1.6 9 0l29.4-17c2.8-1.6 4.5-4.6 4.5-7.8V33c0-3.2-1.7-6.2-4.5-7.8l-29.4-17c-2.8-1.6-6.2-1.6-9 0z"
                                  style="fill: rgb(255, 255, 255);"></path>
                        </g>
                    </g>
                    <g class="ldl-layer">
                        <g class="ldl-ani"
                           style="transform-origin: 50px 50px; transform: matrix(0.01, 0, 0, 0.01, 0, 0); animation: 1.11111s linear 0s 1 normal forwards running bounce-in-fae8b6a9-3771-456b-af56-44ff8c6c526b;">
                            <path fill="#abbd81"
                                  d="M74.7 29.1c-.5-.6-1.4-.8-2-.3-6.1 4.3-11.9 9.2-17.2 14.6-3.9 4-7.5 8.2-10.8 12.7-.6.8-1.7.8-2.3.1-1.4-1.5-2.9-3-4.5-4.2-3.3-2.6-6.9-4.6-10.7-5.9-.8-.3-1.6.1-1.9.8l-.1.3c-.3.7 0 1.5.6 1.9 3.2 1.9 6.1 4.2 8.4 6.8 2.6 3 4.6 6.4 6 10.1l1.7 4.5c.5 1.2 2.1 1.3 2.7.1l2.3-4.4c3.5-6.8 7.6-13.3 12.4-19.3 4.5-5.7 9.6-10.9 15.1-15.7.7-.6.8-1.4.3-2.1z"
                                  style="fill: rgb(171, 189, 129);"></path>
                        </g>
                    </g>
                    <!--<metadata xmlns:d="https://loading.io/stock/">
                        <d:name>ok</d:name>
                        <d:tags>ok,confirm,ready,positive,check,right,correct,affirmative,success,hexagon,form</d:tags>
                        <d:license>by</d:license>
                        <d:slug>m90ikn</d:slug>
                    </metadata>-->
                </g>
            </g>
            <style id="bounce-in-fae8b6a9-3771-456b-af56-44ff8c6c526b"
                   data-anikit="">@keyframes bounce-in-fae8b6a9-3771-456b-af56-44ff8c6c526b {
                                      0% {
                                          animation-timing-function: cubic-bezier(0.0383, 0.3388, 0.0421, 0.6652);
                                          transform: matrix(0.01, 0, 0, 0.01, 0, 0);
                                          opacity: 1;
                                      }
                                      4.7% {
                                          animation-timing-function: cubic-bezier(0.3296, 0.3336, 0.5772, 0.6672);
                                          transform: matrix(0.556, 0, 0, 0.556, 0, 0);
                                          opacity: 1;
                                      }
                                      8.4% {
                                          animation-timing-function: cubic-bezier(0.0781, 0.1617, 0.0874, 0.9301);
                                          transform: matrix(0.979, 0, 0, 0.979, 0, 0);
                                          opacity: 1;
                                      }
                                      12.4% {
                                          animation-timing-function: cubic-bezier(0.8341, 0.1496, 0.8634, 0.7673);
                                          transform: matrix(1.153, 0, 0, 1.153, 0, 0);
                                          opacity: 1;
                                      }
                                      16.3% {
                                          animation-timing-function: cubic-bezier(0.3264, 0.3332, 0.5758, 0.6695);
                                          transform: matrix(1.008, 0, 0, 1.008, 0, 0);
                                          opacity: 1;
                                      }
                                      21.2% {
                                          animation-timing-function: cubic-bezier(0.0921, 0.1883, 0.3277, 1);
                                          transform: matrix(0.704, 0, 0, 0.704, 0, 0);
                                          opacity: 1;
                                      }
                                      24.5% {
                                          animation-timing-function: cubic-bezier(0.6905, 0.0944, 0.8759, 0.7624);
                                          transform: matrix(0.626, 0, 0, 0.626, 0, 0);
                                          opacity: 1;
                                      }
                                      27.7% {
                                          animation-timing-function: cubic-bezier(0.3688, 0.332, 0.624, 0.6684);
                                          transform: matrix(0.704, 0, 0, 0.704, 0, 0);
                                          opacity: 1;
                                      }
                                      32.6% {
                                          animation-timing-function: cubic-bezier(0.1368, 0.2364, 0.2451, 0.9049);
                                          transform: matrix(0.958, 0, 0, 0.958, 0, 0);
                                          opacity: 1;
                                      }
                                      37.4% {
                                          animation-timing-function: cubic-bezier(0.5936, 0.0846, 0.3495, 1);
                                          transform: matrix(1.085, 0, 0, 1.085, 0, 0);
                                          opacity: 1;
                                      }
                                      49.5% {
                                          animation-timing-function: cubic-bezier(0.5522, 0.0981, 0.3807, 1);
                                          transform: matrix(0.802, 0, 0, 0.802, 0, 0);
                                          opacity: 1;
                                      }
                                      62.4% {
                                          animation-timing-function: cubic-bezier(0.4497, 0.1349, 0.4911, 1);
                                          transform: matrix(1.044, 0, 0, 1.044, 0, 0);
                                          opacity: 1;
                                      }
                                      74.1% {
                                          animation-timing-function: cubic-bezier(0.429, 0, 0.5239, 1);
                                          transform: matrix(0.914, 0, 0, 0.914, 0, 0);
                                          opacity: 1;
                                      }
                                      87% {
                                          animation-timing-function: cubic-bezier(0.3501, 0, 0.6422, 1);
                                          transform: matrix(1.013, 0, 0, 1.013, 0, 0);
                                          opacity: 1;
                                      }
                                      95.8% {
                                          animation-timing-function: cubic-bezier(0.3653, 0, 0.6776, 1);
                                          transform: matrix(0.992, 0, 0, 0.992, 0, 0);
                                          opacity: 1;
                                      }
                                      100% {
                                          transform: matrix(1, 0, 0, 1, 0, 0);
                                          opacity: 1;
                                      }
                                  }</style><!-- [ldio] generated by https://loading.io/ --></svg>
    </div>
</div>







<footer class="site-footer text-center" style="padding: 15px;">



 <div class="card" style="margin: 0px 0px 10px 0px;border-width: 0;background: #f5f5f5">
        <div class="card-body">
            <div class="input-group mb-3">

                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon3" style="font-size: 20px;">
                        <A href="https://github.com/alexconrad/plapok" target="_blank">
                <svg height="32" class="octicon octicon-mark-github" viewBox="0 0 16 16" version="1.1" width="32" aria-hidden="true"><path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"></path></svg>
                        </A>
                    </span>
                </div>


                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon3" style="font-size: 20px;">
                        <A href="<?=Common::link([WebController::class,'index'])?>" title="Exit">

<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </A>
                    </span>
                </div>

                <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon3" style="font-size: 20px;">
                    <A href="#" onclick="refreshRoom();" title="debug">
                        <svg xml:space="preserve" viewBox="0 0 100 100" y="0" x="0" xmlns="http://www.w3.org/2000/svg" id="_1" version="1.1" width="24px" height="24px" xmlns:xlink="http://www.w3.org/1999/xlink" style="width:100%;height:100%;background-size:initial;background-repeat-y:initial;background-repeat-x:initial;background-position-y:initial;background-position-x:initial;background-origin:initial;background-color:initial;background-clip:initial;background-attachment:initial;animation-play-state:paused" ><g class="ldl-scale" style="transform-origin:50% 50%;transform:rotate(0deg) scale(1, 1);animation-play-state:paused" ><path stroke-miterlimit="10" stroke-width="9" stroke="#e15b64" fill="#fff" d="M41.6 18.9L11.3 71.4c-3.7 6.5.9 14.6 8.4 14.6h60.6c7.5 0 12.1-8.1 8.4-14.6L58.4 18.9c-3.7-6.4-13.1-6.4-16.8 0z" style="stroke:rgb(225, 91, 100);fill:rgb(255, 255, 255);animation-play-state:paused" ></path>
<circle fill="#333" r="5.4" cy="69.4" cx="50" style="fill:rgb(51, 51, 51);animation-play-state:paused" ></circle>
<path fill="#333" d="M55.4 43.8c0 6-1.6 11.3-3.1 14.9-.8 2.1-3.8 2.1-4.7 0-1.5-3.6-3.1-9-3.1-14.9 0-8.9 2.4-11.9 5.4-11.9s5.5 3 5.5 11.9z" style="fill:rgb(51, 51, 51);animation-play-state:paused" ></path>
<!--<metadata xmlns:d="https://loading.io/stock/" style="animation-play-state:paused" ><d:name style="animation-play-state:paused" >caution</d:name>
<d:tags style="animation-play-state:paused" >hint,note,warning,danger,reminder,sign,exclamation,caution,transportation</d:tags>
<d:license style="animation-play-state:paused" >by</d:license>
<d:slug style="animation-play-state:paused" >1t5sok</d:slug></metadata>--></g><!-- generated by https://loading.io/ --></svg>

                <!--    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path
                                d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path
                                d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>-->
                    </A>
                </span>
                </div>
                <input type="text" class="form-control" id="roomKey" aria-describedby="basic-addon3"
                       style="width:200px;height: 60px;font-size: 18px;text-align: center;"
                       value="<?= Common::link([WebController::class, 'index'], ['rk' => $this->variables['roomKey']]) ?>">
                <div class="input-group-append">
    <span class="input-group-text">
                    <button class="btn" data-clipboard-target="#roomKey">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
     stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path
            d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4"
                                                                                                      rx="1" ry="1"></rect></svg>
</button>
    </span>

                </div>
            </div>
        </div>
    </div>
</footer>




</body>
