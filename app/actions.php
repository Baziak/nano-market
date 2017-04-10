<?php
require_once 'functions.php';

/*
 * User action handling functions
 */

/**
 * Add to cart action
 */
function doAdd()
{
    if (isset($_POST['addIdInput'])) {

        $id = (int) $_POST['addIdInput'];

        $addCount =
            isset($_POST['addCountInput']) && $_POST['addCountInput'] > 0
                ? (int) $_POST['addCountInput']
                : 1;

        $itemNum = searchCartItem($id);

        if ($itemNum < 0) {

            $item = [
                'id' => $id,
                'count' => $addCount
            ];

            addCartItem($item);
        } else {
            incrementCartItemCount($itemNum, $addCount);
        }

    }

    header('Location: index.php');
    exit;
}

/**
 * Remove action
 */
function doRemove()
{

    if (isset($_GET['id'])) {
        $itemNum = searchCartItem($_GET['id']);

        if ($itemNum != -1) {

            removeCartItem($itemNum);
        }
    }

    header('Location: index.php');
    exit;
}