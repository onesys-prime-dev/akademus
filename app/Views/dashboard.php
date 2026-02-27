<h1>THIs THE DASHBOARD</h1>
<h2>Welcome <?= session()->get('firstname'); ?>!</h2>

<p>You are logged in.</p>

<form action="<?= base_url('logout') ?>" method="post">
    <button type="submit">Logout</button>
</form>