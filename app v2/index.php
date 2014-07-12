<!doctype html>
<html prefix="og: http://ogp.me/ns#">
<head>
  <title>WhatPulse Stats</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="./assets/favicon.ico" type="image/x-icon" />  
  <link rel="stylesheet" href="./assets/css/main.css" type="text/css" />
  <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <script src="./assets/javascript/chart.js"></script>
  <meta property="og:title" content="WhatPulse Stats" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="http://dev.boomcraft.co.uk/4" />
  <meta property="og:image" content="http://dev.boomcraft.co.uk/4/assets/logo.png" />
  <script>
    $(function() {
      $("span.pie").peity("pie");

      $(".pie-a").peity("pie", {
        fill: ["#E1E6EF", "#177BBB"]
      });

      $(".pie-b").peity("pie", {
        fill: ["#177BBB", "#E1E6EF"]
      });
    });

  </script>
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
  <div><h1><a href="./">WhatPulse Stats</a></h1></div>
  <nav><a href="./user">User</a> <a href="./team">Team</a> <a href="./#compare">Compare</a></nav>
</header>

<main>

<div class="bg">

  <div class="Grid">

    <div class="Grid-cell User">
      <div>
        <small>User Stats</small>
        <input type="text" placeholder="Wopian" />
        <button>View</button>
      </div>
    </div>

    <div class="Grid-cell Compare">
      <div>
        <small>Compare Stats</small>
        <!--<form>
          <input type="text" class="Compare" />
          <input type="text" /><br />
          <form>
            <input type="radio" name="selector" value="users" checked />Users
            <input type="radio" name="selector" value="teams"/>Teams<br />
          </form>
          <button>Compare</button>
        </form>-->
        <br />
        <span class="h2">Coming Soon!</span>
        <br />
      </div>
    </div>

    <div class="Grid-cell Team">
      <div>
        <small>Team Stats</small>
        <input type="text" placeholder="Reddit" />
        <button>View</button>
      </div>
    </div>

  </div>
</div>

</main>

<footer>
<pre>dev.boomcraft.co.uk/4</pre>
</footer>

</body>
</html>
