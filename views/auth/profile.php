<div class="container-fluid py-4">
    <div class="profile-header d-flex align-items-center">
        <div class="profile-avatar me-3">
            <i class="fas fa-user-circle fa-3x"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-1"><?= htmlspecialchars($user['full_name'] ?? 'User') ?></h4>
            <span class="badge">
                <i class="fas fa-<?= ($user['role'] ?? 'staff') === 'admin' ? 'shield-alt' : 'user' ?> me-1"></i>
                <?= ucfirst(htmlspecialchars($user['role'] ?? 'staff')) ?>
            </span>
        </div>
    </div>

    <div class="card profile-card">
        <div class="card-header">
            <i class="fas fa-edit me-2"></i>Edit Profile
        </div>
        <div class="card-body">
            <?php
            $flash = get_flash_message();
            foreach ($flash as $type => $messages):
                foreach ($messages as $msg):
            ?>
            <div class="alert alert-<?= htmlspecialchars($type) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php
                endforeach;
            endforeach;
            ?>

            <form method="POST" action="<?= site_url('/profile') ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Phone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <hr>
                <div class="profile-section-title">
                    <i class="fas fa-key"></i> Change Password
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Current Password</label>
                        <input type="password" class="form-control" name="current_password" placeholder="Leave blank to keep current">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">New Password</label>
                        <input type="password" class="form-control" name="new_password" placeholder="Min 6 characters">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Confirm Password</label>
                        <input type="password" class="form-control" name="confirm_password" placeholder="Repeat new password">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
