<?php include APP_DIR . 'views/templates/header.php'; ?>
<body>
    <div id="app">
        <?php include APP_DIR . 'views/templates/nav.php'; ?>

        <main class="mt-3 pt-3">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Products</div>
                            <div class="card-body">
                                <a href="/product/add" class="btn btn-primary mb-3">Add Product</a>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Price</th>
                                            <th>Action</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($products)): ?>
                                            <?php foreach ($products as $product): ?>
                                                <tr>
                                                    <td><?= html_escape($product['id']); ?></td>
                                                    <td><?= html_escape($product['name']); ?></td>
                                                    <td><?= html_escape($product['description']); ?></td>
                                                    <td><?= html_escape($product['price']); ?></td>
                                                    <td>
                                                       
                                                        <a href="/product/update/<?= $product['id']; ?>" class="btn btn-warning">Update</a>
                                                        <a href="<?= site_url('product/delete/'.$product['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No products available.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
</body>
</html>
