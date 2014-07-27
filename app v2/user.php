<!doctype html>
<html>
<head>
  <title>Wopian - WhatPulse User Stats</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
  <link rel="stylesheet" type="text/css" href="../assets/css/main.css" />
</head>

<body>
  <header>
    <div>
      <h1>
        <a href="../">WhatPulse Stats</a>
      </h1>
    </div>
  </header>

  <main>

    <?php
      // Byte Conversion
      function formatBytes($bytes, $precision = 2) {
        $units = array('KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
        $bytes = round($bytes, $precision) * 1024;
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        // $bytes /= pow(1024, $pow);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
      };

      // Ordinal Number
      function formatOrd($ordnumber) {
        // return English ordinal number
        return $ordnumber.substr(date('jS', mktime(0,0,0,1,($ordnumber%10==0?9:($ordnumber%100>20?$ordnumber%10:$ordnumber%100)),2000)),-2);
      };

      // (M/B)illionth
      function bigNum($n, $precision = 2) {
        if ($n < 1000000) {
          // Less than a million
          $n_format = number_format($n, $precision);
        } else if ($n < 1000000000) {
          // Less than a billion
          $n_format = number_format($n / 1000000, $precision) . 'M';
        } else {
          // A billion or more
          $n_format = number_format($n / 1000000000, $precision) . 'B';
        }

        return $n_format;
      };

      function pulseNum($pulse) {
        if ($pulse < 60) {
          // < 1 Min
          $pulse_format = number_format(($pulse), 0);
        }
        else if ($pulse >= 60 && $pulse < 3600) {
          // >= 1 Min, < 1 Hour
          $pulse_format = number_format(($pulse / 60), 0);
        }
        else if ($pulse >= 3600 && $pulse < 86400) {
          // >= 1 Hour, < 1 Day
          $pulse_format = number_format(($pulse / 3600), 0);
        }
        else if ($pulse >= 86400 && $pulse < 604800) {
          // >= 1 Day, < 7 Days
          $pulse_format = number_format(($pulse / 86400), 0);
        }
        else if ($pulse >= 604800) {
          // >= 1 Week
          $pulse_format = number_format(($pulse / 86400), 0);
        }

        return $pulse_format;
      };

      function pulseOrd($pulse) {
        if ($pulse < 60) {
          // < 1 Min
          $pulse_ord = "Seconds";
        }
        else if ($pulse >= 60 && $pulse < 3600) {
          // >= 1 Min, < 1 Hour
          $pulse_ord = "Minutes";
        }
        else if ($pulse >= 3600 && $pulse < 86400) {
          // >= 1 Hour, < 1 Day
          $pulse_ord = "Hours";
        }
        else if ($pulse >= 86400 && $pulse < 604800) {
          // >= 1 Day, < 7 Days
          $pulse_ord = "Days";
        }
        else if ($pulse >= 604800) {
          // >= 1 Week
          $pulse_ord = "Weeks";
        }

        return $pulse_ord;
      };

      // WhatPulse Stats
      class Stat {
        private $data;//contains total perminute perhour perday
        function __construct($tot,&$totaltime,$precision=3) {
          $this->data['total'] = $tot;
          $this->calculate($totaltime);
          $this->format($precision);
        }
        function __get($v) {
          return $this->data[$v];
        }
        private function calculate(&$time) {
          $this->data['persecond'] = $this->total/$time;
          $this->data['perminute'] = $this->total/($time/60);
          $this->data['perhour'] = $this->total/($time/3600);
          $this->data['perday'] = $this->total/($time/86400);
          $this->data['perweek'] = $this->total/($time/604800);
          $this->data['permonth'] = $this->total/($time/2629740);
          $this->data['peryear'] = $this->total/($time/31556900);
        }
        private function format(&$precision) {
          $zero = number_format($this->data['total'],0);
          array_walk($this->data,function(&$value,$key) use ($precision) {
            $value = number_format($value,$precision);
          });
          $this->data['total'] = $zero;
        }
      }

      class WhatPulse {
        private $id;//whatpulse id
        private $xml;//xml obtained from whatpulse
        private $clicks;//clicks
        private $keys;//keys
        private $minutes;//user account age in minutes(string formatted)
        private $hours;//user account age in hours(string formatted)
        private $days;//user account age in days (string formatted)
        private $network;//user total network operations in megabytes (string formatted)
        private $networkratio;//download:upload ratio
        private $download;//Stats object of user download in megabytes (string formatted)
        private $upload;//Stats object of user upload in megabytes (string formatted)
        private $uptime;//user total uptime in hours(string formatted)
        private $lastpulse;//unix timestamp of last pulse
        private $lastpulseago;//seconds between now and last pulse
        private $_retrievable = array('id','totalclicks','totalkeys','minutes','hours','days');///variables retrievable using magic functions
        private $built;//whether or not the class has been built

        public function __construct($id) {
          $this->id = $id;
          $this->getXML();
          $this->perform();
        }
        public function __get($name) {
          switch($name) {
            case 'name':
              return $this->xml->AccountName;
            case 'rank':
              return $this->xml->Rank;
            case 'id':
              return $this->xml->UserID;
            case 'pulses':
              return $this->xml->Pulses;
          }
          if(!in_array($name,$this->_retrievable))
            throw new Exception('Variable '.$name.' does not exist in class WhatPulse.');
            return $this->$name;
        }
        private function perform() {
          //time calculation
          $totaltime = time()-strtotime($this->xml->DateJoined);
          $minutes = $totaltime/60;
          $hours = $minutes/60;
          $days = $hours/24;
          $this->minutes = number_format($minutes,2);
          $this->hours = number_format($hours,2);
          $this->days = number_format($days,2);
          $this->clicks = new Stat($this->xml->Clicks+0.0,$totaltime);
          $this->keys = new Stat($this->xml->Keys+0.0,$totaltime);
          //lastpulse
          $temp = date_default_timezone_get();//temporarily store current timezone
          date_default_timezone_set('Europe/Belgrade');//belgrade is where server located
          $datetime = new DateTime($this->xml->LastPulse);//create new DT from belgrade time
          $datetime->setTimezone(new DateTimeZone($temp));//convert belgrade time to current time
          date_default_timezone_set($temp);//reset timezone back to default
          $this->lastpulse = $datetime->getTimestamp();//set lastpulse unix timestamp
          $this->lastpulseago = time()-$this->lastpulse;//get time diff between now and lastpulse
          //network
          $inception = 1356156000;///<time of client 2.0/bandwidth stats release
          $inception = time()-$inception;
          $this->network = number_format($this->xml->DownloadMB+$this->xml->UploadMB,2);
          $this->networkratio = number_format($this->xml->DownloadMB/$this->xml->UploadMB,2);
          if($inception > $totaltime)
            $inception = $totaltime;
            $this->download = new Stat($this->xml->DownloadMB+0.0,$inception);
            $this->upload = new Stat($this->xml->UploadMB+0.0,$inception);
            $this->uptime = number_format($this->xml->UptimeSeconds/3600,3);
        }
        private function getXML() {
          $url = 'http://whatpulse.org/api/user.php?format=xml&user=';
          $f = fopen($url.$this->id,'r');
          if($f === false) {
            throw new Exception('Could not open '.$url.$this->id.'. Please check your internet connection');
          }
          $content = stream_get_contents($f);
          $this->xml = new SimpleXMLElement($content);
        }
        function printStats() {
          if (!empty ($this->xml->AccountName)) {
            echo '
              <div class="bg">
                <p>User Info</p>
                <div class="Grid">

                  <div class="Grid-cell">
                    <div>
                      <small>Username</small>
                      <span class="h2">'.$this->xml->AccountName.'</span>
                      <small>ID '.$this->xml->UserID.'</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Joined</small>
                      <span class="h2">'.$this->days.'</span>
                      <small>Days Ago</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Pulses</small>
                      <span class="h2">'.$this->xml->Pulses.'</span>
                      <small>'.round($this->xml->Pulses / $this->days).' Per Day</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Last Pulsed</small>
                      <span class="h2">'.pulseNum($this->lastpulseago).'</span>
                      <small>'.pulseOrd($this->lastpulseago).' Ago</small>
                    </div>
                  </div>

                </div>
              </div>

              <div class="bg2">
                <p>User Stats</p>
                <div class="Grid">

                  <div class="Grid-cell">
                    <div>
                      <small>Keys</small>
                      <span class="h2">'.$this->keys->total.'</span>
                      <small>'.formatOrd(str_replace(",", "",$this->xml->Ranks->Keys)).'</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Clicks</small>
                      <span class="h2">'.$this->clicks->total.'</span>
                      <small>'.formatOrd(str_replace(",", "",$this->xml->Ranks->Clicks)).'</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Total Keys & Clicks</small>
                      <span class="h2">'.number_format((str_replace(",", "",$this->keys->total) + str_replace(",", "",$this->clicks->total))).'</span>
                      <small>&nbsp;</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Keys & Clicks Ratio</small>
                      <span class="h2 chart">'.round((str_replace(",", "",$this->keys->total) / str_replace(",", "",$this->clicks->total)), 2).':1<span class="pie pie-a" data-diameter="24">'.str_replace(",", "",$this->clicks->total).','.str_replace(",", "",$this->keys->total).'</span></span>
                      <small>Keys To Clicks</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Downloaded</small>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->download->total)).'</span>
                      <small>'.formatOrd(str_replace(",", "",$this->xml->Ranks->Download)).'</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Uploaded</small>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->upload->total)).'</span>
                      <small>'.formatOrd(str_replace(",", "",$this->xml->Ranks->Upload)).'</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Total Download & Upload</small>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->network)).'</span>
                      <small>&nbsp;</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Download & Upload Ratio</small>
                      <span class="h2 chart">'.$this->networkratio.':1<span class="pie pie-a" data-diameter="24">'.str_replace(",", "",$this->upload->total).','.str_replace(",", "",$this->download->total).'</span></span>
                      <small>Download To Upload</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Uptime</small>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->uptime)).'</span>
                      <small>'.formatOrd(str_replace(",", "",$this->xml->Ranks->Uptime)).'</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Average Uptime</small>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->uptime) / str_replace(",", "",$this->days)).'</span>
                      <small>Hours</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Uptime</small>
                      <span class="h2 chart">'.bigNum((str_replace(",", "",$this->uptime)/(str_replace(",", "",$this->days)*24))*100).'%</span>
                      <small>Of Account Age</small>
                    </div>
                  </div>

                </div>
              </div>

              <div class="bg">
                <p>Key Stats</p>
                <div class="Grid">

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->keys->perminute)).'</span>
                      <small>Per Minute</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->keys->perhour)).'</span>
                      <small>Per Hour</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->keys->perday)).'</span>
                      <small>Per Day</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->keys->permonth)).'</span>
                      <small>Per Month</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->keys->peryear)).'</span>
                      <small>Per Year</small>
                    </div>
                  </div>

                </div>
              </div>

              <div class="bg2">
                <p>Click Stats</p>
                <div class="Grid">

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->clicks->perminute)).'</span>
                      <small>Per Minute</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->clicks->perhour)).'</span>
                      <small>Per Hour</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->clicks->perday)).'</span>
                      <small>Per Day</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->clicks->permonth)).'</span>
                      <small>Per Month</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.bigNum(str_replace(",", "",$this->clicks->peryear)).'</span>
                      <small>Per Year</small>
                    </div>
                  </div>

                </div>
              </div>

              <div class="bg">
                <p>Download Stats</p>
                <div class="Grid">

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->download->perminute)).'</span>
                      <small>Per Minute</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->download->perhour)).'</span>
                      <small>Per Hour</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->download->perday)).'</span>
                      <small>Per Day</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->download->permonth)).'</span>
                      <small>Per Month</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->download->peryear)).'</span>
                      <small>Per Year</small>
                    </div>
                  </div>

                </div>
              </div>

              <div class="bg2">
                <p>Upload Stats</p>
                <div class="Grid">

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->upload->perminute)).'</span>
                      <small>Per Minute</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->upload->perhour)).'</span>
                      <small>Per Hour</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->upload->perday)).'</span>
                      <small>Per Day</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->upload->permonth)).'</span>
                      <small>Per Month</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">'.formatBytes(str_replace(",", "",$this->upload->peryear)).'</span>
                      <small>Per Year</small>
                    </div>
                  </div>

                </div>
              </div>';
              };

              if (empty ($this->xml->AccountName)) {
                echo '<div class="bg"><p>Oops<br /> this account doesn&#39;t exist!</p><div class="Grid">
                  <div class="Grid-cell Exist">
                    <div>
                      <small>User Stats</small>
                      <input id="userid" name="id" type="text" placeholder="Wopian" value="" required>
                      <button type="submit" onclick="user()">View</button>
                    </div>
                  </div>
                </div></div>';
              };
            }
          }
          $stats_id = $_GET["id"];
          $stats = new WhatPulse($stats_id);
          $stats->printStats();
    ?>

  </main>

  <footer>
    <pre>dev.boomcraft.co.uk/4</pre>
  </footer>

  <script src="../assets/javascript/app.js"></script>
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
    function user() {
      userid = document.getElementById('userid').value;
      /*window.history.replaceState('page2', 'Title', '/user/' + userid);*/
      location.href = '/user/' + userid;
    };
  </script>

</body>
</html>
