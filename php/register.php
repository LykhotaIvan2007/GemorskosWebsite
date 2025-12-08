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
        <div class="formDiv">
            <label for="name">Username:</label>
            <input type="text" name="userName" id="name">
        </div>
        <div class="formDiv second">
            <label for="email">Email</label>
            <input type="text" name="email" id="email">
        </div>
        <div class="formDiv second">
            <label for="password">Password:</label>
            <input type="text" name="password" id="password">
        </div>
        <div class="formDiv second">
            <label for="password2">Password again:</label>
            <input type="text" name="password2" id="password2">
        </div>
        </div>
        <button name="log" class="submitButton">Log in</button>
        
    </form>

    <?php include 'footer.php'; ?>
</body>
</html>