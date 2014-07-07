<?php include_once './assets/templates/header.php'; ?>
    <section class="scrollable wrapper w-f">
        <p class="h3 text-center">WhatPulse Stats<br /><small>&nbsp;</small></p>
        <br />
        <div class="row">
            <div class="col-sm-3 col-md-3 col-lg-4">
            </div>
            
            <div class="col-sm-6 col-md-6 col-lg-4 text-center">
                <script type="text/javascript">
                    function get_action() {
                        userid = document.getElementById('userid').value;
                        /*return '/user/' + userid;*/
                        window.history.replaceState('page2', 'Title', '/user/' + userid);
                    }
                    function get_action2() {
                        teamid = document.getElementById('teamid').value;
                        return '/team/' + teamid;
                    }
                </script>

                <p class="h4">User Stats</p>
                <br />

                <form name="api" class="form-horizontal" method="GET" action="">
                    <div class="form-group">
                        <div class="col-sm-10 col-md-6 col-sm-offset-1 col-md-offset-3">
                            <div class="input-group">
                                <span class="input-group-addon no-border">Username or ID</span>
                                <input id="userid" name="id" type="text" class="form-control no-border" placeholder="Wopian" value="" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-offset-3">
                            <button type="submit" class="btn btn-default btn-primary btn-block" onClick="get_action()">View User Stats</button>
                            <span class="help-block">If the username contains special characters please use the account's ID instead</span>
                        </div>
                    </div>
                </form>

                <br />
                <br />
                <p class="h4">Team Stats <span class="label bg-danger">Beta</span></p>
                <br />

                <form name="api2" class="form-horizontal" method="GET" action="team.php">
                    <div class="form-group">
                        <div class="col-sm-10 col-md-6 col-sm-offset-1 col-md-offset-3">
                            <div class="input-group">
                                <span class="input-group-addon no-border">Team Name or ID</span>
                                <input id="teamid" name="teamid" type="text" class="form-control no-border" placeholder="Coccyx_Bashers" value="" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-offset-3">
                            <button type="submit" class="btn btn-default btn-primary btn-block">View Team Stats</button>
                            <span class="help-block">If the team name contains special characters please use the team's ID instead</span>
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </section>
<?php include './assets/temp/footer.php'; ?>
