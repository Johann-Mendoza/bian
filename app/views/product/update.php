<?php include APP_DIR . 'views/templates/header.php'; ?>
<body>
    <div id="app">
        <?php include APP_DIR . 'views/templates/nav.php'; ?>

        <main class="mt-3 pt-3">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">Update Product</div>
                            <div class="card-body">
                                <?php if (isset($product)): ?>
                                    <form action="/product/update/<?= $product['id']; ?>" method="POST">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Product Name</label>
                                            <input type="text" name="name" id="name" class="form-control" value="<?= html_escape($product['name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea name="description" id="description" class="form-control" rows="4" required><?= html_escape($product['description']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price</label>
                                            <input type="number" name="price" id="price" class="form-control" value="<?= html_escape($product['price']); ?>" step="0.01" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Product</button>
                                        <a href="/home" class="btn btn-secondary">Cancel</a>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-warning">Product not found.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
