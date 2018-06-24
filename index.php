<?php
require_once 'core/init.php';

//echo Config::get('mysql/username');

//$user = DB::getInstance()->query("SELECT username FROM users WHERE username = ? ", array('alex'));

// using the db helping function ==
//$user = DB::getInstance()->get('users', array('username', '=', 'alex'));

// if(!$user->count()){
//   echo 'No User';
// }else{
//   echo $user->first()->username;
// }
// $user = DB::getInstance()->update('users',1, array(
//   'password' => 'newpassword',
//   'name'     => 'morris'
//
// ));

if(Session::exists('home')){

  echo '<p>' . Session::flash('home') . '</p>';

}

$user = new User(); // current user
if($user->isLoggedIn()){
  ?>

    <p> Hello <a href="profile.php?user=<?php echo escape($user->data()->username);?>"><?php echo escape($user->data()->username);?></a> !</p>

    <ul>
        <li><a href="logout.php">Log out</a></li>
        <li><a href="update.php">Update details</a></li>
        <li><a href="changepassword.php">change password</a></li>
    </ul>

  <?php

  if($user->hasPermission('admin')){

    echo '<p>You are an Admin</p>';
    echo '<p>view users list</p>';

  }

}else{

  echo '<p>You need to <a href="login.php"> log in </a> or <a href="register.php">register</a></p>';
}
