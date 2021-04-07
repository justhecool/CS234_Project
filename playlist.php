<?php
/* File: music.php
   Author: Justin Schwertmann */

$page_id = 1;
$playlist = true;
$title = "Music";
$delete = 1;
include 'inc.header.php';
global $userID;
global $username;
unset ($_SESSION['playlist'], $_SESSION['numSongs']);
?>
    <div style="margin-left:25%">

    <div class="w3-container">
        <?php
        if (isset($_SESSION['error']) || isset($_SESSION['success']))
        {
            $error_msg = '<div class="w3-panel w3-';
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
        <?php if (!isset($_GET['view']))
            {?>
        <div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
            <div class="w3-center">
                <h1>All Playlists</h1>
            </div>

            <div class="user-info">
                <form action="playlist.php" method="POST">
                    <ul class="w3-ul w3-card-2 w3-round-xlarge">
                        <?php viewPlaylist('all', $userID); ?>
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
        <?php } elseif (isset($_GET['view'])) {
        (isset($_GET['view']) == true) ? $playlistID = $_GET['view'] : $playlistID = 0; ?>

        <div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
            <?php if ($userID == viewPlaylist('user_id', $playlistID) || isAdmin($userID)) echo
            '<div class="w3-row">
            <form action="playlist.php?view='.$playlistID.'" method="POST">
                <button name="delete" value="'. $playlistID.'" class="w3-btn w3-red w3-left w3-round-large w3-cell">Delete Playlist</button>
            </form>
            <a href="edit.php?playlist='. $playlistID .'"><button  class="w3-btn w3-blue w3-right w3-round-medium">Edit Playlist</button></a>
            </div>';?>
                <div class="w3-center">
                <h1><?php echo viewPlaylist('pname', $playlistID);?></h1>
                <p>By <?php echo getUser(viewPlaylist('user_id', $playlistID), 'name')?></p>
            </div>

            <div class="user-info">
                <form action="playlist.php?view=<?php echo $playlistID;?>" method="POST">
                    <ul class="w3-ul w3-card-2 w3-round-xlarge">
                        <?php if (empty(viewSongs_fromPlaylist($playlistID)))
                            echo '<li class="w3-bar w3-center"><div class="w3-item"><p>No songs in playlist!</p></div></li>';
                        if ($userID == viewPlaylist('user_id', $playlistID) || isAdmin($userID))
                         echo '
                        <li class="w3-bar">
                            <div class="w3-padding w3-right">
                                <a href="./song.php?playlist='. $playlistID.'" class="w3-btn w3-green w3-right w3-round-large">Add</a>
                            </div>
                            <p>Add a song to playlist</p>
                        </li>';?>
                    </ul>
                    <input type="hidden" name="size" value="<?php echo viewPlaylist('size', '', $playlistID)['size'];?>">
                </form>
            </div>
        </div>
        <?php } ?>
    </div>
<?php include 'inc.footer.php'; ?>