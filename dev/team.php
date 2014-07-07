<?php include './assets/temp/header.php'; ?>
          <script>
window.history.replaceState('page2', 'Title', '/team/<?php echo $_GET["teamid"] ?>');
</script>
          <section class="scrollable wrapper w-f">
            <?

// Byte Conversion
function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'); 

    $bytes = round($bytes, $precision)*1024*1024; 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // $bytes /= pow(1024, $pow);
     $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
};

// 1st 2nd 3rd 4th...
function formatOrd($ordnumber) {
  // return English ordinal number
  return $ordnumber.substr(date('jS', mktime(0,0,0,1,($ordnumber%10==0?9:($ordnumber%100>20?$ordnumber%10:$ordnumber%100)),2000)),-2);
};

// Million Billion...
function largenumber($n, $precision = 2) {
    if ($n < 1000000) {
        // Anything less than a million
        $n_format = number_format($n, $precision);
    } else if ($n < 1000000000) {
        // Anything less than a billion
        $n_format = number_format($n / 1000000, $precision) . 'M';
    } else {
        // At least a billion
        $n_format = number_format($n / 1000000000, $precision) . 'B';
    }

    return $n_format;
};

// WhatPulse Stats
class Stat {
    private $data;///<contains total perminute perhour perday
    function __construct($tot,&$totaltime,$precision=2) {
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
    private $clicks;///<Stat object with information on clicks
    private $keys;///<Stat object with information on keys
    private $minutes;///<user account age in minutes(string formatted)
    private $hours;///<user account age in hours(string formatted)
    private $days;///<user account age in days (string formatted)
    private $network;///<user total network operations in megabytes (string formatted)
    private $networkratio;///<download:upload ratio
    private $download;///<Stats object of user download in megabytes (string formatted)
    private $upload;///<Stats object of user upload in megabytes (string formatted)
    private $uptime;///<user total uptime in hours(string formatted)
    private $lastpulse;///<unix timestamp of last pulse
    private $lastpulseago;///<seconds between now and last pulse
    private $_retrievable = array('id','totalclicks','totalkeys','minutes','hours','days');///<variables retrievable using magic functions
    private $built;///<whether or not the class has been built

    public   function __construct($id) {
        $this->id = $id;
        $this->getXML();
        $this->perform();
    }
    public function __get($name) {
        switch($name) {
        case 'name':
            return $this->xml->Name;
        case 'rank':
            return $this->xml->Rank;
        case 'id':
            return $this->xml->TeamID;
        case 'pulses':
            return $this->xml->Pulses;
        }
        if(!in_array($name,$this->_retrievable))
            throw new Exception('Variable '.$name.' does not exist in class WhatPulse.');
        return $this->$name;
    }
    private    function perform() {
//time calculation
        $totaltime = time()-strtotime($this->xml->DateFormed);
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
//echo $datetime->format('Y-m-d H:i:s').'::::'.$this->xml->LastPulse;
//-----------------------------------------------------------------------------
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
        $url = 'http://whatpulse.org/api/team.php?format=xml&team=';

        $f = fopen($url.$this->id,'r');
        if($f === false) {
            throw new Exception('Could not open '.$url.$this->id.'. Please check your internet connection');
        }
        $content = stream_get_contents($f);
//echo $content;
        $this->xml = new SimpleXMLElement($content);
//var_dump($this->xml);
    }
    function printStats() {
      if (!empty ($this->xml->Name)) {
        include_once "./assets/temp/t.php";
      };

      if (empty ($this->xml->Name)) {
        include_once "./assets/temp/t_empty.php";
      };
/*echo 'Account Name: '.$this->xml->AccountName.' (id ' .$this->xml->UserID.")\n";
  echo $this->xml->Pulses.' pulses (last pulsed '.number_format($this->lastpulseago/3600,2).' hours ago '.date('n/j/y @ g:iA',$this->lastpulse).")\n";
  echo 'Key presses: '.$this->keys->total.' (ranked '.$this->xml->Ranks->Keys.")\n";
  echo "\t".$this->keys->perminute.'/minute'."\n\t".$this->keys->perhour.'/hour'."\n\t".$this->keys->perday.'/day'."\n";
  echo 'Mouse clicks: '.$this->clicks->total.' (ranked '.$this->xml->Ranks->Clicks.")\n";
  echo "\t".$this->clicks->perminute.'/minute'."\n\t".$this->clicks->perhour.'/hour'."\n\t".$this->clicks->perday.'/day'."\n";
  echo 'Total network operations: '.$this->network.' MBytes ('.$this->networkratio.' D/U ratio'.")\n";
  echo "\t".$this->download->total.' MBytes downloaded (ranked '.$this->xml->Ranks->Download.")\n";
  echo "\t\t".$this->download->perminute.'/minute'."\n";
  echo "\t\t".$this->download->perhour.'/hour'."\n";
  echo "\t\t".$this->download->perday.'/day'."\n";
  echo "\t".$this->upload->total.' MBytes uploaded (ranked '.$this->xml->Ranks->Upload.")\n";
  echo "\t\t".$this->upload->perminute.'/minute'."\n";
  echo "\t\t".$this->upload->perhour.'/hour'."\n";
  echo "\t\t".$this->upload->perday.'/day'."\n";
  echo 'Total uptime: '.$this->uptime." hours\n";
  echo 'Date joined: '.$this->xml->DateJoined.' ('.$this->days.' days)'."\n";*/
    }
}
$stats_id = $_GET["teamid"];
//$stats_id = 22068;
$stats = new WhatPulse($stats_id);
$stats->printStats();
?>
          </section>
<?php include './assets/temp/footer.php'; ?>
