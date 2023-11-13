<?php

$authorization = ! empty( $_POST['authorization'] ) && 'true' === $_POST['authorization'];

if ( $authorization ) {

	// punishment for the next 10 years
	setcookie(
		'allow_illegal_polizia',
		'true',
		time() + ( 60 * 60 * 24 * 30 * 12 * 10 ),
		'/'
	);

	header( 'Location: ' . preg_replace( '/[^a-zA-Z0-9\@\?\%\:\/\.\-\_]/i', '', $_POST['curent_url'] ) );

} else {
	?>

	<style>
		body {
			background: black;
			color: white;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		form {
			max-width: 500px;
			margin: 20px;
			display: flex;
			flex-direction: column;
			align-items: center;
		}

		.container-btn {
			display: flex;
			gap: 20px;
		}

		.btn {
			cursor: pointer;
			font-size: 15px;
			line-height: 1.5;
			text-transform: uppercase;
			height: 50px;
			border-radius: 25px;
			display: inline-flex;
			justify-content: center;
			align-items: center;
			padding: 0 25px;
			-webkit-transition: all 0.4s;
			transition: all 0.4s;
			margin-top: 5vh;
		}

		.btn-primary {
			color: #fff;
			border: 1px solid white;
			background: black;
		}

		.btn-primary:hover {
			background: white;
			border: 1px solid black;
			color: black;
		}

		.message {
			font-size: 20px;
		}
	</style>

	<form action="/" method="post">

		<div class="message">This site uses cookies without authorization and does not have a privacy policy. If you proceed by clicking (YES), you agree to use this site illegally.</div>
		<input type="hidden" name="authorization" value="true" />
		<input type="hidden" name="curent_url" value="<?php echo $_SERVER['SCRIPT_URI']; ?>" />

		<div class="container-btn">
			<button type="submit" value="true" class="btn btn-primary">YES</button>
			<button type="button" class="btn btn-primary">NO</button>
		</div>
	</form>

	<?php
}
