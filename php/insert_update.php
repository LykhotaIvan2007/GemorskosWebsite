<?php
//Connect with the database 
$dbHandler = null; //Create an empty variable that will contain the handler
try{
    $dbHandler = new PDO("mysql:host=mysql;dbname=dbe_demo;charset=utf8", "root", "qwerty"); //Connect to the database with the provided connectstring
}catch(Exception $ex){//If something goes wrong, catch the error and print it
    printError($ex);
}

//function for printing errors nicely
function printError(String $err){
    echo "<h1>The following error occured</h1>
          <p>{$err}</p>";
}

//Insert somthing into the database
if($dbHandler){//If the dbHandler is not null, we can continue (Simply put: we are checking if there is a connection)
    try{
        $stmt = $dbHandler->prepare("INSERT INTO `artists` (`id`, `name`) 
                                     VALUES (NULL, 'Vengaboys');"
                                ); //We prepare a query, this will return a statement
        $stmt->execute(); //Execute the statement
        echo "Query executed! {$stmt->rowCount()} row(s) affected<br>";//Get the amount of rows that were affected
        $stmt->closeCursor();//Only needed in special databases, we close the handler
    }catch(Exception $ex){
        printError($ex);
    }
}

//Insert something in the database with a variable in the query
if($dbHandler){
    try{
        $stmt = $dbHandler->prepare("INSERT INTO `artists` (`id`, `name`) 
                                     VALUES (NULL, :artistName);"
                                );//Similar to line 19, but this time we add a variable that we are going to bind
        $artistName = "The Village People";
        $stmt->bindParam("artistName", $artistName, PDO::PARAM_STR);//We bind the param "artistName" in the query to the varable $artistName in PHP. We also specify that this is a string
        $stmt->execute(); 
        echo "Query executed! {$stmt->rowCount()} row(s) affected<br>";
        $stmt->closeCursor();
    }catch(Exception $ex) {
        printError($ex);
    }
}

//Update an existing record
if($dbHandler){
    try{
        $stmt = $dbHandler->prepare("UPDATE `artists` 
                                     SET `name` = 'Patrick Hernandez' 
                                     WHERE `artists`.`name` = 'The Village People';"
                                );//The only thing that changes is the query
        $stmt->execute();
        echo "Query executed! {$stmt->rowCount()} row(s) affected<br>";
        $stmt->closeCursor();
        
    }catch(Exception $ex) {
        printError($ex);
    }
}

$dbHandler = null; //We close the connection