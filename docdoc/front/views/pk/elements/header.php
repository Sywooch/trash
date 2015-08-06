
<header class="header">
	<div class="header_clinic">
		<?php if ($this->_partner): ?>
			<p class="header_clinic__employees">
				<span class="strong">Здравствуйте, <strong><?php echo $this->_partner->contact_name; ?></strong> !</span>
			</p>
		<?php endif; ?>
	</div>

	<a class="logo" href="/pk/patients">
		<img class="logo_img" src="/i/logo-lk.png" alt="Docdoc.ru" />
	</a>
</header>
