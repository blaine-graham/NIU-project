<html>
        <head>
		<title>Administration Desk</title>
		<link href="stylesheet.css" rel="stylesheet" type="text/css">
        </head>
	<body>
<?php
// draft 1 for Administration Desk page
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


//_____________________________________________________________________________________________________________
//declare variables
$dateLow=0;
$dateHigh=0;
$dateSearch = "";
$priceLow=0;
$priceHigh=0;
$priceSearch = "";
$bothSearch = "";
$finalSearch = "";
$statusSort = "SELECT * FROM orders_view";









//___________________________________________________________________________________________________________
// Form Data



	echo"<form method='post' id='administrationform'>";

	// "searchStat" will accept a 'status' and retrieve database info
	echo"Select a status for a search: </br>";
	echo"<select id='status' name='statList'>";
		echo"<option value='none'>Both</option>";
		echo"<option value='authorized'>Authorized</option>";
		echo"<option value='shipped'>Shipped</option>";
	echo"</select></br>";
	echo"Enter the date range to be searched: (yyyymmdd)";	

	// "lowDate" will accept the bottom range for a "search by date"
	echo"<input type='textbox' class = 'searchBox' name='lowDate' value=' '></input>";
	echo" / ";
	// "highDate" will accept the upper range for a "search by date"
	echo"<input type='textbox' class = 'searchBox' name='highDate' value=' '></input></br>";
	echo"Enter the price range to be searched: ";

	// "lowPrice" will accept the bottom range for a "search by price"
	echo"<input type='textbox' class = 'searchBox' name='lowPrice' value=' '></input>";
	echo" / ";
	// "highPrice" will accept the upper range for a "search by price"
	echo"<input type='textbox' class = 'searchBox' name='highPrice' value=' '></input></br>";

	// "submit" button will enter any input into the system
	echo"<input type='submit' name = 'submit' class = 'buttonSearch' value='submit'></input></br>";

	echo"</form>";

/*
	//button for navigation to 'shipping control'
	echo"<form action="http://students.cs.niu/~z1838458/ShippingControl.php>";
		echo"<input type=submit value="Shipping Administration" />";
	echo"</form>";
 */	



//________________________________________________________________________________________________________________
// switch case for the 'status' form element

if(isset($_POST['submit'])){
	switch($_POST['statList']){
	case 'none':
		$statusSort = excludeNone();
		break;
	case 'authorized':
		$statusSort = excludeShipped();
		break;
	case 'shipped':
		$statusSort = excludeAuthorized();
		break;
	}
}


//______________________________________________________________________________________________________________
// Search algorithim
if(isset($_POST['lowPrice']))
{
$priceLow = $_POST['lowPrice'];
}
if(isset($_POST['highPrice']))
{
$priceHigh = $_POST['highPrice'];
}
if(isset($_POST['lowDate']))
{
$dateLow = $_POST['lowDate'];
}
if(isset($_POST['highDate']))
{
$dateHigh = $_POST['highDate'];
}

// if both are set
if($priceLow != ' ' && $priceHigh != ' ' && $dateLow != ' ' && $dateHigh != ' ' )
	{
	$priceLow = $_POST['lowPrice'];
	$priceHigh = $_POST['highPrice'];
	$priceSearch = "CREATE OR REPLACE VIEW both_sort AS SELECT * FROM orders_view WHERE price BETWEEN '$priceLow' AND '$priceHigh' ORDER BY price";
	$pdoQuery = $pdo->prepare($priceSearch);
	$pdoQuery->execute();
 	$dateLow = $_POST['lowDate'];
	$dateHigh = $_POST['highDate'];
	$bothSearch = "SELECT * FROM both_sort WHERE order_date BETWEEN '$dateLow' AND '$dateHigh' ORDER BY order_date"; 
	$finalSearch = $bothSearch;
	}

// if date Range is set
elseif($dateLow != ' ' && $dateHigh != ' ')
	{
	$dateLow = $_POST['lowDate'];
	$dateHigh = $_POST['highDate'];
	$dateSearch = "SELECT * FROM orders_view WHERE order_date BETWEEN '$dateLow' AND '$dateHigh' ORDER BY order_date"; 
	$finalSearch = $dateSearch;
	}

// if price range is set
elseif($priceLow != ' ' && $priceHigh != ' ')
	{
	$priceLow = $_POST['lowPrice'];
	$priceHigh = $_POST['highPrice'];
	$priceSearch = "SELECT * FROM orders_view WHERE price >= '$priceLow' AND price <= '$priceHigh' ORDER BY price"; 
	$finalSearch = $priceSearch;
	}


// if neither
else
	{
	$finalSearch = $statusSort;
	}
 


// Run the search
/*
$pdoQuery = $pdo->prepare($statusSort);
$pdoQuery->execute();
$pdoQuery = $pdo->prepare($finalSearch);
$pdoQuery->execute();
 */

//________________________________________________________________________________________________________________
// This block of code will create the table to display orders

	// table element
	echo"<table id='myTable'>";
		
	// table headers
	echo"<tr>";
	echo"<th> Order # </th>";
	echo"<th> Customer ID </th>";
	echo"<th> Order Date </th>";
	echo"<th> price </th>";
	echo"<th> Address </th>";
	echo"<th> City </th>";
	echo"<th> state </th>";
	echo"<th> zip </th>";
	echo"<th> status </th>";
	echo"</tr>";


	//table body
	$drawTable = $finalSearch;
	$pdoQuery = $pdo->prepare($finalSearch);
	$pdoQuery->execute();
	$result = $pdoQuery->fetchAll(PDO::FETCH_ASSOC);
	foreach($result as $row)
	{
	foreach($row as $head=>$info)
		{
		if(!is_numeric($head))
			{
			echo"<td>".$info."</td>";
			}
	}
	echo"</tr>";
	}



// ___________________________________________________________________________________________________________________
// function 'excludeNone'
function excludeNone()
{
	global $pdo;
	$statusSort = "CREATE OR REPLACE VIEW orders_view AS SELECT order_id, customer_id, order_date, price, street_address, city, state, zip, status FROM orders";
	$pdoQuery = $pdo->prepare($statusSort);
	$pdoQuery->execute();
	$statusSort = "SELECT * FROM orders_view";
	return $statusSort;
}

// ___________________________________________________________________________________________________________________
// function 'excludeShipped'
function excludeShipped()
{
	global $pdo;
	$statusSort = "CREATE OR REPLACE VIEW orders_view AS SELECT * FROM orders WHERE status = 'Authorized'";
	$pdoQuery = $pdo->prepare($statusSort);
	$pdoQuery->execute();
	$statusSort = "SELECT * FROM orders_view";
	return $statusSort;
}


// ___________________________________________________________________________________________________________________
// function 'excludeAuthorized'
function excludeAuthorized()
{
	global $pdo;
	$statusSort = "CREATE OR REPLACE VIEW orders_view AS SELECT * FROM orders WHERE status = 'Shipped'";
	$pdoQuery = $pdo->prepare($statusSort);
	$pdoQuery->execute();
	$statusSort = "SELECT * FROM orders_view";
	return $statusSort;
}




?>
	<form method='post' action="ShippingControl.php">
		<input type=submit value="Shipping Administration" />
	</form>
</body>
</html>
