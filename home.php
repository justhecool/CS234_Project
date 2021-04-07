<?php
/* File: home.php
   Author: Justin Schwertmann */

$page_id = 1;
$index = true;
$title = "Welcome";
include 'inc.header.php';
global $userID;
global $username;
?>
<div style="margin-left:25%">
<div class="w3-container">
    <?php
    if (isset($_SESSION['error']) || isset($_SESSION['success']))
    {
        $error_msg = '<div class="w3-animate-opacity w3-animate-top w3-panel w3-';
        (isset($_SESSION['success']) == true && $_SESSION['success'] == true) ? $error_msg.= "green" : $error_msg.= "red";
        $error_msg .= ' w3-center w3-display-container" style=""><h1>';
        (isset($_SESSION['error']) == true && $_SESSION['error'] == true) ? $error_msg .= $_SESSION['error'] : $error_msg.= $_SESSION['success'];
        $error_msg .= "</h1></div>";
        echo $error_msg;
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") //Since we redirect to GET don't unset until after it is displayed
        unset($_SESSION['success']);
    unset($_SESSION['error']);

    ?>
<div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
    <div class="w3-center">
        <div class="w3-centered">
         <h1>Welcome</h1>
            <p>Here you can edit your user details, add your favorite artists and songs to share</p>
        </div>
        <div class="w3-row">
            <a href="edit.php?user=<?php echo $userID;?>"><button  class="w3-btn w3-blue w3-right w3-cell w3-round-medium">Edit</button></a>
        </div>
            <div class="pp">
                <img src="<?php echo getUser($userID, 'profile_pic');?>" class="avatar" height="100" width="100" alt="Profile Picture upload"/>
                <p class="w3-large"><?php echo $username; ?></p>
            </div>
    </div>
            <div class="w3-center">
            <h2>Your Playlists</h2>
            </div>
            <!-- Display All music Table -->
            <form action="home.php" method="POST">
                <ul class="w3-ul w3-card-2 w3-round-xlarge">
                    <?php viewPlaylist('user', $userID); ?>
                    <li class="w3-bar">
                        <div class="w3-padding w3-right">
                            <a href="./music.php" class="w3-btn w3-green w3-right w3-round-large">Add</a>
                        </div>
                        <p>New Playlist</p>
                    </li>
                </ul>
            </form>

    </div>
</div>
<?php include 'inc.footer.php'; ?>