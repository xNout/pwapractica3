<?php
session_start();

$products = [
    ['id' => 1, 'name' => 'Aceite Cocinero', 'description' => 'Hecho a partir de soya', 'price' => 2.75, 'image' => 'https://th.bing.com/th/id/R.bcbc5b52da1ce5bfc772dda33fd03893?rik=FpAk0eRyE6R7%2bA&pid=ImgRaw&r=0'],
    ['id' => 2, 'name' => 'Jabón Macho', 'description' => 'Barra de detergente poder multiuso para cocina', 'price' => 0.49, 'image' => 'https://th.bing.com/th/id/R.cd74c225a3dffa7cc9c87ae79fe14439?rik=TL2FpJB0IKgaLA&pid=ImgRaw&r=0'],
    // Agregar más productos aquí
];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['total'])) {
    $_SESSION['total'] = 0;
}

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'addToCart') {
        $productId = $_POST['productId'];
        $product = getProductById($productId);

        if ($product) {
            addToCart($product);
        }
    }
}

function getProductById($productId)
{
    global $products;
    foreach ($products as $product) {
        if ($product['id'] == $productId) {
            return $product;
        }
    }
    return null;
}

function addToCart($product)
{
    global $_SESSION;
    $productId = $product['id'];

    $productInCart = array_filter($_SESSION['cart'], function ($item) use ($productId) {
        return $item['id'] == $productId;
    });

    if ($productInCart) {
        // Si el producto ya está en el carrito, aumentar la cantidad en lugar de agregarlo nuevamente
        $_SESSION['cart'][$productId]['quantity']++;
    } else {
        // Si es la primera vez que se agrega, inicializar la cantidad en 1
        $_SESSION['cart'][$productId] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => 1,
        ];
    }

    $_SESSION['total'] += $product['price'];
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Tienda en línea</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        .product-list {
            display: flex;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .product {
            width: 300px;
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
        }

        .cart {
            border: 1px solid #ccc;
            padding: 20px;
        }

        /* Estilos para las imágenes de los productos */
        .product img {
            max-width: 100%;
            height: auto;
        }

        .container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
    </style>
</head>
<body>
    <h1>Tienda en línea</h1>
    <div class="container">
        <div>
            <!-- Lista de productos (generados dinámicamente) -->
            <div class="product-list" id="product-list">
                <?php
                foreach ($products as $product) {
                    echo '<div class="product">';
                    echo '<img src="' . $product['image'] . '" alt="' . $product['name'] . '">';
                    echo '<h2>' . $product['name'] . '</h2>';
                    echo '<p>' . $product['description'] . '</p>';
                    echo '<p>Precio: $' . number_format($product['price'], 2) . '</p>';
                    echo '<form method="post" action="">';
                    echo '<input type="hidden" name="action" value="addToCart">';
                    echo '<input type="hidden" name="productId" value="' . $product['id'] . '">';
                    echo '<button type="submit" class="btn btn-primary">Agregar al carrito</button>';
                    echo '</form>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <div>
            <!-- Carrito de compras -->
            <div class="cart">
                <h2>Carrito de compras</h2>
                <ul id="cart-items">
                    <?php
                    foreach ($_SESSION['cart'] as $item) {
                        echo '<li>' . $item['name'] . ' - Cantidad: ' . $item['quantity'] . ' - $' . number_format($item['price'] * $item['quantity'], 2) . '</li>';
                    }
                    ?>
                </ul>
                <p>Total: <span id="cart-total">$<?php echo number_format($_SESSION['total'], 2); ?></span></p>
            </div>
        </div>
    </div>
</body>
</html>
