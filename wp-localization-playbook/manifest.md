# 翻译清单 (Manifest)

> 每篇翻译文章占一行。增量检测用内容 Hash（SHA-256）比对源文是否变更。
> 字段说明：
> - `Status`: `pending` / `translating` / `published` / `needs_review` / `archived`
> - `Content Hash`: 源 EN 文章 `post_content` 的 SHA-256 哈希
> - `Verified`: `✅` 或 `❌`

| EN ID | EN Slug | DE ID | DE Slug | 状态 | Content Hash | 翻译日期 | Verified |
|---|---|---|---|---|---|---|---|
| {en_id_1} | {en_slug_1} | {de_id_1} | {de_slug_1} | published | — | 2026-08-19 | ✅ |
| {en_id_2} | {en_slug_2} | {de_id_2} | {de_slug_2} | published | — | 2026-08-19 | ✅ |

## 使用方式

1. 新增文章：在清单末尾追加一行，状态设为 `pending`
2. 增量检测：比对 Content Hash，仅翻译 Hash 变化的文章
3. 发布后：更新 DE_ID、DE Slug、状态为 `published`、Verified 设为 ✅
4. 重新翻译：Hash 变化 → 状态回 `pending`
