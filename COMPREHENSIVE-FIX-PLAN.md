# Comprehensive Fix Plan: Chroma Excellence Theme

**Date**: 2025-11-26
**Theme Score**: 92/100 (Excellent)
**Production Ready**: YES (after fixes)
**Total Issues**: 9 (3 critical, 5 medium, 1 low)

---

## Executive Summary

This document provides a complete, actionable plan to fix all identified issues in the Chroma Excellence WordPress theme. Issues are prioritized and include step-by-step implementation instructions with exact code changes.

### Time Estimates

| Priority | Issues | Time Required |
|----------|--------|--------------|
| **Critical** (Required) | 3 | 17-22 minutes |
| **Medium** (Optional) | 5 | 25 minutes |
| **Low** (Optional) | 1 | 2 minutes |
| **Testing** | - | 10 minutes |
| **TOTAL** | 9 | 54-59 minutes |

**Recommended Approach**: Fix critical issues first (22-27 min with testing) → Deploy → Optionally address medium/low issues later.

---

## Table of Contents

1. [Critical Issues](#critical-issues) - **MUST FIX BEFORE LAUNCH**
2. [Medium Issues](#medium-issues) - Optional quality improvements
3. [Low Issues](#low-issues) - Cosmetic fixes
4. [Testing Checklist](#testing-checklist)
5. [Deployment Plan](#deployment-plan)

---

# CRITICAL ISSUES

## Issue 1: Stats Strip Color Mismatch 🔴

**Priority**: CRITICAL
**Time**: 5 minutes
**Difficulty**: Easy
**File**: `template-parts/home/stats-strip.php`

### Problem
All stats display in red (`text-chroma-red`) instead of varied colors (red, yellow, blue, green).

**Current Code** (line 19):
```php
<div class="font-serif text-3xl font-bold text-chroma-red ...">
```

### Solution
Use the About page pattern which correctly implements varied colors.

### Step-by-Step Fix

**Step 1**: Open `template-parts/home/stats-strip.php`

**Step 2**: Locate the stats data structure (around line 10):
```php
$stats = chroma_home_stats();
```

**Step 3**: Find the helper function `chroma_home_stats()` in `inc/acf-homepage.php`

**Step 4**: Update the helper function to include color data:

**FIND** (around line 350-370 in `inc/acf-homepage.php`):
```php
return array(
    array(
        'value' => $stat1_value,
        'label' => $stat1_label,
    ),
    array(
        'value' => $stat2_value,
        'label' => $stat2_label,
    ),
    array(
        'value' => $stat3_value,
        'label' => $stat3_label,
    ),
    array(
        'value' => $stat4_value,
        'label' => $stat4_label,
    ),
);
```

**REPLACE WITH**:
```php
return array(
    array(
        'value' => $stat1_value,
        'label' => $stat1_label,
        'color' => 'chroma-red',
    ),
    array(
        'value' => $stat2_value,
        'label' => $stat2_label,
        'color' => 'chroma-yellow',
    ),
    array(
        'value' => $stat3_value,
        'label' => $stat3_label,
        'color' => 'chroma-blue',
    ),
    array(
        'value' => $stat4_value,
        'label' => $stat4_label,
        'color' => 'chroma-green',
    ),
);
```

**Step 5**: Update the template to use the color data

In `template-parts/home/stats-strip.php`:

**FIND** (line 19):
```php
<div class="font-serif text-3xl font-bold text-chroma-red mb-2">
    <?php echo esc_html( $stat['value'] ); ?>
</div>
```

**REPLACE WITH**:
```php
<?php
$color_class = ! empty( $stat['color'] ) ? 'text-' . $stat['color'] : 'text-chroma-red';
?>
<div class="font-serif text-3xl font-bold <?php echo esc_attr( $color_class ); ?> mb-2">
    <?php echo esc_html( $stat['value'] ); ?>
</div>
```

**Step 6**: Clear cache and test

### Verification
- [ ] Each stat displays in a different color
- [ ] Colors cycle: red → yellow → blue → green
- [ ] All stats visible and readable

---

## Issue 2: Missing Hero Video 🔴

**Priority**: CRITICAL
**Time**: 2 minutes
**Difficulty**: Very Easy
**File**: `template-parts/home/hero.php` and `/assets/video/`

### Problem
Video file `hero-classroom.mp4` doesn't exist, causing broken video element.

**Current Code** (line 64):
```php
<source src="<?php echo get_template_directory_uri(); ?>/assets/video/hero-classroom.mp4" type="video/mp4" />
```

### Solution Options

#### Option A: Upload Video File (Recommended if you have video)

**Step 1**: Obtain hero video
- Professional classroom footage (20-30 seconds)
- Format: MP4 (H.264 codec)
- Resolution: 1920x1080 or 1280x720
- File size: < 5MB for performance

**Step 2**: Create video directory
```bash
mkdir -p wp-content/themes/chroma-excellence-theme/assets/video/
```

**Step 3**: Upload video
```bash
# Upload hero-classroom.mp4 to:
wp-content/themes/chroma-excellence-theme/assets/video/hero-classroom.mp4
```

**Step 4**: Test video plays on homepage

#### Option B: Use Featured Image Fallback (Quick alternative)

If you don't have a video, add an image fallback.

**Step 1**: Open `template-parts/home/hero.php`

**Step 2**: Replace video section (lines 60-68):

**FIND**:
```php
<div class="absolute inset-y-0 left-16 right-0 rounded-[3rem] overflow-hidden border border-white/10 shadow-soft">
    <video autoplay muted playsinline loop class="w-full h-full object-cover">
        <source src="<?php echo get_template_directory_uri(); ?>/assets/video/hero-classroom.mp4" type="video/mp4" />
    </video>
</div>
```

**REPLACE WITH**:
```php
<div class="absolute inset-y-0 left-16 right-0 rounded-[3rem] overflow-hidden border border-white/10 shadow-soft">
    <?php
    $hero_video = get_template_directory_uri() . '/assets/video/hero-classroom.mp4';
    $hero_video_path = get_template_directory() . '/assets/video/hero-classroom.mp4';
    $hero_image = get_theme_mod( 'chroma_home_hero_image' );
    $featured_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );

    if ( file_exists( $hero_video_path ) ) : ?>
        <video autoplay muted playsinline loop class="w-full h-full object-cover">
            <source src="<?php echo esc_url( $hero_video ); ?>" type="video/mp4" />
        </video>
    <?php elseif ( $hero_image ) : ?>
        <img src="<?php echo esc_url( $hero_image ); ?>" class="w-full h-full object-cover" alt="Chroma Classroom" />
    <?php elseif ( $featured_image ) : ?>
        <img src="<?php echo esc_url( $featured_image ); ?>" class="w-full h-full object-cover" alt="Chroma Classroom" />
    <?php else : ?>
        <div class="w-full h-full bg-gradient-to-br from-chroma-blue to-chroma-green"></div>
    <?php endif; ?>
</div>
```

**Step 3**: Set homepage featured image in WordPress admin
- Edit the homepage
- Set Featured Image
- Recommended size: 1200x800px

### Verification
- [ ] Video plays automatically on page load (Option A)
- [ ] OR Image displays properly (Option B)
- [ ] No broken video player visible
- [ ] Mobile responsiveness works

---

## Issue 3: Schedule Tab Images Missing 🔴 🆕

**Priority**: CRITICAL
**Time**: 10-15 minutes
**Difficulty**: Easy
**File**: Program posts (featured images)

### Problem
Schedule tabs show placeholder icon (`fa-image`) instead of classroom photos because Program posts don't have featured images set.

**Current State**:
```php
<?php if ( ! empty( $track['image'] ) ) : ?>
    <img src="<?php echo esc_url( $track['image'] ); ?>" ... />
<?php else : ?>
    <div class="..."><i class="fa-solid fa-image"></i></div> <!-- PLACEHOLDER SHOWING -->
<?php endif; ?>
```

**Root Cause**: `$track['image']` comes from `get_the_post_thumbnail_url()` which returns empty if Programs have no featured images.

### Solution: Set Featured Images on Program Posts

#### Step 1: Obtain Classroom Photos

**Option A - Use Stock Photos** (Quick):
Download from Unsplash (free, no attribution required):

1. **Infant classroom**: https://unsplash.com/photos/photo-1555252333-9f8e92e65df9
2. **Toddler classroom**: https://unsplash.com/photos/photo-1503454537195-1dcabb73ffb9
3. **Pre-K classroom**: https://unsplash.com/photos/photo-1503919545874-86c1d9a04595

Download high-quality versions (~800x800px or larger).

**Option B - Use Your Own Photos** (Recommended):
- Professional classroom photos
- Format: JPG or PNG
- Size: 800x800px minimum (square or 4:3 ratio)
- Quality: High resolution
- Content: Children engaged in activities (ensure parent permissions)

**Required Photos**:
- Infant Care (6 weeks-12 months)
- Toddler Program (1-2 years)
- Preschool (2-3 years)
- Pre-K Prep (3 years)
- GA Pre-K (4 years)
- After-School (5-12 years) - optional

#### Step 2: Upload to WordPress Media Library

1. Go to **Media → Add New**
2. Upload all classroom photos
3. Add descriptive titles (e.g., "Infant Classroom", "Toddler Classroom")
4. Add alt text for accessibility

#### Step 3: Set Featured Images on Programs

For each program post:

1. Go to **Programs → All Programs**
2. Edit **Infant Care** program
3. Look for **Featured Image** box (right sidebar)
4. Click **Set featured image**
5. Select the Infant classroom photo
6. Click **Set featured image**
7. Click **Update**

Repeat for all programs:
- Infant Care → Infant classroom photo
- Toddler Program → Toddler classroom photo
- Preschool → Preschool classroom photo
- Pre-K Prep → Pre-K classroom photo
- GA Pre-K → Pre-K classroom photo
- After-School → After-school room photo

#### Step 4: Verify Images Appear

1. Visit homepage
2. Scroll to "A Daily Rhythm of Joy" section
3. Click each tab (Infant, Toddler, Pre-K, etc.)
4. Confirm classroom photos appear (not placeholder icon)

### Alternative: Code Fallback (If Missing Photos)

If you can't get photos immediately, add a graceful fallback:

**Edit** `template-parts/home/schedule-tabs.php` (lines 64-70):

**FIND**:
```php
<?php else : ?>
    <div class="w-full h-full flex items-center justify-center text-chroma-blueDark/60 text-5xl">
        <i class="fa-solid fa-image"></i>
    </div>
<?php endif; ?>
```

**REPLACE WITH**:
```php
<?php else : ?>
    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-chroma-blue/10 to-chroma-green/10">
        <div class="text-center text-chroma-blueDark/60">
            <i class="fa-solid fa-image text-5xl mb-3"></i>
            <p class="text-sm">Classroom photo coming soon</p>
        </div>
    </div>
<?php endif; ?>
```

This makes the placeholder look more intentional while you gather photos.

### Verification
- [ ] All schedule tabs show classroom photos
- [ ] Images load quickly (< 1 second)
- [ ] Images are high quality and properly sized
- [ ] No placeholder icons visible
- [ ] Mobile display works correctly

---

# MEDIUM ISSUES (Optional)

## Issue 4-6: Schedule Tabs Color Variations 🟡

**Priority**: MEDIUM (Optional)
**Time**: 15 minutes
**Difficulty**: Moderate
**Files**: `template-parts/home/schedule-tabs.php`, `inc/acf-homepage.php`

### Problem
Timeline, badges, and titles use hardcoded colors instead of varying per track. This reduces visual color-coding effectiveness.

**Current** (lines 52, 55, 57):
```php
<div class="absolute left-[19px] top-2 bottom-2 w-0.5 bg-chroma-blue/20"></div> <!-- Always blue -->
<div class="w-10 h-10 rounded-full bg-white text-brand-ink ..."> <!-- Always brand-ink -->
<h4 class="font-bold text-brand-ink">...</h4> <!-- Always brand-ink -->
```

**Expected**: Each track uses its own color (Infant=blue, Toddler=yellow, Pre-K=red, etc.)

### Solution

#### Step 1: Add Color to Track Data

**Edit** `inc/acf-homepage.php` - Find the track building loop (around line 315-340):

**FIND**:
```php
return array(
    'key'         => $anchor_slug,
    'label'       => $program_title,
    'title'       => $schedule_title ?: $program_title,
    'description' => $description,
    'color'       => $color_scheme, // Already exists
    'background'  => $colors['background'],
    'image'       => $image_url,
    'steps'       => $steps,
);
```

Add color classes mapping:

**ADD BEFORE THE RETURN**:
```php
// Get text color for this color scheme
$text_color_map = array(
    'red'      => 'chroma-red',
    'blue'     => 'chroma-blue',
    'yellow'   => 'chroma-yellow',
    'blueDark' => 'chroma-blueDark',
    'green'    => 'chroma-green',
);
$text_color = $text_color_map[ $color_scheme ] ?? 'chroma-blue';
```

**UPDATE RETURN**:
```php
return array(
    'key'         => $anchor_slug,
    'label'       => $program_title,
    'title'       => $schedule_title ?: $program_title,
    'description' => $description,
    'color'       => $color_scheme,
    'textColor'   => $text_color, // NEW
    'background'  => $colors['background'],
    'image'       => $image_url,
    'steps'       => $steps,
);
```

#### Step 2: Update Template to Use Colors

**Edit** `template-parts/home/schedule-tabs.php`:

**FIND** (line 52):
```php
<div class="absolute left-[19px] top-2 bottom-2 w-0.5 bg-chroma-blue/20"></div>
```

**REPLACE WITH**:
```php
<?php
$timeline_color = ! empty( $track['textColor'] ) ? 'bg-' . $track['textColor'] . '/20' : 'bg-chroma-blue/20';
?>
<div class="absolute left-[19px] top-2 bottom-2 w-0.5 <?php echo esc_attr( $timeline_color ); ?>"></div>
```

**FIND** (line 55):
```php
<div class="w-10 h-10 rounded-full bg-white text-brand-ink flex items-center justify-center shadow-sm relative z-10 text-xs font-bold">
```

**REPLACE WITH**:
```php
<?php
$badge_color = ! empty( $track['textColor'] ) ? 'text-' . $track['textColor'] : 'text-brand-ink';
?>
<div class="w-10 h-10 rounded-full bg-white <?php echo esc_attr( $badge_color ); ?> flex items-center justify-center shadow-sm relative z-10 text-xs font-bold">
```

**FIND** (line 57):
```php
<h4 class="font-bold text-brand-ink">
```

**REPLACE WITH**:
```php
<?php
$title_color = ! empty( $track['textColor'] ) ? 'text-' . $track['textColor'] : 'text-brand-ink';
?>
<h4 class="font-bold <?php echo esc_attr( $title_color ); ?>">
```

### Verification
- [ ] Each tab's timeline uses different color
- [ ] Time badges match track color
- [ ] Step titles match track color
- [ ] Colors are readable on backgrounds

---

## Issue 7-8: Locations Preview Grid & Hover Colors 🟡

**Priority**: MEDIUM (Optional)
**Time**: 10 minutes
**Difficulty**: Easy
**File**: `template-parts/home/locations-preview.php`

### Problem
1. Grid uses 3 columns instead of 4
2. All location cards hover to same color instead of varied region colors

### Solution

#### Fix 1: Grid Columns

**Edit** `template-parts/home/locations-preview.php`:

**FIND** (line 60):
```php
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
```

**REPLACE WITH**:
```php
<div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
```

#### Fix 2: Varied Hover Colors

**FIND** (line 77):
```php
<a href="..." class="block p-4 bg-white rounded-2xl border border-chroma-blue/10 hover:border-chroma-red/50 hover:bg-chroma-redLight transition ...">
```

**REPLACE WITH**:
```php
<?php
// Get region term and colors
$regions = wp_get_post_terms( $loc_id, 'location_region' );
$hover_color = 'chroma-red';
$hover_bg = 'chroma-redLight';

if ( ! empty( $regions ) && ! is_wp_error( $regions ) ) {
    $region = $regions[0];
    $region_colors = chroma_get_region_color_from_term( $region->term_id );
    $hover_color = $region_colors['border'];
    $hover_bg = $region_colors['bg'];
}

$hover_classes = 'hover:border-' . $hover_color . '/50 hover:bg-' . $hover_bg;
?>
<a href="<?php echo esc_url( get_permalink( $loc_id ) ); ?>" class="block p-4 bg-white rounded-2xl border border-chroma-blue/10 <?php echo esc_attr( $hover_classes ); ?> transition ...">
```

### Verification
- [ ] Grid shows 4 columns on desktop
- [ ] Each location hovers to region-specific color
- [ ] Layout doesn't break on mobile
- [ ] Hover animations smooth

---

# LOW ISSUES (Optional)

## Issue 9: Undefined chroma-orange Color 🟢

**Priority**: LOW (Cosmetic)
**Time**: 2 minutes
**Difficulty**: Very Easy
**File**: `tailwind.config.js` OR `page-about.php`

### Problem
About page uses `text-chroma-orange` which isn't defined in Tailwind config, causing icon to display without color.

**Current** (`page-about.php:350`):
```php
<i class="fa-solid fa-sun text-chroma-orange ..."></i>
```

### Solution Options

#### Option A: Add Orange to Tailwind Config (Recommended)

**Edit** `tailwind.config.js`:

**FIND** (around line 15-25):
```javascript
colors: {
    'chroma-red': '#D67D6B',
    'chroma-yellow': '#E6BE75',
    'chroma-green': '#7CAD8C',
    'chroma-blue': '#4A6C7C',
    'chroma-blueDark': '#2F4858',
    // ...
}
```

**ADD**:
```javascript
colors: {
    'chroma-red': '#D67D6B',
    'chroma-yellow': '#E6BE75',
    'chroma-green': '#7CAD8C',
    'chroma-blue': '#4A6C7C',
    'chroma-blueDark': '#2F4858',
    'chroma-orange': '#E89654', // NEW - orange between red and yellow
    // ...
}
```

**Rebuild CSS**:
```bash
npm run build
# OR
npx tailwindcss -i ./assets/css/tailwind.css -o ./assets/css/style.css --watch
```

#### Option B: Use Existing Color (Quick fix, no build needed)

**Edit** `page-about.php`:

**FIND** (line 350):
```php
<i class="fa-solid fa-sun text-chroma-orange ..."></i>
```

**REPLACE WITH**:
```php
<i class="fa-solid fa-sun text-chroma-yellow ..."></i>
```

### Verification
- [ ] Icon displays with color (not gray)
- [ ] Color fits design palette
- [ ] No console errors

---

# TESTING CHECKLIST

After applying fixes, systematically test:

## Visual Testing

### Homepage
- [ ] Stats strip shows 4 different colors (red, yellow, blue, green)
- [ ] Hero section shows video OR image (not broken player)
- [ ] Schedule tabs show classroom photos (all tabs)
- [ ] Schedule tab timeline/badges use varied colors (if fixed)
- [ ] Locations preview grid shows 4 columns (if fixed)
- [ ] Location hover colors vary by region (if fixed)

### About Page
- [ ] Sun icon displays with color (if fixed)

## Functional Testing
- [ ] All sections load without errors
- [ ] No JavaScript console errors
- [ ] No broken images
- [ ] Video autoplays (if using video)
- [ ] Schedule tabs switch smoothly
- [ ] All links work

## Performance Testing
- [ ] Homepage loads in < 3 seconds
- [ ] Images load progressively
- [ ] Video doesn't slow page load
- [ ] No layout shift (CLS)

## Cross-Browser Testing
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari (if available)

## Responsive Testing
- [ ] Mobile (320px - 767px)
- [ ] Tablet (768px - 1023px)
- [ ] Desktop (1024px+)

## Accessibility Testing
- [ ] All images have alt text
- [ ] Color contrast meets WCAG AA
- [ ] Keyboard navigation works
- [ ] Screen reader friendly

---

# DEPLOYMENT PLAN

## Pre-Deployment

1. **Backup Current Site**
   ```bash
   # Backup database
   wp db export backup-pre-fixes-$(date +%Y%m%d).sql

   # Backup theme
   zip -r chroma-theme-backup-$(date +%Y%m%d).zip wp-content/themes/chroma-excellence-theme/
   ```

2. **Test on Staging** (if available)
   - Apply all fixes to staging environment
   - Complete full testing checklist
   - Get client approval

3. **Document Changes**
   - List all files modified
   - Note any new media uploaded
   - Record Tailwind rebuild (if applicable)

## Deployment Steps

### For Critical Fixes Only (Recommended First)

**Time**: 22-27 minutes total

1. Fix Issue 1 (Stats colors) - 5 min
2. Fix Issue 2 (Hero video) - 2 min
3. Fix Issue 3 (Schedule images) - 10-15 min
4. Test homepage - 5 min
5. Deploy to production

### For All Fixes (Perfect Score)

**Time**: 54-59 minutes + testing

1. Apply all critical fixes (17-22 min)
2. Apply all medium fixes (25 min)
3. Apply low fix (2 min)
4. Complete testing checklist (10 min)
5. Deploy to production

## Post-Deployment

1. **Verify Live Site**
   - Check homepage loads correctly
   - Verify all fixes applied
   - Test on multiple devices

2. **Monitor Performance**
   - Check page load speed
   - Monitor error logs
   - Review analytics for issues

3. **Clear Caches**
   ```bash
   # WordPress object cache
   wp cache flush

   # If using caching plugin
   wp rocket clean --confirm
   # OR
   wp w3tc flush all
   ```

4. **Update Documentation**
   - Mark issues as resolved
   - Document any ongoing maintenance needed

---

# APPENDIX

## Files Modified by Fixes

### Critical Fixes
- `inc/acf-homepage.php` - Stats color data
- `template-parts/home/stats-strip.php` - Stats color display
- `template-parts/home/hero.php` - Video/image fallback
- `/assets/video/hero-classroom.mp4` - New video file (if Option A)
- Program posts (via WordPress admin) - Featured images

### Medium Fixes
- `inc/acf-homepage.php` - Schedule track colors
- `template-parts/home/schedule-tabs.php` - Color display
- `template-parts/home/locations-preview.php` - Grid and hover

### Low Fixes
- `tailwind.config.js` - Orange color (Option A)
- `page-about.php` - Icon color (Option B)

## Helper Functions Used

```php
// Get program featured image
get_the_post_thumbnail_url( $post_id, 'large' );

// Get region colors
chroma_get_region_color_from_term( $term_id );

// Check file exists
file_exists( get_template_directory() . '/path/to/file' );

// Get theme mod
get_theme_mod( 'setting_name' );
```

## Useful Commands

```bash
# Rebuild Tailwind CSS
npm run build

# Clear WordPress cache
wp cache flush

# Check file permissions
ls -la wp-content/themes/chroma-excellence-theme/assets/video/

# Optimize images
wp media regenerate --yes

# Database search/replace (for URLs)
wp search-replace 'old-url.com' 'new-url.com' --dry-run
```

---

## Support Resources

- **WordPress Codex**: https://codex.wordpress.org/
- **Tailwind CSS Docs**: https://tailwindcss.com/docs
- **Audit Reports**: See all `AUDIT-2-*.md` files in project root
- **Issue Details**: `AUDIT-2-IMAGE-LOADING-ISSUES.md`

---

**END OF FIX PLAN**

*This document provides complete instructions to resolve all identified issues. Start with critical fixes for fastest path to production.*

**Recommended Next Steps**:
1. Review this plan with your team
2. Gather required assets (classroom photos, hero video)
3. Allocate 25-30 minutes for critical fixes + testing
4. Deploy critical fixes
5. Optionally schedule medium/low fixes for next iteration
