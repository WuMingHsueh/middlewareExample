<h1>
	<?= $this->escape($this->title) ?>
</h1>
<ul>
	<?php foreach ($this->dataPool as $name => $value) : ?>
		<li>
			<?= $this->escape($value) ?>
		</li>
	<?php endforeach; ?>
</ul>
<form action="<?= $this->routerRoot ?>/user/auth/sign-in" method="post">
	<label>
		Email:
		<input type="text" name="email" placeholder="Email" value="<?= $this->escape($this->email); ?>">
	</label>
	<label>
		Password:
		<input type="password" name="password" placeholder="Password">
	</label>
	<button>登入</button>
</form>
