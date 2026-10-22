#!/usr/bin/env bash
# Deploy the committed landing page to production: pull, test, build static, rsync, verify.
# Usage: npm run deploy   (or: bash scripts/deploy.sh)
set -euo pipefail

ORIGIN="https://gehihi.vn"

cd "$(dirname "$0")/.."

# Server details stay out of git: .deploy.env is ignored (see .deploy.env.example).
[[ -f .deploy.env ]] && source .deploy.env
: "${DEPLOY_HOST:?Thiếu DEPLOY_HOST, tạo .deploy.env từ .deploy.env.example}"
: "${DEPLOY_DEST:?Thiếu DEPLOY_DEST, tạo .deploy.env từ .deploy.env.example}"
HOST="$DEPLOY_HOST"
DEST="$DEPLOY_DEST"

# Production must match a commit; the build reads the working tree.
if [[ -n "$(git status --porcelain)" ]]; then
    echo "✗ Còn thay đổi chưa commit. Commit (và push) trước khi deploy:" >&2
    git status --short >&2
    exit 1
fi
git pull --ff-only --quiet
REV="$(git rev-parse --short HEAD)"
echo "→ Deploy commit $REV: $(git log -1 --format=%s)"

php artisan test --compact
npm run --silent dist:static

rsync -az --delete --exclude saola-dist.json dist-static/web/ "$HOST:$DEST/"
ssh "$HOST" "chown -R www-data:www-data '$DEST'"

# Every sitemap URL must answer 200 from the live site.
failed=0
for url in $(grep -o '<loc>[^<]*' dist-static/web/sitemap.xml | sed 's/<loc>//'); do
    code="$(curl -s -o /dev/null -w '%{http_code}' "$url")"
    echo "  $code $url"
    [[ "$code" == 200 ]] || failed=1
done
((failed == 0)) || { echo "✗ Có trang không trả 200 sau khi deploy" >&2; exit 1; }
echo "✓ $ORIGIN đang chạy commit $REV"
