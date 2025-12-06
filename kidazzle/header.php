<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php wp_title('|', true, 'right'); ?></title>
	<!-- Tailwind CSS (CDN for immediate design fidelity) -->
	<script src="https://cdn.tailwindcss.com"></script>

	<?php wp_head(); ?>
	<style>
		.graph-bar {
			transition: height 1s cubic-bezier(0.4, 0, 0.2, 1);
		}

		.fade-in {
			animation: fadeIn 0.5s ease-in;
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
			}

			to {
				opacity: 1;
			}
		}

		html {
			scroll-behavior: smooth;
		}
	</style>
</head>

<body <?php body_class('font-sans text-slate-800 bg-white'); ?>>
	<?php wp_body_open(); ?>

	<!-- TOP UTILITY BAR -->
	<div
		class="bg-slate-50 text-slate-600 text-xs py-2 px-4 hidden md:flex justify-between items-center border-b border-slate-200 fixed w-full top-0 z-50 h-10">
		<div class="flex gap-4 items-center">
			<div class="h-6 w-auto mr-2">
				<!-- Logo Placeholder or Dynamic Logo if needed -->
				<span class="font-bold text-slate-700">KIDazzle</span>
			</div>
			<a href="<?php echo home_url('/locations'); ?>"
				class="flex items-center gap-1 cursor-pointer hover:text-cyan-600 transition">
				<i data-lucide="map-pin" class="w-3 h-3 text-red-500"></i> Locations in GA, TN, & FL
			</a>
			<span class="flex items-center gap-1">
				<i data-lucide="phone" class="w-3 h-3 text-green-500"></i> 877-410-1002
			</span>
		</div>
		<div class="flex gap-6 font-medium">
			<a href="<?php echo home_url('/careers'); ?>"
				class="hover:text-cyan-600 transition flex items-center gap-1">Careers</a>
			<a href="<?php echo home_url('/teacher-portal'); ?>"
				class="hover:text-cyan-600 transition flex items-center gap-1 font-bold text-orange-500">
				<i data-lucide="users" class="w-3 h-3"></i> Teacher Portal
			</a>
		</div>
	</div>

	<!-- MAIN NAVIGATION -->
	<nav id="navbar" class="fixed top-10 w-full z-40 transition-all duration-300 bg-white py-4 shadow-sm">
		<div class="container mx-auto px-4 md:px-6 flex justify-between items-center">
			<!-- Logo -->
			<a href="<?php echo home_url(); ?>" class="flex items-center gap-2 cursor-pointer">
				<div class="h-12 md:h-16 flex items-center relative">
					<?php
					if (has_custom_logo()) {
						the_custom_logo();
					} else {
						echo '<h1 class="text-3xl font-extrabold text-black pl-2 tracking-tighter hidden md:block">KID<span class="text-yellow-500">azzle</span></h1>';
					}
					?>
				</div>
			</a>

			<!-- Desktop Links -->
			<div class="hidden lg:flex items-center gap-6 font-bold text-slate-600 text-sm tracking-wide">
				<a href="<?php echo home_url(); ?>" class="hover:text-indigo-600 transition uppercase">Home</a>
				<a href="<?php echo home_url('/programs'); ?>"
					class="hover:text-red-500 transition uppercase">Programs</a>
				<a href="<?php echo home_url('/curriculum'); ?>"
					class="hover:text-cyan-500 transition uppercase">Curriculum</a>
				<a href="<?php echo home_url('/locations'); ?>"
					class="hover:text-green-500 transition uppercase">Locations</a>
				<a href="<?php echo home_url('/resources'); ?>"
					class="hover:text-purple-500 transition uppercase">Resources</a>
				<a href="<?php echo home_url('/contact'); ?>"
					class="bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-slate-700 transition ml-2">Contact
					Us</a>
			</div>

			<!-- Mobile Toggle -->
			<button class="lg:hidden text-slate-900" id="mobile-menu-btn">
				<i data-lucide="menu" class="w-7 h-7"></i>
			</button>
		</div>

		<!-- Mobile Menu -->
		<div id="mobile-menu" class="hidden fixed inset-0 bg-white z-50 pt-24 px-6 overflow-y-auto">
			<button id="close-menu-btn" class="absolute top-4 right-4 text-slate-900">
				<i data-lucide="x" class="w-8 h-8"></i>
			</button>
			<div class="flex flex-col gap-6 font-bold text-xl text-slate-800 pt-4">
				<a href="<?php echo home_url(); ?>" class="text-left">Home</a>
				<a href="<?php echo home_url('/programs'); ?>" class="text-left text-red-500">Programs</a>
				<a href="<?php echo home_url('/curriculum'); ?>" class="text-left text-cyan-500">Curriculum</a>
				<a href="<?php echo home_url('/locations'); ?>" class="text-left text-green-500">Locations</a>
				<a href="<?php echo home_url('/resources'); ?>" class="text-left text-purple-500">Resources</a>
				<a href="<?php echo home_url('/careers'); ?>" class="text-left">Careers</a>
				<a href="<?php echo home_url('/teacher-portal'); ?>" class="text-left text-orange-500">Teacher
					Portal</a>
				<a href="<?php echo home_url('/contact'); ?>" class="text-left">Contact Us</a>
			</div>
		</div>
	</nav>

	<!-- MAIN CONTENT WRAPPER -->
	<main class="mt-20 min-h-screen">
		<script>
			document.addEventListener('DOMContentLoaded', function () {
				const menuBtn = document.getElementById('mobile-menu-btn');
				const closeBtn = document.getElementById('close-menu-btn');
				const mobileMenu = document.getElementById('mobile-menu');

				if (menuBtn && mobileMenu) {
					menuBtn.addEventListener('click', () => {
						mobileMenu.classList.remove('hidden');
					});
				}
				if (closeBtn && mobileMenu) {
					closeBtn.addEventListener('click', () => {
						mobileMenu.classList.add('hidden');
					});
				}
			});
		</script>