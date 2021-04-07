<?php
/* File: add.php
   Author: Justin Schwertmann */

$page_id = 1;
$add = 1;
$title = "Add Users";
include 'inc.header.php';
global $userID;
?>
<div style="margin-left:25%">
    <div class="w3-container">
        <div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
            <div class="w3-center">
                <h1>Add User</h1>
                <form method="POST" action="add.php" enctype="multipart/form-data">
                    <div class="pp">
                        <label for="img">
                            <img src="img/avatar.png" class="avatar" height="100" width="100" alt="Profile Picture Upload"/>
                        </label>
                    </div>
                    <label for="img">Profile Picture Upload
                        <input class="w3-input w3-padding" type="file" id="img" name="image"  accept="image/*"/>
                    </label>
                    <br><br>
                    <div class="w3-cell-row">
                        <div class="w3-cell w3-padding">
                            <label>Username
                                <input class="w3-input" type="text"  name="username" placeholder="juschwe" />
                            </label>
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['uname']) == true)
                                echo '<p class="w3-text-red">'. $_SESSION['uname'] .'</p>';?>
                        </div>
                        <div class="w3-cell">
                            <label>Password
                                <input class="w3-input" type="password" name="password" placeholder="*******" />
                            </label>
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['pwd']) == true)
                              echo '<p class="w3-text-red">'. $_SESSION['pwd'] .'</p>';?>
                        </div>
                    </div>
                    <div class="w3-cell-row w3-padding">
                        <label>Email (optional)
                            <input class="w3-input" type="email" name="email" placeholder="juschwe@siue.edu" />
                        </label>
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['email']) == true)
                            echo '<p class="w3-text-red">'. $_SESSION['email'] .'</p>';?>
                    </div>
                    <?php if (isSU($userID)){?>
                    <div class="w3-cell-row">
                        <label>Administrator
                            <input class="w3-check" type="checkbox" name="admin" value="true" />
                        </label>
                    </div>
                        <br />
                    <? } ?>
                    <div class="w3-row">
                        <button class="w3-btn w3-green w3-round-medium w3-cell-row">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php include 'inc.footer.php'; ?>