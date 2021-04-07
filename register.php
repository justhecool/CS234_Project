<?php
/* File: register.php
   Author: Justin Schwertmann */

$page_id = 0;
$register = 1;
$title= "Register";
include 'inc.header.php';

?>
<body class="intro-bgc">
<div class="w3-container">
    <div class="w3-display-container">
        <div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
            <div class="w3-center">
                <h1>Register</h1>
                <p>Enter your information to sign up.</p>
            </div>
            <form action="register.php" method="POST">
                <div class="w3-cell-row">
                    <div class="w3-cell w3-padding">
                        <label for="username">Username:
                            <input class="w3-input" type="text" name="username" placeholder="juschwe" id="username" autofocus required>
                        </label>
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST")
                {
                    if (isset($_SESSION['uname']) == true && $_SESSION['uname'] == 202)
                        echo "<p class='w3-text-red'>Username cannot be empty!</p>";
                    if (isset($_SESSION['uname']) == true && $_SESSION['uname'] == 205)
                        echo "<p class='w3-text-red'>Username must be less than 15 characters!</p>";
                    if (isset($_SESSION['error']) == true)
                        echo '<p class="w3-text-red">' . $_SESSION['error'] . '</p>';
                }
                ?>
                    </div>
                    <div class="w3-cell w3-padding">
                        <label for="pwd">Password:
                            <input class="w3-input" type="password" name="password" placeholder="********" id="pwd" required>
                        </label>
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST")
                        {
                            if (isset($_SESSION['pwd']) == true && $_SESSION['pwd'] == 203)
                                echo "<p class='w3-text-red'>You're password is too long!</p>";
                            if (isset($_SESSION['pwd']) == true && $_SESSION['pwd'] == 204)
                                echo "<p class='w3-text-red'>Password cannot be empty!</p>";
                        }
                        ?>
                    </div>
                </div>
                <div class="w3-padding">
                    <label for="email">Email:
                        <input class="w3-input" type="email" name="email" placeholder="juschwe@siue.edu (optional)" id="email">
                    </label>
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST")
                {
                    if (isset($_SESSION['email']) == true)
                        echo '<p class="w3-text-red">' . $_SESSION['email'] . '</p>';
                    if (isset($_SESSION['email']) == true && $_SESSION['email'] == 201)
                        echo "<p class='w3-text-red'>You're email is too long!</p>";
                }
                ?>
                </div>
                <br>
                <a class="w3-btn w3-blue w3-left w3-round-medium" href="index.php">Login</a>
                <input  type="submit" class="w3-btn w3-green w3-right w3-round-medium" value="Register">
            </form>
        </div>
    </div>
</div>
<?php include 'inc.footer.php';