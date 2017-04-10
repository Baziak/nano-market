<?php

$products = include 'data/products.php';
require_once 'app/actions.php';

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'add':
            doAdd();
            break;
        case 'remove':
            doRemove();
            break;
    }
}

?>
<?php include 'tpl/header.phtml' ?>
    <div class="row">
        <div class="two-thirds column">
            <?php include 'tpl/products.phtml' ?>
        </div>
        <div class="one-third column">
            <?php include 'tpl/cart.phtml' ?>
        </div>
    </div>
<?php include 'tpl/footer.phtml' ?>
