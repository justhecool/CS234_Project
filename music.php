<?php
/* File: music.php
   Author: Justin Schwertmann */

$page_id = 1;
$music = true;
$title = "Music";
include 'inc.header.php';
global $userID;
global $username;
unset ($_SESSION['playlist'], $_SESSION['numSongs']);
?>
<div style="margin-left:25%">
    <div class="w3-container">
        <div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
            <div class="w3-center">
                <div class="w3-centered">
                    <h1>Add a new playlist</h1>
                </div>
                <div class="user-info">
                    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
                        <div class="w3-cell-row">
                            <div class="w3-cell w3-padding">
                                <label for="playlist">Playlist Name
                                    <input class="w3-input" type="text" name="playlist_name" id="playlist" placeholder="Rock n' Roll" autofocus required>
                                </label>
                                <?php
                                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['playlist']) == true)
                                    echo '<p class="w3-text-red">'. $_SESSION['playlist'] .'</p>';?>
                            </div>
                        </div>
                        <div class="w3-cell-row">
                            <button class="w3-btn w3-green w3-right w3-round-medium w3-cell">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php include 'inc.footer.php'; ?>