<html>
        <head>
		<title>Receiving Desk</title>
		<link href="stylesheet.css" rel="stylesheet" type="text/css">
        </head>
	<body>
<?php
// draft 1 for receiving Desk page
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
//declar variables
$check=0;
$search=0;
$add=0;
$num=0;






// ___________________________________________________________________________________________________________

// This code block creates the form data for the page

	echo"<form method='post' id='receivingform'>";
	// hidden form for printing tables
	echo"<input type='hidden' name='printer'>";
	// hidden form for holding add data
	echo"<input type='hidden' name='add'></input>";
	// hidden form for holding product numbers
	echo"<input type='hidden' name='num'></input>";
	echo"Search: </br>";
	// "search" will accept either a part number or a product description and retrieve database info
	echo"<input type='textbox' class = 'searchBox' name='search' value='0'></input></br>";
	// "add" will accept the amount of product recieved, and be used to update the inventory
	echo"Quantity to add:  <input type='textbox' name='product' value='NULL'></input></br>";
	// "prodID" will accept input on the product ID to be modified
	echo"Product ID number: <input type='textbox' name='prodID' value='NULL'></input></br>";
	// "submit" button will enter any input into the system
	echo"<input type='submit' name = 'submit' class = 'buttonSearch' value='submit'></input></br>";


	// "confirm" will print a message confirming the alterations to the database
	if(isset($_POST['product']) and isset($_POST['prodID']))
	{
		$add = $_POST['product'];
		$num = $_POST['prodID'];
		if($add != 0 and $num != 0)
		{echo"<textarea name='confirm' rows='3' cols='80'> Successfully added: $add to inventory for product #: $num </textarea>";}
	}


// _______________________________________________________________________________________________________________


// declare variables
$check = 0;	
$search = 0;

// This code block forms the SQL queries needed to access database

	// search by number for inventory
	if(isset($_POST['search']))
	{
	$search = $_POST['search'];
	}
	$pdoFillTable = "SELECT * FROM inventory WHERE number='$search' ORDER BY number";
	$pdoQuery = $pdo->prepare($pdoFillTable);
	$pdoQuery->execute();
	$count = $pdoQuery->rowCount();
	// search by number for parts
	$pdoFillTwo = "SELECT * FROM parts WHERE number = '$search' ORDER BY number";
	$pdoQueryTwo = $pdoTwo->prepare($pdoFillTwo);
	$pdoQueryTwo->execute();
	$countTwo = $pdoQueryTwo->rowCount();

	// search by description
	if($count == 0 and $countTwo == 0)
	{
//		echo"test";
		$pdoFillTwoDesc = "SELECT * FROM parts WHERE description LIKE '%$search%' ORDER BY number";
		$pdoQuery = $pdoTwo->prepare($pdoFillTwoDesc);
		$pdoQuery->execute();
//		$check = $pdoQuery->fetchAll(PDO::FETCH_ASSOC);
//		echo" check desc fill"; print_r($check);
		$check = -1;
		$count = -1;
		}
	if($count == 0)
		{ 
		echo "Fill error, no results found";
		}
	$_POST['printer'] = $pdoFillTable;
	

//__________________________________________________________________________________________________

// this block contains code for printing a Table from the database


	// create table element
	echo' <table id="myTable" >';

	// headers for table
	echo"<tr>";
	echo"<th>Product Number</th>";
	echo"<th># In Inventory</th>";
	echo"<th>Description</th>";
	echo"</tr>";

	// fill table rows
	if($check == -1)
	{
	$pdoFillTwo = $pdoFillTwoDesc;
	}
	$check=0;
	$pdoPrinter = $pdoFillTable;
	$pdoPrinterTwo = $pdoFillTwo;
	$pdoQueryTwo = $pdoTwo->prepare($pdoPrinterTwo);
	$pdoQueryTwo->execute();
	$pdoQuery = $pdo->prepare($pdoFillTable);
	$pdoQuery->execute();
//	$result = $pdoQuery->fetch(PDO::FETCH_NUM);
	$resultTwo = $pdoQueryTwo->fetchAll(PDO::FETCH_ASSOC);
	foreach($resultTwo as $row2)
	{
	$pdoQuery = $pdo->prepare("SELECT * FROM inventory WHERE number = '$row2[number]'");
	$pdoQuery->execute();
	if($row = $pdoQuery->fetch(PDO::FETCH_ASSOC))
		{
		echo"<tr>";
		echo"<td>$row[number]</td>";
		echo"<td>$row[quantity]</td>";
		echo"<td>$row2[description]</td>";
		echo"</tr>";
		}
	}
	echo"</table>";
	




// ________________________________________________________________________________________________________________________________________


// this code block is for adding product to the inventory


	if(isset($_POST['product']))
	{$add = $_POST['product'];}
	if(isset($_POST['prodID']))
	{$num = $_POST['prodID'];}
	$newTotal = 0;
	// if statement to check if something is added
	if($add != 0 and $num != 0)
	{
		$pdoQuery = $pdo->prepare("SELECT * FROM inventory WHERE number = $num");
		$pdoQuery->execute();
		$current = $pdoQuery->fetchAll(PDO::FETCH_ASSOC);
		foreach($current as $row)
		{
		$newTotal = $row['quantity'];
		$newTotal += $add;
		$pdoQuery = $pdo->prepare("UPDATE inventory SET quantity = ? WHERE number=?");
		$pdoQuery->execute([$newTotal, $num]);
		}
	}
?>
</body>
</html>
