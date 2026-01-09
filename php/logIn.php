<?php 

    function logIn()
    {
        if(isset($_POST['log']))
        {
        
            $name=filter_input(INPUT_POST,"userName",FILTER_SANITIZE_SPECIAL_CHARS);
            $email=filter_input(INPUT_POST,"email",FILTER_SANITIZE_EMAIL);
            $password=filter_input(INPUT_POST,"password",FILTER_SANITIZE_SPECIAL_CHARS);
            if(!empty($name)&&!empty($email)&&!empty($password))
            {
                function printError(String $err)
                {
                    echo "<p>the followed error occured</p>
                    <p>{$err}</p>";
                }
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
                                header("Location:logIn.php?reg=1");
                                exit;
                                return;
                            }
                        }
                        $stmt->closeCursor();
                        $dbHandler=null;
                    }
                }
            }
            header("Location:logIn.php?reg=2");
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
    <?php include 'header.php'; ?>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="centralDiv">
        <?php logIn(); 
            $check=filter_input(INPUT_GET,"reg",FILTER_SANITIZE_SPECIAL_CHARS);
            if($check == 1)
            {
                echo "<p>you have successfully loged in</p>";
            }else if($check == 2)
            {
                echo "<p>some data is incorrect</p>";
            }
        ?>
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
        <button name="see" class="submitButton q">Check all users</button>
        </div>
    </form>

    <?php include 'footer.php'; ?>
</body>
</html>