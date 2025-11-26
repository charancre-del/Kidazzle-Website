<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-brand-cream text-brand-ink antialiased selection:bg-chroma-red selection:text-white' ); ?>>
<?php wp_body_open(); ?>

<!-- Skip Links for Accessibility -->
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-chroma-blue focus:text-white focus:rounded focus:shadow-lg">
	Skip to main content
</a>

<header class="sticky top-0 z-40 bg-white/85 backdrop-blur-xl border-b border-chroma-blue/10">
	<div class="max-w-7xl mx-auto px-4 lg:px-6 h-[82px] flex items-center justify-between">
		<!-- Logo -->
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3 group">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/chroma-logo.png' ); ?>"
			     srcset="<?php echo esc_url( get_template_directory_uri() . '/assets/images/chroma-logo.png' ); ?> 1x,
			             <?php echo esc_url( get_template_directory_uri() . '/assets/images/chroma-logo-highres.png' ); ?> 2x"
			     alt="Chroma Early Learning"
			     class="h-12 w-auto" />
			<?php
			// Parse header text lines
			$header_text = get_theme_mod( 'chroma_header_text', "Early Learning\nAcademy" );
			$all_lines = array_map( 'trim', explode( "\n", $header_text ) );

			// Check if first line is empty
			$first_line_empty = empty( $all_lines[0] );

			if ( $first_line_empty ) {
				// First line is empty, so all lines use "line 2" formatting
				$line1 = '';
				$line2_array = array_filter( $all_lines ); // Remove all empty lines
			} else {
				// First line has content, use it as line 1
				$line1 = $all_lines[0];
				// Remove first element and filter empty lines for line 2
				array_shift( $all_lines );
				$line2_array = array_filter( $all_lines );
			}
			?>
			<?php if ( $line1 || ! empty( $line2_array ) ) : ?>
			<div class="leading-tight">
				<?php if ( $line1 ) : ?>
					<p class="font-bold text-lg text-brand-ink"><?php echo esc_html( $line1 ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $line2_array ) ) : ?>
					<?php foreach ( $line2_array as $line2 ) : ?>
						<p class="text-[10px] uppercase tracking-[0.2em] text-chroma-blue"><?php echo esc_html( $line2 ); ?></p>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</a>

		<!-- Desktop Navigation -->
		<nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-brand-ink/70">
			<a href="<?php echo esc_url( home_url( '/' . chroma_get_program_base_slug() ) ); ?>" class="hover:text-chroma-blue transition">Programs</a>

			<!-- About Us Dropdown -->
			<div class="relative group" data-dropdown>
				<button class="hover:text-chroma-blue transition flex items-center gap-1" aria-expanded="false" aria-haspopup="true">
					About Us
					<svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
					</svg>
				</button>
				<div class="absolute top-full left-0 mt-2 w-48 bg-white rounded-2xl shadow-lg border border-chroma-blue/10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
					<div class="py-2">
						<a href="<?php echo esc_url( home_url( '/about#our-story' ) ); ?>" class="block px-4 py-2 text-sm text-brand-ink hover:bg-chroma-blueLight hover:text-chroma-blue transition">Our Story</a>
						<a href="<?php echo esc_url( home_url( '/about#chroma-standard' ) ); ?>" class="block px-4 py-2 text-sm text-brand-ink hover:bg-chroma-blueLight hover:text-chroma-blue transition">The Chroma Standard</a>
						<a href="<?php echo esc_url( home_url( '/about#leadership' ) ); ?>" class="block px-4 py-2 text-sm text-brand-ink hover:bg-chroma-blueLight hover:text-chroma-blue transition">Leadership</a>
						<a href="<?php echo esc_url( home_url( '/about#giving-back' ) ); ?>" class="block px-4 py-2 text-sm text-brand-ink hover:bg-chroma-blueLight hover:text-chroma-blue transition">Giving Back</a>
					</div>
				</div>
			</div>

			<a href="<?php echo esc_url( home_url( '/locations' ) ); ?>" class="hover:text-chroma-blue transition">Locations</a>
			<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="hover:text-chroma-blue transition">Contact</a>
		</nav>

		<!-- CTA Button -->
		<a href="<?php echo esc_url( get_theme_mod( 'chroma_book_tour_url', home_url( '/contact#tour' ) ) ); ?>" class="hidden sm:inline-flex items-center gap-2 bg-brand-ink text-white text-xs font-semibold tracking-[0.2em] px-5 py-3 rounded-full shadow-soft hover:bg-chroma-blueDark">
			Book A Tour
		</a>

		<!-- Mobile Menu Button -->
		<button data-mobile-nav-toggle class="md:hidden text-2xl text-brand-ink" aria-label="Open menu">☰</button>
	</div>

	<!-- Mobile Menu -->
	<div data-mobile-nav class="fixed inset-0 bg-white z-50 translate-x-full transition-transform duration-300 md:hidden flex flex-col">
		<div class="flex items-center justify-between px-5 py-5 border-b border-chroma-blue/10">
			<div class="flex items-center gap-2">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/chroma-logo.png' ); ?>"
				     srcset="<?php echo esc_url( get_template_directory_uri() . '/assets/images/chroma-logo.png' ); ?> 1x,
				             <?php echo esc_url( get_template_directory_uri() . '/assets/images/chroma-logo-highres.png' ); ?> 2x"
				     alt="Chroma Early Learning"
				     class="h-10 w-auto" />
				<?php
				// Parse header text for mobile menu
				$mobile_header_text = get_theme_mod( 'chroma_header_text', "Early Learning\nAcademy" );
				$mobile_all_lines = array_map( 'trim', explode( "\n", $mobile_header_text ) );
				$mobile_first_empty = empty( $mobile_all_lines[0] );

				if ( $mobile_first_empty ) {
					// First line empty, use second line or 'Menu'
					$mobile_non_empty = array_filter( $mobile_all_lines );
					$mobile_line1 = ! empty( $mobile_non_empty ) ? reset( $mobile_non_empty ) : 'Menu';
				} else {
					// Use first line
					$mobile_line1 = $mobile_all_lines[0];
				}
				?>
				<span class="font-serif text-lg font-bold text-brand-ink"><?php echo esc_html( $mobile_line1 ); ?> Menu</span>
			</div>
			<button data-mobile-nav-toggle class="text-3xl text-brand-ink" aria-label="Close menu">×</button>
		</div>
		<nav class="flex-1 px-6 py-6 text-lg font-semibold text-brand-ink space-y-6">
			<a href="<?php echo esc_url( home_url( '/' . chroma_get_program_base_slug() ) ); ?>" class="block hover:text-chroma-blue transition">Programs</a>

			<!-- About Us Accordion (Mobile) -->
			<div data-mobile-dropdown>
				<button class="w-full flex items-center justify-between hover:text-chroma-blue transition" data-mobile-dropdown-toggle>
					<span>About Us</span>
					<svg class="w-5 h-5 transition-transform" data-mobile-dropdown-icon fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
					</svg>
				</button>
				<div class="hidden mt-2 ml-4 space-y-2" data-mobile-dropdown-content>
					<a href="<?php echo esc_url( home_url( '/about#our-story' ) ); ?>" class="block text-base text-brand-ink/70 hover:text-chroma-blue transition">Our Story</a>
					<a href="<?php echo esc_url( home_url( '/about#chroma-standard' ) ); ?>" class="block text-base text-brand-ink/70 hover:text-chroma-blue transition">The Chroma Standard</a>
					<a href="<?php echo esc_url( home_url( '/about#leadership' ) ); ?>" class="block text-base text-brand-ink/70 hover:text-chroma-blue transition">Leadership</a>
					<a href="<?php echo esc_url( home_url( '/about#giving-back' ) ); ?>" class="block text-base text-brand-ink/70 hover:text-chroma-blue transition">Giving Back</a>
				</div>
			</div>

			<a href="<?php echo esc_url( home_url( '/locations' ) ); ?>" class="block hover:text-chroma-blue transition">Locations</a>
			<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="block hover:text-chroma-blue transition">Contact</a>

			<a href="<?php echo esc_url( get_theme_mod( 'chroma_book_tour_url', home_url( '/contact#tour' ) ) ); ?>" class="block bg-brand-ink text-white text-center py-3 rounded-2xl shadow-soft hover:bg-chroma-blueDark transition mt-4">
				Book A Tour
			</a>
		</nav>
	</div>
</header>

<main id="main-content">
