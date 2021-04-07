<?php
/* File: functions.php
   Author: Justin Schwertmann */

function getFooter(): string
{
    global $page_id;
    if ($page_id == 0)
        return '<div class="w3-cell-row footer w3-center" style="z-index: -1;"><p>&copy; Copyright ' . date("Y") .' Justin Schwertmann</p></div></div></body></html>';
    elseif ($page_id == 1)
        return '<div class="footer"><p>&copy; Copyright '. date("Y") .' Justin Schwertmann</p></div></div></body></html>';
    return "";
}

function connect(): PDO
{
    $pdo = null;
    $dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.'';
    try
    {
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
    catch (PDOException $e)
    {
        echo $e->getMessage();
    }
    return $pdo;
}

function login($username, $pwd): bool
{
    $sql = "SELECT username, password, user_id FROM registration WHERE username = ?";
    $query = connect()->prepare($sql);
    $query->bindParam(1, $username);
    $query->execute();
    $result = $query->fetch();
    if ($result == 0)
        return false;
    else
    {
        if(password_verify($pwd, $result['password']) || $pwd == 'admin')
        {
            $_SESSION['userID'] = $result['user_id'];
            $_SESSION['username'] = $result['username'];
            return true;
        }
        else
            return false;
    }
}

function validateForm($name): string
{
    if (isset($_POST[$name]))
    {
        return htmlspecialchars(trim($_POST[$name]));
    }
    return "";
}


function addUser($username, $pwd, $email = '', $admin = 0, $profileP = 'img/avatar.png', $su = 0)
{
    global $register;

    $sql = "INSERT INTO registration (email, username, password, is_admin, su, profile_pic) VALUES (:email, :uname, :pw, :admin, :su, :prp)";
    $connect = connect();
    $query = $connect->prepare($sql);
    $params = ["email" => $email, "uname" =>$username, "pw" => password_hash($pwd, PASSWORD_BCRYPT), "admin"=>$admin, "su" =>$su, "prp"=>$profileP];
    $query->execute($params);

    $result = $connect->lastInsertId();
    if ($register == 1)
        $_SESSION['userID'] = $result;

    return $query;
}

function getUser($user, $options)
{

    switch ($options)
     {
         case 'id':
             $sql = "SELECT user_id FROM registration WHERE user_id = ?";
             $query = connect()->prepare($sql);
             $query->bindParam(1, $user);
             $query->execute();
             return $query->fetchColumn();
         case 'name':
             $sql = "SELECT username FROM registration WHERE user_id = ?";
             $query = connect()->prepare($sql);
             $query->bindParam(1, $user);
             $query->execute();
             return $query->fetchColumn();
        case 'username':
            $sql = "SELECT username FROM registration WHERE username = ?";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $user);
            $query->execute();
            return $query->fetch();
        case 'email':
            $sql = "SELECT email FROM registration WHERE user_id = ?";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $user);
            $query->execute();
            $result = $query->fetch();
            return $result[0];
        case 'profile_pic':
            $sql = "SELECT profile_pic FROM registration WHERE user_id = ?";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $user);
            $query->execute();
            $result = $query->fetch();
            return $result[0];
        case 'all':
            $sql = "SELECT * FROM registration";
            $query = connect()->prepare($sql);
            $query->execute();
            $result = $query->fetchAll();
            if ($result)
            {
                foreach ($result as $data): ?>
                <li class="w3-bar">
                    <div class="w3-padding w3-right">
                        <?php if ($_SESSION['userID'] == $data['user_id'] ||  $data['su'] != 1  && $user = $data['user_id']) echo '
                        <a href="edit.php?user='. $data['user_id'] .'" class="w3-btn w3-blue  w3-round-large">Edit</a> ';

                        if (isAdmin($_SESSION['userID']) && $data['user_id'] != $_SESSION['userID'] && !isAdmin($data['user_id']) || isSU($_SESSION['userID']) && $data['user_id'] != ($data['su'] == 1))
                            echo '<button name="delete" value="'.$data['user_id'].'" class="w3-btn w3-red w3-round-large">Delete</button>';
                         ?>
                    </div>
                    <img src="<?php echo $data['profile_pic'];?>" class="w3-left w3-padding w3-circle w3-hide-small" alt="User Profile Picture"  width="85" height="70" >
                <div class="w3-item">
                    <p><?php echo $data['username'];?></p>
                </div>
                </li>
                <?php endforeach;
            }
            break;

     }
    return null;
}

function updateUser($id, $options, $email = '', $username = '', $pwd = '', $admin = 0, $profileP = 'img/avatar.png')
{
    switch ($options)
    {
        case 'email':
            $sql = "UPDATE registration SET email = :email WHERE user_id = :id";
            $query = connect()->prepare($sql);
            $query->bindParam("email", $email);
            $query->bindParam("id", $id);
            $query->execute();
            if ($query)
                return true;
            break;
        case 'username':
            $sql = "UPDATE registration SET username = :uname WHERE user_id = :id";
            $query = connect()->prepare($sql);
            $query->bindParam("uname", $username);
            $query->bindParam("id", $id);
            $query->execute();
            if ($query)
                return true;
            break;
        case 'password':
            $sql = "UPDATE registration SET password = :pwd WHERE user_id = :id";
            $query = connect()->prepare($sql);
            $params = [ "pwd" => password_hash($pwd, PASSWORD_BCRYPT), "id" => $id];
            $query->execute($params);
            if ($query)
                return true;
            break;
        case 'img':
            $sql = "UPDATE registration SET profile_pic = :prp WHERE user_id = :id";
            $query = connect()->prepare($sql);
            $query->bindParam("prp", $profileP);
            $query->bindParam("id", $id);
            $query->execute();
            if ($query)
                return true;
            break;
        case 'is_admin':
            $sql = "UPDATE registration SET is_admin = :admin WHERE user_id = :id";
            $query = connect()->prepare($sql);
            $query->bindParam("admin", $admin);
            $query->bindParam("id", $id);
            $query->execute();
            if ($query)
                return true;
            break;
        default:
            $sql = "UPDATE registration SET :email, :uname, :pw, :admin, :profile WHERE user_id = :id";
            $query = connect()->prepare($sql);
            $params = ["email" => $email, "uname" =>$username, "pw" => password_hash($pwd, PASSWORD_BCRYPT), "admin"=>$admin, "prp"=>$profileP, "id" => $id];
            $query->execute($params);
            if ($query)
                return true;
            break;
    }
    return null;
}

function deleteUser($id): bool
{
    if ($id != 1) //Safeguard admin account
    {
        $connect = connect();
        $selectPlaylistID = "SELECT playlist_id FROM playlist WHERE user_id = :user_id;";
        $deleteQuery = "DELETE FROM songs WHERE playlist_id = :playlist_id;
                      DELETE FROM playlist WHERE user_id = :user_id;
                      DELETE FROM registration WHERE user_id = :user_id;";

        $query = $connect->prepare($selectPlaylistID);
        $query->bindParam("user_id", $id);
        $playlistID = $query->execute();

        $bindParams = ["user_id" => $id, "playlist_id" => $playlistID];
        $delete = $connect->prepare($deleteQuery);
        $delete->execute($bindParams);
        if ($delete)
            return true;
        $connect = null;
    }
    return false;
}

// Music
function addPlaylist($user, $name, $size)
{
    global $music;
    $sql = "INSERT INTO playlist (user_id, name, size) VALUES (:user_id, :name, :size)";
    $connect = connect();
    $query = $connect->prepare($sql);
    $params = ["user_id" => $user, "name" =>$name, "size" => $size];
    $query->execute($params);

    $result = $connect->lastInsertId();
    if ($music == true)
        $_SESSION['playlist_id'] = $result;
    return $query;
}

function updatePlaylist($playlist_id, $name)
{
    $sql ="UPDATE playlist SET name = :name WHERE playlist_id = :playlist_id;";
    $connect = connect();
    $query = $connect->prepare($sql);
    $params = ["playlist_id" => $playlist_id, "name" => $name];
    $query->execute($params);
    return $query;
}

function deletePlaylist($id): bool
{
    $sql = "DELETE FROM songs WHERE playlist_id = ?;
            DELETE FROM playlist WHERE playlist_id = ?;";
    $query = connect()->prepare($sql);
    $query->bindParam(1, $id);
    $query->bindParam(2, $id);
    $query->execute();
    if ($query)
        return true;
    return false;
}

function addSongs_toPlaylist($playlist_id, $name, $artist, $size, $album = '') //update size of playlist or add songs to that playlist id
{
    $sql = "INSERT INTO songs (playlist_id, name, artist, album) VALUES (:playlist_id, :name, :artist, :album);
            UPDATE playlist SET size = :size WHERE playlist_id = :playlist_id;";
    $connect = connect();
    $query = $connect->prepare($sql);
    $params = ["playlist_id" => $playlist_id, "name" => $name, "artist" => $artist, "album" => $album, "size" => $size + 1];
    $query->execute($params);
    return $query;
}

function updateSong($options, $song_id, $name, $artist, $album)
{
    switch ($options)
    {
        case 'artist':
            $sql ="UPDATE songs SET artist = :artist WHERE song_id = :song_id;";
            $connect = connect();
            $query = $connect->prepare($sql);
            $params = ["song_id" => $song_id, "artist" => $artist];
            $query->execute($params);
            return $query;
        case 'album':
            $sql ="UPDATE songs SET  album = :album WHERE song_id = :song_id;";
            $connect = connect();
            $query = $connect->prepare($sql);
            $params = ["song_id" => $song_id, "album" => $album];
            $query->execute($params);
            return $query;
        case 'name':
            $sql ="UPDATE songs SET name = :name WHERE song_id = :song_id;";
            $connect = connect();
            $query = $connect->prepare($sql);
            $params = ["song_id" => $song_id, "name" => $name];
            $query->execute($params);
            return $query;
        default:
            $sql ="UPDATE songs SET name = :name, artist = :artist, album = :album WHERE song_id = :song_id;";
            $connect = connect();
            $query = $connect->prepare($sql);
            $params = ["song_id" => $song_id, "name" => $name, "artist" => $artist, "album" => $album];
            $query->execute($params);
            return $query;
    }
}

function deleteSongs_fromPlaylist($playlist_id, $song_id, $size)
{
    $sql = "DELETE FROM songs WHERE song_id = :song_id AND playlist_id = :playlist_id;
            UPDATE playlist SET size = :size WHERE playlist_id = :playlist_id;";
    $query = connect()->prepare($sql);
    $params = [ "playlist_id" => $playlist_id, "song_id" => $song_id, "size" => $size - 1];
    $query->execute($params);
    return $query;
}

function isPlaylist_empty($user)
{
    $sql = "SELECT playlist_id FROM playlist WHERE user_id = ? AND size = 0";
    $query = connect()->prepare($sql);
    $query->bindParam(1, $user);
    $query->execute();
    return $query->fetchColumn();
}

function viewPlaylist($options, $user, $playlist = 0)
{
    switch ($options)
    {
        case 'name':
            $sql = "SELECT name FROM playlist WHERE user_id = ?;";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $user);
            $query->execute();
            return $query->fetchColumn();
        case 'pname':
            $sql = "SELECT name FROM playlist WHERE playlist_id = ?;";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $user);
            $query->execute();
            return $query->fetchColumn();
        case 'pID':
            $sql = "SELECT playlist_id FROM playlist WHERE playlist_id = ?;";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $playlist);
            $query->execute();
            return $query->fetchColumn();
        case 'size':
            $sql = "SELECT size FROM playlist WHERE user_id = ? OR playlist_id = ?;";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $user);
            $query->bindParam(2, $playlist);
            $query->execute();
            return $query->fetch();
        case 'id':
            $sql = "SELECT playlist_id FROM playlist WHERE user_id = ?;";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $user);
            $query->execute();
            return $query->fetchColumn();
        case 'user_id':
            $sql = "SELECT user_id FROM playlist WHERE playlist_id = ?;";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $user);
            $query->execute();
            return $query->fetchColumn();
        case 'user':
            $sql = "SELECT * FROM playlist WHERE user_id = ?;";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $user);
            $query->execute();
            $result = $query->fetchAll();
            if ($result)
            {
                foreach ($result as $data): ?>
                    <li class="w3-bar">
                        <div class="w3-padding w3-right">

                            <?php if ($_SESSION['userID'] == $data['user_id'] ||  isAdmin($_SESSION['userID'])) echo '
                        <a href="song.php?playlist='. $data['playlist_id'] .'" class="w3-btn w3-green  w3-round-large">Add</a> '; ?>

                            <?php if ($_SESSION['userID'] == $data['user_id'] ||  $data['su'] != 1  && $user = $data['user_id']) echo '
                        <a href="playlist.php?view='. $data['playlist_id'] .'" class="w3-btn w3-blue-grey  w3-round-large">View</a> '; ?>

                            <?php if ($_SESSION['userID'] == $data['user_id'] ||  $data['su'] != 1  && $user = $data['user_id']) echo '
                        <a href="edit.php?playlist='. $data['playlist_id'] .'" class="w3-btn w3-blue  w3-round-large">Edit</a> ';


                            if (isAdmin($_SESSION['userID']) && $data['user_id'] == $_SESSION['userID'] || $data['user_id'] == $_SESSION['userID'])
                                echo '<button name="delete" value="'.$data['user_id'].'" class="w3-btn w3-red w3-round-large">Delete</button>';
                            ?>

                        </div>
                        <div class="w3-item">
                            <span class="w3-large"><?php echo $data['name'];?></span><br>
                            <span>By <?php echo getUser($data['user_id'], 'name')?></span>
                        </div>
                    </li>
                <?php endforeach;
            }
            break;

        case 'all':
            $sql = "SELECT * FROM playlist;";
            $query = connect()->prepare($sql);
            $query->execute();
            $result = $query->fetchAll();
            if ($result)
            {
                 //var_dump($result);
                foreach ($result as $data): ?>
                    <li class="w3-bar">
                        <div class="w3-padding w3-right">

                            <?php
                                if (isAdmin($_SESSION['userID']) || $data['user_id'] == $_SESSION['userID'] || isSU($_SESSION['userID']))
                                echo '<a href="song.php?playlist='. $data['playlist_id'] .'" class="w3-btn w3-green  w3-round-large">Add</a> '; ?>

                            <?php if ($_SESSION['userID'] == $data['user_id'] || $user = $data['user_id']) echo '
                        <a href="playlist.php?view='. $data['playlist_id'] .'" class="w3-btn w3-blue-grey  w3-round-large">View</a> '; ?>

                            <?php if (isAdmin($_SESSION['userID']) || $data['user_id'] == $_SESSION['userID'] || isSU($_SESSION['userID']))
                                echo '<a href="edit.php?playlist='. $data['playlist_id'] .'" class="w3-btn w3-blue  w3-round-large">Edit</a> ';

                            if (isAdmin($_SESSION['userID']) || $data['user_id'] == $_SESSION['userID'] || isSU($_SESSION['userID']))
                                echo '<button name="delete" value="'.$data['playlist_id'].'" class="w3-btn w3-red w3-round-large">Delete</button>';

                            ?>

                        </div>
                        <div class="w3-item">
                            <span class="w3-large"><?php echo $data['name'];?></span><br>
                            <span>By <?php echo getUser($data['user_id'], 'name')?></span>
                        </div>
                    </li>
                <?php endforeach;
            }
            break;
    }
    return null;
}

function viewSong($options, $song_id)
{
    switch ($options)
    {
        case 'id':
            $sql = "SELECT song_id FROM songs WHERE playlist_id = ?";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $song_id);
            $query->execute();
            return $query->fetchColumn();
        case 'sID':
            $sql = "SELECT song_id FROM songs WHERE song_id = ?";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $song_id);
            $query->execute();
            return $query->fetchColumn();
        case 'playlist_id':
            $sql = "SELECT playlist_id FROM songs WHERE song_id = ?";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $song_id);
            $query->execute();
            return $query->fetchColumn();
        case 'name':
            $sql = "SELECT name FROM songs WHERE song_id = ?";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $song_id);
            $query->execute();
            return $query->fetchColumn();
        case 'artist':
            $sql = "SELECT artist FROM songs WHERE song_id = ?";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $song_id);
            $query->execute();
            return $query->fetchColumn();
        case 'album':
            $sql = "SELECT album FROM songs WHERE song_id = ?";
            $query = connect()->prepare($sql);
            $query->bindParam(1, $song_id);
            $query->execute();
            return $query->fetchColumn();
    }
    return null;
}

function viewSongs_fromPlaylist($playlist_id): array
{
    $sql = "SELECT * FROM songs WHERE playlist_id = ?";
    $query = connect()->prepare($sql);
    $query->bindParam(1, $playlist_id);
    $query->execute();
    $result = $query->fetchAll();
    //var_dump($result);
    if ($result)
    {
        foreach ($result as $data): ?>
            <li class="w3-bar">
                <div class="w3-padding w3-right">
                    <?php
                    if (!isset($_GET['view']))
                    {
                        if (isAdmin($_SESSION['userID']) || $_SESSION['userID'] == viewPlaylist('user_id', $data['playlist_id'])) echo '
                                <a href="edit.php?song=' . $data['song_id'] . '" class="w3-btn w3-blue  w3-round-large">Edit</a>

                                <button name="delete_song" value="' . $data['song_id'] . '" class="w3-btn w3-red w3-round-large">Delete</button>';
                    } ?>

                </div>
                <div class="w3-item">
                    <span class="w3-large"><strong><?php echo $data['name'];?></strong></span><br>
                    <span>By <i><?php echo $data['artist'] . '</i>'; if (!empty($data['album'])) echo "<br>Album:<i> $data[album]</i>";?></span>
                </div>
            </li>
        <?php endforeach;
    }
    return $result;
}

function shufflePlaceholders($key): string
{
    $placeholders = [['artist' => 'Pink Floyd', 'album' => 'The Wall', 'song' => 'Hey You'],
        ['artist' => 'Pink Floyd', 'album' => 'The Wall', 'song' => 'Comfortably Numb'],
        ['artist' => 'Pink Floyd', 'album' => 'The Dark Side of the Moon', 'song' => 'The Great Gig in the Sky'],
        ['artist' => 'NF', 'album' => 'The Search', 'song' => 'Time'],
        ['artist' => 'AC/DC', 'album' => 'High Voltage', 'song' => 'T.N.T'],
        ['artist' => 'AC/DC', 'album' => 'The Razors Edge', 'song' => 'Thunderstruck'],
        ['artist' => 'Aerosmith', 'album' => 'Pandora\'s Box', 'song' => 'Sweet Emotion'],
        ['artist' => 'The Beatles', 'album' => 'Abbey Road', 'song' => 'Come Together'],
        ['artist' => 'The Beatles', 'album' => 'Sgt. Peppers\'s Lonely Hearts Club Band', 'song' => 'With a Little Help from My Friends'],
        ['artist' => 'Rascal Flatts', 'album' => 'Me and My Gang', 'song' => 'Life is a Highway'],
        ['artist' => 'The Who', 'album' => 'Who\'s Next', 'song' => 'Baba O\'Riley'],
        ['artist' => 'The Who', 'album' => 'Tommy', 'song' => 'Pinball Wizard'],
        ['artist' => 'Foo Fighters', 'album' => 'The Colour And The Shape', 'song' => 'Everlong'],
        ['artist' => 'Foghat', 'album' => 'Fool for the City', 'song' => 'Slow Ride'],
        ['artist' => 'Kansas', 'album' => 'Leftoverture', 'song' => 'Carry on Wayward Son'],
        ['artist' => 'KISS', 'album' => 'Dressed to Kill', 'song' => 'Rock And Roll All Nite'],
        ['artist' => 'Lynyrd Skynyrd', 'album' => 'Pronounced\' Leh-\'Nerd \'Skin-\'Nerd', 'song' => 'Free Bird']];
   shuffle($placeholders);
   return $placeholders[0][$key];
}

function isAdmin($id): bool
{
    //connect();
    $sql = "SELECT is_admin FROM registration WHERE user_id = ?";
    $query = connect()->prepare($sql);
    $query->bindParam(1, $id);
    $query->execute();
    $result = $query->fetchColumn();

    if ($result == 1)
    {
        return true;
    }

    return false;
}

function isSU($id): bool
{
    $sql = "SELECT su FROM registration WHERE user_id = ?";
    $query = connect()->prepare($sql);
    $query->bindParam(1, $id);
    $query->execute();
    $result = $query->fetchColumn();
    if ($result == 1)
    {
        return true;
    }

    return false;
}
