<?php
/**
 * Template Name: Contact
 * Auto-applies to the page with slug "contact".
 *
 * "Site intake" module — a floating, glowing multi-step wizard.
 * Server wiring (admin-post action, nonce, honeypot, lh_* field names) is
 * unchanged, so the Luxury Homes Core contact handler processes it as before.
 */
get_header();

$sent  = isset( $_GET['sent'] );
$error = isset( $_GET['ct_error'] ) ? sanitize_key( $_GET['ct_error'] ) : '';

$prefill_email = isset( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '';
if ( $prefill_email && ! is_email( $prefill_email ) ) { $prefill_email = ''; }

$error_messages = array(
	'fields'   => 'Please answer the required questions, including a valid email and phone.',
	'security' => 'Your session expired. Please try sending again.',
	'send'     => 'Something went wrong sending your message. Please email us directly.',
);
?>

<main id="main" class="si-page">
	<?php while ( have_posts() ) : the_post(); ?>
	<section class="si-wrap">
		<div class="si-layout">
		<div class="si-stage">
			<div class="si-ground" aria-hidden="true"></div>
			<div class="si-card">
				<span class="si-tick si-tick-tl"></span><span class="si-tick si-tick-br"></span>

				<div class="si-head">
					<div class="si-eyebrow"><?php echo esc_html( lh_field( 'contact_eyebrow', 'Begin your site file' ) ); ?></div>
					<h1>Start the <em>conversation</em>.</h1>
					<?php if ( trim( get_the_content() ) ) : ?>
						<div class="si-lede"><?php the_content(); ?></div>
					<?php else : ?>
						<p class="si-lede">Tell us about your land and the way you live. We take on a limited number of homes each year.</p>
					<?php endif; ?>
				</div>

				<div class="si-body" id="form">
					<?php if ( $sent ) :
						$ct_name = sanitize_text_field( wp_unslash( $_GET['ct_name'] ?? '' ) ); ?>
						<div class="si-done" role="status">
							<div class="si-chk"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 12l5 5L20 6"/></svg></div>
							<h2>Thank you<?php echo $ct_name ? ', ' . esc_html( $ct_name ) : ''; ?>.</h2>
							<p>Your site file is open. We'll call within 24 hours to talk through your land and how you live. No scripts, no pressure.</p>
						</div>
					<?php else : ?>

						<?php if ( $error && isset( $error_messages[ $error ] ) ) : ?>
							<div class="si-alert" role="alert"><?php echo esc_html( $error_messages[ $error ] ); ?></div>
						<?php endif; ?>

						<div class="si-meter" aria-hidden="true">
							<div class="si-meter-top">
								<span class="si-idx"><b id="siCur">01</b> <i>/ 08</i></span>
								<span class="si-fieldname" id="siField">Land</span>
							</div>
							<div class="si-track"><span class="si-fill" id="siFill"></span></div>
						</div>

						<form class="si-form" id="siWizard" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate>
							<input type="hidden" name="action" value="lh_contact">
							<?php wp_nonce_field( 'lh_contact', 'lh_contact_nonce' ); ?>
							<p class="si-hp" aria-hidden="true">
								<label for="lh_website">Leave this field empty</label>
								<input type="text" id="lh_website" name="lh_website" tabindex="-1" autocomplete="off">
							</p>

							<div class="si-steps">
								<fieldset class="si-step is-active" data-auto="1" data-field="Land">
									<legend class="si-q">Do you own land for your home?</legend>
									<div class="si-opts">
										<label class="si-opt"><input type="radio" name="lh_land" value="Owns land" data-req="1" hidden><span class="si-opt-label">Yes, I own the parcel</span><span class="si-arw">&rarr;</span></label>
										<label class="si-opt"><input type="radio" name="lh_land" value="Currently buying" hidden><span class="si-opt-label">Currently buying</span><span class="si-arw">&rarr;</span></label>
										<label class="si-opt"><input type="radio" name="lh_land" value="Needs help finding land" hidden><span class="si-opt-label">Need help finding land</span><span class="si-arw">&rarr;</span></label>
									</div>
									<p class="si-err" aria-live="polite"></p>
								</fieldset>

								<fieldset class="si-step" data-auto="1" data-field="Budget">
									<legend class="si-q">What budget range are you considering?</legend>
									<div class="si-opts">
										<label class="si-opt"><input type="radio" name="lh_budget" value="$1M-2M" data-req="1" hidden><span class="si-opt-label">$1M &ndash; $2M</span><span class="si-arw">&rarr;</span></label>
										<label class="si-opt"><input type="radio" name="lh_budget" value="$2M-4M" hidden><span class="si-opt-label">$2M &ndash; $4M</span><span class="si-arw">&rarr;</span></label>
										<label class="si-opt"><input type="radio" name="lh_budget" value="$4M+" hidden><span class="si-opt-label">$4M+</span><span class="si-arw">&rarr;</span></label>
										<label class="si-opt"><input type="radio" name="lh_budget" value="Prefer to discuss" hidden><span class="si-opt-label">Prefer to discuss</span><span class="si-arw">&rarr;</span></label>
									</div>
									<p class="si-err" aria-live="polite"></p>
								</fieldset>

								<fieldset class="si-step" data-auto="1" data-field="Timeline">
									<legend class="si-q">When would you like to start?</legend>
									<div class="si-opts">
										<label class="si-opt"><input type="radio" name="lh_timeline" value="As soon as possible" data-req="1" hidden><span class="si-opt-label">As soon as possible</span><span class="si-arw">&rarr;</span></label>
										<label class="si-opt"><input type="radio" name="lh_timeline" value="3-6 months" hidden><span class="si-opt-label">3 &ndash; 6 months</span><span class="si-arw">&rarr;</span></label>
										<label class="si-opt"><input type="radio" name="lh_timeline" value="6-12 months" hidden><span class="si-opt-label">6 &ndash; 12 months</span><span class="si-arw">&rarr;</span></label>
										<label class="si-opt"><input type="radio" name="lh_timeline" value="Just exploring" hidden><span class="si-opt-label">Just exploring</span><span class="si-arw">&rarr;</span></label>
									</div>
									<p class="si-err" aria-live="polite"></p>
								</fieldset>

								<fieldset class="si-step" data-field="Coordinates">
									<legend class="si-q">What zip code will you build in?</legend>
									<div class="si-inp-wrap">
										<label class="si-lab" for="lh_zip">Build zip</label>
										<div class="si-fieldline"><input class="si-inp" id="lh_zip" name="lh_zip" inputmode="numeric" maxlength="10" data-req="1" autocomplete="postal-code" placeholder="84060"></div>
										<p class="si-hint">So we can confirm your area within the Wasatch Back.</p>
									</div>
									<p class="si-err" aria-live="polite"></p>
								</fieldset>

								<fieldset class="si-step" data-field="Name">
									<legend class="si-q">What is your name?</legend>
									<div class="si-inp-wrap si-two">
										<div><label class="si-lab" for="lh_first">First</label><div class="si-fieldline"><input class="si-inp" id="lh_first" name="lh_first" data-req="1" autocomplete="given-name" placeholder="Jane"></div></div>
										<div><label class="si-lab" for="lh_last">Last</label><div class="si-fieldline"><input class="si-inp" id="lh_last" name="lh_last" data-req="1" autocomplete="family-name" placeholder="Rivera"></div></div>
									</div>
									<p class="si-err" aria-live="polite"></p>
								</fieldset>

								<fieldset class="si-step" data-field="Email">
									<legend class="si-q">Where can we email you?</legend>
									<div class="si-inp-wrap">
										<label class="si-lab" for="lh_email">Email</label>
										<div class="si-fieldline"><input class="si-inp" id="lh_email" name="lh_email" type="email" data-req="1" autocomplete="email" value="<?php echo esc_attr( $prefill_email ); ?>" placeholder="jane@studio.com"></div>
									</div>
									<p class="si-err" aria-live="polite"></p>
								</fieldset>

								<fieldset class="si-step" data-field="Phone">
									<legend class="si-q">Best number to reach you?</legend>
									<div class="si-inp-wrap">
										<label class="si-lab" for="lh_phone">Phone</label>
										<div class="si-fieldline"><input class="si-inp" id="lh_phone" name="lh_phone" type="tel" data-req="1" autocomplete="tel" placeholder="(801) 555-0142"></div>
										<p class="si-subl">Best time to reach you</p>
										<div class="si-opts si-opts-row">
											<label class="si-opt"><input type="radio" name="lh_besttime" value="Morning" hidden><span class="si-opt-label">Morning</span></label>
											<label class="si-opt"><input type="radio" name="lh_besttime" value="Afternoon" hidden><span class="si-opt-label">Afternoon</span></label>
											<label class="si-opt"><input type="radio" name="lh_besttime" value="Evening" hidden><span class="si-opt-label">Evening</span></label>
										</div>
									</div>
									<p class="si-err" aria-live="polite"></p>
								</fieldset>

								<fieldset class="si-step" data-optional="1" data-field="Brief">
									<legend class="si-q">Anything else we should know?</legend>
									<div class="si-inp-wrap">
										<label class="si-lab" for="lh_message">Your note &mdash; optional</label>
										<div class="si-fieldline"><textarea class="si-inp si-textarea" id="lh_message" name="lh_message" rows="3" placeholder="A view we want to frame, a way we live&hellip;"></textarea></div>
									</div>
									<p class="si-err" aria-live="polite"></p>
								</fieldset>
							</div>

							<div class="si-nav">
								<button type="button" class="si-back" id="siBack" hidden>&larr; Back</button>
								<span class="si-autonote" id="siNote"><span class="si-mini"></span>Select an answer to continue</span>
								<button type="button" class="si-next" id="siNext" hidden>Continue &rarr;</button>
								<button type="submit" class="si-next" id="siSend" hidden>Send &rarr;</button>
							</div>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<aside class="si-side">
			<div class="si-detail">
				<h3>Phone</h3>
				<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', lh_field( 'phone', '+18015550100' ) ) ); ?>"><?php echo esc_html( lh_field( 'phone_display', '(801) 555-0100' ) ); ?></a></p>
			</div>
			<div class="si-detail">
				<h3>Email</h3>
				<p><a href="mailto:<?php echo esc_attr( lh_field( 'email', 'hello@example.com' ) ); ?>"><?php echo esc_html( lh_field( 'email', 'hello@example.com' ) ); ?></a></p>
			</div>
			<div class="si-detail">
				<h3>Service area</h3>
				<p><?php echo esc_html( lh_field( 'service_areas', 'Park City, Heber Valley, Salt Lake &amp; the Wasatch Back.' ) ); ?></p>
			</div>
			<div class="si-detail si-detail-note">
				<p>We reply within 24 hours. No pressure and no sales scripts. Just a conversation about your land and how you live.</p>
			</div>
		</aside>
		</div>

		<div class="si-maparea">
			<div class="si-maphead">
				<div class="si-eyebrow">Service area</div>
				<h2>The Wasatch Back.</h2>
				<p>Park City &middot; Heber Valley &middot; Salt Lake &middot; Kamas</p>
				<a class="si-maplink" href="https://www.google.com/maps/search/?api=1&amp;query=Park+City,+Utah" target="_blank" rel="noopener">Open in Google Maps &rarr;</a>
			</div>
			<div class="si-mapframe"><div id="si-map"></div></div>
		</div>
	</section>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>
