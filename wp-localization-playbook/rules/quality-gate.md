# 质量门禁

每篇翻译文章发布前必须通过自动化验证。

## 验证清单（Playwright）

```js
// 访问 /{locale}/{slug}/ 后执行
const art = document.querySelector('article');
const html = art?.innerHTML || '';
const text = art?.innerText || '';
return {
  nnn:         (html.match(/nnnn/g)||[]).length === 0,    // 无 nnnn 乱码
  nnnShort:    (html.match(/nnn[^n]/g)||[]).length === 0,  // 无 nnn 短乱码
  noCJK:       !/[\u4e00-\u9fff]/.test(text),             // 无中/日/韩文字符泄漏
  noCorrupt:   !/—\d/.test(html),                         // 无 em-dash+数字脏数据
  imgs:        art?.querySelectorAll('img').length,        // 图片数量 > 0
  h2s:         art?.querySelectorAll('h2').length,         // h2 数量 = 源文章
  tables:      art?.querySelectorAll('table').length,      // 表格数量 = 源文章
  lists:       art?.querySelectorAll('li').length,         // 列表项数量 = 源文章
  linkOk:      text.includes('目标产品分类'),              // 内部链接指向 /{locale}/...
  language:    document.documentElement.lang === '{locale}', // html lang 正确
};
```

## 回翻检测（可选，增强）

将目标语言文本回翻成 EN，与原文比对：
- 相同的技术术语、URL、数字 → 一致
- 明显漏翻的段落 → flag 并人工补翻
- 新增的无关内容 → flag

## 人工抽检（必做）

- 关键词一致性：同一术语全文翻译一致
- 数字/日期/货币格式符合目标语言习惯
- 无逐字硬译痕迹（语序、搭配自然）
- FAQ 问答格式正确（Q: / A: 标识）
- 表格内容完整（无合并单元格丢失）

## 发布流程

```
创建目标语言文章 → 自动化验证 → 人工抽检 → 发布 → 更新 manifest
```

## 发布-审核模式（publish-then-ratify）

- 机器翻译先上线（`publish` 状态）
- native speaker 事后标记 `ratified`
- 未审核的文章在语言切换器中显示提示 "Machine translation"
- 仅当所有文章被 `ratified` 后才视为完成
