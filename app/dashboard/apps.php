<?php
// --- begin template code ----------------------------------
// buffer larger content areas like the main page content
ob_start();
$pagetitle = "Apria Dashboard";
// --- end template code ------------------------------------

include('_session.php');

// include database login credentials
include_once('login_info.php');

//your table name
$tbl_name="apps";

// How many adjacent pages should be shown on each side?
$adjacents = 3;

/*
   First get total number of rows in data table.
   If you have a WHERE clause in your query, make sure you mirror it here.
*/
$query = "SELECT COUNT(*) as num FROM $tbl_name";
$total_pages = mysql_fetch_array(mysql_query($query));
$total_pages = $total_pages[num];

/* Setup vars for query. */
$targetpage = "apps.php";            // your file name  (the name of this file)
$limit = 25;                                // how many items to show per page
$page = $_GET['page'];
if($page)
	$start = ($page - 1) * $limit;          // first item to display on this page
else
	$start = 0;                             // if no page var is given, set start to 0

/* Get data. */
$sql = "SELECT * FROM $tbl_name ORDER BY id DESC LIMIT $start, $limit";
$result = mysql_query($sql);

/* Setup page vars for display. */
if ($page == 0) $page = 1;                  // if no page var is given, default to 1.
$prev = $page - 1;                          // previous page is page - 1
$next = $page + 1;                          // next page is page + 1
$lastpage = ceil($total_pages/$limit);      // lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;                      // last page minus 1

/*
	Now we apply our rules and draw the pagination object.
	We're actually saving the code to a variable in case we want to draw it more than once.
*/
$pagination = "";

if($lastpage > 1)
{
	$pagination .= "<ul class=\"pagination\">\n";
	//previous button
	if ($page > 1)
		$pagination.= "<li><a href=\"$targetpage?page=$prev\">Previous</a></li>\n";
	else
		$pagination.= "<li class=\"disabled\"><span>Previous</span></li>\n";

	//pages
	if ($lastpage < 7 + ($adjacents * 2))   //not enough pages to bother breaking it up
	{
		for ($counter = 1; $counter <= $lastpage; $counter++)
		{
			if ($counter == $page)
				$pagination.= "<li class=\"active\"><a href=\"#\">$counter</a></li>\n";
			else
				$pagination.= "<li><a href=\"$targetpage?page=$counter\">$counter</a></li>\n";
		}
	}
	elseif($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
	{
		//close to beginning; only hide later pages
		if($page < 1 + ($adjacents * 2))
		{
			for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
			{
				if ($counter == $page)
					$pagination.= "<li class=\"active\"><a href=\"#\">$counter</a></li>\n";
				else
					$pagination.= "<li><a href=\"$targetpage?page=$counter\">$counter</a></li>\n";
			}
			$pagination.= "<li class=\"disabled\"><span>&hellip;</span></li>\n";
			$pagination.= "<li><a href=\"$targetpage?page=$lpm1\">$lpm1</a></li>\n";
			$pagination.= "<li><a href=\"$targetpage?page=$lastpage\">$lastpage</a></li>\n";
		}
		//in middle; hide some front and some back
		elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
		{
			$pagination.= "<li><a href=\"$targetpage?page=1\">1</a></li>\n";
			$pagination.= "<li><a href=\"$targetpage?page=2\">2</a></li>\n";
			$pagination.= "<li class=\"disabled\"><span>&hellip;</span></li>\n";
			for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<li class=\"active\"><a href=\"#\">$counter</a></li>\n";
				else
					$pagination.= "<li><a href=\"$targetpage?page=$counter\">$counter</a></li>\n";
			}
			$pagination.= "<li class=\"disabled\"><span>&hellip;</span></li>\n";
			$pagination.= "<li><a href=\"$targetpage?page=$lpm1\">$lpm1</a></li>\n";
			$pagination.= "<li><a href=\"$targetpage?page=$lastpage\">$lastpage</a></li>\n";
		}
		//close to end; only hide early pages
		else
		{
			$pagination.= "<li><a href=\"$targetpage?page=1\">1</a></li>\n";
			$pagination.= "<li><a href=\"$targetpage?page=2\">2</a></li>\n";
			$pagination.= "<li class=\"disabled\"><span>&hellip;</span></li>\n";
			for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<li class=\"active\"><a href=\"#\">$counter</a></li>\n";
				else
					$pagination.= "<li><a href=\"$targetpage?page=$counter\">$counter</a></li>\n";
			}

		}
	}

	//next button
	if ($page < $counter - 1)
		$pagination.= "<li><a href=\"$targetpage?page=$next\">Next</a></li>\n";
	else
		$pagination.= "<li class=\"disabled\"><span>Next</span></li>\n";
		$pagination.= "</ul>\n";
}
?>

<h1>Apps (<?= number_format($total_pages); ?> records)</h1>

<div class="table-responsive">
	<table class="table table-hover">
		<thead>
			<tr>
				<th nowrap="nowrap">ID</th>
				<th nowrap="nowrap">Name</th>
				<th nowrap="nowrap">Email</th>
				<th nowrap="nowrap">Phone</th>
				<th nowrap="nowrap">Call experience</th>
				<th nowrap="nowrap">Position</th>
				<th nowrap="nowrap">Health experience</th>
				<th nowrap="nowrap">Resume</th>
				<th nowrap="nowrap">Submitted</th>
				<th nowrap="nowrap">Submitted IP</th>
			</tr>
		</thead>
		<tbody>
		<?php
			while($row = mysql_fetch_array($result)) {
				echo "<tr>\n";
				echo "	<td nowrap=\"nowrap\">" . $row['id'] . "</td>\n";
				echo "  <td nowrap=\"nowrap\">" . $row['name'] . "</td>\n";
				echo "  <td nowrap=\"nowrap\">" . $row['email'] . "</td>\n";
				echo "	<td nowrap=\"nowrap\">" . $row['phone'] . "</td>\n";
				echo "	<td nowrap=\"nowrap\">" . $row['question1'] . "</td>\n";
				echo "	<td nowrap=\"nowrap\">" . $row['question2'] . "</td>\n";
				echo "	<td nowrap=\"nowrap\">" . $row['question3'] . "</td>\n";
				echo "	<td nowrap=\"nowrap\"><a href=\"http://apria.careers/logistics/uploads/resumes/" . $row['resume'] . "\">" . $row['resume'] . "</a></td>\n";
				echo "	<td nowrap=\"nowrap\">" . $row['submitted'] . "</td>\n";
				echo "	<td nowrap=\"nowrap\">" . $row['submitted_ip'] . "</td>\n";
				echo "</tr>\n";
			}
		?>
		</tbody>
	</table>
</div><!-- table-responsive -->

<?= $pagination ?>

<?php
// assign all page specific variables
$pagemaincontent = ob_get_contents();
ob_end_clean();

// apply the template
include("_template.php");
?>