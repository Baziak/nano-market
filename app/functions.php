<?php

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
