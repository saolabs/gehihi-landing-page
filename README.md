# Gehihi Landing Page

Trang giới thiệu [Gehihi App](../gehihi-app), xây trên Saola (Laravel SSR + SPA) và xuất được thành site tĩnh.

Nguồn view nằm ở `resources/saola/web/themes/default/views/`, logic dùng chung giữ tại `web/app/`. Context manifest khai theme mặc định; mapping tường minh trong `sao.config.json` dùng mode `published`, giữ namespace `web.themes.default.views.*`, route và pipeline xuất tĩnh với dependency đang ghim. Assets tại `public/assets/` giữ nguyên URL. Đây là cấu trúc local theme của project, không bắt cài gói web.

Chiến lược SEO: tận dụng meme “Gehihi” (biệt danh cộng đồng AI Việt đặt cho Gemini) để chiếm truy vấn `gehihi`, `gehihi là gì`, `gehihi gemini`, rồi dẫn sang hệ thống AI Agent với Agent Store, trình tạo agent không cần lập trình, kỹ năng, kiến thức riêng, quy trình và lịch chạy. Chat là một phương thức tương tác trong hệ thống.

## Thư viện Saola

Bộ phiên bản đã nâng và kiểm tra ngày 30/10/2026: `saola/core` 2.7.0, `saola/compiler` 3.0.0, `@saolabs/builder` 1.3.3 và `@saolabs/client` 1.5.1. Hai gói JavaScript được cài từ tag GitHub chính thức; `composer.lock` và `package-lock.json` giữ phiên bản dùng để build/deploy.

## Trang

| URL | View | Từ khoá chính |
| --- | --- | --- |
| `/` | `modules/home/index.sao` | gehihi, gehihi ai, hệ thống ai agent, tạo agent không cần lập trình |
| `/about.html` | `modules/home/about.sao` | gehihi là gì, gehihi gemini |

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
- `npm run deploy`: kéo commit mới nhất, chạy test, xuất tĩnh rồi rsync lên VPS và kiểm tra các URL trong sitemap. Dừng nếu còn thay đổi chưa commit. Địa chỉ server đọc từ `.deploy.env` (không commit, xem `.deploy.env.example`); nginx/SSL cấu hình sẵn trên server.
- `npm run og`: render lại ảnh OG từ `scripts/og-image.html` (cần Google Chrome).

## Trước khi phát hành

- Tên miền: landing `https://gehihi.vn` (`sao.config.json`; production đặt `APP_URL=https://gehihi.vn`), app `https://app.gehihi.vn` (nút gọi hành động trong các view).
- Gắn Google Search Console và analytics.
