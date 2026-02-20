<h2>Welcome <?= session()->get('firstname'); ?>!</h2>

<p>You are logged in.</p>

<a href="<?= base_url('logout') ?>">Logout</a>