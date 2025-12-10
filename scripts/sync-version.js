const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Read package.json
const packagePath = path.join(__dirname, '..', 'package.json');
const packageData = JSON.parse(fs.readFileSync(packagePath, 'utf8'));
const version = packageData.version;

console.log(`📦 Syncing version to ${version}...`);

// Read style.css (theme header file)
const stylePath = path.join(__dirname, '..', 'style.css');
let styleContent = fs.readFileSync(stylePath, 'utf8');

// Update version in style.css header (case-insensitive)
styleContent = styleContent.replace(
    /version:\s*\d+\.\d+\.\d+/i,
    `Version: ${version}`
);

// Write updated style.css
fs.writeFileSync(stylePath, styleContent);
console.log(`✅ Updated style.css`);

// Update README.md
const readmePath = path.join(__dirname, '..', 'README.md');
if (fs.existsSync(readmePath)) {
    let readmeContent = fs.readFileSync(readmePath, 'utf8');

    // Update Stable tag in README.md (WordPress.org format)
    readmeContent = readmeContent.replace(
        /\*\*Stable tag:\*\*\s*[\d\.]+/,
        `**Stable tag:** ${version}`
    );

    fs.writeFileSync(readmePath, readmeContent);
    console.log(`✅ Updated README.md`);
}

// Update CHANGELOG.md
const changelogPath = path.join(__dirname, '..', 'CHANGELOG.md');
if (!fs.existsSync(changelogPath)) {
    // Create initial CHANGELOG.md
    const initialContent = `# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [${version}] - ${new Date().toISOString().split('T')[0]}

### Added
- Initial release of webentwicklerin theme

`;
    fs.writeFileSync(changelogPath, initialContent);
    console.log(`📝 Created CHANGELOG.md`);
} else {
    let changelogContent = fs.readFileSync(changelogPath, 'utf8');

    // Check if this version already exists in changelog
    const versionPattern = new RegExp(`## \\[${version.replace(/\./g, '\\.')}\\]`);
    if (!versionPattern.test(changelogContent)) {
        // Get current date
        const dateStr = new Date().toISOString().split('T')[0];

        // Extract all commit messages already in CHANGELOG to avoid duplicates
        const existingCommits = new Set();
        const changelogLines = changelogContent.split('\n');
        changelogLines.forEach(line => {
            // Match lines that start with "- " (changelog entries)
            const match = line.match(/^-\s+(.+)$/);
            if (match) {
                const commitMsg = match[1].trim();
                existingCommits.add(commitMsg);
            }
        });

        // Get git commits since last tag with full messages (Subject + Body)
        let gitLog = '';
        try {
            // First, try to get the last tag
            let lastTag = '';
            try {
                lastTag = execSync('git describe --tags --abbrev=0', {
                    encoding: 'utf8',
                    stdio: ['pipe', 'pipe', 'ignore']
                }).trim();
            } catch (e) {
                // No tags yet, use all commits
                lastTag = '';
            }

            // Get commits since last tag with full messages (Subject + Body)
            // Use a unique separator to handle multiline commits
            const COMMIT_SEPARATOR = '---COMMIT_SEPARATOR---';
            const gitCommand = lastTag
                ? `git log ${lastTag}..HEAD --pretty=format:"%B${COMMIT_SEPARATOR}" --no-merges`
                : `git log -20 --pretty=format:"%B${COMMIT_SEPARATOR}" --no-merges`;

            let allCommitsRaw = execSync(gitCommand, {
                encoding: 'utf8',
                stdio: ['pipe', 'pipe', 'ignore']
            }).trim();

            // Split by separator and process each commit
            const allCommits = allCommitsRaw
                .split(COMMIT_SEPARATOR)
                .map(commit => commit.trim())
                .filter(commit => commit.length > 0)
                .filter(commit => {
                    // Filter out release commits and empty lines
                    const firstLine = commit.split('\n')[0].trim();
                    return firstLine &&
                        !firstLine.match(/^Release v\d+\.\d+\.\d+$/i) &&
                        !firstLine.match(/^Bump version/i) &&
                        !firstLine.match(/^Version update$/i);
                });

            // Filter out commits that are already in CHANGELOG
            const newCommits = allCommits.filter(commit => {
                const firstLine = commit.split('\n')[0].trim();
                return !existingCommits.has(firstLine);
            });

            // Format commits for changelog
            // For multiline commits: first line as main entry, body lines indented
            if (newCommits.length > 0) {
                gitLog = newCommits.map(commit => {
                    const lines = commit.split('\n').map(l => l.trim()).filter(l => l.length > 0);
                    const subject = lines[0];
                    const body = lines.slice(1);

                    if (body.length > 0) {
                        // Multiline commit: subject + indented body lines
                        return `- ${subject}\n  ${body.join('\n  ')}`;
                    } else {
                        // Single line commit
                        return `- ${subject}`;
                    }
                }).join('\n');
            } else {
                gitLog = '';
            }
        } catch (e) {
            // Fallback if git fails
            gitLog = '- Version update';
        }

        // Get unreleased changes if they exist
        const unreleasedMatch = changelogContent.match(/## \[Unreleased\]([\s\S]*?)(?=## \[|$)/);
        let unreleasedContent = '';
        if (unreleasedMatch && unreleasedMatch[1]) {
            unreleasedContent = unreleasedMatch[1].trim();
        }

        // Combine unreleased content and git log, prioritizing unreleased
        let changelogEntry = '';
        if (unreleasedContent) {
            changelogEntry = unreleasedContent;
        } else if (gitLog) {
            changelogEntry = gitLog;
        } else {
            // If no commits found, try to get commits from the last 20 commits
            try {
                const COMMIT_SEPARATOR = '---COMMIT_SEPARATOR---';
                const allCommitsRaw = execSync(`git log -20 --pretty=format:"%B${COMMIT_SEPARATOR}" --no-merges`, {
                    encoding: 'utf8',
                    stdio: ['pipe', 'pipe', 'ignore']
                }).trim();

                const allCommits = allCommitsRaw
                    .split(COMMIT_SEPARATOR)
                    .map(commit => commit.trim())
                    .filter(commit => commit.length > 0)
                    .filter(commit => {
                        const firstLine = commit.split('\n')[0].trim();
                        return firstLine &&
                            !firstLine.match(/^Release v\d+\.\d+\.\d+$/i) &&
                            !firstLine.match(/^Bump version/i) &&
                            !firstLine.match(/^Version update$/i);
                    });

                const newCommits = allCommits.filter(commit => {
                    const firstLine = commit.split('\n')[0].trim();
                    return !existingCommits.has(firstLine);
                });

                if (newCommits.length > 0) {
                    changelogEntry = newCommits.slice(0, 10).map(commit => {
                        const lines = commit.split('\n').map(l => l.trim()).filter(l => l.length > 0);
                        const subject = lines[0];
                        const body = lines.slice(1);

                        if (body.length > 0) {
                            return `- ${subject}\n  ${body.join('\n  ')}`;
                        } else {
                            return `- ${subject}`;
                        }
                    }).join('\n');
                } else {
                    changelogEntry = '- Version update';
                }
            } catch (e) {
                changelogEntry = '- Version update';
            }
        }

        // Create new changelog entry
        const newEntry = `## [${version}] - ${dateStr}

${changelogEntry}

`;

        // Insert after the first heading (main title)
        const lines = changelogContent.split('\n');
        const firstHeadingIndex = lines.findIndex(line => line.startsWith('## ['));

        if (firstHeadingIndex !== -1) {
            lines.splice(firstHeadingIndex, 0, newEntry);
            changelogContent = lines.join('\n');
        } else {
            // No existing entries, add after main heading
            changelogContent = changelogContent.replace(
                /(# Changelog.*?\n\n)/s,
                `$1${newEntry}`
            );
        }

        // Get repository URL for release link
        let repoUrl = '';
        try {
            const remoteUrl = execSync('git remote get-url origin', {
                encoding: 'utf8',
                stdio: ['pipe', 'pipe', 'ignore']
            }).trim();
            // Convert SSH to HTTPS if needed
            if (remoteUrl.includes('github.com')) {
                repoUrl = remoteUrl
                    .replace(/git@github\.com:/, 'https://github.com/')
                    .replace(/\.git$/, '');
            }
        } catch (e) {
            // Could not determine repo URL
        }

        // Add release link at the bottom if it doesn't exist and we have a repo URL
        if (repoUrl && !changelogContent.includes(`[${version}]:`)) {
            const releaseLink = `\n[${version}]: ${repoUrl}/releases/tag/v${version}\n`;
            changelogContent = changelogContent.trim() + releaseLink;
        }

        fs.writeFileSync(changelogPath, changelogContent);
        console.log(`📝 Updated CHANGELOG.md with version ${version}`);
    } else {
        console.log(`ℹ️  Version ${version} already exists in CHANGELOG.md`);
    }
}

console.log(`✅ Version synchronized to ${version}`);

