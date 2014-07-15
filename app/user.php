<?php include './assets/temp/h.php'; ?>
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
    private    function perform() {
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
        $url = 'http://whatpulse.org/api/user.php?format=xml&user=';

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
      if (!empty ($this->xml->AccountName)) {
        echo '<script>
                function toggleText() {
                  $(".text").toggle();
                }
              </script>
              <p class="h3 text-center">'.$this->xml->AccountName.'<br /><small>ID '.$this->xml->UserID.'</small></p>
              <br />
              <div class="row">
                <div class="col-sm-2 col-md-3 col-lg-4">
                </div>
                <div class="col-sm-8 col-md-6 col-lg-4 text-center">

<!-- USER -->

<div class="col-md-12">
<div class="panel">
  <div class="panel-heading">
    <p class="panel-title">User Info</p>
  </div>
  <div class="panel-body row">

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Joined
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.$this->days.'
          </div>
          <p class="panel-title text-gray">
            days ago
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Pulses
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.$this->xml->Pulses.'
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Last Pulsed
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.number_format($this->lastpulseago/3600,2).'
          </div>
          <p class="panel-title text-gray">
            hours ago
          </p>
        </div>
      </div>
    </div>

  </div>
</div>
</div>

<!-- ./END USER -->

<!-- KEYBOARD & MOUSE -->

<div class="col-md-12">
<div class="panel">
  <div class="panel-heading">
    <p class="panel-title">Keyboard & Mouse</p>
  </div>
  <div class="panel-body row">

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Keys
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.$this->keys->total.'
          </div>
          <p class="panel-title text-gray">
            ('.formatOrd(str_replace(",", "",$this->xml->Ranks->Keys)).')
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Total
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.number_format((str_replace(",", "",$this->keys->total) + str_replace(",", "",$this->clicks->total))).'
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Clicks
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.$this->clicks->total.'
          </div>
          <p class="panel-title text-gray">
            ('.formatOrd(str_replace(",", "",$this->xml->Ranks->Clicks)).')
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-12 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Ratio
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.round((str_replace(",", "",$this->keys->total) / str_replace(",", "",$this->clicks->total)), 2).':1
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
</div>

<!-- ./END KEYBOARD & MOUSE -->

<!-- BANDWIDTH -->

<div class="col-md-12">
<div class="panel">
  <div class="panel-heading">
    <p class="panel-title">Bandwidth</p>
  </div>
  <div class="panel-body row">

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Download
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->download->total.' MB">
          <div class="h3 font-bold">
            '.formatBytes(str_replace(",", "",$this->download->total)).'
          </div>
          <p class="panel-title text-gray">
            ('.formatOrd(str_replace(",", "",$this->xml->Ranks->Download)).')
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Total
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->network.' MB">
          <div class="h3 font-bold">
            '.formatBytes(str_replace(",", "",$this->network)).'
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Upload
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->upload->total.' MB">
          <div class="h3 font-bold">
            '.formatBytes(str_replace(",", "",$this->upload->total)).'
          </div>
          <p class="panel-title text-gray">
            ('.formatOrd(str_replace(",", "",$this->xml->Ranks->Upload)).')
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-12 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Ratio
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.$this->networkratio.':1
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
</div>

<!-- ./END BANDWIDTH -->

<!-- UPTIME -->

<div class="col-md-12">
<div class="panel">
  <div class="panel-heading">
    <p class="panel-title">Uptime</p>
  </div>
  <div class="panel-body row">

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Uptime
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.largenumber(str_replace(",", "",$this->uptime)).'
          </div>
          <p class="panel-title text-gray">
            ('.formatOrd(str_replace(",", "",$this->xml->Ranks->Uptime)).')
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Total
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.largenumber((str_replace(",", "",$this->uptime)/(str_replace(",", "",$this->days)*24))*100).'%
          </div>
          <p class="panel-title text-gray">
            of account age
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Average Uptime
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.largenumber(str_replace(",", "",$this->uptime)/str_replace(",", "",$this->days)).'
          </div>
          <p class="panel-title text-gray">
            hours
          </p>
        </div>
      </div>
    </div>

  </div>
</div>
</div>

<!-- ./END UPTIME -->

<!-- MISCELLANEOUS -->

<div class="col-md-12">
<div class="panel">
  <div class="panel-heading">
    <p class="panel-title">
      Miscellaneous
    </p>
    <br />
    <ul class="nav nav-tabs nav-justified no-borders panel-title">
      <li class="active"><a href="#keys" data-toggle="tab">Keys</a></li>
      <li><a href="#clicks" data-toggle="tab">Clicks</a></li>
      <li><a href="#download" data-toggle="tab">Download</a></li>
      <li><a href="#upload" data-toggle="tab">Upload</a></li>
    </ul>
  </div>
  <div class="panel-body row">
    <div class="tab-content">

      <div class="tab-pane active" id="keys">

        <div class="col-md-4 no-border">
          <div class=" panel-heading no-border text-center">
            <p class="panel-title">
              Keys/second
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->keys->persecond)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border text-center">
            <p class="panel-title text-gray">
              Keys/minute
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->keys->perminute)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Keys/hour
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->keys->perhour)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Keys/day
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->keys->perday)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Keys/month
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->keys->permonth)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Keys/year
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.number_format(round(str_replace(",", "",$this->keys->peryear), 0)).' clicks">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->keys->peryear)).'
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="tab-pane" id="clicks">
        
        <div class="col-md-4 no-border">
          <div class=" panel-heading no-border text-center">
            <p class="panel-title">
              Clicks/second
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->clicks->persecond)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border text-center">
            <p class="panel-title text-gray">
              Clicks/minute
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->clicks->perminute)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Clicks/hour
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->clicks->perhour)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Clicks/day
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->clicks->perday)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Clicks/month
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->clicks->permonth)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Clicks/year
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.number_format(round(str_replace(",", "",$this->clicks->peryear), 0)).' clicks">
              <div class="h3 font-bold">
                '.largenumber(str_replace(",", "",$this->clicks->peryear)).'
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="tab-pane" id="download">

        <div class="col-md-4 no-border">
          <div class=" panel-heading no-border text-center">
            <p class="panel-title">
              Download/second
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->download->persecond.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->download->persecond)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border text-center">
            <p class="panel-title text-gray">
              Download/minute
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->download->perminute.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->download->perminute)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Download/hour
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->download->perhour.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->download->perhour)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Download/day
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->download->perday.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->download->perday)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Download/month
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->download->permonth.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->download->permonth)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Download/year
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->download->peryear.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->download->peryear)).'
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="tab-pane" id="upload">
        
        <div class="col-md-4 no-border">
          <div class=" panel-heading no-border text-center">
            <p class="panel-title">
              Upload/second
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->upload->persecond.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->upload->persecond)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border text-center">
            <p class="panel-title text-gray">
              Upload/minute
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->upload->perminute.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->upload->perminute)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Upload/hour
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->upload->perhour.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->upload->perhour)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Upload/day
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->upload->perday.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->upload->perday)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Upload/month
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->upload->permonth.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->upload->permonth)).'
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-4 no-border">
          <div class="panel-heading no-border bg-default lt text-center">
            <p class="panel-title text-gray">
              Upload/year
            </p>
          </div>
          <div class="padder-v text-center clearfix">
            <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->upload->peryear.' MB">
              <div class="h3 font-bold">
                '.formatBytes(str_replace(",", "",$this->upload->peryear)).'
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>
</div>

<!-- ./END MISCELLANEOUS -->

              <form name="api" class="form-horizontal" method="GET" action="/user.php">
                <div class="form-group">
                  <div class="col-xs-12">
                    <div class="input-group">
                      <span class="input-group-addon no-border">Username or ID</span>
                      <input name="id" type="text" class="form-control no-border" placeholder="Wopian" value="" required>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12">
                    <button type="submit" class="btn btn-default btn-primary btn-block">View User Stats</button>
                    <span class="help-block">If the username contains special characters please use the account&#39;s ID instead</span>
                  </div>
                </div>
              </form>

            </div>
          </div>
        ';
      };

      if (empty ($this->xml->AccountName)) {
        echo '<p class="h3 text-center">Oops!<br /><small>This account doesn&#39;t exist</small></p>
              <br />
              <div class="row">
                <div class="col-sm-2 col-md-3 col-lg-4">
                </div>
                <div class="col-sm-8 col-md-6 col-lg-4 text-center">

                <form name="api" class="form-horizontal" method="GET" action="/user.php">
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
                            <button type="submit" class="btn btn-default btn-primary btn-block">View User Stats</button>
                            <span class="help-block">If the username contains special characters please use the account&#39;s ID instead</span>
                        </div>
                    </div>
                </form>

              </div>';
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
$stats_id = $_GET["id"];
$stats = new WhatPulse($stats_id);
$stats->printStats();

?>
          </section>
<?php include './assets/temp/footer.php'; ?>
