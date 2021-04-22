<?php
/* File: users.php
   Author: Justin Schwertmann */

$page_id = 1;
$delete = 1;
$users = 1;
$title = "All Users";
include 'inc.header.php';
global $userID;
global $username;
?>
<div style="margin-left:25%">
    <div class="w3-container">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
            if (isset($_SESSION['success'])) {
                $error_msg = '<div class="w3-panel w3-';
                (isset($_SESSION['success']) == true) ? $error_msg .= "green" : $error_msg .= "red";
                $error_msg .= ' w3-center w3-display-container" style=""><h1>';
//                (isset($_SESSION['error']) == true && $_SESSION['error'] == true) ? $error_msg .= $_SESSION['error'] : $error_msg.= $_SESSION['success'];
                if (isset($_SESSION['add'])) $error_msg .= "User Successfully Added!";
                elseif (isset($_SESSION['delete'])) $error_msg .= "User Successfully Deleted!";
                //else $error_msg .= "Something went wrong, try again..";
                $error_msg .= "</h1></div>";
                echo $error_msg;
            }

            unset($_SESSION['success']);
            unset($_SESSION['error']);
            unset($_SESSION['delete']);
            unset($_SESSION['add']);
        }
        ?>
        <div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
                <div class="w3-center">
                    <h1>All Users</h1>
                </div>

            <div class="user-info">
                <form action="users.php" method="POST">
                <ul class="w3-ul w3-card-2 w3-round-xlarge">
                <?php getUser($userID,'all'); ?>
                <li class="w3-bar">
                    <div class="w3-padding w3-right">
                        <a href="./add.php" class="w3-btn w3-green w3-right w3-round-large">Add</a>
                    </div>
                    <img src="img/avatar.png" class="w3-left w3-padding w3-circle w3-hide-small" alt="User Profile Picture"  width="85" height="70" >
                    <p>New User</p>
                </li>
            </ul>
                </form>
            </div>
        </div>
    </div>
<?php include 'inc.footer.php'; ?>