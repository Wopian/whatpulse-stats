<!doctype html>
<html>
<head>
  <title>WhatPulse Stats</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="./assets/css/main.css" type="text/css" />
  <style>
    @media screen and (max-width: 720px){
      .User {
        order: 1;
      }
      .Team {
        order: 2;
      }
      .Compare {
        order: 3;
      }
    }
  </style>
</head>

<body>
  <header>
    <div><h1><a href="./" data-animation="4">WhatPulse Stats</a></h1></div>
    <nav><a href="./user" data-animation="3">User</a> <a href="./team" data-animation="3">Team</a> <a href="./#compare" data-animation="3">Compare</a></nav>
  </header>

  <main>

    <div class="bg">
      <div class="Grid">

        <div class="Grid-cell User">
          <div>
            <small>User Stats</small>
            <input id="userid" name="id" type="text" placeholder="Wopian" value="" required>
            <button type="submit" onclick="user()">View</button>
          </div>
        </div>

        <div class="Grid-cell Compare">
          <div>
            <small>Compare Stats</small>
            <br />
            <span class="h2">Coming Soon!</span>
            <br />
          </div>
        </div>

        <div class="Grid-cell Team">
          <div>
            <small>Team Stats</small>
            <input id="teamid" name="teamid" type="text" placeholder="Reddit" value="" required>
            <button type="submit" onclick="team()">View</button>
            </form>
          </div>
        </div>

      </div>
    </div>

  </main>

  <footer>
    <pre>dev.boomcraft.co.uk/4</pre>
  </footer>

  <script type="text/javascript">
    function user() {
      userid = document.getElementById('userid').value;
      /*window.history.replaceState('page2', 'Title', '/user/' + userid);*/
      location.href = '/user/' + userid;
    }
    function team() {
      teamid = document.getElementById('teamid').value;
      /*window.history.replaceState('page2', 'Title', '/team/' + teamid);*/
      location.href = '/team/' + teamid;
    }
  </script>

</body>
</html>
