<?php 
// PHP document
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Abraham's Embedded SQL Page</title>
	<script src="sql-lib/jquery-3.2.1.min.js"></script>
	<script src="sql-lib/bootstrap.min.js"></script>
	<script src="sql-lib/bootstrap-select.min.js"></script>
	<link rel="stylesheet" href="sql-lib/bootstrap.min.css">
	<link rel="stylesheet" href="sql-lib/bootstrap-select.min.css">
	<link rel="stylesheet" href="sql-lib/style.css">
</head>
<body>
	<div class="container">
		<div class="page-header">
			<h1>Embedded SQL Project Assignment</h1>
		</div>
		<p class="lead">
			To retrieve city and other information from a given country, please select from the options below. Alternatively, you may enter the information into the address bar. For example, to retrieve U.S. cities with a population greater than 1,000,000 sorted by population, you can enter:
		</p>
		<div class="contained-code">
			<pre>https://abequinonez.000webhostapp.com/world/?code=USA&pop=1000000&sort=city.Population&submit=submit#</pre>
		</div>
		<form class="form-inline" action="#" method="GET">
			<div class="form-group">
				<select class="selectpicker" name="code" required>
					<option value="">Country...</option>
					<?php 
					// Connect to the database
					require_once("./config.php");
					$db = @new mysqli($host, $user, $password, $db_name);
					if ($db->connect_error) {
						exit("Could not connect to database.");
					}
					$sql = "SELECT Code, Name FROM country ORDER BY Name";
					$result = $db->query($sql);
					if ($db->error) {
						exit("SQL error.");
					}
					// Populate the country code options
					while ($array = $result->fetch_array()) {
						if (strlen($array["Name"]) > 23) {
							$array["Name"] = substr($array["Name"], 0, 23) . "...";
						}
						echo "<option value='" . $array["Code"] . "'>" . $array["Name"] . "</option>";
					}
					// Clear the results and close the connection
					$result->free();
					$db->close();
					?>	
  				</select>
			</div>
			<div class="form-group">
				<select class="selectpicker" name="pop" required>
					<option value="">Population greater than...</option>
					<option value="10000">10,000</option>
  					<option value="100000">100,000</option>
  					<option value="1000000">1,000,000</option>
  					<option value="10000000">10,000,000</option>
  				</select>
			</div>
			<div class="form-group">
				<select class="selectpicker" name="sort" required>
					<option value="">Sort by...</option>
					<option value="city.Name">City name</option>
  					<option value="city.Population">City population</option>
  				</select>
			</div>
			<button type="submit" class="btn btn-default" name="submit" value="submit">Submit</button>
		</form>
		<div class="row">
			<div class="col-lg-12">
				<?php 
					if (!empty($_GET["submit"])) {
						if (empty($_GET["code"]) || empty($_GET["pop"]) || empty($_GET["sort"])) {
							exit("Please fill in all the fields.");
						}
						$code = addslashes($_GET["code"]);
						$pop = addslashes($_GET["pop"]);
						$sort = addslashes($_GET["sort"]);
						// Connect to the database
						$db = @new mysqli($host, $user, $password, $db_name);
						if ($db->connect_error) {
							exit("Could not connect to database.");
						}
						$sql = "SELECT city.Name, city.Population, city.CountryCode, country.IndepYear, country.Continent FROM city, country WHERE city.CountryCode = country.Code AND city.CountryCode LIKE '%{$code}' AND city.Population > {$pop} ORDER BY {$sort}";
						$result = $db->query($sql);
						if ($db->error) {
							exit("SQL error.");
						}
						if ($result->num_rows === 0) {
							exit("No results found.");
						}
						echo "<table class='table table-striped table-bordered table-hover'>";
						echo "<thead><tr><th>#</th><th>City</th><th>Population</th><th>Country</th><th class='indep-year'>Independence year</th><th class='continent'>Continent</th></tr></thead>";
						echo "<tbody>";
						// Populate the table with the requested information
						$count = 0;
						while ($array = $result->fetch_array()) {
							echo "<tr>";
							echo "<th scope='row'>" . ++$count . "</th>";
							echo "<td><a href='https://www.google.com/search?q=" . $array["Name"] . "' target='_blank'>" . $array["Name"] . "</a></td>";
							echo "<td>" . $array["Population"] . "</td>";
							echo "<td>" . $array["CountryCode"] . "</td>";
							echo "<td class='indep-year'>" . $array["IndepYear"] . "</td>";
							echo "<td class='continent'>" . $array["Continent"] . "</td>";
							echo "</tr>";
						}
						// Clear the results and close the connection
						$result->free();
						$db->close();
						echo "</tbody></table>";
					}
				?>
			</div>
		</div>
	</div>
</body>
</html>