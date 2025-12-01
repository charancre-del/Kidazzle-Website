<?php
/**
 * Daily Schedule Tabs
 * Template Part: Schedule Tabs
 * "A Day in the Life" - Daily rhythm tabs for different age groups
 *
 * @package Chroma_Excellence
 */

$tracks = chroma_home_schedule_tracks();

if (empty($tracks)) {
        return;
}
?>

<section id="schedule" class="py-20 bg-brand-cream relative" data-section="schedule">
        <div
                class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-chroma-red via-chroma-yellow to-chroma-blue opacity-40">
        </div>
        <div class="max-w-6xl mx-auto px-4 lg:px-6" data-schedule
                data-tracks='<?php echo esc_attr(wp_json_encode($tracks)); ?>'>
                <div class="text-center mb-12">
                        <span class="text-chroma-green font-bold tracking-[0.2em] text-xs uppercase mb-4 block">Day by
                                Day</span>
                        <h2 class="text-3xl md:text-4xl font-serif text-brand-ink mb-3">A Daily Rhythm of Joy</h2>
                        <p class="text-brand-ink/70 max-w-2xl mx-auto">We don't just fill time. Every classroom follows
                                a thoughtful flow designed to balance stimulation, nourishment, and rest.</p>
                </div>

                <div class="flex justify-center mb-12">
                        <div class="bg-white border border-chroma-blue/15 p-1 rounded-full inline-flex"
                                data-schedule-tabs>
                                <?php foreach ($tracks as $index => $track): ?>
                                        <?php
                                        $is_active = 0 === $index;
                                        $tab_classes = $is_active
                                                ? 'bg-chroma-blue text-white shadow-soft'
                                                : 'text-brand-ink/80 hover:text-chroma-blue';
                                        ?>
                                        <button class="schedule-tab px-8 py-3 rounded-full text-sm font-bold transition-all duration-300 <?php echo esc_attr($tab_classes); ?>"
                                                data-schedule-tab="<?php echo esc_attr($track['key']); ?>"
                                                aria-pressed="<?php echo esc_attr($is_active ? 'true' : 'false'); ?>"><?php echo esc_html($track['label'] ?? ucfirst($track['key'])); ?></button>
                                <?php endforeach; ?>
                        </div>
                </div>

                <?php foreach ($tracks as $index => $track): ?>
                        <?php
                        $is_active = 0 === $index;
                        $panel_classes = $is_active ? 'tab-content active' : 'tab-content hidden';
                        $backgroundTint = !empty($track['background']) ? $track['background'] : 'bg-brand-cream';
                        ?>
                        <div class="<?php echo esc_attr($panel_classes); ?>"
                                data-schedule-panel="<?php echo esc_attr($track['key']); ?>">
                                <?php
                                // Get track-specific colors
                                $track_color = !empty($track['color']) ? $track['color'] : 'chroma-blue';
                                $badge_bg = 'bg-' . $track_color; // Solid background for active state
                                $badge_text = 'text-' . $track_color;
                                ?>
                                <div class="rounded-[3rem] p-8 md:p-12 <?php echo esc_attr($backgroundTint); ?> text-center">

                                        <!-- Header -->
                                        <div class="max-w-3xl mx-auto mb-8">
                                                <h3 class="text-3xl font-serif text-brand-ink mb-4">
                                                        <?php echo esc_html($track['title']); ?>
                                                </h3>
                                                <p class="text-brand-ink/70 leading-relaxed">
                                                        <?php echo esc_html($track['description'] ?? ''); ?>
                                                </p>
                                        </div>

                                        <!-- Image -->
                                        <div class="max-w-xl mx-auto mb-10">
                                                <div class="rounded-[2rem] overflow-hidden shadow-lg aspect-video bg-white/50">
                                                        <?php if (!empty($track['image'])): ?>
                                                                <img src="<?php echo esc_url($track['image']); ?>"
                                                                        alt="<?php echo esc_attr($track['title']); ?>"
                                                                        class="w-full h-full object-cover" />
                                                        <?php else: ?>
                                                                <div
                                                                        class="w-full h-full flex items-center justify-center text-chroma-blueDark/20 text-6xl">
                                                                        data-content-title>
                                                                        <?php echo esc_html($track['steps'][0]['title']); ?>
                                                                        </h4>
                                                                        <p class="text-brand-ink/80 leading-relaxed transition-opacity duration-300"
                                                                                data-content-copy>
                                                                                <?php echo esc_html($track['steps'][0]['copy']); ?>
                                                                        </p>
                                                                <?php endif; ?>
                                                        </div>

                                                </div>
                                        </div>
                                <?php endforeach; ?>
                        </div>
</section>