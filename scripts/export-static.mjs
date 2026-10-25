import { spawnSync } from 'node:child_process';
import { readFileSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = fileURLToPath(new URL('../', import.meta.url));
const configuration = JSON.parse(readFileSync(resolve(root, 'sao.config.json'), 'utf8'));
const origin = new URL(configuration.contexts.web.dist.static.origin).origin;
const build = spawnSync(resolve(root, 'node_modules/.bin/sao-dist'), ['web', '--static'], {
    cwd: root,
    env: { ...process.env, APP_URL: origin },
    stdio: 'inherit',
});
if (build.error) throw build.error;
if (build.status !== 0) process.exit(build.status ?? 1);

const gtagSnippet = `<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-F8D6QTYHNW"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-F8D6QTYHNW');
</script>`;

const output = resolve(root, 'dist-static/web');
const manifest = JSON.parse(readFileSync(resolve(output, 'saola-dist.json'), 'utf8'));
if (manifest.skipped?.length) throw new Error('Static export skipped registered pages');
for (const { file, url } of manifest.pages) {
    // The standalone client replaces the Laravel bundle; discard its stale preload.
    let html = readFileSync(resolve(output, file), 'utf8')
        .replace(/^.*<script>if\(location\.protocol.*static\/saola\/web\/js\/app\.web\.js.*<\/script>\s*$/gm, '');
    if (!html.includes('G-F8D6QTYHNW')) {
        html = html.replace('<head>', `<head>\n${gtagSnippet}`);
    }
    writeFileSync(resolve(output, file), html);
    if (html.includes('@useBlock(') || (html.match(/<h1(?:\s|>)/g) || []).length !== 1) {
        throw new Error(`Incomplete static content in ${file}`);
    }
    if (!html.includes(`rel="canonical" href="${origin}${url}"`)) throw new Error(`Unexpected canonical in ${file}`);
}

const urls = manifest.pages.map(({ url }) => `  <url><loc>${origin}${url}</loc></url>`).join('\n');
writeFileSync(resolve(output, 'sitemap.xml'), `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${urls}
</urlset>\n`);
writeFileSync(resolve(output, 'robots.txt'), `User-agent: *\nAllow: /\nSitemap: ${origin}/sitemap.xml\n`);
console.log(`Gehihi static site ready: ${output}`);
