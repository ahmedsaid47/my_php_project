<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar-sticky {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: 1rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .nav-link {
            color: white;
        }
        .nav-link.active {
            background-color: #495057;
            color: white;
        }
        .nav-link:hover {
            background-color: #495057;
            color: white;
        }
        .sidebar-heading {
            font-size: 1.2rem;
            text-transform: uppercase;
            padding: 0.5rem 1rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <nav class="col-md-2 d-none d-md-block bg-dark sidebar">
        <div class="sidebar-sticky">
            <div class="sidebar-heading">Admin Panel</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php?page=cart_items">
                        <span data-feather="shopping-cart"></span>
                        Cart Items
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=carts">
                        <span data-feather="shopping-bag"></span>
                        Carts
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=products">
                        <span data-feather="box"></span>
                        Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=promotions">
                        <span data-feather="tag"></span>
                        Promotions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=services">
                        <span data-feather="briefcase"></span>
                        Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=subscriptions">
                        <span data-feather="mail"></span>
                        Subscriptions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=testimonials">
                        <span data-feather="star"></span>
                        Testimonials
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=users">
                        <span data-feather="users"></span>
                        Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=reviews">
                        <span data-feather="message-square"></span>
                        Reviews
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        feather.replace()
    </script>
</body>
</html>
