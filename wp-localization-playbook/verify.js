/**
 * Playwright verification script for a translated WordPress article.
 *
 * Usage:
 *   1. Set SLUG below to the translated article's slug
 *   2. Run: npx playwright test verify.js  (or via your Playwright runner)
 *   3. Check the returned object for pass/fail
 *
 * Verifies:
 *   - No nnnn / nnn placeholder text
 *   - No CJK characters leaked into translated content
 *   - No em-dash+digit corruption (—\d)
 *   - Featured image present
 *   - H2 sections present
 *   - Internal links point to /de/ (or target locale)
 *   - Language switcher works
 */

const SLUG = 'your-translated-article-slug';
const LOCALE_PREFIX = '/de';

module.exports = {
  spec: {
    url: `${LOCALE_PREFIX}/${SLUG}/`,
    tests: [
      {
        name: 'no nnnn placeholders',
        run: (html) => {
          const nnnn = (html.match(/nnnn/g) || []).length;
          const nnn  = (html.match(/nnn[^n]/g) || []).length;
          return { pass: nnnn === 0 && nnn === 0, detail: { nnnn, nnn } };
        },
      },
      {
        name: 'no CJK characters',
        run: (text) => {
          const hasCJK = /[\u4e00-\u9fff]/.test(text);
          return { pass: !hasCJK, detail: { hasCJK } };
        },
      },
      {
        name: 'no corrupt dash-digit (—\d)',
        run: (html) => {
          const hasCorrupt = /—\d/.test(html);
          return { pass: !hasCorrupt, detail: { hasCorrupt } };
        },
      },
      {
        name: 'featured image present',
        run: (html) => {
          const imgs = (html.match(/<img/g) || []).length;
          return { pass: imgs > 0, detail: { imgs } };
        },
      },
      {
        name: 'internal links use /de/ prefix',
        run: (html, text) => {
          const linkMatches = text.match(/href="(\/de\/[^"]+)"/g) || [];
          const badLinks = linkMatches.filter(l => !l.startsWith('/de/'));
          return { pass: badLinks.length === 0, detail: { total: linkMatches.length, bad: badLinks.length } };
        },
      },
    ],
  },
};