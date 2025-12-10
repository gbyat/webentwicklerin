const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

// Get release type from command line argument (patch, minor, major)
const releaseType = process.argv[2] || 'patch';

if (!['patch', 'minor', 'major'].includes(releaseType)) {
    console.error('❌ Invalid release type. Use: patch, minor, or major');
    process.exit(1);
}

console.log(`🚀 Creating ${releaseType} release for webentwicklerin theme...`);

try {
    // Read current version
    const packagePath = path.join(__dirname, '..', 'package.json');
    const packageData = JSON.parse(fs.readFileSync(packagePath, 'utf8'));
    const currentVersion = packageData.version;

    // Bump version in package.json
    console.log(`⬆️  Bumping ${releaseType} version from ${currentVersion}...`);
    execSync(`npm version ${releaseType} --no-git-tag-version`, { stdio: 'inherit' });

    // Re-read new version
    const newPackageData = JSON.parse(fs.readFileSync(packagePath, 'utf8'));
    const newVersion = newPackageData.version;
    console.log(`✅ New version: ${newVersion}`);

    // Sync version to style.css and update CHANGELOG
    console.log('🔄 Syncing version to style.css...');
    execSync('node scripts/sync-version.js', { stdio: 'inherit' });

    // Build theme assets
    console.log('🔨 Building theme assets...');
    execSync('npm run build', { stdio: 'inherit' });

    // Add all changed files (package.json, style.css, CHANGELOG.md, README.md)
    console.log('📦 Adding all changes to git...');
    execSync('git add -A', { stdio: 'inherit' });

    // Build detailed commit message (Subject + Body from commits since last tag)
    console.log('📝 Preparing commit message from recent commits...');

    const COMMIT_SEPARATOR = '---COMMIT_SEPARATOR---';
    let lastTag = '';
    try {
        lastTag = execSync('git describe --tags --abbrev=0', {
            encoding: 'utf8',
            stdio: ['pipe', 'pipe', 'ignore'],
        }).trim();
    } catch (e) {
        lastTag = '';
    }

    let commitBody = '';
    try {
        const gitCommand = lastTag
            ? `git log ${lastTag}..HEAD --pretty=format:"%B${COMMIT_SEPARATOR}" --no-merges`
            : `git log -20 --pretty=format:"%B${COMMIT_SEPARATOR}" --no-merges`;

        const raw = execSync(gitCommand, {
            encoding: 'utf8',
            stdio: ['pipe', 'pipe', 'ignore'],
        }).trim();

        const commits = raw
            .split(COMMIT_SEPARATOR)
            .map((commit) => commit.trim())
            .filter((commit) => commit.length > 0)
            .filter((commit) => {
                const firstLine = commit.split('\n')[0].trim();
                return (
                    firstLine &&
                    !firstLine.match(/^Release v\d+\.\d+\.\d+$/i) &&
                    !firstLine.match(/^Bump version/i) &&
                    !firstLine.match(/^Version update$/i)
                );
            });

        if (commits.length > 0) {
            commitBody = commits
                .map((commit) => {
                    const lines = commit
                        .split('\n')
                        .map((l) => l.trim())
                        .filter((l) => l.length > 0);
                    const subject = lines[0];
                    const body = lines.slice(1);

                    if (body.length > 0) {
                        return `- ${subject}\n  ${body.join('\n  ')}`;
                    }
                    return `- ${subject}`;
                })
                .join('\n');
        }
    } catch (e) {
        commitBody = '';
    }

    // Commit all changes with generated message
    console.log('💾 Committing changes...');
    try {
        const tmpCommitMsg = path.join(__dirname, '..', 'tmp-release-commitmsg.txt');
        const fullMessage = commitBody
            ? `Release v${newVersion}\n\n${commitBody}\n`
            : `Release v${newVersion}\n`;
        fs.writeFileSync(tmpCommitMsg, fullMessage, 'utf8');

        execSync(`git commit -F "${tmpCommitMsg}"`, { stdio: 'inherit' });
        fs.unlinkSync(tmpCommitMsg);
    } catch (e) {
        console.log('ℹ️  Nothing to commit (that\'s okay)');
    }

    // Delete existing tag if it exists (local and remote)
    try {
        console.log('🗑️  Removing existing tag if it exists...');
        execSync(`git tag -d v${newVersion}`, { stdio: 'pipe' });
        execSync(`git push origin :refs/tags/v${newVersion}`, { stdio: 'pipe' });
    } catch (e) {
        // Tag doesn't exist, that's fine
    }

    // Create annotated tag
    console.log('🏷️  Creating tag...');
    execSync(`git tag -a "v${newVersion}" -m "Release v${newVersion}"`, { stdio: 'inherit' });

    // Detect current branch
    let branch = 'main';
    try {
        branch = execSync('git rev-parse --abbrev-ref HEAD', { encoding: 'utf8' }).trim();
    } catch (e) {
        // Fallback to main
    }

    // Check if remote repository exists
    console.log('🔍 Checking if GitHub repository exists...');
    try {
        execSync('git ls-remote origin', { stdio: 'pipe' });
        console.log('✅ Repository is accessible');
    } catch (e) {
        console.error('');
        console.error('❌ ==========================================');
        console.error('❌ ERROR: GitHub repository not found!');
        console.error('❌ ==========================================');
        console.error('');
        console.error('The remote repository does not exist or is not accessible.');
        console.error('');
        console.error('Please check:');
        console.error('  1. Does the repository exist on GitHub?');
        console.error('  2. Is the remote URL correct? (Check with: git remote -v)');
        console.error('  3. Do you have access to the repository?');
        console.error('');
        console.error('To remove the remote, use: git remote remove origin');
        console.error('To add a new remote, use: git remote add origin <url>');
        console.error('');
        process.exit(1);
    }

    // Push to GitHub
    console.log('⬆️  Pushing to GitHub...');
    console.log(`   Pushing branch: ${branch}`);
    console.log(`   Pushing tag: v${newVersion}`);

    execSync(`git push origin ${branch}`, { stdio: 'inherit' });
    execSync(`git push origin v${newVersion}`, { stdio: 'inherit' });

    console.log('');
    console.log('✅ ==========================================');
    console.log(`✅ Release v${newVersion} successfully created!`);
    console.log('✅ ==========================================');
    console.log('');
    console.log('🎉 GitHub Actions will now:');
    console.log('   1. Run CI/CD checks');
    console.log('   2. Create webentwicklerin.zip');
    console.log(`   3. Create GitHub Release v${newVersion}`);
    console.log('   4. Attach the ZIP file to the release');
    console.log('');
    console.log('🔗 Check progress at:');
    console.log('   https://github.com/gbyat/webentwicklerin/actions');
    console.log('   https://github.com/gbyat/webentwicklerin/releases');
    console.log('');

} catch (error) {
    console.error('❌ Error during release:', error.message);
    process.exit(1);
}

