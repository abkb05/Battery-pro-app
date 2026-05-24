<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= site_url('/customers') ?>" class="btn btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
        <h4 class="fw-bold mb-0"><i class="fas fa-file-csv me-2 text-primary"></i>Import Customers</h4>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <p class="text-muted">CSV file should have columns: <strong>Name, Phone, Email, Address</strong></p>
            <form method="POST" action="<?= site_url('/customers/import') ?>" enctype="multipart/form-data">
                <div class="mb-3"><input type="file" class="form-control" name="csv_file" accept=".csv" required></div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Import</button>
            </form>
        </div>
    </div>
</div>
