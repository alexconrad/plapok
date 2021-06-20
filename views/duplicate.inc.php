<?php
/** @var ViewService $this */

use PlaPok\Common\Common;
use PlaPok\Services\RoomService;
use PlaPok\Services\ViewService;
use PlaPok\Controllers\WebController;

$this->display('_head.inc.php');
?>
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
        margin-bottom: -80px;
    }

    .page-wrap:after {
        content: "";
        display: block;
    }

    .site-footer, .page-wrap:after {
        height: 80px;
    }

    .site-footer {
        background: #f0f0f0;
    }
</style>
<body style="margin-top: 20px;">
<script>
    $(document).ready(function () {
        <?php  if (isset($_GET['rk'])) { ?>
            $('#jrk').focus();
        <?php } ?>
        <?php if ($this->variables['youAreKicked']) { ?>
            $('#youAreKicked').modal(true);
        <?php } ?>
    });


</script>
<div class="page-wrap">
    <div class="container justify-content-center">
        <div class="row justify-content-center">
            <h3><img src="assets/card-1.1s-100px.svg" alt="cards">Planing Poker<img src="assets/card-flip-1.1s-100px.svg" alt="cards2">
            </h3>
        </div>
    </div>

    <div class="container justify-content-center">
        <div class="row justify-content-center">
            <div class="col justify-content-center">
                <div class="card justify-content-center" align="center">
                    <h5 class="card-header" style="background-color: #FFBBBB;">Join Room error</h5>

                    <div class="card-body justify-content-center">
                        <p class="card-text">
                            You tried to join with a name that already exists in the Room ! <br>
                            Choose another name or ask the person that created to room to kick that person.
                        </p>
                        <button class="btn btn-primary" onclick="document.location='/';">Join Room</button>
                        </form>
                    </div>




                </div>
            </div>
        </div>
    </div>
</div>





