<?php
session_start();
if(!isset($_SESSION['check']))
{
    $_SESSION['check']=0;
}

if(isset($_POST['adc']) && $_SESSION['check']==1)
        {
          header("Location: addClient.php");
          exit;         
        }


function printError(String $err)
{
    echo "<p>the followed error occured</p>
            <p>{$err}</p>";
}
    function logIn()
    {
        if(isset($_POST['log']))
        {
            if($_SESSION['check']==1)
            {
                header("Location: logIn.php");
                exit;
            }
        
            $name=filter_input(INPUT_POST,"userName",FILTER_SANITIZE_SPECIAL_CHARS);
            $email=filter_input(INPUT_POST,"email",FILTER_SANITIZE_EMAIL);
            $password=filter_input(INPUT_POST,"password",FILTER_SANITIZE_SPECIAL_CHARS);
            if(!empty($name)&&!empty($email)&&!empty($password))
            {
                
                $dbHandler=null;
                try
                {
                    $dbHandler = new PDO("mysql:host=localhost;port=3306;dbname=gemorskos;charset=utf8","root","root");
                }catch(Exception $ex)
                {
                    printError($ex);
                }
                if($dbHandler)
                {
                    try
                    {
                        $stmt = $dbHandler->prepare("SELECT *
                                                    FROM `users`");
                    }catch(Exception $ex)
                    {
                        printError($ex);
                    }
                    if(isset($stmt))
                    {
                        $stmt->bindColumn("user_name",$username);
                        $stmt->bindColumn("user_email",$useremail);
                        $stmt->bindColumn("user_password",$userpassword);
                        $stmt->execute();
                        while($result=$stmt->fetch())
                        {
                            if($name==$username && $email==$useremail && $password==$userpassword)
                            {
                                $_SESSION['check']=1;
                                return;
                            }
                        }
                        $stmt->closeCursor();
                        $dbHandler=null;
                    }
                }
            }
            $_SESSION['check']=2;
            header("Location: logIn.php");
            exit;
        }
    }

    function createTable()
    {
        if(isset($_POST['see']) && $_SESSION['check']==1)
        {
          header("Location: check.php");
          exit;         
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
    <link rel="stylesheet" href="../css/logIn.css">
    <title>Log In</title>
</head>
<body>
    <?php include 'header.php';?>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="centralDiv">
        <?php logIn(); 
            if($_SESSION['check'] == 1)
            {
                echo "<p>you have successfully loged in</p>";
            }else if($_SESSION['check'] == 2)
            {
                echo "<p>some data is incorrect</p>";
            }
        ?>
        <?php createTable(); ?>
        <div class="formDiv">
            <label for="name">Username:</label>
            <input type="text" name="userName" id="name">
        </div>
        <div class="formDiv second">
            <label for="name">Email:</label>
            <input type="email" name="email" id="name">
        </div>
        <div class="formDiv second">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
        </div>
        </div>
        <div class="w">
        <button name="log" class="submitButton">Log in</button>
        <button name="see" class="submitButton q">Check all clients</button>
        <button name="adc" class="submitButton q">Add client</button>
        </div>
        
    </form>
    
    <?php include 'footer.php'; ?>
</body>
</html>