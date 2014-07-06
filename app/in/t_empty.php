<? echo '<p class="h3 text-center">Oops!<br /><small>This team doesn&#39;t exist</small></p>
              <br />
              <div class="row">
                <div class="col-sm-2 col-md-3 col-lg-4">
                </div>
                <div class="col-sm-8 col-md-6 col-lg-4 text-center">

                <form name="api" class="form-horizontal" method="GET" action="/team.php">
                    <div class="form-group">
                        <div class="col-sm-10 col-md-6 col-sm-offset-1 col-md-offset-3">
                            <div class="input-group">
                                <span class="input-group-addon no-border">Team Name or ID</span>
                                <input name="teamid" type="text" class="form-control no-border" placeholder="Caffeine_Spider" value="" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-offset-3">
                            <button type="submit" class="btn btn-default btn-primary btn-block">View Team Stats</button>
                            <span class="help-block">If the team name contains special characters please use the team&#39;s ID instead</span>
                        </div>
                    </div>
                </form>

              </div>'; ?>
