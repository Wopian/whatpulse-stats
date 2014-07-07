          <footer class="footer bg-white b-t b-light hidden-xs">
            <div class="col-sm-1 col-md-2 col-lg-3">
            </div>
            <div class="col-sm-10 col-md-8 col-lg-6">
              <ul class="nav navbar-nav">
                <p class="navbar-text text-muted">More:</p>
                <li>
                  <a href="http://jamesharris.net" class="text-muted">Portfolio</a>
                </li>
                <li>
                  <a href="http://colonizer.jamesharris.net" class="text-muted">Colonizer</a>
                </li>
                <li>
                  <a href="http://lastfm.jamesharris.net" class="text-muted">Lastistics</a>
                </li>
                <li>
                  <a href="http://sandbox.jamesharris.net" class="text-muted">The Sandbox</a>
                </li>
              </ul>
              <ul class="nav navbar-nav pull-right">
                <p class="navbar-text text-muted text-right">
                  <?php 
    $File0 = "/home/bobstudi/visits/total.counter";
    $handle0 = fopen($File0, 'r+');
    $data0 = fread($handle0, 512); 
    $count0 = $data0 + 1;
    fseek($handle0, 0);
    fwrite($handle0, $count0); 
    fclose($handle0);

    $File1 = "/home/bobstudi/visits/jamesharris.net/whatpulse.counter";
    $handle1 = fopen($File1, 'r+');
    $data1 = fread($handle1, 512); 
    $count1 = $data1 + 1;
    print $count1." Views";
    fseek($handle1, 0);
    fwrite($handle1, $count1); 
    fclose($handle1);
?>
                </p>
              </ul>
            </div>
          </footer>
        </section>
    </section>
  </section>
</section>
<!-- Bootstrap -->
<!-- App -->
<script src="//code.jquery.com/jquery-latest.min.js"></script>
<script src="/assets/js/app.v1.js"></script>
<script src="/assets/js/app.plugin.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/json2/20110223/json2.js"></script>
<script src="/assets/js/jstorage.js"></script>
</body>
</html>
