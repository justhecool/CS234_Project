<?php
/* File: song.php
   Author: Justin Schwertmann */

$page_id = 1;
$song = true;
$title = "Add a song";
include 'inc.header.php';
global $userID;
global $username;

//(isset($_GET['numSongs']) == true) ? $_SESSION['numSongs'] = $_GET['numSongs'] : $numSongs = 1;
(isset($_GET['playlist']) == true) ? $playlistID = $_GET['playlist'] : $playlistID = null;

$numSongs = viewPlaylist('size', '', $playlistID);
$playlist_name = viewPlaylist('pname', $playlistID);
//var_dump($numSongs);

?>
    <div style="margin-left:25%">

        <div class="w3-container">

            <div class="w3-panel w3-card w3-round-xlarge intro-mp intro-contentc">
                <div class="w3-center">
                    <div class="w3-centered">
                        <h1>Add a song to <?php echo $playlist_name ?> </h1>
                        <?php if($numSongs['size'] == 0) echo '<p>A playlist must contain of at least one song</p>';?>
                    </div>
                    <div class="user-info">
                        <form action="song.php?playlist=<?php echo $playlistID;?>" method="POST">
                            <?php echo
                                '<div class="w3-cell-row"><div class="w3-cell w3-padding">
                                    <label for="artist">Artist Name
                                        <input class="w3-input" type="text" name="artist" id="artist" placeholder="'.shufflePlaceholders("artist") .'" required>
                                    </label>
                                </div>
                                <div class="w3-cell">
                                    <label for="album">Album Name (Optional)
                                        <input class="w3-input" type="text" name="album" id="album" placeholder="'. shufflePlaceholders("album").'" />
                                    </label>
                                </div>
                                <div class="w3-cell w3-padding">
                                    <label for="song">Song Name
                                        <input class="w3-input" type="text" name="song" id="song" placeholder="'. shufflePlaceholders("song") .'" autofocus required>
                                    </label>
                                </div><br></div>';

                            ?>
                            <input type="hidden" name="playlist_id" value="<?php echo $playlistID;?>"/>
                            <input type="hidden" name="playlist_size" value="<?php echo $numSongs['size'];?>"/>

                            <div class="w3-cell-row">
                                <button class="w3-btn w3-green w3-right w3-round-medium w3-cell">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php include 'inc.footer.php'; ?>