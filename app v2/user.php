<!doctype html>
<html>
<head>
  <title>Wopian - WhatPulse User Stats</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="../assets/css/main.css" type="text/css" />
</head>

<body>
  <header>
    <div><h1><a href="../">WhatPulse Stats</a></h1></div>
    <nav><a href="./">User</a> <a href="../team">Team</a> <a href="../compare">Compare</a></nav>
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
                      <small>Avg. '.round($this->xml->Pulses / $this->days).' Per Day</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Last Pulsed</small>
                      <span class="h2">'.number_format($this->lastpulseago / 3600, 2).'</span>
                      <small>Minutes Ago</small>
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
                      <small>'.$this->xml->Ranks->Keys.'</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <small>Clicks</small>
                      <span class="h2">'.$this->clicks->total.'</span>
                      <small>'.$this->xml->Ranks->Clicks.'</small>
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
                      <span class="h2 chart">'.round((str_replace(",", "",$this->keys->total) / str_replace(",", "",$this->clicks->total)), 1).':1<span class="pie pie-a" data-diameter="24">'.str_replace(",", "",$this->clicks->total).','.str_replace(",", "",$this->keys->total).'</span></span>
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
                      <span class="h2 chart">'.bigNum((str_replace(",", "",$this->uptime)/(str_replace(",", "",$this->days)*24))*100).'%<span class="pie pie-a" data-diameter="24">'.str_replace(",", "",$this->uptime).','.str_replace(",", "",$this->days).'</span></span>
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
                      <span class="h2">5.8</span>
                      <small>Per Minute</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">353.6</span>
                      <small>Per Hour</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">8,487.9</span>
                      <small>Per Day</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">258,344.8</span>
                      <small>Per Month</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">3.1 M</span>
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
                      <span class="h2">2.8</span>
                      <small>Per Minute</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">172.2</span>
                      <small>Per Hour</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">4,133.1</span>
                      <small>Per Day</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">125,799.2</span>
                      <small>Per Month</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">1.5 M</span>
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
                      <span class="h2">2.9 MB</span>
                      <small>Per Minute</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">174.9 MB</span>
                      <small>Per Hour</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">4.1 GB</span>
                      <small>Per Day</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">124.8 GB</span>
                      <small>Per Month</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">1.4 TB</span>
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
                      <span class="h2">706 KB</span>
                      <small>Per Minute</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">41.6 MB</span>
                      <small>Per Hour</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">999.5 MB</span>
                      <small>Per Day</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">29.7 GB</span>
                      <small>Per Month</small>
                    </div>
                  </div>

                  <div class="Grid-cell">
                    <div>
                      <span class="h2">365.5 GB</span>
                      <small>Per Year</small>
                    </div>
                  </div>

                </div>
              </div>';
              };

              if (empty ($this->xml->AccountName)) {
                echo '<p class="h3 text-center">Oops!<br /><small>This account doesn&#39;t exist</small></p>';
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
    </script>

</body>
</html>
