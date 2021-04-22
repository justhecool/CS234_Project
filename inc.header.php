<?php
/* File: inc.header.php
   Author: Justin Schwertmann */

session_start();
require_once './config.php';
require('./functions.php');
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$upload_path = getcwd() . "/img";

//Make sure we have access to vars
global $login, $register, $title, $page_id, $index, $edit, $editID, $delete, $add, $users, $music, $song, $playlist, $songID;
$editName = getUser($editID, 'name');
(isset($_GET['playlist']) == true) ? $playlistID = $_GET['playlist'] : $playlistID = null;

//Redirect non-logged in user. Check page id so we don't indefinitely loop as this is included on every page.
if (!isset($username) && $page_id != 0)
{
    header("Location: ./index.php");
    die;
}

//User must be signed in redirect to homepage.
elseif (isset($username) && $page_id == 0)
{
    header("Location: ./home.php");
    die;
}

if (!isAdmin($userID))
{
    //Members are not allowed to access these pages!
    if ($add == 1 || $users == 1)
    {
        header("Location: ./home.php");
        die;
    }
}

// Edit Redirection Logic
if (!isset($_GET['user']) && $edit == 1 && !isset($_GET['song']) && !isset($_GET['playlist']))
{
    header("Location: ./edit.php?user=$userID");
    die;
}

if (isset($_GET['playlist']) && $edit == 1 && $playlistID != viewPlaylist('id', $userID) && !isAdmin($userID))
{
    header("Location: ./playlist.php");
    die;
}

if (isset($_GET['song']) && $edit == 1 && $songID != viewSong('id', viewSong('playlist_id', $songID)) && !isAdmin($userID))
{
    header("Location: ./playlist.php");
    die;
}

if (isset($_GET['playlist']) && $song == true && $playlistID != viewPlaylist('id', $userID) && !isAdmin($userID))
{
    header("Location: ./playlist.php");
    die;
}

//if (!isset($_GET['playlist']) && empty($_GET['playlist']) && $song == 1)
//{
//    $_SESSION['error'] = "Please select or add a playlist!";
//    if (!isAdmin($userID))
//        header("Location: ./home.php");
//    header("Location: ./playlist.php");
//    die;
//}

if (isset($_GET['user']) && $edit == 1 && !isset($_GET['song']))
{

    if(!getUser($_GET['user'], 'id'))
    {
        header("Location: ./edit.php?user=$userID");
        $_SESSION['success'] = false;
        $_SESSION['error'] = "That user does not exist!";
        die;
    }
    if ($_GET['user'] != $userID && !isAdmin($userID) && !isSU($userID))
    {
        header("Location: ./edit.php?user=$userID");
        die;
    }
    elseif (isSU($_GET['user']) && $userID != isSU($userID))
    {
        header("Location: ./edit.php?user=$userID");
        die;
    }
}

// Update User
if($_SERVER["REQUEST_METHOD"] == "POST" && $edit == 1 && !isset($_GET['playlist']) && !isset($_GET['song']))
{
    $id = $_POST['editID'];
    $uname = validateForm('username');
    $pwd = validateForm('password');
    $email = validateForm('email');
    $is_admin = isset($_POST['admin']) ? 1 : 0;

//        unset($_SESSION['error']);
        unset($_SESSION['email']);
        unset($_SESSION['uname']);
        unset($_SESSION['pwd']);
        unset($_SESSION['file']);
//        unset($_SESSION['success']);

        if ($_FILES['image']['error'] != UPLOAD_ERR_NO_FILE)
        {
            $tmp_name = $_FILES['image']['tmp_name'];
            $filename = basename($_FILES['image']['name']);
            move_uploaded_file($tmp_name, "$upload_path/$filename");

            // file uploaded, try to add user to db with rest of form data
            $_SESSION['file'] = 'img/' . $filename;
        }

    $filename = isset($filename) ? $_SESSION['file'] : 'img/avatar.png';

    //User uploaded file
    if (isset($_SESSION['file']))
    {
        updateUser($id, 'img', '', '', '', '', $filename);
        header("Location: ./edit.php?user=$id"); //Success, Redirect to GET request so the form isn't submitted every refresh!
        $_SESSION["success"] = getUser($id, 'name') . " successfully updated!";
        $_SESSION['error'] = false;
    }

    if (isset($uname))
    {
        if (strlen($uname) > 15)
            $_SESSION['uname'] = "Username must be less than 15 characters!";
        if (empty($uname))
            $_SESSION["uname"] = false;
        if (!isset($_SESSION['uname'])) {
            if (getUser($uname, 'username'))
                $_SESSION['uname'] = "Sorry $uname is in use!";
            elseif (updateUser($id, 'username', '', $uname))
            {
                //Show success
                if ($_SESSION['userID'] == $id)
                    $_SESSION['username'] = $uname;
                header("Location: ./edit.php?user=$id"); //Success, Redirect to GET request so the form isn't submitted every refresh!
                $_SESSION["success"] = getUser($id, 'name') . " successfully updated!";
                $_SESSION['error'] = false;
            }
        }
    }

    do
    {
        // Email
        if (empty($email))
        {
            $_SESSION['email'] = false;
            break;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["email"] = "$email is not a valid email!";
            break;
        }

        elseif (strlen($email) > 320) {
            $_SESSION['email'] = "Email is too long!";
            break;
        }


        if (!isset($_SESSION['email'])) {
            updateUser($id, 'email', $email);
            if (!isset($_SESSION['success']) || !isset($_SESSION['error']))
            {
                $_SESSION["success"] = getUser($id, 'name') . " successfully updated!";
                $_SESSION['error'] = false;
            }
            header("Location: ./edit.php?user=$id"); //Success, Redirect to GET request so the form isn't submitted every refresh!
            break;
        }

    } while ($email);

    //Password
    if (empty($pwd))
    {
        $_SESSION['pwd'] = false;
    }

    if (strlen($pwd) > 60)
    {
        $_SESSION['pwd'] = "Password is too long!";
    }

    if (!isset($_SESSION['pwd']) && isset($pwd) == true)
    {
        updateUser($id, 'password', '', '', $pwd);
        header("Location: ./edit.php?user=$id"); //Success, Redirect to GET request so the form isn't submitted every refresh!
        if (!isset($_SESSION['success']) || !isset($_SESSION['error']))
        {
            $_SESSION["success"] = getUser($id, 'name') . " successfully updated!";
            $_SESSION['error'] = false;
        }
    }

    if (isSu($userID) && isset($_POST['admin']))
    {
        if(isAdmin($id) && !$_POST['admin'] == false)
        {//User unchecked admin
            updateUser($id, 'is_admin');
            header("Location: ./edit.php?user=$id"); //Success, Redirect to GET request so the form isn't submitted every refresh!
            if (!isset($_SESSION['success']) || !isset($_SESSION['error']))
            {
                $_SESSION["success"] = getUser($id, 'name') . " successfully updated!";
                $_SESSION['error'] = false;
            }
        }
        elseif($_POST['admin'] == true)
        {
            updateUser($id, 'is_admin', '', '', '', 1);
            header("Location: ./edit.php?user=$id"); //Success, Redirect to GET request so the form isn't submitted every refresh!
            if (!isset($_SESSION['success']) || !isset($_SESSION['error']))
            {
                $_SESSION["success"] = getUser($id, 'name') . " successfully updated!";
                $_SESSION['error'] = false;
            }
        }
    }
}

// Register User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $register == 1)
{
    $email = validateForm('email');
    $uname = validateForm('username');
    $pwd = validateForm('password');

    if ($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'POST')
    {   // Clear Session each time user submits form or visits page
        unset($_SESSION['error']);
        unset($_SESSION['email']);
        unset($_SESSION['uname']);
        unset($_SESSION['pwd']);
    }

    //User can be registered without email, but make sure they have a valid email if they input one
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))
        $_SESSION["email"] = "$email is not a valid email!";

    elseif(strlen($email) > 320)
        $_SESSION['email'] = 201;

    // User should never reach here but prevent cross-scripting
    if (strlen($uname) > 15)
        $_SESSION["uname"] = 205;

    if (empty($uname))
        $_SESSION["uname"] = 202;

    if (strlen($pwd) > 60)
        $_SESSION['pwd'] = 203;

    if (empty($pwd))
        $_SESSION["pwd"] = 204;

    if (!isset($_SESSION['email']) && !isset($_SESSION['uname']) && !isset($_SESSION['pwd']))
    {
        if (getUser($uname, 'username'))
        {
            //username is in use!
            $_SESSION['error'] = "Sorry $uname is in use!";
        }
        else
        {

            if (addUser($uname, $pwd, $email))
            {
                $_SESSION['username'] = $uname;
                header('Location: ./home.php');
            }
            else
                $_SESSION['error'] = "There was an error, please try again";
        }

    }
}

//Admin only, Add users
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $add == 1 && isAdmin($userID))
{
    $user_name = validateForm('username');
    $pwd = validateForm('password');
    $email = validateForm('email');
    $is_admin = isset($_POST['admin']) ? 1 : 0;

    unset($_SESSION['error']);
    unset($_SESSION['email']);
    unset($_SESSION['uname']);
    unset($_SESSION['pwd']);
    unset($_SESSION['file']);

    if ($_FILES['image']['error'] != UPLOAD_ERR_NO_FILE)
    {
        $tmp_name = $_FILES['image']['tmp_name'];
        $filename = basename($_FILES['image']['name']);
        move_uploaded_file($tmp_name, "$upload_path/$filename");

        // file uploaded, try to add user to db with rest of form data
        $_SESSION['file'] = 'img/' . $filename;
   }

    $filename = isset($filename) ? $_SESSION['file'] : 'img/avatar.png';

    //User uploaded file
    if (strlen($user_name) > 15)
        $_SESSION['uname'] = "Username must be less than 15 characters!";

    if (empty($user_name))
        $_SESSION["uname"] = "Username cannot be empty!";

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))
        $_SESSION["email"] = "$email is not a valid email!";

    elseif(strlen($email) > 320)
        $_SESSION['email'] = "Email is too long!";

    if (empty($pwd))
        $_SESSION["pwd"] = "Password cannot be empty!";

    if (strlen($pwd) > 60)
        $_SESSION['pwd'] = "Password is too long!";

    if (!isset($_SESSION['email']) && !isset($_SESSION['uname']) && !isset($_SESSION['pwd']))
    {
        if (getUser($user_name, 'username'))
            $_SESSION['uname'] = "Sorry $user_name is in use!";
        elseif (addUser($user_name, $pwd, $email, $is_admin, $filename) && !isset($_SESSION['email']) && !isset($_SESSION['uname']) && !isset($_SESSION['pwd']))
        {
            $_SESSION['success'] = true;
            $_SESSION['add'] = true;
            header("Location: ./users.php");
        }
    }
}

// Delete User
if (isset($_POST['delete']) && $delete == 1 && isAdmin($userID) && !isset($_GET['playlist']) && !isset($_GET['song']))
{
    $id = $_POST['delete'];
     //Prevent user deleting themselves & admin users cannot be deleted
    if ($id != $userID && !isAdmin($id) || !isSU($id) && isAdmin($id) && isSU($userID) && $id != $userID)
    {

        if (deleteUser($id))
        {
            $_SESSION['success'] = true;
            $_SESSION['delete'] = true;

            if ($edit == 1)
            {
                header("Location: ./users.php");
                $_SESSION['success'] = true;
                $_SESSION['delete'] = true;
            }
        }
        else
        {
            $_SESSION['success'] = false;
            $_SESSION['delete'] = false;
        }
    }
    else
    {
        $_SESSION['success'] = false;
        $_SESSION['delete'] = false;
    }
}

// Delete + Update D(song) or playlist from edit.php
if ($edit == 1 && isset($_GET['playlist']) == true)
{
    if (isset($_GET['playlist']) && viewPlaylist('pID', '', $_GET['playlist']) == false)
    {
        header("Location: ./playlist.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if (isset($_POST['delete']) && $delete == 1)
        {
            deletePlaylist($_POST['delete']);
            unset($_SESSION['playlist_id']);
            header("Location: ./playlist.php");

        }

        if (isset($_POST['delete_song']) && $delete == 1)
        {
            deleteSongs_fromPlaylist($_GET['playlist'], $_POST['delete_song'], $_POST['size']);
        }

        if (isset($_POST['playlist_name']))
        {
            $playlistID = $_POST['playlist_id'];
            $playlist_name = validateForm('playlist_name');
            if (!empty($playlist_name) && strlen($playlist_name) <= 50)
            {
                updatePlaylist($playlistID, $playlist_name);
                header("Location: ./playlist.php?view=$playlistID");
            }
            else
            {
                header("Location: ./edit.php?playlist=$playlistID"); //redirect to same page to prevent form submit on refresh
            }
        }
    }
}

//Update song
if ($edit == 1 && isset($_GET['song']) == true)
{
    if (isset($_GET['song']) && viewSong('sID',  $_GET['song']) == false)
    {
        header("Location: ./playlist.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $playlistID = $_POST['playlist_id'];
        $artist = validateForm("artist");
        $album = validateForm("album");
        $song = validateForm("song");
        $songID = $_POST['songID'];

        if (isset($_POST['delete']) && $delete == 1)
        {
            deleteSongs_fromPlaylist($_POST['playlist_id'], $_POST['songID'], $_POST['size']);
            header('Location: ./playlist.php?view='.$_POST['playlist_id'].'');
        }

        if (isset($songID) && !empty($artist && $song && $album))
        {
            updateSong('', $songID, $song, $artist, $album);
            header("Location: ./playlist.php?view=$playlistID");
        }

        elseif(isset($songID) && !empty($artist) && empty($song) && empty($album))
        {
            updateSong('artist', $songID, $song, $artist, $album);
            header("Location: ./playlist.php?view=$playlistID");
        }

        elseif(isset($songID) && empty($artist) && !empty($song) && empty($album))
        {
            updateSong('name', $songID, $song, $artist, $album);
            header("Location: ./playlist.php?view=$playlistID");
        }

        elseif(isset($songID) && empty($artist) && empty($song) && !empty($album))
        {
            updateSong('album', $songID, $song, $artist, $album);
            header("Location: ./playlist.php?view=$playlistID");
        }
       // else
         //   header("Location: ./edit.php?song=$songID");
    }
}

//Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $login == 1)
{
    $username = validateForm('username');
    $pwd = validateForm('password');

    if (login($username, $pwd))
    {
        header("Location: ./home.php");
        die;
    }
    else
        $error['login'] = 500;
}

// Music
if ($music == true)
{
    if (isset($_SESSION['playlist_id']) && $_SESSION['playlist_id'] != false  || viewPlaylist('size', '', isPlaylist_empty($userID)) != false && viewPlaylist('size', '', isPlaylist_empty($userID))['size'] == 0)
    {
        $_SESSION['playlist_id'] = isPlaylist_empty($userID);
        header("Location: ./song.php?playlist=$_SESSION[playlist_id]");
        die;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $playlist_name = validateForm('playlist_name');
        $numSongs = 0;

        if (strlen($playlist_name) > 50)
            $_SESSION['playlist'] = "Sorry playlist name is too long!";

        if (empty($playlist_name))
            $_SESSION['playlist'] = "Playlist name cannot be empty!";

        if (viewPlaylist('name', $userID) == $playlist_name)
            $_SESSION['playlist'] = "Sorry $playlist_name is in use!";

        if (!isset($_SESSION['playlist']) && addPlaylist($userID, $playlist_name, $numSongs))
        {
            //$_SESSION['success'] = true;
            header("Location: ./song.php?playlist=$_SESSION[playlist_id]");
            die;
        }
    }
}
if ($song == true)
{
//    if ($_SERVER['REQUEST_METHOD'] == 'GET')

//        if (!isset($_GET['playlist_name']) || isset($_GET['playlist_name']) && empty($_GET['playlist_name']) || !isset($_GET['numSongs']) || isset($_GET['numSongs']) && $_GET['numSongs'] <= 0 || $_GET['numSongs'] > 20)
//        {
//            header("Location: ./music.php");
//            die;
//        }
    if (isset($_GET['playlist']) && viewPlaylist('pID', '', $_GET['playlist']) == false)
    {
        header("Location: ./playlist.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {

        $playlistID = $_POST['playlist_id'];
        $artist = validateForm("artist");
        $album = validateForm("album");
        $song = validateForm("song");
        $size = $_POST['playlist_size'];

        if (isset($_GET['playlist']) && !empty($artist && $song || empty($album)))
        {
            addSongs_toPlaylist($playlistID, $song, $artist, $size, $album);
            unset($_SESSION['playlist_id']);
            //success
            header("Location: ./playlist.php?view=$playlistID");
        }
    }
}

if ($playlist == true)
{
    if (isset($_GET['view']) && viewPlaylist('pID', '', $_GET['view']) == false)
    {
        header("Location: ./playlist.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if (isset($_POST['delete']) && $delete == 1)
        {
            deletePlaylist($_POST['delete']);
            unset($_SESSION['playlist_id'], $_SESSION['success']);
            header("Location: ./playlist.php");
        }
        if (isset($_POST['delete_song']) && $delete == 1)
        {
            deleteSongs_fromPlaylist($_GET['view'], $_POST['delete_song'], $_POST['size']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $title;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/w3.css"/>
</head>
<?php
if ($page_id == 1)
{?>
<body class="bg">
<div class="w3-sidebar w3-bar-block nav-color" style="width:25%">
    <div class="w3-container w3-light-blue w3-padding-16">
        <img src="<?php echo getUser($userID, 'profile_pic');?>" class="avatar" alt="Profile Picture" height="40" width="40" />
        <span><strong><?php echo $username ?></strong></span>
    </div>

    <a class="w3-bar-item w3-button w3-padding-24<?php ($index == true) ? $url=' active" href="#"' : $url='" href="./home.php"'; echo $url ?>>Home</a>
    <a class="w3-bar-item w3-button w3-padding-24<?php ($editID == $userID && $edit == 1) ? $url=' active" href="#"' : $url='" href="./edit.php?user='.$userID.'"'; echo $url ?>>Edit Profile</a>
    <?php if($editID != $userID && $edit == 1 && !isset($_GET['playlist']) && !isset($_GET['song'])) { echo '<a class="w3-bar-item w3-button w3-padding-24 active" href="#">Editing '.$editName.'</a>'; }?>
    <?php if( $edit == 1 && isset($_GET['playlist']) && !isset($_GET['song'])) { echo '<a class="w3-bar-item w3-button w3-padding-24 active" href="#">Editing '. viewPlaylist('pname', $_GET['playlist']).'</a>'; }?>
    <?php if( $edit == 1 && !isset($_GET['playlist']) && isset($_GET['song'])) { echo '<a class="w3-bar-item w3-button w3-padding-24 active" href="#">Editing '. viewSong('name', $_GET['song']).'</a>'; }?>

    <a class="w3-bar-item w3-button w3-padding-24<?php ($playlist == true && !isset($_GET['view'])) ? $url=' active" href="#"' : $url='" href="./playlist.php"'; echo $url ?>>Playlists</a>
        <?php if($playlist == 1 && isset($_GET['view'])) { echo '<a class="w3-bar-item w3-button w3-padding-24 active" href="#">Viewing '. viewPlaylist('pname', $_GET['view']).'</a>'; }?>

    <a class="w3-bar-item w3-button w3-padding-24<?php ($music == true) ? $url=' active" href="#"' : $url='" href="./music.php"'; echo $url ?>>Add new playlist</a>
    <?php if ($song == true) echo '<a class="w3-bar-item w3-button w3-padding-24 active" href="#">Add a song</a>';?>
    <?php if (isAdmin($userID))
    {
        $users_header = '<a class="w3-bar-item w3-button w3-padding-24 ';
        if ($users == 1) $users_header .= 'active" href="#">Users</a>';
        else $users_header .= '" href="./users.php">Users</a>';
        $users_header .= '<a class="w3-bar-item w3-button w3-padding-24 ';
        if ($add == 1)  $users_header .= 'active " href="#">Add User</a>';
        else $users_header .= '" href="./add.php">Add User</a>';
        echo $users_header;
    } ?>
<form action="./logout.php" method="POST">
        <button class="w3-bar-item w3-button w3-padding-24">Logout</button>
    </form>
</div>
<?php
}
?>