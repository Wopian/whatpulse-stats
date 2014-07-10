<!doctype html>

<head>
  <title>WhatPulse Stats</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <link rel="stylesheet" href="./assets/css/main.css" type="text/css" />
  <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <script src="./assets/javascript/chart.js"></script>
  <script>
    $(function() {
      $("span.pie").peity("pie");

      $(".pie-a").peity("pie", {
        fill: ["#F2F4F8", "#177BBB"]
      });

      $(".pie-b").peity("pie", {
        fill: ["#177BBB", "#F2F4F8"]
      });
    });

  </script>
</head>

<body>

<header>
  <div><h1>WhatPulse Stats</h1></div>
  <nav><a class="cl-effect-15" href="./user">User Stats</a><a>Team Stats</a><a>Compare</a></nav>
</header>

<main>
  <div class="Grid">

    <div class="Grid-cell">
      <div>
        <small>Username</small>
        <span class="h2">Wopian</span>
        <small>ID 490180</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Joined</small>
        <span class="h2">598.81</span>
        <small>Days Ago</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Pulses</small>
        <span class="h2">5932</span>
        <small>&nbsp;</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Last Pulsed</small>
        <span class="h2">51.6</span>
        <small>Minutes Ago</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Keys</small>
        <span class="h2">5,081,308</span>
        <small>11905th</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Clicks</small>
        <span class="h2">2,467,337</span>
        <small>14395th</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Total Keys & Clicks</small>
        <span class="h2">7,548,645</span>
        <small>&nbsp;</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Keys & Clicks Ratio</small>
        <span class="h2 chart">2.06:1<span class="pie pie-a" data-diameter="24">2467337,5081308</span></span>
        <small>Keys To Clicks</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Downloaded</small>
        <span class="h2">2.26 TB</span>
        <small>1126th</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Uploaded</small>
        <span class="h2">552.61 GB</span>
        <small>2207th</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Total Download & Upload</small>
        <span class="h2">2.8 TB</span>
        <small>&nbsp;</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Download & Upload Ratio</small>
        <span class="h2 chart">4.19:1<span class="pie pie-a" data-diameter="24">552.61,2314.24</span></span>
        <small>Download To Upload</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Uptime</small>
        <span class="h2">3,341.76</span>
        <small>7433rd</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Average Uptime</small>
        <span class="h2">5.58</span>
        <small>Hours</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Uptime</small>
        <span class="h2 chart">23.25%<span class="pie pie-a" data-diameter="24">14371.4,3341.76</span></span>
        <small>Of Account Age</small>
      </div>
    </div>

    <div class="Grid-cell">
      <div>
        <small>Keys</small>
        <div class="Per">
          <div>
            <span class="h2">5.89</span>
            <small>Per Minute</small>
          </div>
          <div>
            <span class="h2">353.66</span>
            <small>Per Hour</small>
          </div>
          <div>
            <span class="h2">8,487.91</span>
            <small>Per Day</small>
          </div>
          <div>
            <span class="h2">258,344.85</span>
            <small>Per Month</small>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</main>

<footer>
<pre>dev.boomcraft.co.uk/4</pre>
</footer>

</body>
