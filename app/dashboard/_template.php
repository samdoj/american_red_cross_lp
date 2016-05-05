<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Favicon -->
<link rel="apple-touch-icon" sizes="57x57" href="../media/favicons/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="../media/favicons/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="../media/favicons/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="../media/favicons/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="../media/favicons/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="../media/favicons/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="../media/favicons/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="../media/favicons/apple-touch-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="../media/favicons/apple-touch-icon-180x180.png">
<link rel="icon" type="image/png" href="../media/favicons/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="../media/favicons/android-chrome-192x192.png" sizes="192x192">
<link rel="icon" type="image/png" href="../media/favicons/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/png" href="../media/favicons/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="../media/favicons/manifest.json">
<link rel="mask-icon" href="../media/favicons/safari-pinned-tab.svg" color="#ed1b2e">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="msapplication-TileImage" content="/mstile-144x144.png">
<meta name="theme-color" content="#ed1b2e">

<title><?php echo $pagetitle; ?></title>
<link rel="stylesheet" href="inc/bootstrap.min.css">
<link rel="stylesheet" href="inc/dashboard.css">
</head>
<body>

<?php
// include database login credentials
include_once('login_info.php');

$queryApps = "SELECT COUNT(*) as num FROM apps_biomed";
$totalApps = mysql_fetch_array(mysql_query($queryApps));
$totalApps = $totalApps[num];

// close database connection
mysql_close($cn);
?>

<header class="container">
    <div class="row">
        <div class="col-xs-6">
            <p>Welcome <?php echo $login_session; ?></p>
        </div><!-- col-xs-* -->
        <div class="col-xs-6 text-right">
            <p><a href="index.php?status=logout" class="btn btn-info btn-xs">Log Out</a></p>
        </div><!-- col-xs-* -->
    </div><!-- row --><!-- container -->
</header>

<div class="container">
    <div class="row">
        <nav class="col-sm-3">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">apps_biomed <span class="badge"><?= number_format($totalStates); ?></span></h3>
                </div><!-- panel-heading -->
                <div class="panel-body">
                    <ul class="list-unstyled">
                        <li><a href="biomed.php">View</a></li>
                        <li><a href="biomed_export.php">Export .csv</a></li>
                    </ul>
                </div><!-- panel-body -->
            </div><!-- panel -->

        </nav><!-- col-sm-* -->
        <main class="col-sm-9">

			<?php echo $pagemaincontent; ?>

        </main><!-- col-sm-* -->
    </div><!-- row -->
</div><!-- container -->

<script src="inc/jquery-1.11.2.min.js"></script>
<script src="inc/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip({'placement': 'top'});
});
</script>
</body>
</html>