<?php
/* File: index.php
   Author: Justin Schwertmann */

$page_id = 0;
$login = 1;
$title = "Login";
include 'inc.header.php';
global $error;
?>

<body class="intro-bgc">
<div class="w3-container">
    <div class="w3-display-container">
        <div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
            <div class="w3-center">
                <h1>Welcome</h1>
                <p>Login or Register </p>
                <?php
                if ( $_SERVER['REQUEST_METHOD'] == 'POST')
                    {
                        if ($error['login'] == 500)
                            echo "<p class='w3-text-red'>Username or Password is invalid!</p>";
                    }
                ?>
            </div>
            <form action="index.php" method="POST">
                <div class="w3-cell-row">
                    <div class="w3-padding">
                        <label for="username">Username:
                            <input class="w3-input" type="text" name="username" id="username" placeholder="juschwe" required autofocus>
                        </label>
                    </div>
                    <div class="w3-padding">
                        <label for="pwd">Password:
                            <input class="w3-input" type="password" name="password" placeholder="********" id="pwd" required>
                        </label>
                    </div>
                </div>
                <br>
                <input  type="submit" class="w3-btn w3-green w3-right w3-round-medium" value="Login">
                <a class="w3-btn w3-blue w3-left w3-round-medium" href="register.php">Register</a>
            </form>
        </div>
    </div>
</div>
    <?php include 'inc.footer.php';?>