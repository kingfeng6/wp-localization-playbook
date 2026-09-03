# 翻译规则

## 什么能翻
- 所有面向读者的可见文本（标题、正文、列表项、表格单元格、FAQ 问答、CTA 按钮）
- 图片 alt 文本
- 链接锚文本（但保留 URL 目标）
- 前端显示的 meta 描述 / SEO 文本（如果有）

## 什么不能翻（必须保留原样）
- Gutenberg 块注释：`<!-- wp:paragraph -->`、`<!-- /wp:paragraph -->`
- HTML 标签和属性：`class`、`id`、`src`、`href`、`alt`（保留值中的英文）、`data-*`
- 短代码：`[shortcode]`、`{{< shortcode >}}`
- 代码块：围栏代码、inline 代码、Liquid 标签
- Frontmatter 键：`title`、`date`、`draft`、`weight`、`tags`、`slug`、`lang` 等
- Schema JSON-LD 块（不翻 `Organization`、`Person`、`Brand`、`URL`、`@id`、`sameAs` 等）
- 图片/附件 URL、文件路径
- 数字、日期、货币、度量衡（按目标语言习惯转换格式，但值不变）
- 品牌名、产品名、机构名、人名
- 技术术语（除非目标语言有公认译法）
- 内部链接锚文本中的路径部分

## 格式保留
- Gutenberg 文章：保留所有 `<!-- wp:* -->` 注释
- 纯 HTML 文章：保留 `<h2>`、`<img>`、`<strong>` 等标签
- 不改变 HTML 结构、class 名、锚点 ID
- 不改变链接目标 URL

## 字符修正
- `鈥` → `—`（em dash）
- `碌m` / `渭m` → `μm`（微米）
- 中文全角标点 → 半角（用于技术文本）
- `'` → `'`（直单引号）
- `"` → `"`（直双引号）

## 语言特定规则
- 目标语言名词首字母大写规则（如德语）
- 复合词写在一起（如德语）
- 标点后空格规则（如法语）
- 数字格式：千位分隔符按目标语言习惯（如德语 `.`、法语 `,`）
- 日期格式：按目标语言习惯（TT.MM.JJJJ、JJ/MM/AAAA 等）
