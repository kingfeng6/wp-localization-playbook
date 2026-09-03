# Contributing

本仓库是 WordPress 多语言翻译工作流的经验总结和可复用模板。

## 如何贡献

1. **发现问题**：提交 Issue 描述问题
2. **改进模板**：Fork → 修改 → 提交 Pull Request
3. **补充规则**：在 `rules/` 下新建文件，更新 README 和 QUICKSTART

## 贡献范围

- ✅ `templates/` — 改进导出/创建/验证脚本
- ✅ `rules/` — 补充翻译规则、质量门禁
- ✅ `adapters/` — 新增 AI 工具适配说明
- ✅ `manifest.md` — 示例数据
- ✅ `README.md` / `QUICKSTART.md` — 文档改进
- ❌ 不接受具体业务的翻译内容（这是模板仓库）

## 格式要求

- Markdown 文件用 LF 换行
- PHP 文件用 4 空格缩进
- 脚本文件顶部必须有文件头注释（用途、参数、示例）
- 所有修改必须通过 `verify.js` 验证

## 版本管理

- 主分支：`main`（稳定）
- 更新流程：`feat/xxx` 分支 → PR → merge to main
- 每次更新 manifest.md 记录变更
