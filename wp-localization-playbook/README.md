# WordPress 多语言翻译工作流与经验

把 WordPress 单语（英文）站点扩展为多语言站点的完整工作流、关键技术决策、常见坑和可复用模板。适用于中小型 WordPress 站点的人工+自动化混合翻译场景。

---

## 概述

- **目标**：将近期高价值英文文章批量翻译成目标语言，建立 Polylang（或其他多语言插件）翻译关联，复制 Featured Image，并自动化验证每篇翻译文章。
- **规模**：从 10 篇起步，逐步扩展到数十篇。
- **核心原则**：结构不动、文本换语；Manifest 驱动增量；发布-审核模式。

---

## 工作流（5 步）

```
导出 EN 内容 → 翻译 → 创建目标语言文章 → 多语言插件关联 + 复制缩略图 → 验证
```

### Step 1：导出 EN 内容

用 WP-CLI 批量导出文章（支持多 ID），输出为本地 HTML 文件。

- 格式：`=====ID:{en_id}=====\n{title}\n---THUMB---\n{thumb_id}\n---CONTENT---\n{post_content}`
- 一次导出多篇文章，减少往返：把多个 `=====ID:n=====` 块合并到一个脚本里执行

### Step 2：翻译

- **保留 HTML 结构**：Gutenberg 注释（`<!-- wp:paragraph -->`）、class、链接、图片 src、锚点 ID
- **仅翻译可见文本**：不改变代码、标签、属性、URL、Schema JSON-LD
- **字符修正**：参考 `rules/translation-rules.md`
- **格式保留**：Gutenberg 文章保留 Gutenberg 注释；纯 HTML 文章保留纯 HTML

### Step 3：创建目标语言文章（PHP 脚本）

通过 `wp_insert_post` + 多语言插件 API + `set_post_thumbnail` 创建并关联。

```php
// 1. 读取翻译后的 HTML
$content = file_get_contents(__DIR__ . '/de_{en_id}.html');

// 2. 创建目标语言文章
$de_id = wp_insert_post(array(
    'post_title'   => $title,
    'post_content' => $content,
    'post_status'  => 'publish',
    'post_type'    => 'post',
    'post_author'  => 1,
), true);

// 3. 设置语言（替换为目标语言的 locale）
pll_set_post_language($de_id, '{locale}');

// 4. 建立翻译组
pll_save_post_translations(array('en' => $en_id, '{locale}' => $de_id));

// 5. 复制 Featured Image
set_post_thumbnail($de_id, get_post_thumbnail_id($en_id));
```

### Step 4：清理 + 验证

- 删除服务器上的临时 `.php`、`.html` 文件
- Playwright 自动化验证

---

## 关键技术决策

### 多语言插件关联机制

- **Language taxonomy**：`term_taxonomy_id`，源语言和目标语言各一个
- **翻译组**：`post_translations` taxonomy，term 的 `description` 字段存序列化数组 `a:2:{s:2:"en";i:<EN_ID>;s:2:"{locale}";i:<DE_ID>;}`
- **必须同时调用** `pll_set_post_language()` + `pll_save_post_translations()`，仅挂 `language` taxonomy 不够（语言切换器不显示）
- **菜单**：需在目标语言菜单中创建 `#pll_switcher` 菜单项

### WP-CLI 注意事项

| 坑 | 解决 |
|---|---|
| `--skip-plugins=polylang` 导致 `pll_*` 函数不存在 | 永远不要用 `--skip-plugins`，用 `wp eval-file script.php` |
| `wp eval '内联代码'` 引号被 PowerShell 剥离 | 一律用 `eval-file` 传文件 |
| CLI PHP 直接加载 WP + 多语言插件崩溃 | 同上，用 `wp eval-file` |

### 验证清单（自动化）

```js
// 访问 /{locale}/{slug}/ 后执行
const art = document.querySelector('article');
const html = art?.innerHTML || '';
const text = art?.innerText || '';
return {
  nnn:         (html.match(/nnnn/g)||[]).length === 0,   // 无 nnnn 乱码
  nnnShort:    (html.match(/nnn/g)||[]).length  === 0,   // 无 nnn 短乱码
  noCJK:       !/[\u4e00-\u9fff]/.test(text),            // 无中文字符泄漏
  noCorrupt:   !/—\d/.test(html),                        // 无 em-dash+数字脏数据
  imgs:        art?.querySelectorAll('img').length,       // 图片数量
  h2s:         art?.querySelectorAll('h2').length,        // h2 数量
  tables:      art?.querySelectorAll('table').length,      // 表格数量
  linkOk:      text.includes('目标产品分类'),              // 内部链接指向 /{locale}/...
};
```

---

## 常见坑与解决

### 1. "nnnn" 乱码
- **根因**：Gutenberg block 结构间字面字符 `n`（应为换行符）
- **修复**：前端 JS 清理脚本删除纯 `n` 文本节点 + 空白包裹的 n 序列；URL 中的 `n` 需数据库 `REPLACE`
- **教训**：前端 fix-n 脚本会跳过 URL 文本节点，URL 里的 `n` 必须数据库修

### 2. 中文/日文等字符泄漏
- **原因**：机翻残留或翻译时误粘贴
- **修复**：验证正则 `/[\u4e00-\u9fff]/` = false

### 3. 源数据脏值
- **现象**：EN 表格中含有 `—2`、`—0`、`—4` 等 em-dash+数字（实际是损坏值）
- **修复**：在目标语言翻译中重构正确值，验证 `/—\d/` = false
- **教训**：不要原样复制脏数据

### 4. 源文章原始 HTML 问题
- 部分 EN 文章是纯 HTML（无 Gutenberg 注释），部分含 Gutenberg 块
- **策略**：保留原格式，不强制转换

### 5. 翻译内容 typo
- 机翻或手写翻译可能带入 typo（如德语中 `Plattentärke` → `Plattenstärke`）
- **修复**：发布后人工抽检 + 验证阶段关键词核对

---

## 经验法则

1. **Source of Truth**：EN 是唯一人工维护源，其他语言是 build artifact
2. **Manifest 驱动**：维护翻译清单（EN_ID、DE_ID、状态、content_hash、翻译日期），增量检测只翻译新增/变更文章
3. **发布-审核**（publish-then-ratify）：机器翻译先上线，native speaker 事后标记确认
4. **遮蔽-恢复**：代码块、短代码、URL、frontmatter 键用占位符保护，翻译完恢复
5. **结构不动、文本换语**：绝不破坏原始 HTML 结构
6. **验证即门禁**：每篇发布必须过自动化验证清单（nnn=0、无 CJK、无脏数据、图片/表格/链接完整）
7. **进度即文档**：每篇发布即记录 DE_ID/日期/验证结果，换设备/换 AI 都能接上

---

## 可复用资产（模板）

### 目录结构

```
{your-repo}/
├── README.md                     # 本文件
├── manifest.md                   # 翻译清单（EN_ID, DE_ID, status, hash, date）
├── templates/
│   ├── export_content.php        # 批量导出 EN 内容模板
│   ├── create_de.php             # 创建目标语言文章模板（参数化）
│   └── verify.js                 # Playwright 验证脚本
├── rules/
│   ├── translation-rules.md      # 什么能翻什么不能翻
│   └── quality-gate.md           # 验证清单 + 回翻检测
└── adapters/
    └── agent-instructions.md     # 不同 AI 工具的接入说明
```

### manifest.md 格式建议

```markdown
| EN ID | EN Slug | DE ID | DE Slug | 状态 | 内容 Hash | 翻译日期 | 验证 |
|---|---|---|---|---|---|---|---|
| {en_id} | {en_slug} | {de_id} | {de_slug} | published | sha256:abc... | 2026-08-19 | ✅ |
```

### 翻译清单字段

- `EN ID`、`EN Title`、`EN Slug`
- `DE ID`、`DE Title`、`DE Slug`、`Thumb ID`
- `Status`：`pending` / `translating` / `published` / `needs_review`
- `Content Hash`：源内容 SHA-256，用于增量检测
- `Translated Date`、`Verified`（✅/❌）

---

## 新语言站点启动清单

1. 确认目标语言在多语言插件的 term_taxonomy_id
2. 确认目标语言菜单已有 `#pll_switcher` 项（或按模式新建）
3. 导出 8-10 篇高价值 EN 文章 → 批量翻译 → 逐篇发布验证
4. 首篇跑通全链路（导出→翻译→创建→关联→缩略图→验证），之后按流水线复制
5. 同步更新 manifest，每篇发布即记录
6. 同步更新 README/本文件

---

## 一句话心法

> **结构不动、文本换语；关联靠多语言插件两步走；验证靠正则跑自动；进度靠 manifest 传接力。**

---

## 更新历史

- 2026-09-03：初版。基于 WordPress 站点 EN→多语言翻译 15 篇文章的实践经验整理，已通用化处理。

---

## 许可

MIT。自由使用、修改、分发。
