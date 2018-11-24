<?php

require "vendor/autoload.php";
use Dompdf\Dompdf;

$html = "";

function drawballot($cssclass) {
?>
          <div class="<?php echo htmlspecialchars($cssclass) ?>">
            <p>For each person, check the box if you believe the person is
            worthy of becoming a member of the Order of the Arrow. You may
            select as many as you feel are worthy. If you do not feel any
            of them are worthy, hand in a blank ballot. If you do not feel
            like you know anyone well enough to make a decision, do not
            hand in your ballot. It will not affect the outcome.</p>
            <?php foreach ($_POST['names'] as $row => $user) { ?>
            <div class="ballotrow">
              <div class="name"><?php echo htmlspecialchars($user['name']) ?></div>
              <div class="clear"></div>
            </div>
            <?php } ?>
          </div>
<?php
}

if (isset($_POST['names'])) {
  ob_start();
  ?><!DOCTYPE html>
  <html>
  <head>
  <title>Unit Election Ballot</title>
  <style type="text/css"><1--
@page {
    size: letter;
    margin: 0.5in;
}
html {
    height: 98%;
    width: 98%
}
body {
    font-family: DejaVu Sans;
    font-size: 9pt;
    height: 100%;
    width: 100%;
}
p {
    margin: 0;
    font-size: 7pt;
}
.left {
    left: 0in;
}
.right {
    left: 4.25in;
}
.top {
    top: 0in;
}
.bottom {
    top: 5.25in;
}
.oneballot {
    position: absolute;
    border-collapse: collapse;
    width: 3.25in;
    height: 5in;
}
.ballotrow {
    width: 100%;
    border: 1px solid black;
    border-collapse: collapse;
    padding: 0;
}
.name {
    width: 85%;
    border-right: 1px solid black;
    border-collapse: collapse;
    margin: 0;
    padding: 0.25em;
    float: left;
}
.clear {
    clear: both;
}
  --></style>
  </head>
  <body>
  <?php
  drawballot("oneballot top left");
  drawballot("oneballot top right");
  drawballot("oneballot bottom left");
  drawballot("oneballot bottom right");
  ?>
  </body>
</html><?php
  $html = ob_get_clean();
}

if ($html == "") {
    echo "No names were entered. Please use your browser's back button and try again.";
} else {
    $DEBUG = 0;
    if ($DEBUG) {
        echo $html;
    } else {
        $dompdf = new Dompdf();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->setPaper('letter','portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream("ballot.pdf");
        //$dompdf->stream("ballot.pdf", array("Attachment"=>0));
    }
}
