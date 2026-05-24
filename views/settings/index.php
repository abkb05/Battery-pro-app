<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4"><i class="fas fa-cog me-2 text-primary"></i>Settings</h4>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="<?= site_url('/settings') ?>">
                <h5 class="fw-bold mb-3"><i class="fas fa-store me-2 text-primary"></i>Shop Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Shop Name</label>
                        <input type="text" class="form-control" name="shop_name" value="<?= htmlspecialchars($settings['shop_name'] ?? 'BatteryPro') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="shop_phone" value="<?= htmlspecialchars($settings['shop_phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="shop_email" value="<?= htmlspecialchars($settings['shop_email'] ?? '') ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="shop_address" rows="2"><?= htmlspecialchars($settings['shop_address'] ?? '') ?></textarea>
                    </div>
                </div>

                <hr>
                <h5 class="fw-bold mb-3"><i class="fas fa-globe me-2 text-primary"></i>Regional Settings</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Currency</label>
                        <input type="text" class="form-control" name="currency" value="<?= htmlspecialchars($settings['currency'] ?? 'USD') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Symbol</label>
                        <input type="text" class="form-control" name="currency_symbol" value="<?= htmlspecialchars($settings['currency_symbol'] ?? '$') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Timezone</label>
                        <select class="form-select" name="timezone">
                            <?php $tz = $settings['timezone'] ?? 'UTC'; ?>
                            <option value="UTC" <?= $tz === 'UTC' ? 'selected' : '' ?>>UTC</option>
                            <option value="America/New_York" <?= $tz === 'America/New_York' ? 'selected' : '' ?>>Eastern</option>
                            <option value="America/Chicago" <?= $tz === 'America/Chicago' ? 'selected' : '' ?>>Central</option>
                            <option value="America/Denver" <?= $tz === 'America/Denver' ? 'selected' : '' ?>>Mountain</option>
                            <option value="America/Los_Angeles" <?= $tz === 'America/Los_Angeles' ? 'selected' : '' ?>>Pacific</option>
                            <option value="Asia/Karachi" <?= $tz === 'Asia/Karachi' ? 'selected' : '' ?>>Karachi</option>
                            <option value="Asia/Dubai" <?= $tz === 'Asia/Dubai' ? 'selected' : '' ?>>Dubai</option>
                            <option value="Asia/Kolkata" <?= $tz === 'Asia/Kolkata' ? 'selected' : '' ?>>India</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Theme</label>
                        <select class="form-select" name="theme">
                            <option value="light" <?= ($settings['theme'] ?? 'light') === 'light' ? 'selected' : '' ?>>Light</option>
                            <option value="dark" <?= ($settings['theme'] ?? 'light') === 'dark' ? 'selected' : '' ?>>Dark</option>
                        </select>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-2"></i>Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
