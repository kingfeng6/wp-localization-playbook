# AI 工具适配器

本 playbook 可被任意 AI 编码助手（Claude、Cursor、Codex、OpenCode 等）使用。

## 通用接入方式

将本仓库路径告知 AI 助手，并说明：

> "读一下 README.md，按 QUICKSTART.md 的流程翻译 WordPress 文章。"

AI 助手应能自动完成：
1. 读取 manifest.md 获取待翻译文章列表
2. 导出 EN 内容（`templates/export_content.php`）
3. 翻译并写入 `de_{id}.html`
4. 生成 `create_de.php` 并执行
5. 运行 `verify.js` 验证
6. 更新 manifest.md

## Claude / Cursor / Codex 提示词模板

```
你是 WordPress 多语言翻译助手。请按以下流程处理文章翻译：

1. 读 manifest.md，找到状态为 pending 的文章
2. 修改 templates/export_content.php 中的 $post_ids，运行 wp eval-file
3. 阅读导出的 EN 内容，翻译为目标语言（参考 rules/translation-rules.md）
4. 保存为 de_{id}.html
5. 生成 create_de.php（参考 templates/create_de.php 模板）
6. 上传并运行 wp eval-file create_de.php
7. 清理服务器临时文件
8. 用 Playwright 运行验证清单
9. 更新 manifest.md

重要规则：
- 永远不要用 --skip-plugins
- 永远用 wp eval-file 不用 wp eval
- 保留原始 HTML 结构
- 翻译后运行验证清单
```

## OpenCode 专用

OpenCode 用户可将本仓库路径添加到项目配置中。关键文件：
- `README.md` — 总览
- `QUICKSTART.md` — 快速启动
- `rules/translation-rules.md` — 翻译规则
- `rules/quality-gate.md` — 验证清单
- `templates/` — 可复用脚本模板
- `verify.js` — Playwright 验证

## 手动使用（不依赖 AI）

1. 打开 `templates/export_content.php`，填入要导出的 EN 文章 ID
2. 通过服务器运行 `wp eval-file export_content.php`
3. 打开生成的 `.txt`，手动翻译为 `.html`
4. 填入 `templates/create_de.php`，运行
5. 验证 → 发布 → 更新 manifest
