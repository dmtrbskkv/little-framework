<?php

use App\Extensions\View;

$user = View::getData('user');
$user_is_admin = $user['is_admin'] ?? false;
?>
