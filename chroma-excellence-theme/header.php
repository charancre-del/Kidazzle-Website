<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class('bg-brand-cream text-brand-ink antialiased selection:bg-chroma-red selection:text-white'); ?>>
	<?php wp_body_open(); ?>

		return $line !== '\\';
		});
		$all_lines = array_values($all_lines); // Re-index array

		if ($first_line_is_placeholder) {
		// First line was placeholder, so all remaining lines use "line 2" formatting
		$line1 = '';
		$line2_array = array_filter($all_lines); // Remove all empty lines
		} else {
		// First line has content, use it as line 1
		$line1 = $all_lines[0];
		// Remove first element and filter empty lines for line 2
		array_shift($all_lines);
		$line2_array = array_filter($all_lines);
		}
		?>
		<?php if ($line1 || !empty($line2_array)): ?>
			<div class="leading-tight">
				<?php if ($line1): ?>
					<p class="font-bold text-lg text-brand-ink"><?php echo esc_html($line1); ?></p>
				<?php endif; ?>
				<?php if (!empty($line2_array)): ?>
					<?php foreach ($line2_array as $line2): ?>
						<p class="text-[10px] uppercase tracking-[0.2em] text-chroma-blue"><?php echo esc_html($line2); ?>
						</p>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		</a>

		<!-- Desktop Navigation -->
		<nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-brand-ink/70">
			<?php chroma_primary_nav(); ?>
		</nav>

		<!-- CTA Button -->
		<a href="<?php echo esc_url(home_url('/contact#tour')); ?>"
			class="hidden md:inline-flex items-center gap-2 bg-brand-ink text-white text-xs font-semibold tracking-[0.2em] px-5 py-3 rounded-full shadow-soft hover:bg-chroma-blueDark transition">
			Book A Tour
		</a>

		<!-- Mobile Menu Toggle -->
		<button data-mobile-nav-toggle class="md:hidden text-2xl text-brand-ink" aria-label="Open menu">
			<i class="fa-solid fa-bars"></i>
		</button>
		</div>

		<!-- Mobile Menu -->
		<div data-mobile-nav
			class="fixed inset-0 bg-white z-50 translate-x-full transition-transform duration-300 md:hidden flex flex-col">
			<div class="flex items-center justify-between px-5 py-5 border-b border-chroma-blue/10">
				<div class="flex items-center gap-2">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo_optimized_40x40.webp'); ?>"
						srcset="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo_optimized_40x40.webp'); ?> 1x,
							 <?php echo esc_url(get_template_directory_uri() . '/assets/images/logo_optimized_70x70.webp'); ?> 2x"
						alt="Chroma Early Learning" width="40" height="40" class="h-10 w-auto" />
					<?php
					// Parse header text for mobile menu
					$mobile_header_text = get_theme_mod('chroma_header_text', "Early Learning\nAcademy");
					$mobile_all_lines = array_map('trim', explode("\n", $mobile_header_text));

					// Check if first line is backslash or empty BEFORE filtering
					$mobile_first_is_placeholder = empty($mobile_all_lines[0]) || $mobile_all_lines[0] === '\\';

					// Remove backslash placeholders
					$mobile_all_lines = array_filter($mobile_all_lines, function ($line) {
						return $line !== '\\';
					});
					$mobile_all_lines = array_values($mobile_all_lines);

					if ($mobile_first_is_placeholder) {
						// First line was placeholder, use second line or 'Menu'
						$mobile_non_empty = array_filter($mobile_all_lines);
						$mobile_line1 = !empty($mobile_non_empty) ? reset($mobile_non_empty) : 'Menu';
					} else {
						// Use first line
						$mobile_line1 = $mobile_all_lines[0];
					}
					?>
					<span class="font-serif text-lg font-bold text-brand-ink"><?php echo esc_html($mobile_line1); ?>
						Menu</span>
				</div>
				<button data-mobile-nav-toggle class="text-3xl text-brand-ink" aria-label="Close menu">×</button>
			</div>
			<nav class="flex-1 px-6 py-6 text-lg font-semibold text-brand-ink space-y-6">
				<?php chroma_primary_nav(); ?>
				<a href="<?php echo esc_url(home_url('/contact#tour')); ?>"
					class="block bg-brand-ink text-white text-center py-3 rounded-2xl shadow-soft hover:bg-chroma-blueDark transition mt-4">
					Book A Tour
				</a>
			</nav>
		</div>
	</header>

	<main id="main-content">