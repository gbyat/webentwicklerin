const gulp = require('gulp');
const clean = require('gulp-clean');
const cleanCSS = require('gulp-clean-css');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
const cssnano = require('gulp-cssnano');
const rename = require('gulp-rename');
const concat = require('gulp-concat');
const wpPot = require('gulp-wp-pot');
const log = require('fancy-log');
// const Rsync = require('rsync');
const { exec } = require('child_process');
/* const fs = require('fs');
const path = require('path');
const crypto = require('crypto'); */

const web_path = "C:\\inetpub\\wwwroot\\wp_webentwicklerin\\wp-content\\themes\\";
const thisname = 'webentwicklerin';
const themedir = web_path + thisname;
const storage = "/home/www/wp-webentwicklerin/wp-content/themes/" + thisname;

var globs = [
    './assets/**/*',
    './inc/**/*',
    './parts/**/*',
    './patterns/**/*',
    './templates/**/*',
    './languages/**/*',
    './styles/**/*',
    'functions.php',
    'screenshot.png',
    'theme.json',
    'style.css'
];

gulp.task('potfile', function () {
    var translatePath = './languages/';
    return gulp.src('./**/*.php')
        .pipe(wpPot({ domain: thisname, package: thisname }))
        .pipe(gulp.dest(translatePath + '/' + thisname + '.pot'));
});

gulp.task('watch', function () {
    gulp.watch(['./scss/**/*.scss']).on(
        'change',
        gulp.series(
            'mergecss',
            'mergeeditorcss',
            'css_minify'
        )
    );
});

gulp.task('mergecss', function () {
    var srce = "./scss/**/*.scss";
    var dst = "./assets/css/";
    return gulp.src(srce)
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('style.css'))
        .pipe(autoprefixer())
        .pipe(gulp.dest(dst));
});

gulp.task('mergeeditorcss', function () {
    var srce = "./scss-editor/**/*.scss";
    var dst = "./assets/css/";
    return gulp.src(srce)
        .pipe(sass().on('error', sass.logError))
        .pipe(concat('editor-style.css'))
        .pipe(autoprefixer())
        .pipe(gulp.dest(dst));
});

gulp.task('css_minify', function () {
    return gulp.src(['./assets/css/style.css', './assets/css/editor-style.css'])
        .pipe(rename({ suffix: ".min" }))
        .pipe(cssnano({ zindex: false }))
        .pipe(gulp.dest('./assets/css'));
});

// Neue Deploy-Task mit rsync
/* gulp.task('deploy', function (cb) {
    const rsync = new Rsync()
        .executable('C:/cygwin64/bin/rsync.exe')  // Using forward slashes
        .shell('C:/cygwin64/bin/ssh.exe')         // Explicitly set SSH path
        .flags('avzP')
        .source('.')
        .destination(`web73@s137.goserver.host:${storage}`)
        .exclude('node_modules')
        .exclude('.git')
        .exclude('.git*')
        .exclude('*.log')
        .exclude('.DS_Store')
        .exclude('package*.json')
        .exclude('gulpfile.js')
        .exclude('.*')
        .option('e', `C:/cygwin64/bin/ssh.exe -i "${process.env.USERPROFILE}/.ssh/id_rsa"`);

    // Log the command
    console.log('Executing rsync command:');
    console.log(rsync.command());

    rsync.execute(function (error, code, cmd) {
        if (error) {
            console.error('Error:', error);
            console.error('Exit Code:', code);
            console.error('Command:', cmd);
        } else {
            console.log('Deployment completed successfully.');
        }
        cb(error);
    });
}); */

// Funktion zum Berechnen des MD5-Hashes einer Datei
function calculateFileHash(filePath) {
    try {
        const fileBuffer = fs.readFileSync(filePath);
        const hashSum = crypto.createHash('md5');
        hashSum.update(fileBuffer);
        return hashSum.digest('hex');
    } catch (err) {
        return null;
    }
}

// Funktion zum Speichern/Laden der Hashes
const hashFile = '.deploy-hashes.json';
function saveHashes(hashes) {
    fs.writeFileSync(hashFile, JSON.stringify(hashes, null, 2));
}

function loadHashes() {
    try {
        return JSON.parse(fs.readFileSync(hashFile));
    } catch (err) {
        return {};
    }
}

/* // Verbesserte Deploy task
gulp.task('deploy', function (cb) {
    const files = [
        'assets', 'inc', 'parts', 'patterns', 'templates',
        'languages', 'styles', 'functions.php',
        'screenshot.png', 'theme.json', 'style.css'
    ];

    console.log('Starting deployment...');
    const previousHashes = loadHashes();
    const currentHashes = {};
    const changedFiles = [];

    // Prüfe welche Dateien sich geändert haben
    files.forEach(file => {
        if (fs.existsSync(file)) {
            if (fs.lstatSync(file).isDirectory()) {
                // Für Verzeichnisse: Rekursiv alle Dateien prüfen
                function checkDirectory(dir) {
                    fs.readdirSync(dir).forEach(item => {
                        const fullPath = path.join(dir, item);
                        if (fs.lstatSync(fullPath).isDirectory()) {
                            checkDirectory(fullPath);
                        } else {
                            const hash = calculateFileHash(fullPath);
                            currentHashes[fullPath] = hash;
                            if (hash !== previousHashes[fullPath]) {
                                changedFiles.push(dir);
                            }
                        }
                    });
                }
                checkDirectory(file);
            } else {
                // Für einzelne Dateien
                const hash = calculateFileHash(file);
                currentHashes[file] = hash;
                if (hash !== previousHashes[file]) {
                    changedFiles.push(file);
                }
            }
        }
    });

    // Entferne Duplikate
    const uniqueChangedFiles = [...new Set(changedFiles)];

    if (uniqueChangedFiles.length === 0) {
        console.log('No changes detected. Skipping deployment.');
        cb();
        return;
    }

    console.log('Changed files/directories:', uniqueChangedFiles);

    // Erstelle SCP commands nur für geänderte Dateien
    const scpCommands = uniqueChangedFiles.map(file => {
        return new Promise((resolve, reject) => {
            const cmd = `scp -r "${file}" web73@s137.goserver.host:${storage}/`;
            console.log(`Deploying: ${file}`);

            exec(cmd, (error, stdout, stderr) => {
                if (error) {
                    console.error(`Error deploying ${file}: ${error}`);
                    reject(error);
                    return;
                }
                if (stdout) console.log(stdout);
                if (stderr) console.log(stderr);
                console.log(`Successfully deployed ${file}`);
                resolve();
            });
        });
    });

    // Führe die SCP commands aus
    Promise.all(scpCommands)
        .then(() => {
            console.log('Deployment completed successfully!');
            // Speichere neue Hashes
            saveHashes(currentHashes);
            cb();
        })
        .catch((error) => {
            console.error('Deployment failed:', error);
            cb(error);
        });
});
 */


// Server Konfigurationen
const servers = {
    production: {
        host: 'web73@s137.goserver.host',
        path: '/home/www/wp-webentwicklerin/wp-content/themes/webentwicklerin',
        port: 22,
        minDeployInterval: 5 * 60 * 1000  // 5 Minuten in Millisekunden
    },
    test: {
        host: 'ssh-w0156460@w0156460.kasserver.com',
        path: '/www/htdocs/w0156460/webentwicklerin/wp-content/themes/webentwicklerin'
    }
};

// Variable für den Zeitpunkt des letzten Deployments
let lastDeployTime = 0;

gulp.task('deploy', function (cb) {
    let server = 'production'; // Default
    if (process.env.SERVER) {
        const requestedServer = process.env.SERVER.trim();
        if (servers[requestedServer]) {
            server = requestedServer;
        } else {
            console.error(`Warnung: Server "${requestedServer}" nicht gefunden, verwende Production.`);
        }
    }

    console.log(`Using server configuration: ${server}`);

    const now = Date.now();
    const timeSinceLastDeploy = now - lastDeployTime;

    if (servers[server].minDeployInterval && timeSinceLastDeploy < servers[server].minDeployInterval) {
        const waitTime = Math.ceil((servers[server].minDeployInterval - timeSinceLastDeploy) / 1000 / 60);
        console.error(`Bitte warte noch ${waitTime} Minuten bis zum nächsten Deploy.`);
        cb(new Error('Deploy-Intervall noch nicht erreicht'));
        return;
    }

    const wslSource = process.cwd().replace(/\\/g, '/').replace(/^(\w):/, '/mnt/$1').toLowerCase();
    const portOption = servers[server].port ? `-p ${servers[server].port}` : '';
    const rsyncCommand = `wsl rsync -avz --delete --delete-excluded --force --progress --timeout=60 -e "ssh ${portOption}" --exclude=node_modules --exclude=.git --exclude=*.log --exclude=.DS_Store --exclude=package*.json --exclude=gulpfile.js --exclude=scss --exclude=scss-editor --exclude=.* "${wslSource}/" "${servers[server].host}:${servers[server].path}/"`;

    console.log('Starting deployment...');
    console.log(`Target: ${servers[server].host}`);
    console.log('Command:', rsyncCommand);

    exec(rsyncCommand, { maxBuffer: 1024 * 1024 * 10 }, function (error, stdout, stderr) {
        if (error) {
            console.error('Error:', error);
            if (stderr) console.error('stderr:', stderr);
            cb(error);
            return;
        }
        console.log(stdout);
        console.log('Deployment completed successfully!');
        lastDeployTime = Date.now();
        cb();
    });
});

/**
 * $env:SERVER="test"; gulp deploy
 * $env:SERVER="" setzt variable zurück
 */

// Dry-Run Task
gulp.task('deploydry', function (cb) {
    const wslSource = process.cwd().replace(/\\/g, '/').replace(/^(\w):/, '/mnt/$1').toLowerCase();
    const rsyncCommand = `wsl rsync -avzn --delete --progress --exclude='node_modules' --exclude='.git*' --exclude='*.log' --exclude='.DS_Store' --exclude='package*.json' --exclude='gulpfile.js' --exclude='.*' "${wslSource}/" web73@s137.goserver.host:${storage}/`;

    console.log('Simulating deployment (dry-run)...');
    exec(rsyncCommand, { maxBuffer: 1024 * 1024 * 10 }, function (error, stdout, stderr) {
        if (error) {
            console.error('Error:', error);
            if (stderr) console.error('stderr:', stderr);
            cb(error);
            return;
        }
        console.log(stdout);
        console.log('Dry run completed. No files were actually transferred.');
        cb();
    });
});


// Simple rsync test task
gulp.task('testrsync', function (cb) {
    const server = process.env.SERVER || 'production';

    // Einfacher rsync Befehl mit minimalen Optionen
    const rsyncCommand = `wsl rsync -av ./style.css "${servers[server].host}:${servers[server].path}/test.css"`;

    console.log('Testing rsync connection...');
    console.log(`Target: ${servers[server].host}`);
    console.log('Command:', rsyncCommand);

    exec(rsyncCommand, function (error, stdout, stderr) {
        if (error) {
            console.error('Error:', error);
            if (stderr) console.error('stderr:', stderr);
            cb(error);
            return;
        }
        console.log(stdout);
        console.log('Test completed successfully!');
        cb();
    });
});

/* 
// Actual Deploy Task
gulp.task('deploy', function (cb) {
    const wslSource = process.cwd().replace(/\\/g, '/').replace(/^(\w):/, '/mnt/$1').toLowerCase();
    const rsyncCommand = `wsl rsync -avz --delete --progress --exclude='node_modules' --exclude='.git*' --exclude='*.log' --exclude='.DS_Store' --exclude='package*.json' --exclude='gulpfile.js' --exclude='.*' "${wslSource}/" web73@s137.goserver.host:${storage}/`;

    console.log('Starting deployment...');
    exec(rsyncCommand, { maxBuffer: 1024 * 1024 * 10 }, function (error, stdout, stderr) {
        if (error) {
            console.error('Error:', error);
            if (stderr) console.error('stderr:', stderr);
            cb(error);
            return;
        }
        console.log(stdout);
        console.log('Deployment completed successfully!');
        cb();
    });
}); */


gulp.task('clean-blocks', function () {
    return gulp.src('assets/css/blocks/*.min.css', {
        read: false,
        allowEmpty: true,
    })
        .pipe(clean());
});

gulp.task('minify-blocks', function () {
    return gulp.src('assets/css/blocks/*.css')
        .pipe(cssnano({ zindex: false }))
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest('assets/css/blocks'));
});

gulp.task('deploy-locl', function () {
    return gulp.src(globs, { base: ".", buffer: false })
        .pipe(gulp.dest(themedir));
});


// Simple SSH connection test
gulp.task('testconnection', function (cb) {
    const server = process.env.SERVER || 'production';

    const sshCommand = `wsl ssh ${servers[server].host} "pwd"`;

    console.log('Testing SSH connection...');
    console.log(`Target: ${servers[server].host}`);
    console.log('Command:', sshCommand);

    exec(sshCommand, function (error, stdout, stderr) {
        if (error) {
            console.error('Connection Error:', error);
            if (stderr) console.error('stderr:', stderr);
            cb(error);
            return;
        }
        console.log('Connection successful!');
        console.log('Current remote directory:', stdout);
        cb();
    });
});



// Update exports
exports.default = gulp.series(
    'mergecss',
    'mergeeditorcss',
    'potfile',
    'css_minify',
    'clean-blocks',
    'minify-blocks',
    'deploy-locl'
);

exports.deploy = gulp.series('deploy');
exports.dry = gulp.series('deploydry');

