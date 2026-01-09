<?php
function registration()
{
    if(isset($_POST['log']))
        {
            
                $name=filter_input(INPUT_POST,"userName",FILTER_SANITIZE_SPECIAL_CHARS);
                $email=filter_input(INPUT_POST,"email",FILTER_SANITIZE_EMAIL);
                $password=filter_input(INPUT_POST,"password",FILTER_SANITIZE_SPECIAL_CHARS);
                $checkpassword=filter_input(INPUT_POST,"password2",FILTER_SANITIZE_SPECIAL_CHARS);
                if($password!==$checkpassword)
                {
                    echo "<p>please check your password</p>";
                    return;
                }
                if(!empty($name) && !empty($email) && !empty($password))
                {
                    function printError(String $err){
                        echo "<h1>The following error occured</h1>
                        <p>{$err}</p>";
                    }
                    $dbHandler = null; 
                    try{
                        $dbHandler = new PDO("mysql:host=localhost;port=3306;dbname=gemorskos;charset=utf8", "root", "root"); //Connect to the database with the provided connectstring
                    }catch(Exception $ex){
                        printError($ex);
                    }
                    if($dbHandler)
                    {
                        try
                        {
                            $stmt= $dbHandler->prepare("INSERT INTO `users`(`user_name`,`user_email`,`user_password`)
                                                        VALUES (:nm,:em,:ps)");
                            $nm=$name;
                            $em=$email;
                            $ps=$password;
                            $stmt->bindParam("nm",$nm,PDO::PARAM_STR);
                            $stmt->bindParam("em",$em,PDO::PARAM_STR);
                            $stmt->bindParam("ps",$ps,PDO::PARAM_STR);
                            $stmt->execute();
                            $stmt->closeCursor();
                            header("Location: register.php");
                            exit;
                        }catch(Exception $ex)
                        {
                            printError($ex);
                        }
                    }
                    $dbHandler=null;
                }else
                {
                    echo "<p>please fill all fields</p>";
                    return;
                }
            
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/register.css">
    <title>Register</title>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="centralDiv">
        <?php registration(); ?>
        <div class="formDiv">
            <label for="name">Username:</label>
            <input type="text" name="userName" id="name">
        </div>
        <div class="formDiv second">
            <label for="email">Email</label>
            <input type="email" name="email" id="email">
        </div>
        <div class="formDiv second">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
        </div>
        <div class="formDiv second">
            <label for="password2">Password again:</label>
            <input type="password" name="password2" id="password2">
        </div>
        </div>
        <button name="log" class="submitButton">Log in</button>
        
    </form>
    

    <?php include 'footer.php'; ?>
</body>
</html>