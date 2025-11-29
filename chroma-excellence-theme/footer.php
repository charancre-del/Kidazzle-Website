<?php
/**
 * The template for displaying the footer
 *
 * @package Chroma_Excellence
 */
?>
</main>

<footer class="bg-brand-ink text-white py-12 px-4 lg:px-6">
	<div class="max-w-7xl mx-auto">
		<!-- Top Section -->
		<div class="grid md:grid-cols-4 gap-8 mb-8">
			<!-- Logo and Description -->
			<div class="md:col-span-1">
				<div class="flex items-center gap-2 mb-4">
					<div class="flex -space-x-1">
						<span class="w-3 h-3 rounded-full bg-chroma-red"></span>
						<span class="w-3 h-3 rounded-full bg-chroma-yellow"></span>
						<span class="w-3 h-3 rounded-full bg-chroma-green"></span>
						<span class="w-3 h-3 rounded-full bg-chroma-blue"></span>
					</div>
					<span class="font-semibold text-white text-sm">Chroma</span>
				</div>
				<p class="text-[11px] text-white/60 leading-relaxed">
					Premium childcare & early education across Metro Atlanta.
				</p>
			</div>

			<!-- Quick Links -->
			<div class="md:col-span-1">
				<h3 class="font-bold text-sm mb-3">Quick Links</h3>
				<nav class="space-y-2 text-xs text-white/70">
					<?php chroma_footer_nav(); ?>
				</nav>
			</div>

			<!-- Contact Info -->
			<div class="md:col-span-1">
				<h3 class="font-bold text-sm mb-3">Contact</h3>
				<div class="space-y-2 text-xs text-white/70">
					<?php
					// Get contact info from customizer (with fallback to global settings)
					$footer_phone = get_theme_mod('chroma_footer_phone', '') ?: chroma_global_phone();
					$footer_email = get_theme_mod('chroma_footer_email', '') ?: chroma_global_email();
					$footer_address = get_theme_mod('chroma_footer_address', '') ?: chroma_global_full_address();
					?>
					<?php if ($footer_phone): ?>
						<p><a href="tel:<?php echo esc_attr($footer_phone); ?>"
								class="hover:text-white"><?php echo esc_html($footer_phone); ?></a></p>
					<?php endif; ?>
					<?php if ($footer_email): ?>
						<p><a href="mailto:<?php echo esc_attr($footer_email); ?>"
								class="hover:text-white"><?php echo esc_html($footer_email); ?></a></p>
					<?php endif; ?>
					<?php if ($footer_address): ?>
						<p><?php echo esc_html($footer_address); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<!-- Social Links -->
			<div class="md:col-span-1">
				<h3 class="font-bold text-sm mb-3">Connect With Us</h3>
				<div class="flex gap-3">
					<a href="https://www.facebook.com/ChromaPreschool/" target="_blank" rel="noopener noreferrer"
						class="w-12 h-12 flex items-center justify-center bg-white/10 rounded-full hover:bg-white/20 transition"
						aria-label="Visit our Facebook page">
						<i class="fa-brands fa-facebook-f text-lg"></i>
					</a>
					<a href="https://www.instagram.com/chromapreschool/" target="_blank" rel="noopener noreferrer"
						class="w-12 h-12 flex items-center justify-center bg-white/10 rounded-full hover:bg-white/20 transition"
						aria-label="Visit our Instagram page">
						<i class="fa-brands fa-instagram text-lg"></i>
					</a>
				</div>
			</div>
		</div>

		<!-- Bottom Section -->
		<div
			class="border-t border-white/10 pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-white/50">
			<p>&copy; <?php echo date('Y'); ?> Chroma Early Learning Academy. All rights reserved.</p>
			<div class="flex gap-4">
				<a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" class="hover:text-white">Privacy
					Policy</a>
				<a href="<?php echo esc_url(home_url('/terms-of-service')); ?>" class="hover:text-white">Terms of
					Service</a>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>

</html>