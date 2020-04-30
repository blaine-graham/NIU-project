<html>
        <head>
		<title>addtocart</title>
		<link href="stylesheet.css" rel="stylesheet" type="text/css">
        </head>
	<body>
<?php


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
// function to add passed info to cart



function add_to_cart($number, $quantity)
{
	global $pdo;
	global $pdoTwo;
	$getNum = $number;
	$getQuant = $quantity;
	$getDesc = "";
	$getPrice = 0;
	$getPic = "";
	$pdoQuery = $pdoTwo->prepare("SELECT * FROM parts WHERE number = '$getNum'");
	$pdoQuery->execute();
	$result = $pdoQuery->fetch(PDO::FETCH_ASSOC);
	foreach($result as $row)
		{
		foreach($row as $head=>$info)
			{
			if($head == 'number')
				{
				$getNum = $info;
				}
			if($head == 'description')
				{
				$getDesc = $info;
				}
			if($head == 'price')
				{
				$getPrice = $info;
				}
			if($head == 'pictureURL')
				{
				$getPic = $info;
				}
			}
		}
	$pdoQuery = $pdo->prepare("INSERT INTO cart (number, quantity, description, price, pictureURL) VALUES ('$getNum', '$getQuant', '$getDesc', '$getPrice', '$getPic')");
	$pdoQuery->execute();
	}


?>
</body>
</html>
