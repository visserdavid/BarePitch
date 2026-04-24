# Prompt: Add Git Workflow to CLAUDE.md

Append the following section to the existing `CLAUDE.md` file in the project root. Do not remove or modify any existing content — only add the new section at the end.

---

## Git Workflow

**Branch model**

```
main      stable, always runnable, production-ready
wip       daily development branch
spike/*   temporary experiments
backup/*  safety snapshots before risky changes
```

**Daily start**

```bash
git checkout wip
git status
```

**Before every session that touches existing code**

Create a checkpoint commit before making any changes:

```bash
git add .
git commit -m "checkpoint: before AI changes to <area>"
```

**Commit prefixes**

```
wip:        unfinished but useful progress
checkpoint: safe point before risky change
feat:       new working functionality
fix:        bug fix
refactor:   structure improvement without behavior change
security:   security improvement
docs:       documentation change
cleanup:    removal of temporary code or duplication
```

**After a feature works**

```bash
git add .
git commit -m "feat: <what now works>"
```

**Merge wip to main**

Only merge when: core flow works, no debug code remains, CSRF/auth/validation are present.

```bash
git checkout main
git merge wip
git tag v0.x.0
git push
git push --tags
git checkout wip
git merge main
```

**Version milestones**

```
v0.1.0  project structure and database connection
v0.2.0  authentication
v0.3.0  teams
v0.4.0  players
v0.5.0  matches
v0.6.0  attendance
v0.7.0  responsive cleanup
v0.8.0  security cleanup
v1.0.0  production-ready release
```

**GitHub CLI — useful commands**

```bash
gh repo view                  # open repository overview
gh issue list                 # list open issues
gh issue create               # create a new issue
gh issue close <number>       # close an issue
gh release create v0.x.0 --title "v0.x.0 <Title>" --notes "<Summary of changes>"
```

## Git Rules

These rules apply to every task in this project:

- Never commit `.env`, `*.log`, or anything inside `storage/logs/` or `storage/uploads/`
- Never commit directly to `main` — always work on `wip`
- Before modifying existing files, create a `checkpoint:` commit first
- After completing a feature, suggest the correct commit message using the prefix conventions above
- When a version milestone is complete, remind to: merge `wip` → `main`, create a version tag, push branch and tags
- When creating a GitHub release, use `gh release create` with a short summary of what changed
