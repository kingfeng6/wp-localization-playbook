# 🌐 wp-localization-playbook

> 把 WordPress 单语站变成多语言站的完整工作流 — 经过 15 篇文章实战验证，含模板、规则、自动化验证清单。拿来即用，改哪都行。

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![WordPress](https://img.shields.io/badge/WordPress-Compatible-%2321759b)](https://wordpress.org/)
[![Polylang](https://img.shields.io/badge/Polylang-Compatible-%23CE3D3D)](https://polylang.pro/)
[![GitHub](https://img.shields.io/badge/GitHub-Repo-%23181717)](https://github.com/)

---

## 😱 为什么需要这个

给 WordPress 站点加一个新语言？大多数人都会：

- ❌ 一篇一篇手动复制粘贴，效率低、易出错
- ❌ 翻译完忘记建立 Polylang 关联，语言切换器不工作
- ❌ 忘了复制 Featured Image，DE 页面没有缩略图
- ❌ 没有验证机制，发布后才发现乱码、中文泄漏、结构破坏
- ❌ 没有清单管理，重复翻译或漏译

这个 playbook 把这些步骤**固化成可复用的模板和自动化验证**，10 篇文章只需一个下午，之后新文章按流水线复制。

---

## ✨ 功能亮点

- **5 步流水线** — 导出 → 翻译 → 创建 → 关联 → 验证，每步都有模板脚本
- **Manifest 驱动增量** — 仅翻译新增/变更文章，不重复劳动
- **自动化验证门禁** — Playwright 一键检查：nnn 乱码、CJK 泄漏、脏数据、图片/表格/链接完整性
- **Polylang 关联自动化** — `pll_set_post_language` + `pll_save_post_translations` + `set_post_thumbnail` 一步到位
- **多 AI 工具适配** — Claude / Cursor / Codex / OpenCode 均可接入
- **发布-审核模式** — 机器翻译先上线，native speaker 事后确认
- **通用化处理** — 不绑定任何品牌或语言，换个 repo 名就能用

---

## 🎯 适合谁

- **WordPress 开发者** — 需要给客户站点加多语言支持
- **技术翻译 / 译员** — 想系统化 WordPress 文章翻译流程
- **SEO 专员** — 需要多语言内容覆盖，提升各语言市场搜索可见度
- **自由职业者 / 工作室** — 把这个 playbook 当作交付物模板，卖给多个客户
- **想学 WordPress 多语言开发的 AI** — 读完就能独立完成翻译任务

---

## 🔄 工作流（5 步）

```
导出 EN 内容 ──→ 翻译 ──→ 创建目标语言文章 ──→ 多语言插件关联 + 复制缩略图 ──→ 自动化验证
    ↓               ↓             ↓                        ↓                         ↓
 templates/    rules/     templates/create_de.php    pll_set_post_language    verify.js
 export_content  translation- + set_post_thumbnail   + pll_save_translations   (Playwright)
 .php          rules.md
```

---

## 📦 仓库结构

```
wp-localization-playbook/
├── README.md                     # 本文件
├── CONTRIBUTING.md               # 贡献指南
├── manifest.md                   # 翻译清单（增量检测）
├── verify.js                     # Playwright 自动化验证脚本
├── .gitignore
├── templates/
│   ├── export_content.php        # 批量导出 EN 内容模板
│   └── create_de.php             # 创建目标语言文章模板（参数化）
├── rules/
│   ├── translation-rules.md      # 什么能翻什么不能翻 + 字符修正
│   └── quality-gate.md           # 验证清单 + 回翻检测 + 发布流程
└── adapters/
    └── AI-tool-adapter.md        # Claude / Cursor / Codex / OpenCode 接入说明
```

---

## 🚀 快速上手

```bash
# 1. 克隆仓库
git clone https://github.com/your-username/wp-localization-playbook.git
cd wp-localization-playbook

# 2. 填入 manifest.md 的 EN 文章列表
# 3. 编辑 templates/export_content.php 的 $post_ids

# 4. 导出 EN 内容（服务器上执行）
wp eval-file templates/export_content.php

# 5. 翻译导出的 .txt → de_{id}.html

# 6. 创建目标语言文章
wp eval-file templates/create_de.php

# 7. 验证
npx playwright test verify.js

# 8. 清理服务器临时文件，更新 manifest.md
```

详细步骤见 [QUICKSTART.md](QUICKSTART.md)。

---

## 🎓 核心经验法则

| # | 法则 | 说明 |
|---|---|---|
| 1 | **Source of Truth** | EN 是唯一人工维护源，其他语言是 build artifact |
| 2 | **Manifest 驱动** | 维护翻译清单 + content_hash，增量检测只翻译变更文章 |
| 3 | **发布-审核** | 机器翻译先上线，native speaker 事后标记 `ratified` |
| 4 | **遮蔽-恢复** | 代码块、短代码、URL、frontmatter 键用占位符保护 |
| 5 | **结构不动、文本换语** | 绝不破坏原始 HTML 结构 |
| 6 | **验证即门禁** | 每篇必须过自动化验证清单才能发布 |
| 7 | **进度即文档** | 每篇发布即记录，换设备/换 AI 都能无缝接上 |

---

## ⚠️ 常见坑（踩过的告诉你）

| 坑 | 现象 | 解决 |
|---|---|---|
| nnnn 乱码 | 渲染出可见的 `nnnn` 文本 | 前端 JS 清理 + 数据库 REPLACE |
| CJK 泄漏 | DE 文章里出现中文/日文 | 验证正则 `/[\u4e00-\u9fff]/` |
| 脏数据 | EN 表格含 `—2` 等损坏值 | 在翻译中重构正确值，验证 `/—\d/` |
| 源 HTML 混合 | 部分 EN 文章纯 HTML，部分 Gutenberg | 保留原格式，不强制转换 |
| WP-CLI 崩溃 | `pll_*` 函数不存在 | 永远不用 `--skip-plugins`，用 `wp eval-file` |

---

## 🤝 贡献

欢迎提 Issue 和 Pull Request！详见 [CONTRIBUTING.md](CONTRIBUTING.md)。

贡献方向：
- ✅ `templates/` — 改进导出/创建/验证脚本
- ✅ `rules/` — 补充翻译规则、质量门禁
- ✅ `adapters/` — 新增 AI 工具适配说明
- ✅ `manifest.md` — 示例数据
- ✅ 文档改进

---

## 📄 许可

MIT。自由使用、修改、分发。

---

## 更新历史

- **2026-09-03** — 初版。基于 WordPress 站点 EN→多语言翻译 15 篇文章的实践经验整理，已通用化处理。
