<?php
            function createTable()
            {
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
                                echo "<tr>
                                <td>{$client_name}</td>
                                <td>{$client_company}</td>
                                <td>{$client_phone}</td>
                                <td>{$client_email}</td>
                                </tr>";
                            }
                            $stmt->closeCursor();
                            $dbHandler=null;
                        }
                    }catch(Exception $ex)
                    {
                        printError($ex);
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
    <link rel="stylesheet" href="../css/check.css">
    <title>Check</title>
</head>
<body>
    <?php include "header.php"; ?>

    <div class="centralDiv">
        <table>
            <tr>
                <th>Name</th>
                <th>Company name</th>
                <th>Phone</th>
                <th>Email</th>
            </tr>
            <?php createTable(); ?>
        </table>
    </div>

    <?php include "footer.php"; ?>
</body>
</html>