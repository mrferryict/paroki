#!/usr/bin/env bash
# Rebuild Tailwind CSS and stage public/assets/css/app.css for commit.
#
# Usage:
#   ./scripts/deploy-check.sh
#   ./scripts/deploy-check.sh -a -m "Update tampilan beranda"
#   ./scripts/deploy-check.sh -a -m "Update tampilan beranda" --push
#
# Options:
#   -a, --all       Stage all changes including new files (git add -A)
#   -m, --message   Commit with this message (implies staging -a if not set)
#   --push          Push to origin/master after commit
#   -h, --help      Show help

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

COMMIT_MSG=""
PUSH=false
ADD_ALL=false

usage() {
    sed -n '2,12p' "$0" | sed 's/^# \?//'
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        -m|--message)
            COMMIT_MSG="${2:?Missing commit message after $1}"
            shift 2
            ;;
        --push)
            PUSH=true
            shift
            ;;
        -a|--all)
            ADD_ALL=true
            shift
            ;;
        -h|--help)
            usage
            exit 0
            ;;
        *)
            echo "Unknown option: $1" >&2
            usage >&2
            exit 1
            ;;
    esac
done

if [[ -n "$COMMIT_MSG" && "$ADD_ALL" == false ]]; then
    ADD_ALL=true
fi

echo "==> Paroki deploy-check"
echo "    $(pwd)"
echo

if [[ ! -d node_modules ]]; then
    echo "==> npm install (node_modules belum ada)"
    npm install
    echo
fi

echo "==> npm run build"
npm run build
echo

echo "==> Stage public/assets/css/app.css"
git add public/assets/css/app.css

if [[ "$ADD_ALL" == true ]]; then
    echo "==> Stage semua perubahan (git add -A)"
    git add -A
fi

echo
echo "==> Git status:"
git status --short

if [[ -z "$(git status --porcelain)" ]]; then
    echo
    echo "Tidak ada perubahan. Working tree bersih — push tidak diperlukan."
    exit 0
fi

if [[ -z "$COMMIT_MSG" ]]; then
    echo
    echo "Langkah berikutnya:"
    echo "  git add <file lain jika perlu>"
    echo "  git commit -m \"Pesan commit Anda\""
    echo "  git push origin master"
    echo
    echo "Atau sekaligus:"
    echo "  ./scripts/deploy-check.sh -a -m \"Pesan commit\" --push"
    exit 0
fi

echo
echo "==> git commit"
git commit -m "$COMMIT_MSG"

if [[ "$PUSH" == true ]]; then
    echo "==> git push origin master"
    git push origin master
    echo
    echo "Selesai — sudah di-push ke GitHub."
else
    echo
    echo "Commit berhasil. Push manual:"
    echo "  git push origin master"
    echo
    echo "Atau jalankan ulang dengan --push"
fi
