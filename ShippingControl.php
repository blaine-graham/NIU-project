<html>
        <head>
		<title>Shipping Administration</title>
		<link href="stylesheet.css" rel="stylesheet" type="text/css">
        </head>
	<body>
<?php
// draft 1 for Shipping Administration page
// coded by Blaine Graham

// _____________________________________________________________________________________



// attempt connection to new database

try { 
	$user = "z1813781"; // holds my username
	$db = "z1813781";
	$pw = "1999May25"; // pw for sql server
	$dsn = "mysql:host=courses;dbname=".$db; // holds db name
	$pdo = new PDO($dsn, $user, $pw); // creates db obj
	$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOexception $e) { // handle exception
	echo "Connection failed: " . $e->getMessage();
}
  
// _________________________________________________________________________________________________________

// attempt connection to legacy database
try { 
	$user = "student"; // holds my username
	$db = "csci467"; // holds database name
	$pw = "student"; // pw for sql server
	$dsn = "mysql:host=blitz.cs.niu.edu;dbname=".$db; // holds db name
	$pdoTwo = new PDO($dsn, $user, $pw); // creates db obj
	$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOexception $e) { // handle exception
	echo "Connection failed: " . $e->getMessage();
}


//___________________________________________________________________________________________________________
// Declared variables
$mass=0;
$cost=0;
$table_mass=0;
$x = 0;
$count = 0;




//___________________________________________________________________________________________________________
// Form Data



	echo"<form method='post' id='ShippingAdministrationForm'>";
	echo"Would you like to add a shipping bracket?</br>";
	echo"Low end of weight: ";
	// "addWeight" will accept the bottom range for a "weight bracket"
	echo"<input type='textbox' class = 'searchBox' name='addWeight' value='0'></input></br>";
	echo"Price of bracket: ";
	// "addPrice" will accept the shipping price for a "weight bracket"
	echo"<input type='textbox' class = 'searchBox' name='addPrice' value='0'></input></br>";
	// "addBracket" will validate given data, then add bracket to database
	echo"<input type='submit' class='buttonSearch' name='submit' value='add to database'></input></br>";
	echo"</form>";


//___________________________________________________________________________________________________________
// This code block will add a bracket to the 'shipping_cost' table

if(isset($_POST['addWeight']) && isset($_POST['addPrice'])) 
{
	$weight = $_POST['addWeight'];
	$cost = $_POST['addPrice'];
	// replaces all non alpha-numeric chars with escaped char
	$weight = preg_replace('/([^a-zA-Z\d\s:])/', "\\\\$1", $weight);
	$CheckWeight = "SELECT * FROM shipping_cost WHERE weight='".$weight."' ";
	$DBCheck = $pdo->prepare($CheckWeight);
	$DBCheck->execute();
	$count = $DBCheck->rowCount();
	if($count == 0)
		{
		$add['weight'] = $weight;
		$add['cost'] = $cost;
		$new = "
		INSERT INTO shipping_cost 
			(weight, cost) 
		VALUES 
			(${add['weight']}, ${add['cost']});
		";
		$pdoQuery = $pdo->prepare($new);
		$pdoQuery->execute();
		}
	else
		{
		echo"Not added, duplicate weight found";
		}
	
}


//_______________________________________________________________________________________________________
// this code block will print the 'shipping_cost' table

	// table element
	echo"<table id='myTable'>";
		
	// table headers
	echo"<tr>";
	echo"<th> weight >= </th>";
	echo"<th> Cost of Shipping </th>";
	echo"<th> Delete? </th>";
	echo"</tr>";
//	$del = "DELETE FROM shipping_cost WHERE weight = $mass";

	// table body
	$drawTable = "SELECT * FROM shipping_cost ORDER BY weight ASC";
	$pdoQuery = $pdo->prepare($drawTable);
	$pdoQuery->execute();
	$result = $pdoQuery->fetchAll(PDO::FETCH_ASSOC);
	foreach($result as $row)
	{
	foreach($row as $head=>$info)
		{
		if(!is_numeric($head))
			{
			echo"<td>".$info."</td>";
			if($head == 'weight')
				{
				$table_mass = $info;
				}
			if($head == 'cost')
				{
				$table_cost = $info;
				}
			}
		}
		
		echo"<td><form action='ShippingControl.php' method='post'>
			<input type='hidden' name='secretWeight' value=$table_mass />
			<input type='hidden' name='secretCost' value=$table_cost />
			<input type='submit' value='delete' class='btn' />
			</form></td>";
		echo"</tr>";
	}

	if(isset($_POST['secretWeight']))
{
		$mass = $_POST['secretWeight'];

		$pdoFill = "SELECT weight FROM shipping_cost WHERE weight = $table_mass";
		$fillQuery = $pdo->prepare($pdoFill);
		$fillQuery->execute();
		$res = $fillQuery->fetch(PDO::FETCH_ASSOC);
		if($count != 0)
		{
		foreach($res as $num)
			{
			$mass = $num;
			}
		}
	if(count($_POST) >=2 && isset($_POST['secretWeight']))
	{
		//		echo"Here we are";
		$mass = $_POST['secretWeight'];
		$del = "DELETE FROM shipping_cost WHERE weight = $mass";
		$pdoQuery = $pdo->prepare($del);
		try
			{
			$pdoQuery->execute();
			}
		catch (PDOexception $e)
			{
			echo"deletion failed";
			}
		}
	elseif(isset($_POST['reset']))
	{}
}
	echo"</table>";

?>
	<form method='post' action="Administration.php">
		<input type=submit value="Administration Home" />
	</form>
</body>
</html>
