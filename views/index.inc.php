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
<?php  if (isset($_GET['rk'])) { ?>
    $(document).ready(function () {
        $('#jrk').focus();
    });

<?php } ?>

</script>
<div class="page-wrap">
<div class="container justify-content-center">
    <div class="row justify-content-center">
        <h3><img src="assets/card-1.1s-100px.svg" alt="cards">Planing Poker<img src="assets/card-flip-1.1s-100px.svg" alt="cards2"></h3>
    </div>
</div>
<br>

<div class="container justify-content-center">
    <div class="row justify-content-center">
        <div class="col justify-content-center" align="right">
            <div class="card justify-content-center" style="width: 300px;" align="center">
              <h5 class="card-header">Join Room</h5>
              <div class="card-body justify-content-center">
                <h5 class="card-title" style="padding-left: 0px;">
                    <img src="assets/login-2s-100px.svg" alt="login">
                    </h5>
                <form action="<?= Common::link([WebController::class, 'joinRoom'])?>" method="post">
                <p class="card-text">
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" name="room_key" placeholder="Room key" required value="<?php
                      if (isset($_GET['rk'])) {
                          echo preg_replace('/[^a-zA-Z0-9]/', '', $_GET['rk']);
                      }
                      ?>">
                    </div>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" name="name"  id="jrk" placeholder="Your name" required autocomplete="off" value="<?php
                      echo (!empty($_COOKIE[RoomService::COOKIE_NAME]) ? $_COOKIE[RoomService::COOKIE_NAME] : '');
                      ?>">
                    </div>
                </p>
                    <button class="btn btn-primary">Join Room</button>
                </form>
              </div>
            </div>
        </div>

        <div class="col justify-content-center">
            <div class="card" style="width: 300px;" align="center">
              <h5 class="card-header">Create Room</h5>
              <div class="card-body justify-content-center" align="center">
                <h5 class="card-title" style="padding-left: 0px;">
                    <img src="assets/add-0.7s-100px.svg" alt="create">
                    </h5>
                <p class="card-text">
                    <div class="input-group mb-3">
                      &nbsp;
                    </div>
                  <form action="<?= Common::link([WebController::class, 'createRoom'])?>" method="post">
                    <div class="input-group mb-3" style="padding-top: 14px;">
                      <input type="text" name="username" class="form-control" id="basic-url" aria-describedby="basic-addon3" autocomplete="off" placeholder="Your name" required value="<?php
                       echo (!empty($_COOKIE[RoomService::COOKIE_NAME]) ? $_COOKIE[RoomService::COOKIE_NAME] : '');
                      ?>">
                    </div>
                </p>
                      <button class="btn btn-success">Create Room</button>
                  </form>
              </div>
            </div>

        </div>

    </div>
</div>

</div>
<footer class="site-footer text-center" style="padding: 15px;">
    <A href="https://github.com/alexconrad/plapok" target="_blank">
<svg height="32" class="octicon octicon-mark-github" viewBox="0 0 16 16" version="1.1" width="32" aria-hidden="true"><path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"></path></svg>
    </A>
</footer>


</body>
