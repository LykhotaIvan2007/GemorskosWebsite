<?php
session_start();
if(!isset($_SESSION['messageAdd']))
{
    $_SESSION['messageAdd']=0;
}
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                if(isset($_POST['addClient']))
                {
                    $name=filter_input(INPUT_POST,"userName",FILTER_SANITIZE_SPECIAL_CHARS);
                    $company=filter_input(INPUT_POST,"company",FILTER_SANITIZE_SPECIAL_CHARS);
                    $telephone=filter_input(INPUT_POST,"telephone",FILTER_SANITIZE_SPECIAL_CHARS);
                    $email=filter_input(INPUT_POST,"email",FILTER_SANITIZE_EMAIL);

                    if(empty($name) || empty($company) || empty($telephone) || empty($email))
                    {
                        $_SESSION['messageAdd']=1;
                        header("Location:addClient.php");
                        exit;
                        return;
                    }
                }
                $dbHandler=null;
                function printError($err)
                {
                    echo "<p>some error ocured</p>
                            <p>{$err}</p>";
                }
                try
                {
                    $dbHandler=new PDO("mysql:host=localhost;port=3306;dbname=gemorskos;charset=utf8","root","root");
                }catch(Exception $ex)
                {
                    printError($ex);
                }
                if($dbHandler)
                {
                    try
                    {
                        $stmt=$dbHandler->prepare("SELECT *
                                                    FROM `clients`");
                        if(isset($stmt))
                        {
                            $stmt->bindColumn("client_name",$client_name);
                            $stmt->bindColumn("client_company",$client_company);
                            $stmt->bindColumn("client_phone",$client_phone);
                            $stmt->bindColumn("client_email",$client_email);
                            $stmt->execute();
                            while($result=$stmt->fetch())
                            {
                              if($client_email==$email)
                                {
                                    $_SESSION['messageAdd']=2;
                                    header("Location:addClient.php");
                                    exit;
                                    return;
                                }  
                            }
                        }
                    }catch(Exception $ex)
                    {
                        printError($ex);
                    }
                }
                if($dbHandler)
                {    
                    try
                    {
                        $stmt=$dbHandler->prepare("INSERT INTO `clients`(`client_name`,`client_company`,`client_phone`,`client_email`)
                                                    VALUES (:nm,:cm,:pn,:em)");
                        $stmt->bindParam("nm",$name,PDO::PARAM_STR);
                        $stmt->bindParam("cm",$company,PDO::PARAM_STR);
                        $stmt->bindParam("pn",$telephone,PDO::PARAM_STR);
                        $stmt->bindParam("em",$email,PDO::PARAM_STR);
                        $stmt->execute();
                        $stmt->closeCursor();
                        $dbHandler=null;
                        $_SESSION['messageAdd']=3;
                    }catch(Exception $ex)
                    {
                        printError($ex);
                    }
                }
                header("Location:addClient.php");
                exit;
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
    <link rel="stylesheet" href="../css/addClient.css">
    <title>Add</title>
</head>
<body>
    <?php include "header.php"; ?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
    <div class="centralDiv">
        <?php 
        
        
        switch($_SESSION['messageAdd'])
        {
            case 1:
                {
                    echo "some data is incorrect or some fields is empty";
                    break;
                }
            case 2:
                {
                    echo "client with this email is alredy exist";
                    break;
                }
            case 3:
                {
                    echo "you successfully added new client";
                    break;
                }

        }
        $_SESSION['messageAdd']=0;
        ?>
        <div class="formDiv">
            <label for="name">Name:</label>
            <input type="text" name="userName" id="name">
        </div>
        <div class="formDiv second">
            <label for="password">Company:</label>
            <input type="text" name="company" id="password">
        </div>
        <div class="formDiv second">
            <label for="password">Phone:</label>
            <input type="tel" name="telephone" id="password">
        </div>
        <div class="formDiv second">
            <label for="name">Email:</label>
            <input type="email" name="email" id="name">
        </div>
        </div>
        <div class="w">
        <button name="addClient" class="submitButton">Add</button>
        </div>
</form>
    

    <?php include "footer.php"; ?>
</body>
</html>