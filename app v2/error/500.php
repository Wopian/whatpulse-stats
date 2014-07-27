<!doctype html>
<html>
<head>
  <title>500 Server Error - WhatPulse Stats</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="/assets/css/main.css" type="text/css" />
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
    <div>
      <h1>
        <a href="/" data-animation="4">WhatPulse Stats</a>
      </h1>
    </div>
  </header>

  <main>

    <div class="bg">
      <p><strong>500</strong><br /> Server Error</p>
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
          </div>
        </div>

      </div>
    </div>

  </main>

  <footer>
    <div>
      <span class="left">
        <a href="/stats">Overall Stats</a> //
        <a href="/privacy">Privacy Policy</a>
      </span>
      <span class="right">
        <a href="//jamesharris.net">Portfolio</a> //
        <a href="//colonizer.jamesharris.net">Colonizer</a> //
        <a href="//lastfm.jamesharris.net">Lastistics</a> //
        <a href="//boomcraft.co.uk">Boomcraft</a>
      </span>
    </div>
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
