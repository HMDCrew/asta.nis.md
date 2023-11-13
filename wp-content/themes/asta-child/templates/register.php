<?php
/**
 * Template Name: Register
 */
?>

<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php ASTA_USER::redirect_auth_user( get_site_url() ); ?>
	<?php wp_head(); ?>
</head>
<body>

	<div class="container-fluid">

		<div class="card">

			<div class="register-pic js-tilt">
				<a href="/">
					<img src="/wp-content/uploads/2023/04/logo_dark-1.svg" alt="IMG">
				</a>
			</div>

			<form class="register-form validate-form" action="<?php echo esc_url( site_url( 'wp-login.php?action=register', 'login_post' ) ); ?>" method="POST">
				<span class="register-form-title"><?php echo __( 'Register', 'asta-child' ); ?></span>

				<div class="wrap-input" data-validate="Valid email is required: ex@abc.xyz">
					<input class="input" type="email" name="log" placeholder="<?php echo __( 'Email', 'asta-child' ); ?>">
					<span class="focus-input"></span>
					<span class="symbol-input">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
					</span>
				</div>

				<div class="wrap-input" data-validate="Password is required">
					<input class="input" type="password" name="pwd" placeholder="<?php echo __( 'Password', 'asta-child' ); ?>">
					<span class="focus-input"></span>
					<span class="symbol-input">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/></svg>
					</span>
				</div>

				<div class="wrap-input" data-validate="Password is required">
					<input class="input" type="password" name="repeat_password" placeholder="<?php echo __( 'Repeat password', 'asta-child' ); ?>">
					<span class="focus-input"></span>
					<span class="symbol-input">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/></svg>
					</span>
				</div>

				<div class="container-register-form-btn">
					<button class="btn btn-primary w-100 register-form-btn"><?php echo __( 'Register now!', 'asta-child' ); ?></button>
				</div>

				<div class="registered">
					<a class="link" href="<?php echo site_url( '/login' ); ?>">
						<?php echo __( 'Are you registered?', 'asta-child' ); ?>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l370.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"/></svg>
					</a>
				</div>

				<!-- Is used by js for redirect -->
				<input type="hidden" name="redirect_to" value="<?php echo esc_url( get_site_url() ); ?>">
			</form>
		</div>
	</div>

	<?php wp_footer(); ?>
</body>
</html>
