const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

// Configuration
const BASE_URL = 'https://x3yyntt5tp.wpdns.site';
const PAGES = [
    { name: 'Homepage', url: '/' },
    { name: 'Single Program', url: '/programs/infant-care/' },
    { name: 'Single Location', url: '/locations/midway-campus/' },
    { name: 'Contact', url: '/contact/' },
    { name: 'Programs Archive', url: '/programs/' },
    { name: 'Locations Archive', url: '/locations/' }
];

const OUTPUT_DIR = 'lighthouse-reports';
const REPORT_FILE = 'lighthouse-issues.md';

// Ensure output directory exists
if (!fs.existsSync(OUTPUT_DIR)) {
    fs.mkdirSync(OUTPUT_DIR);
}

// Helper to run command
function runCommand(command) {
    try {
        // Redirect stderr to null to hide the permission errors
        execSync(`${command} 2>NUL`, { stdio: ['ignore', 'pipe', 'ignore'] });
        return true;
    } catch (e) {
        // If it fails but produces output, we might still be okay (the permission error often returns exit code 1)
        return false;
    }
}

// Initialize Report
let markdownReport = `# Comprehensive Lighthouse Audit Report\n\nDate: ${new Date().toLocaleString()}\n\n`;

console.log('Starting Comprehensive Audit...');

PAGES.forEach(page => {
    const fullUrl = `${BASE_URL}${page.url}`;
    const safeName = page.name.replace(/\s+/g, '-').toLowerCase();

    console.log(`\nAnalyzing ${page.name} (${fullUrl})...`);
    markdownReport += `## ${page.name}\nURL: ${fullUrl}\n\n`;

    ['mobile', 'desktop'].forEach(strategy => {
        console.log(`  Running ${strategy} audit...`);

        const filename = `${safeName}-${strategy}.json`;
        const filePath = path.join(OUTPUT_DIR, filename);

        // Construct Lighthouse command
        // Note: --preset=desktop for desktop, default is mobile
        // We use --output-path=stdout and capture it because of the file permission issues with CLI writing directly
        const presetFlag = strategy === 'desktop' ? '--preset=desktop' : '';
        // Chrome flags for headless
        const chromeFlags = '--chrome-flags="--headless=new"';

        const cmd = `lighthouse ${fullUrl} --output=json --output-path="${filePath}" --only-categories=performance,accessibility,best-practices,seo ${presetFlag} --quiet ${chromeFlags}`;

        runCommand(cmd);

        // Analyze the result
        if (fs.existsSync(filePath)) {
            try {
                const data = JSON.parse(fs.readFileSync(filePath, 'utf8'));
                const scores = {
                    performance: Math.round(data.categories.performance.score * 100),
                    accessibility: Math.round(data.categories.accessibility.score * 100),
                    bestPractices: Math.round(data.categories['best-practices'].score * 100),
                    seo: Math.round(data.categories.seo.score * 100),
                };

                markdownReport += `### ${strategy.charAt(0).toUpperCase() + strategy.slice(1)}\n`;
                markdownReport += `**Scores:** 🟢 Perf: ${scores.performance} | ♿ Acc: ${scores.accessibility} | 🛡️ Best: ${scores.bestPractices} | 🔍 SEO: ${scores.seo}\n\n`;

                // Extract Metrics
                const lcp = data.audits['largest-contentful-paint'].displayValue;
                const cls = data.audits['cumulative-layout-shift'].displayValue;
                const tbt = data.audits['total-blocking-time'].displayValue;

                markdownReport += `**Key Metrics:**\n`;
                markdownReport += `- **LCP:** ${lcp}\n`;
                markdownReport += `- **CLS:** ${cls}\n`;
                markdownReport += `- **TBT:** ${tbt}\n\n`;

                // Extract Issues (Audits with score < 1 and not manual)
                const issues = [];
                Object.values(data.audits).forEach(audit => {
                    if (audit.score !== null && audit.score < 0.9 && audit.scoreDisplayMode !== 'manual' && audit.scoreDisplayMode !== 'informative') {
                        // Filter out some noise if needed, but keeping it verbose for now
                        issues.push({
                            title: audit.title,
                            score: audit.score,
                            displayValue: audit.displayValue
                        });
                    }
                });

                if (issues.length > 0) {
                    markdownReport += `**Top Issues:**\n`;
                    // Sort by score ascending (worst first)
                    issues.sort((a, b) => a.score - b.score);

                    // Show top 5
                    issues.slice(0, 5).forEach(issue => {
                        markdownReport += `- 🔴 **${issue.title}** (Score: ${Math.round(issue.score * 100)})\n`;
                        if (issue.displayValue) {
                            markdownReport += `  - ${issue.displayValue}\n`;
                        }
                    });
                    markdownReport += `\n`;
                } else {
                    markdownReport += `✅ No significant issues found.\n\n`;
                }

            } catch (err) {
                console.error(`Error parsing ${filename}:`, err.message);
                markdownReport += `⚠️ Error parsing report for ${strategy}.\n\n`;
            }
        } else {
            console.error(`Failed to generate report for ${page.name} (${strategy})`);
            markdownReport += `⚠️ Failed to generate report for ${strategy}.\n\n`;
        }
    });

    markdownReport += `---\n\n`;
});

fs.writeFileSync(REPORT_FILE, markdownReport);
console.log(`\nAudit complete! Report saved to ${REPORT_FILE}`);
