import fs from 'fs';

const summaryPath = './lighthouse-reports/audit-summary.json';
const outputPath = './accessibility-issues.txt';

try {
    const summaryData = fs.readFileSync(summaryPath, 'utf8');
    const summary = JSON.parse(summaryData);
    let output = "Accessibility Issues Report\n===========================\n\n";

    summary.forEach(page => {
        if (page.scores.accessibility < 100) {
            output += `Page: ${page.url} (Score: ${page.scores.accessibility})\n`;
            output += `--------------------------------------------------\n`;

            // We need to look at the 'audits' object in the summary if available, 
            // but audit-summary.json might only have top-level audits. 
            // Let's check if we have detailed accessibility audits.
            // The current audit-script.mjs structure puts *some* audits in the summary.
            // If not, we might need to look at individual json files, but let's see what we have.

            // Actually, the summary JSON structure I saw earlier had "audits" but mostly performance metrics.
            // I might need to read the individual report files for detailed accessibility issues.
            // Let's list the low scoring pages first.
            output += "\n";
        }
    });

    fs.writeFileSync(outputPath, output);
    console.log(`Accessibility report generated at ${outputPath}`);

} catch (error) {
    console.error('Error processing accessibility report:', error);
}
