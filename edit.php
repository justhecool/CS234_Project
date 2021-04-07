<?php
/* File: edit.php
   Author: Justin Schwertmann */

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
$page_id = 1;
$edit = 1;
$delete = 1;
$title = "Editing ";
(isset($_GET['user']) == true) ? $editID = $_GET['user'] : $editID = null;
(isset($_GET['song']) == true) ? $songID = $_GET['song'] : $songID = null;
(isset($_GET['playlist']) == true) ? $playlistID = $_GET['playlist'] : $playlistID = null;
if ($editID != null)
    $title .= "user";
elseif ($songID != null)
    $title .= "song";
elseif ($playlistID)
    $title .= "playlist";
include 'inc.header.php';
global $username, $userID, $add, $editName;

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
<?php if ($editID != null)
    {?>
        <div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
            <div class="w3-center">
                <div class="w3-center">
                    <h1>Editing <?php echo $editName;?></h1>
                    <p>Here you can edit your user details.</p>
                </div>
                <form action="edit.php?user=<?php echo $editID;?>" method="POST" enctype="multipart/form-data">
                    <div class="pp">
                        <label for="img">
                            <img src="<?php echo getUser($editID, 'profile_pic');?>" class="avatar" height="100" width="100" alt="Profile Picture"/>
                        </label>
                    </div>
                    <label for="img">Profile Picture Upload
                        <input class="w3-input" type="file" id="img" name="image"  accept="image/*"/>
                    </label>
                    <br><br>
                    <?php if($editID != isSU($editID)){ //Don't allow the admin user to change their username or password.
                        ?>
                    <div class="w3-cell-row">
                        <div class="w3-cell w3-padding">
                            <label>Username
                                <input class="w3-input" type="text" name="username"  placeholder="<?php echo $editName;?>"/>
                            </label>
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_SESSION['uname']) == true))
                                echo '<p class="w3-text-red">' . $_SESSION['uname'] . '</p>';
                            ?>
                        </div>
                        <div class="w3-cell">
                            <label>Password
                                <input class="w3-input" type="password" name="password" placeholder="*******" />
                            </label>
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['pwd']) == true)
                                    echo '<p class="w3-text-red">' . $_SESSION['pwd'] . '</p>';
                            ?>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="w3-cell-row w3-padding">
                        <label>Email (optional)
                            <input class="w3-input" type="email" name="email" placeholder="<?php if(getUser($editID, 'email') != '') echo getUser($editID, 'email'); else echo "juschwe@siue.edu";?>" />
                            </label>
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['email']) == true)
                                echo '<p class="w3-text-red">' . $_SESSION['email'] . '</p>';
                        ?>
                        </div>
                    <?php
                    if (isSU($userID) && $editID != $userID){?>
                    <div class="w3-cell-row">
                        <label>Administrator </label>
                        <input class="w3-radio" type="radio" name="admin" id="adminT" value="true" <?php if(isAdmin($editID)) echo "checked disabled "; if (isAdmin($userID) && !isSU($userID)) echo "disabled ";?>><label for="adminT"> Yes </label>
                        <input class="w3-radio" type="radio" name="admin" id="adminF" value="false" <?php if(!isAdmin($editID)) echo " checked disabled"; if (isAdmin($userID) && !isSU($userID)) echo "disabled ";?>><label for="adminF"> No </label>
                    </div>
                    <?php } ?>

                    <div class="w3-cell-row">
                        <button class="w3-btn w3-green w3-right w3-round-large w3-cell">Update</button>
                        <?php //Show delete button to only the SU user and admin user.
                        if (isAdmin($userID) && $editID != $userID && !isAdmin($editID) || isSU($userID) && $userID != $editID) { ?>
                            <button name="delete" value="<?php echo $editID;?>" class="w3-btn w3-red w3-left w3-round-large w3-cell">Delete</button>
                        <?php } ?>
                    </div>
                    <input type="hidden" name="editID" value="<?php echo $editID; ?>" />

                </form>
            </div>
        </div>
                <?php
    }
                if ($songID != null) {
                        $songPlaylistID = viewSong('playlist_id', $songID); ?>
        <div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
            <div class="w3-center">
                <div class="w3-center">
                    <h1>Editing <?php echo viewSong('name', $songID) ?></h1>
                    <p>From <?php echo '<a href="playlist.php?view='.$songPlaylistID .'"> '.viewPlaylist('pname', $songPlaylistID).'</a> By '. getUser(viewPlaylist('user_id', $songPlaylistID), 'name').'';?></p>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>?song=<?php echo $songID;?>" method="POST">
                    <?php echo
                        '<div class="w3-cell-row"><div class="w3-cell w3-padding">
                                    <label for="artist">Artist Name
                                        <input class="w3-input" type="text" name="artist" id="artist" placeholder="'.viewSong('artist', $songID) .'" >
                                    </label>
                                </div>
                                <div class="w3-cell">
                                    <label for="album">Album Name (Optional)
                                        <input class="w3-input" type="text" name="album" id="album" placeholder="'. viewSong('album', $songID) .'" />
                                    </label>
                                </div>
                                <div class="w3-cell w3-padding">
                                    <label for="song">Song Name
                                        <input class="w3-input" type="text" name="song" id="song" placeholder="'.viewSong('name', $songID) .'">
                                    </label>
                                </div><br></div>';

                    ?>
                    <input type="hidden" name="playlist_id" value="<?php echo $songPlaylistID;?>"/>
                    <input type="hidden" name="songID" value="<?php echo $songID; ?>" />


                    <div class="w3-cell-row">
                        <button class="w3-btn w3-green w3-right w3-round-medium w3-cell">Update</button>
                        <button name="delete" value="<?php echo $songID;?>" class="w3-btn w3-red w3-left w3-round-large w3-cell">Delete</button>
                        <input type="hidden" name="size" value="<?php echo viewPlaylist('size', '', $songPlaylistID)['size'];?>">

                    </div>
                </form>
            </div>
        </div>
                <?php } if ($playlistID != null){
                $playlist_name = viewPlaylist('pname', $playlistID);?>
        <div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
            <div class="w3-center">
                <div class="w3-center">
                    <h1>Editing <?php echo $playlist_name;?></h1>
                    <p>Here you can update the playlist name or delete the entire playlist and all of it's songs</p>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']?>?playlist=<?php echo $playlistID;?>" method="POST">
                    <div class="w3-cell-row">
                        <div class="w3-cell w3-padding">
                            <label for="playlist">Playlist Name
                                <input class="w3-input" type="text" name="playlist_name" id="playlist" placeholder="<?php echo $playlist_name;?>" >
                            </label>
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['playlist']) == true)
                                echo '<p class="w3-text-red">'. $_SESSION['playlist'] .'</p>';?>
                        </div>
                    </div><br>


                    <div class="w3-cell-row">
                        <?php if (viewPlaylist('id', $userID) == $playlistID || isAdmin($userID)) {
                                echo '<button class="w3-btn w3-green w3-right w3-round-medium w3-cell">Update</button>';}?>
                        <input type="hidden" name="playlist_id" value="<?php echo $playlistID;?>">

                    </div>
                </form>
                    <br>
            </div>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>?playlist=<?php echo $playlistID;?>" method="POST">

                                <h1 class="w3-center">Songs in <?php echo $playlist_name;?></h1>

                            <ul class="w3-ul w3-card-2 w3-round-xlarge">
                                <?php viewSongs_fromPlaylist($playlistID); ?>

                                <?php if ($userID == viewPlaylist('user_id', $playlistID) || isAdmin($userID))
                                    echo '
                        <li class="w3-bar">
                            <div class="w3-padding w3-right">
                                <a href="./song.php?playlist='. $playlistID.'" class="w3-btn w3-green w3-right w3-round-large">Add</a>
                            </div>
                            <p>Add a song to '. $playlist_name.'</p>
                        </li>';?>
                            </ul>
            <input type="hidden" name="size" value="<?php echo viewPlaylist('size', '', $playlistID)['size'];?>">
            <br>
            <button name="delete" value="<?php echo $playlistID;?>" class="w3-btn w3-red w3-bar w3-round-large ">Delete Playlist (this deletes all songs!)</button>
        </form>
        </div>
                        <?php } ?>

    </div>
    <?php  include 'inc.footer.php'; ?>
