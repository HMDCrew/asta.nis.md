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


$picture = ASTA_USER::get_picture_profile( $user->ID );

// var_dump( isset( $_GET['seller'] ) );
?>

	<main id="primary" class="site-main">
		<div class="container">

			<?php // ASTA_USER::asta_current_user_in_roles_list( is_user_logged_in(), array( 'vendor', 'administrator' ) ) ?>
			<?php if ( ! ASTA_USER::asta_current_user_in_roles_list( is_user_logged_in(), array( 'vendor' ) ) ) : ?>
			<div class="vendor-container">
				<div class="vendor">
					<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000" height="800px" width="800px" version="1.2" baseProfile="tiny" id="Layer_1" viewBox="-351 153 256 256" xml:space="preserve">
						<path id="group" d="M-276.9,265.2h19v14.9h-19V265.2z M-214,253.6h79.5v9.6H-214V253.6z M-214,295.8h79.5v9.6H-214V295.8z   M-164.6,251.4c6.1,0,11.1-5.1,11.1-11.3s-5-11.3-11.1-11.3s-11.1,5.1-11.1,11.3S-170.7,251.4-164.6,251.4z M-184.4,250.6  c3.8,0,6.8-3.1,6.8-6.9c0-3.9-3-6.9-6.8-6.9c-3.8,0-6.8,3.1-6.8,6.9C-191.2,247.6-188.2,250.6-184.4,250.6z M-198.3,251.3  c2.5,0,4.4-2,4.4-4.5c0-2.5-1.9-4.5-4.4-4.5s-4.4,2-4.4,4.5C-202.7,249.2-200.7,251.3-198.3,251.3z M-181.4,277.5h28v14.7h-28V277.5  z M-198.2,277.5h8.7v14.7h-8.7V277.5z M-267.1,227.5c9.5,0,17.1,7.7,17.1,17.1s-7.7,17.1-17.1,17.1s-17.1-7.7-17.1-17.1  S-276.6,227.5-267.1,227.5z M-293,291c0-1.3,1.1-2,2-2c1.3,0,2,0.9,2,2v24.7h8.5v-35.6v-14.9h-6.7c-12,0-19.5,9.8-19.5,22.2v28.3  h13.4V291H-293z M-221.8,154.4L-341,179.8v13.5l8.7-0.1v0.1l0,0v213.5h216v-85.7V193.3h8.7v-13.9L-221.8,154.4z M-130.7,321.1  h-186.9V193.3h186.9V321.1z M-254.5,280.1v35.6h8.7V291c0-1.3,1.1-2,2-2c1.3,0,2,1.1,2,2v24.7h13.4v-28c0.2-12.5-7.3-22.4-19.5-22.4  h-6.8v14.9H-254.5z"/>
					</svg>
					<h3><?php echo __( 'Become a seller with Asta', 'asta-child' ); ?></h3>
					<p><?php echo __( 'To become a seller the platform requires further details in addition to identity documents and photos to certify its authenticity', 'asta-child' ); ?></p>
					<button type="button" class="btn btn-primary-outer ask-for-vendor"><?php echo __( 'Ask for vendor', 'asta-child' ); ?></button>
				</div>
			</div>
			<?php endif; ?>

			<div class="user-profile">

				<div class="user-picture">
					<div class="user-image">
						<img src="<?php echo $picture['url']; ?>" alt="Profile Picture" <?php echo ( $picture['is_placeholder'] ? 'placeholder' : '' ); ?>>
					</div>
					<input type="file" name="profile-picture" class="profile-picture" />
				</div>

				<?php if ( ASTA_USER::asta_current_user_in_roles_list( is_user_logged_in(), array( 'vendor', 'administrator' ) ) ) : ?>
				<div class="row balance">
					<div class="total_balance">
						<div class="label"><?php echo __( 'Your profits', 'asta-child' ); ?></div>

						<span class="balance-container">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M160 0c17.7 0 32 14.3 32 32V67.7c1.6 .2 3.1 .4 4.7 .7c.4 .1 .7 .1 1.1 .2l48 8.8c17.4 3.2 28.9 19.9 25.7 37.2s-19.9 28.9-37.2 25.7l-47.5-8.7c-31.3-4.6-58.9-1.5-78.3 6.2s-27.2 18.3-29 28.1c-2 10.7-.5 16.7 1.2 20.4c1.8 3.9 5.5 8.3 12.8 13.2c16.3 10.7 41.3 17.7 73.7 26.3l2.9 .8c28.6 7.6 63.6 16.8 89.6 33.8c14.2 9.3 27.6 21.9 35.9 39.5c8.5 17.9 10.3 37.9 6.4 59.2c-6.9 38-33.1 63.4-65.6 76.7c-13.7 5.6-28.6 9.2-44.4 11V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V445.1c-.4-.1-.9-.1-1.3-.2l-.2 0 0 0c-24.4-3.8-64.5-14.3-91.5-26.3c-16.1-7.2-23.4-26.1-16.2-42.2s26.1-23.4 42.2-16.2c20.9 9.3 55.3 18.5 75.2 21.6c31.9 4.7 58.2 2 76-5.3c16.9-6.9 24.6-16.9 26.8-28.9c1.9-10.6 .4-16.7-1.3-20.4c-1.9-4-5.6-8.4-13-13.3c-16.4-10.7-41.5-17.7-74-26.3l-2.8-.7 0 0C119.4 279.3 84.4 270 58.4 253c-14.2-9.3-27.5-22-35.8-39.6c-8.4-17.9-10.1-37.9-6.1-59.2C23.7 116 52.3 91.2 84.8 78.3c13.3-5.3 27.9-8.9 43.2-11V32c0-17.7 14.3-32 32-32z"></path></svg>
							<?php echo ASTA_USER::get_user_balance( get_current_user_id() ); ?>
						</span>

						<?php $money_looked = ASTA_USER::get_user_looked_balance( get_current_user_id() ); ?>
						<?php if ( $money_looked > 0 ) : ?>
							<div class="money-looked">
								<?php echo __( 'looked for suspect transation', 'asta-child' ); ?>: <?php echo $money_looked; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>

				<div class="row">
					<div class="col-6">
						<div class="wrap-input">
							<input class="input full-name" type="text" name="full-name" placeholder="<?php echo __( 'Full Name', 'asta-child' ); ?>" value="<?php echo $user->user_email !== $user->display_name ? esc_html( $user->display_name ) : ''; ?>">
							<span class="focus-input"></span>
							<span class="symbol-input">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
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

				<?php if ( ASTA_USER::asta_current_user_in_roles_list( is_user_logged_in(), array( 'vendor', 'administrator' ) ) ) : ?>
				<div class="row container-iban">
					<?php $iban = ASTA_USER::get_user_iban( get_current_user_id() ); ?>
					<div class="col-6">
						<div class="wrap-input">
							<input class="input iban" type="text" name="iban" placeholder="<?php echo __( 'IBAN', 'asta-child' ); ?>" value="<?php echo ! empty( $iban['iban'] ) ? partial_hider( $iban['iban'], 2, 4 ) : ''; ?>">
							<span class="focus-input"></span>
							<span class="symbol-input">
								<svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.052 1.25H11.948C11.0495 1.24997 10.3003 1.24995 9.70552 1.32991C9.07773 1.41432 8.51093 1.59999 8.05546 2.05546C7.59999 2.51093 7.41432 3.07773 7.32991 3.70552C7.27259 4.13189 7.25637 5.15147 7.25179 6.02566C5.22954 6.09171 4.01536 6.32778 3.17157 7.17157C2 8.34315 2 10.2288 2 14C2 17.7712 2 19.6569 3.17157 20.8284C4.34314 22 6.22876 22 9.99998 22H14C17.7712 22 19.6569 22 20.8284 20.8284C22 19.6569 22 17.7712 22 14C22 10.2288 22 8.34315 20.8284 7.17157C19.9846 6.32778 18.7705 6.09171 16.7482 6.02566C16.7436 5.15147 16.7274 4.13189 16.6701 3.70552C16.5857 3.07773 16.4 2.51093 15.9445 2.05546C15.4891 1.59999 14.9223 1.41432 14.2945 1.32991C13.6997 1.24995 12.9505 1.24997 12.052 1.25ZM15.2479 6.00188C15.2434 5.15523 15.229 4.24407 15.1835 3.9054C15.1214 3.44393 15.0142 3.24644 14.8839 3.11612C14.7536 2.9858 14.5561 2.87858 14.0946 2.81654C13.6116 2.7516 12.964 2.75 12 2.75C11.036 2.75 10.3884 2.7516 9.90539 2.81654C9.44393 2.87858 9.24644 2.9858 9.11612 3.11612C8.9858 3.24644 8.87858 3.44393 8.81654 3.9054C8.771 4.24407 8.75661 5.15523 8.75208 6.00188C9.1435 6 9.55885 6 10 6H14C14.4412 6 14.8565 6 15.2479 6.00188ZM12 9.25C12.4142 9.25 12.75 9.58579 12.75 10V10.0102C13.8388 10.2845 14.75 11.143 14.75 12.3333C14.75 12.7475 14.4142 13.0833 14 13.0833C13.5858 13.0833 13.25 12.7475 13.25 12.3333C13.25 11.9493 12.8242 11.4167 12 11.4167C11.1758 11.4167 10.75 11.9493 10.75 12.3333C10.75 12.7174 11.1758 13.25 12 13.25C13.3849 13.25 14.75 14.2098 14.75 15.6667C14.75 16.857 13.8388 17.7155 12.75 17.9898V18C12.75 18.4142 12.4142 18.75 12 18.75C11.5858 18.75 11.25 18.4142 11.25 18V17.9898C10.1612 17.7155 9.25 16.857 9.25 15.6667C9.25 15.2525 9.58579 14.9167 10 14.9167C10.4142 14.9167 10.75 15.2525 10.75 15.6667C10.75 16.0507 11.1758 16.5833 12 16.5833C12.8242 16.5833 13.25 16.0507 13.25 15.6667C13.25 15.2826 12.8242 14.75 12 14.75C10.6151 14.75 9.25 13.7903 9.25 12.3333C9.25 11.143 10.1612 10.2845 11.25 10.0102V10C11.25 9.58579 11.5858 9.25 12 9.25Z" fill="#000000"/></svg>
							</span>
						</div>
					</div>
					<div class="col-6">
						<div class="wrap-input">
							<input class="input bic" type="text" name="bic" placeholder="<?php echo __( 'BIC', 'asta-child' ); ?>" value="<?php echo ! empty( $iban['bic'] ) ? partial_hider( $iban['bic'], 0, 3 ) : ''; ?>">
						</div>
					</div>
				</div>
				<?php endif; ?>

				<div class="row credit-cards-row">
					<div class="col-12">

						<h5><?php echo __( 'Credit cards', 'asta-child' ); ?></h5>

						<?php if ( ASTA_USER::asta_user_is_aproved() ) : ?>
							<?php do_action( 'asta_user_credit_cards', array( 'user_id' => $user->ID ) ); ?>
						<?php else : ?>
							<div class="no-permicess">
								<?php echo __( 'for add you credit cards you need validate your email adress', 'asta-child' ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<button type="submit" class="btn btn-primary update-profile"><?php echo __( 'Update profile', 'asta-child' ); ?></button>
			</div>

		</div>
	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
