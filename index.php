<?php

/*
 * Server side data
 */

$products = [
    [
        'title' => 'Cola',
        'description' => 'Sweetened, carbonated soft drink, that contain caffeine from the kola nut and derivatives from coca leaves.',
        'imageName' => 'cola.jpg',
        'price' => 8.81
    ],
    [
        'title' => 'Bread',
        'description' => 'Staple food prepared from a dough of flour and water, usually by baking.',
        'imageName' => 'bread.jpg',
        'price' => 7.95
    ],
    [
        'title' => 'Nuts',
        'description' => 'A fruit composed of a hard shell and a seed, which is generally edible.',
        'imageName' => 'nuts.jpg',
        'price' => 5.51
    ]
];

/*
 * Cookie storing related functions
 */

/**
 * Update whole user cart store with new items
 *
 * @param array $cartItems new items list
 */
function updateCartStore(array $cartItems)
{
    // remove all current items
    foreach (getCartItems() as $itemNum => $item) {
        updateCartStoreRecord($itemNum, $item, time()-1);
    }

    // add existing
    foreach ($cartItems as $itemNum => $item) {
        updateCartStoreRecord($itemNum, $item);
    }
}

/**
 * Update one record in store
 *
 * @param $itemNum
 * @param $item
 * @param int $expire
 */
function updateCartStoreRecord($itemNum, $item, $expire = 0)
{
    foreach ($item as $field => $value) {
        setcookie("cart[$itemNum][$field]", $value, $expire);
    }
}

/**
 * Get cart items data
 *
 * @return array
 */
function getCartItems() : array
{
    static $cart;

    if (!isset($cart)) {
        $cart = isset($_COOKIE['cart']) ? $_COOKIE['cart'] : [];
        ksort($cart);

    }

    return $cart;
}

/*
 * Functions for working with data model
 */

/**
 * Updates cart item list
 *
 * @param array $cartItems new items list
 */
function setCartItems(array $cartItems)
{
    updateCartStore($cartItems);
    $_COOKIE['cart'] = $cartItems;
}

/**
 * @param $id
 * @return int|string
 */
function searchCartItem($id)
{
    foreach (getCartItems() as $key => $item) {
        if ($item['id'] == $id) {
            return $key;
            break;
        }
    }
    return -1;
}

/**
 * Add item to cart
 *
 * @param array $item new item
 */
function addCartItem(array $item)
{
    $cart = getCartItems();
    $cart[] = $item;
    setCartItems($cart);
}

/**
 * Increments cart item products count
 *
 * @param int $itemNum item position in list
 * @param int $incCount number of items to add
 */
function incrementCartItemCount(int $itemNum, int $incCount)
{
    $cart = $_COOKIE['cart'];
    $cart[$itemNum]['count'] += $incCount;
    setCartItems($cart);
}

/**
 * Removes cart item from cart
 *
 * @param $itemNum
 */
function removeCartItem($itemNum)
{
    $cart = $_COOKIE['cart'];
    unset($cart[$itemNum]);
    var_dump($cart, $itemNum);
    setCartItems($cart);
}

/**
 * Calculates cart total price of products
 *
 * @return float
 */
function calculateCartTotalPrice() : float
{
    global $products;

    $total = 0;

    foreach (getCartItems() as $item) {
        $total += $item['count'] * $products[$item['id']]['price'];
    }

    return $total;
}

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

/*
 *
 */

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
<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Basic Page Needs
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <meta charset="utf-8">
    <title>Nano Market</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Mobile Specific Metas
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- FONT
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">

    <!-- CSS
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/skeleton.css">
    <link rel="stylesheet" href="css/styles.css">

    <!-- Favicon
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link rel="icon" type="image/png" href="images/favicon.png">

</head>
<body>

<!-- Primary Page Layout
–––––––––––––––––––––––––––––––––––––––––––––––––– -->
<div class="container">
    <h1 class="page-title">Nano Market</h1>
    <div class="row">
        <div class="two-thirds column">
            <table class="u-full-width">
                <thead>
                <tr>
                    <th>Image</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $id => $product) :?>
                <tr>
                    <td><img src="images/<?= $product['imageName'] ?>" class="item-image" /></td>
                    <td><h5><?= $product['title'] ?></h5>
                        <?= $product['description'] ?><br/>
                        <br/>
                        <form method="POST"
                              action="index.php?action=add"
                              class="add-to-cart-form">
                            <input type="hidden" name="addIdInput" value="<?= $id ?>" >
                            <input class="button" type="submit" value="Buy">
                            <input type="text" class="count-input"
                                   name="addCountInput"
                                   placeholder="1"
                            />
                            item(s) for <strong><?= $product['price'] ?>&nbsp;₴</strong>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="one-third column">
            <?php if (empty(getCartItems())) : ?>

            <?php else: ?>
                <table class="u-full-width">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Count</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach (getCartItems() as $item) :?>
                        <tr>
                            <td><?= $products[$item['id']]['title'] ?></td>
                            <td class="price-cell"><?= $products[$item['id']]['price'] ?>&nbsp;₴<br/></td>
                            <td>
                                <input type="text" class="count-input"
                                       disabled="disabled"
                                       value="<?= $item['count'] ?>"
                                />
                                <a class="remove"
                                   href="index.php?action=remove&id=<?= $item['id'] ?>"
                                   title="Remove">✕</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td>Total:</td>
                        <td class="price-cell"><?= calculateCartTotalPrice() ?>&nbsp;₴</td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <footer>© Good Seller Inc.</footer>
</div>

<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
</body>
</html>
