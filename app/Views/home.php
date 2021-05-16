<?php

use App\Extensions\View;

$user = View::getData('user');

?>

<!doctype html>
<html lang="en">
<head>
    <?php View::include('common/meta') ?>
</head>
<body>
<?php View::include('common/header') ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Welcome</h1>
            <p>It's Home page</p>
        </div>
    </div>
</div>
<?php View::include('common/footer') ?>
</body>
</html>