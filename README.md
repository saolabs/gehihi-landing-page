# Gehihi Landing Page

Trang giới thiệu [Gehihi App](../gehihi-app), xây trên Saola (Laravel SSR + SPA) và xuất được thành site tĩnh.

Chiến lược SEO: tận dụng meme “Gehihi” (biệt danh cộng đồng AI Việt đặt cho Gemini) để chiếm truy vấn `gehihi`, `gehihi là gì`, `gehihi gemini`, rồi dẫn sang sản phẩm chat nhiều model.

## Trang

| URL | View | Từ khoá chính |
| --- | --- | --- |
| `/` | `modules/home/index.sao` | gehihi, gehihi ai, chat ai nhiều model |
| `/gehihi-la-gi.html` | `modules/home/gehihi-la-gi.sao` | gehihi là gì, gehihi gemini |

Thêm trang: tạo view `.sao` rồi khai báo trong `config/site.php`; route, sitemap và test tự nhận.

SEO đã có: SSR đủ nội dung, title/description riêng, canonical, Open Graph + ảnh `public/assets/img/og-gehihi.png`, microdata (WebApplication, FAQPage, Article, BreadcrumbList, Organization), `sitemap.xml`, `robots.txt`. Dùng microdata thay JSON-LD vì bản tĩnh bỏ mọi `<script>` inline.

Mọi trang phải ghi “không liên kết với Google” ở footer; test kiểm tra điều này.

## Chạy

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
npm run build:views && npx vite build
php artisan serve --port=8005
```

- `php artisan test`: kiểm tra metadata, link nội bộ, sitemap.
- `npm run dist:static`: xuất site tĩnh ra `dist-static/web` với origin trong `sao.config.json`.
- `npm run og`: render lại ảnh OG từ `scripts/og-image.html` (cần Google Chrome).

## Trước khi phát hành

- Tên miền: landing `https://gehihi.vn` (`sao.config.json`; production đặt `APP_URL=https://gehihi.vn`), app `https://app.gehihi.vn` (nút gọi hành động trong các view).
- Gắn Google Search Console và analytics.
