# 快速启动：把 WordPress 单语站扩展为多语言站

## 前提
- WordPress 站点已安装并激活 Polylang（或其他多语言插件）
- 源语言（通常为英文）已配置
- 目标语言已添加并分配 term_taxonomy_id

## 5 分钟上手

1. **克隆/下载本仓库**
2. **编辑 `manifest.md`**：填入你的 EN 文章列表
3. **批量导出**：修改 `templates/export_content.php` 中的 `$post_ids`，运行 `wp eval-file export_content.php`
4. **翻译**：打开导出的 `.txt`，复制到 `de_{id}.html`，翻译可见文本（参考 `rules/translation-rules.md`）
5. **创建目标语言文章**：设置 `templates/create_de.php` 中的 `$en_id`、`$title`、`$locale`，运行 `wp eval-file create_de.php`
6. **验证**：用 `verify.js` 模式访问 `/{locale}/{slug}/`，运行清单
7. **清理**：删除服务器临时文件，更新 `manifest.md`
8. **重复** 3-7 直到所有文章完成

## 目录结构

```
{your-repo}/
├── README.md               # 本文件
├── CONTRIBUTING.md         # 如何为项目贡献
├── manifest.md             # 翻译清单（增量检测）
├── verify.js               # Playwright 自动化验证脚本
├── .gitignore              # 忽略临时文件
├── templates/
│   ├── export_content.php  # 批量导出 EN 文章
│   └── create_de.php       # 创建目标语言文章 + 多语言插件关联
├── rules/
│   ├── translation-rules.md  # 什么能翻什么不能翻
│   └── quality-gate.md       # 验证清单 + 发布流程
└── adapters/               # 不同 AI 工具的接入说明
```

## 关键原则

- **结构不动、文本换语**：绝不破坏原始 HTML 结构
- **Manifest 驱动增量**：只翻译新增/变更文章
- **发布-审核**：机器翻译先上线，native speaker 事后确认
- **验证即门禁**：每篇必须过自动化验证清单

## 支持的多语言插件

- Polylang（已验证）
- WPML
- TranslatePress
- WPGlobus
- GTranslate

插件相关代码（`pll_set_post_language`、`pll_save_post_translations`）需按插件 API 调整。