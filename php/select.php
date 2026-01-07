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

//Select something from the database
if($dbHandler){
    try{
        $stmt = $dbHandler->prepare("SELECT *
                                     FROM `artists`"
                                );
        $stmt->execute(); //Note: We only execute the statement, the gathering of data is done in thee second segment           
    }catch(Exception $ex) {
        printError($ex);
    }
}

//Print the selection by fetchall
echo "<h1>fetchall</h1>";
if(isset($stmt)){
    $results = $stmt->fetchall(PDO::FETCH_ASSOC); //Fetch all the results from the statement in an assoc array
    //var_dump($results);
    foreach($results as $result){//And print them
        echo "ID {$result["id"]} is: {$result["name"]}<br>";
    }
    $stmt->closeCursor();
}



//Print the selection by fetch
echo "<h1>fetch</h1>";
if(isset($stmt)){
    $stmt->execute();//Because we already have a statement prepared with the result we want, we can re-use it here!


    while($result = $stmt->fetch(PDO::FETCH_ASSOC)){//We fetch every record one by one and print it
        echo "ID {$result["id"]} is: {$result["name"]}<br>";
    }
    $stmt->closeCursor();
}

//Print the selection by fetch and bind
echo "<h1>fetch and bind</h1>";
if(isset($stmt)){
    $stmt->bindColumn("id", $id); //We bind the column "id" to the variable $id
    $stmt->bindColumn("name", $name); //We bind the column "name" to the variable $name
    //We can also bind by column number. Note, this starts counting at 1
    //$stmt->bindColumn(1, $id);
    //$stmt->bindColumn(2, $name);
    $stmt->execute();
    
    while($result = $stmt->fetch()){//We can again fetch every record one by one
        echo "ID {$id} is: {$name}<br>"; //But this time we print by using the "nicely bound" variables instead of an array
    }
    $stmt->closeCursor();
}

$dbHandler = null;