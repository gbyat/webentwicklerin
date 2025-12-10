#!/usr/bin/env node

const fs = require('fs');
const path = require('path');

// File paths
const stylePath = path.join(__dirname, '..', 'style.css');
const packagePath = path.join(__dirname, '..', 'package.json');
const readmePath = path.join(__dirname, '..', 'README.md');

console.log('📊 Version Status Check\n');

try {
    // Check package.json
    if (fs.existsSync(packagePath)) {
        const packageData = JSON.parse(fs.readFileSync(packagePath, 'utf8'));
        console.log(`📦 package.json:     ${packageData.version}`);
    } else {
        console.log('❌ package.json:     NOT FOUND');
    }

    // Check style.css (theme header file)
    if (fs.existsSync(stylePath)) {
        const styleContent = fs.readFileSync(stylePath, 'utf8');

        // Extract version from header (case-insensitive)
        const headerMatch = styleContent.match(/Version:\s*(\d+\.\d+\.\d+)/i);
        if (headerMatch) {
            console.log(`🎨 style.css:        ${headerMatch[1]}`);
        } else {
            console.log('❌ style.css:        VERSION NOT FOUND');
        }
    } else {
        console.log('❌ style.css:         NOT FOUND');
    }

    // Check README.md stable tag
    if (fs.existsSync(readmePath)) {
        const readmeContent = fs.readFileSync(readmePath, 'utf8');
        const stableTagMatch = readmeContent.match(/\*\*Stable tag:\*\*\s*(\d+\.\d+\.\d+)/);
        if (stableTagMatch) {
            console.log(`📄 README.md:        ${stableTagMatch[1]}`);
        } else {
            console.log('❌ README.md:        STABLE TAG NOT FOUND');
        }
    } else {
        console.log('❌ README.md:         NOT FOUND');
    }

    // Check if versions are in sync
    console.log('\n🔍 Synchronization Check:');

    const versions = [];

    if (fs.existsSync(packagePath)) {
        const packageData = JSON.parse(fs.readFileSync(packagePath, 'utf8'));
        versions.push(packageData.version);
    }

    if (fs.existsSync(stylePath)) {
        const styleContent = fs.readFileSync(stylePath, 'utf8');
        const headerMatch = styleContent.match(/Version:\s*(\d+\.\d+\.\d+)/i);
        if (headerMatch) {
            versions.push(headerMatch[1]);
        }
    }

    if (fs.existsSync(readmePath)) {
        const readmeContent = fs.readFileSync(readmePath, 'utf8');
        const stableTagMatch = readmeContent.match(/\*\*Stable tag:\*\*\s*(\d+\.\d+\.\d+)/);
        if (stableTagMatch) {
            versions.push(stableTagMatch[1]);
        }
    }

    const uniqueVersions = [...new Set(versions)];

    if (uniqueVersions.length === 1) {
        console.log('✅ All versions are synchronized');
    } else if (uniqueVersions.length > 1) {
        console.log('❌ Versions are out of sync!');
        console.log('   Found versions:', uniqueVersions.join(', '));
        console.log('   Run: npm run sync-version to sync');
    } else {
        console.log('⚠️ No version information found');
    }

} catch (error) {
    console.error('❌ Error checking status:', error.message);
    process.exit(1);
}
