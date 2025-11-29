const fs = require('fs');
const path = require('path');

const files = [
    'lighthouse-homepage.json',
    'lighthouse-program.json',
    'lighthouse-location.json',
    'lighthouse-contact.json'
];

console.log('| Page | Performance | Accessibility | Best Practices | SEO | LCP |');
console.log('|---|---|---|---|---|---|');

files.forEach(file => {
    try {
        const content = fs.readFileSync(file, 'utf8');
        const report = JSON.parse(content);

        const scores = {
            perf: Math.round(report.categories.performance.score * 100),
            acc: Math.round(report.categories.accessibility.score * 100),
            bp: Math.round(report.categories['best-practices'].score * 100),
            seo: Math.round(report.categories.seo.score * 100),
            lcp: report.audits['largest-contentful-paint'].displayValue
        };

        const name = file.replace('lighthouse-', '').replace('.json', '');
        console.log(`| ${name} | ${scores.perf} | ${scores.acc} | ${scores.bp} | ${scores.seo} | ${scores.lcp} |`);
    } catch (e) {
        console.error(`Error parsing ${file}: ${e.message}`);
    }
});
