<?php
/**
 * Template Name: Profile
 *
 * This is the template that displays profile page.
 *
 * @package Asta
 */

ASTA_USER::redirect_not_logged_user(
	site_url( '/login' )
);

get_header();

$user = wp_get_current_user();
?>

	<main id="primary" class="site-main">
		<div class="container">

			<div class="user-profile">

				<div class="user-picture">
					<div class="user-image">
						<img src="<?php echo ASTA_USER::get_picture_profile( $user->ID ); ?>" alt="Profile Picture">
					</div>
					<input type="file" name="profile-picture" class="profile-picture" />
				</div>

				<div class="row">
					<div class="col-6">
						<div class="wrap-input">
							<input class="input first-name" type="text" name="first-name" placeholder="<?php echo __( 'First Name', 'asta-child' ); ?>" value="<?php echo esc_html( $user->first_name ); ?>">
							<span class="focus-input"></span>
							<span class="symbol-input">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
							</span>
						</div>
					</div>
					<div class="col-6">
						<div class="wrap-input">
							<input class="input last-name" type="text" name="last-name" placeholder="<?php echo __( 'Last Name', 'asta-child' ); ?>" value="<?php echo esc_html( $user->last_name ); ?>">
							<span class="focus-input"></span>
							<span class="symbol-input">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
							</span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						<div class="wrap-input">
							<input class="input website" type="text" name="website" placeholder="<?php echo __( 'Web site', 'asta-child' ); ?>" value="<?php echo esc_html( $user->user_url ); ?>">
							<span class="focus-input"></span>
							<span class="symbol-input">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M352 256c0 22.2-1.2 43.6-3.3 64H163.3c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64H348.7c2.2 20.4 3.3 41.8 3.3 64zm28.8-64H503.9c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64H380.8c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32H376.7c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0H167.7c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0H18.6C48.6 85.9 112.2 29.1 190.6 8.4C165.1 42.6 145.3 96.1 135.3 160zM8.1 192H131.2c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64H8.1C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26-20.9-58.2-27-94.6H344.3c-6.1 36.4-15.5 68.6-27 94.6c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512s-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352H135.3zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6c25.5-34.2 45.2-87.7 55.3-151.6H493.4z"/></svg>
							</span>
						</div>
					</div>
					<div class="col-6">
						<div class="wrap-input">
							<input class="input email" type="email" name="email" placeholder="<?php echo __( 'E-mail', 'asta-child' ); ?>" value="<?php echo esc_html( $user->user_email ); ?>">
							<span class="focus-input"></span>
							<span class="symbol-input">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
							</span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-6">
						<div class="wrap-input">
							<input class="input password" type="password" name="password" placeholder="<?php echo __( 'Password', 'asta-child' ); ?>" value="password">
							<span class="focus-input"></span>
							<span class="symbol-input">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/></svg>
							</span>
						</div>
					</div>
					<div class="col-6">
						<div class="wrap-input">
							<input class="input repeat-password" type="password" name="repeat-password" placeholder="<?php echo __( 'Repeat password', 'asta-child' ); ?>" value="password">
							<span class="focus-input"></span>
							<span class="symbol-input">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/></svg>
							</span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12">
						<?php do_action( 'asta_user_credit_cards', array( 'user_id' => $user->ID ) ); ?>
					</div>
				</div>

				<div class="row">
					<div class="col-12">
						<div class="wrap-input">
							<textarea class="input description" name="description" placeholder="<?php echo __( 'Description', 'asta-child' ); ?>"><?php echo esc_html( $user->description ); ?></textarea>
							<span class="focus-input"></span>
							<span class="symbol-input">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm80 256h64c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zm256-32H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H496c8.8 0 16 7.2 16 16s-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>
							</span>
						</div>
					</div>
				</div>

				<button type="submit" class="btn btn-primary update-profile"><?php echo __( 'Update profile', 'asta-child' ); ?></button>
			</div>

		</div>
	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
