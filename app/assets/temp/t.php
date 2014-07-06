<? 

function totalKeyboard($no1, $no2) {
  return number_format(str_replace(",", "",$no1) + str_replace(",", "",$no2));
};

echo '<script>
                function toggleText() {
                  $(".text").toggle();
                }
              </script>
              <p class="h3 text-center">'.$this->xml->Name.'<br /><small><!--ID '.$this->xml->TeamID.'-->&nbsp;</small></p>
              <br />
              <div class="row">
                <div class="col-sm-2 col-md-3 col-lg-4">
                </div>
                <div class="col-sm-8 col-md-6 col-lg-4 text-center">

<!-- USER -->

<div class="col-md-12">
<div class="panel">
  <div class="panel-heading">
    <p class="panel-title">Team Info</p>
  </div>
  <div class="panel-body row">

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Founder
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.$this->xml->Founder.'
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 no-border">
      <div class="panel-heading no-border bg-default lt text-center">
        <p class="panel-title text-gray">
          Founded
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
          Members
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.$this->xml->Users.'
          </div>
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
        <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->keys->total.' keys">
          <div class="h3 font-bold">
            '.largenumber(str_replace(",", "",$this->keys->total)).'
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
        <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.totalKeyboard($this->keys->total, $this->clicks->total).' total">
          <div class="h3 font-bold">
            '.largenumber(str_replace(",", "",$this->keys->total) + str_replace(",", "",$this->clicks->total)).'
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
        <div class="col-xs-12" data-toggle="tooltip" data-placement="top" title="'.$this->clicks->total.' clicks">
          <div class="h3 font-bold">
            '.largenumber(str_replace(",", "",$this->clicks->total)).'
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
          Average Uptime
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.largenumber((str_replace(",", "",$this->uptime)/$this->days)/$this->xml->Users).'
          </div>
          <p class="panel-title text-gray">
            hours per user
          </p>
        </div>
      </div>
    </div>

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
          Total Avg. Uptime
        </p>
      </div>
      <div class="padder-v text-center clearfix">
        <div class="col-xs-12">
          <div class="h3 font-bold">
            '.largenumber(str_replace(",", "",$this->uptime)/$this->xml->Users).'
          </div>
          <p class="panel-title text-gray">
            hours per user
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

              <form name="api" class="form-horizontal" method="GET" action="/team.php">
                <div class="form-group">
                  <div class="col-xs-12">
                    <div class="input-group">
                      <span class="input-group-addon no-border">Team Name or ID</span>
                      <input name="teamid" type="text" class="form-control no-border" placeholder="Coccyx_Bashers" value="" required>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12">
                    <button type="submit" class="btn btn-default btn-primary btn-block">View Team Stats</button>
                    <span class="help-block">If the team name contains special characters please use the team&#39;s ID instead</span>
                  </div>
                </div>
              </form>

            </div>
          </div>
        '; ?>
