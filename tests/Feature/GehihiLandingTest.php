<?php

namespace Tests\Feature;

use Tests\TestCase;

class GehihiLandingTest extends TestCase
{
    public function test_every_page_is_server_rendered_with_unique_seo_metadata(): void
    {
        config(['app.url' => 'https://gehihi.vn']);
        $titles = [];
        foreach (config('site.pages') as $slug => $view) {
            $path = '/'.($slug === '' ? '' : $slug.'.html');
            $html = $this->get('http://untrusted.example'.$path.'?utm_source=test')->assertOk()
                ->assertSee('<html lang="vi">', false)
                ->assertSee('<link rel="canonical" href="https://gehihi.vn'.$path.'">', false)
                ->assertSee('<meta property="og:image" content="https://gehihi.vn/assets/img/og-gehihi.png">', false)
                ->getContent();
            $document = new \DOMDocument;
            $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new \DOMXPath($document);

            $this->assertCount(1, $xpath->query('//*[@id="app-root"]//h1'), $path);
            $title = $xpath->query('//title')->item(0)->textContent;
            $this->assertStringContainsString('Gehihi', $title);
            $this->assertNotContains($title, $titles);
            $titles[] = $title;
            $description = $xpath->query('//meta[@name="description"]')->item(0)->getAttribute('content');
            $this->assertGreaterThan(70, mb_strlen($description), $path);
            $this->assertLessThanOrEqual(170, mb_strlen($description), $path);
            // The brand rides on a nickname for Google's product: every page must disclaim affiliation.
            $this->assertStringContainsString('không liên kết với Google', $xpath->query('//footer[contains(@class,"site-footer")]')->item(0)->textContent);
            $this->assertGreaterThan(0, $xpath->query('//*[@itemtype="https://schema.org/FAQPage"]//*[@itemtype="https://schema.org/Question"]')->length, $path);
            foreach ($xpath->query('//a[starts-with(@href,"/")]') as $link) {
                $target = parse_url($link->getAttribute('href'), PHP_URL_PATH);
                $this->get($target)->assertOk();
            }
            foreach ($xpath->query('//img[starts-with(@src,"/")]') as $image) {
                $this->assertFileExists(public_path(ltrim($image->getAttribute('src'), '/')));
            }
        }
        $this->assertFileExists(public_path('assets/img/og-gehihi.png'));
    }

    public function test_sitemap_and_robots_list_every_page(): void
    {
        config(['app.url' => 'https://gehihi.vn']);
        $sitemap = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')->getContent();
        foreach (array_keys(config('site.pages')) as $slug) {
            $this->assertStringContainsString('<loc>https://gehihi.vn/'.($slug === '' ? '' : $slug.'.html').'</loc>', $sitemap);
        }
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: https://gehihi.vn/sitemap.xml');
        $this->get('/khong-ton-tai.html')->assertNotFound();
    }
}
