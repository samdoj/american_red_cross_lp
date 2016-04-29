<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $pagetitle; ?></title>
<link rel="stylesheet" href="inc/bootstrap.min.css">
<link rel="stylesheet" href="inc/dashboard.css">
</head>
<body>

<?php
// include database login credentials
include_once('login_info.php');

$queryApps = "SELECT COUNT(*) as num FROM apps";
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
                    <h3 class="panel-title">Apps <span class="badge"><?= number_format($totalApps); ?></span></h3>
                </div><!-- panel-heading -->
                <div class="panel-body">
                    <ul class="list-unstyled">
                        <li><a href="apps.php">View</a></li>
                        <li><a href="apps_export.php">Export .csv</a></li>
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