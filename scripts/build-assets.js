#!/usr/bin/env node

'use strict';

var fs = require('fs');
var path = require('path');
var esbuild = require('esbuild');
var sass = require('sass');

function ensureDir(dirPath) {
    if (!fs.existsSync(dirPath)) {
        fs.mkdirSync(dirPath, { recursive: true });
    }
}

function removeFileIfExists(filePath) {
    if (fs.existsSync(filePath)) {
        fs.unlinkSync(filePath);
        return true;
    }

    return false;
}

function parseArgs(argv) {
    var mode = 'dev';
    var watch = false;
    var i;

    for (i = 2; i < argv.length; i++) {
        if (argv[i] === 'dev' || argv[i] === 'prod') {
            mode = argv[i];
        } else if (argv[i] === '--watch') {
            watch = true;
        }
    }

    return { mode: mode, watch: watch };
}

function readJson(filePath) {
    var raw = fs.readFileSync(filePath, 'utf8');
    return JSON.parse(raw);
}

function ensureString(value, label) {
    if (typeof value !== 'string' || value.trim() === '') {
        throw new Error('Ungültiger Eintrag in package.json: ' + label);
    }

    return value;
}

function getBuildPaths(projectRoot) {
    var packagePath = path.join(projectRoot, 'package.json');
    var pkg;
    var sourceJs;
    var sourceCss;
    var targetJs;
    var targetCss;

    if (!fs.existsSync(packagePath)) {
        throw new Error('package.json nicht gefunden: ' + packagePath);
    }

    pkg = readJson(packagePath);

    if (!pkg.source || typeof pkg.source !== 'object') {
        throw new Error('package.json enthält keinen gültigen "source"-Block');
    }

    if (!pkg.target || typeof pkg.target !== 'object') {
        throw new Error('package.json enthält keinen gültigen "target"-Block');
    }

    sourceJs = ensureString(pkg.source.js, 'source.js');
    sourceCss = ensureString(pkg.source.css, 'source.css');
    targetJs = ensureString(pkg.target.js, 'target.js');
    targetCss = ensureString(pkg.target.css, 'target.css');

    return {
        source: {
            js: path.resolve(projectRoot, sourceJs),
            css: path.resolve(projectRoot, sourceCss)
        },
        target: {
            js: path.resolve(projectRoot, targetJs),
            css: path.resolve(projectRoot, targetCss)
        }
    };
}

function toRelativePath(projectRoot, filePath) {
    return path.relative(projectRoot, filePath);
}

function logBuiltFiles(headline, files, projectRoot) {
    var i;

    if (!files || files.length === 0) {
        console.log(headline + ': Keine Ausgabedateien erzeugt.');
        return;
    }

    console.log(headline + ':');

    for (i = 0; i < files.length; i++) {
        console.log(' - ' + toRelativePath(projectRoot, files[i]));
    }
}

function listTopLevelFiles(dirPath, extension, excludeLeadingUnderscore) {
    var entries;
    var result;

    if (!fs.existsSync(dirPath)) {
        return [];
    }

    entries = fs.readdirSync(dirPath, { withFileTypes: true });
    result = entries
        .filter(function filterEntry(entry) {
            if (!entry.isFile()) {
                return false;
            }

            if (path.extname(entry.name) !== extension) {
                return false;
            }

            if (excludeLeadingUnderscore && entry.name.charAt(0) === '_') {
                return false;
            }

            return true;
        })
        .map(function mapEntry(entry) {
            return path.join(dirPath, entry.name);
        })
        .sort();

    return result;
}

function listFilesRecursiveByExtension(dirPath, extension) {
    var result = [];
    var entries;
    var i;
    var fullPath;

    if (!fs.existsSync(dirPath)) {
        return result;
    }

    entries = fs.readdirSync(dirPath, { withFileTypes: true });

    for (i = 0; i < entries.length; i++) {
        fullPath = path.join(dirPath, entries[i].name);

        if (entries[i].isDirectory()) {
            result = result.concat(listFilesRecursiveByExtension(fullPath, extension));
        } else if (entries[i].isFile() && path.extname(entries[i].name) === extension) {
            result.push(fullPath);
        }
    }

    return result.sort();
}

function listSubdirectoriesRecursive(dirPath) {
    var result = [];
    var entries;
    var i;
    var fullPath;

    if (!fs.existsSync(dirPath)) {
        return result;
    }

    entries = fs.readdirSync(dirPath, { withFileTypes: true });

    for (i = 0; i < entries.length; i++) {
        if (entries[i].isDirectory()) {
            fullPath = path.join(dirPath, entries[i].name);
            result.push(fullPath);
            result = result.concat(listSubdirectoriesRecursive(fullPath));
        }
    }

    return result;
}

function removeSourceMapFiles(dirPath) {
    var mapFiles;
    var removedFiles = [];
    var i;

    if (!fs.existsSync(dirPath)) {
        return removedFiles;
    }

    mapFiles = listFilesRecursiveByExtension(dirPath, '.map');

    for (i = 0; i < mapFiles.length; i++) {
        if (removeFileIfExists(mapFiles[i])) {
            removedFiles.push(mapFiles[i]);
        }
    }

    return removedFiles;
}

function cleanupProdSourceMaps(buildPaths) {
    return {
        js: removeSourceMapFiles(buildPaths.target.js),
        css: removeSourceMapFiles(buildPaths.target.css)
    };
}

function getJsEntryPoints(buildPaths) {
    return listTopLevelFiles(buildPaths.source.js, '.js', false);
}

function getScssEntryPoints(buildPaths) {
    return listTopLevelFiles(buildPaths.source.css, '.scss', true);
}

function getJsOutputFiles(entryPoints, buildPaths) {
    var result = [];
    var i;
    var fileName;

    for (i = 0; i < entryPoints.length; i++) {
        fileName = path.basename(entryPoints[i], '.js');
        result.push(path.join(buildPaths.target.js, fileName + '.js'));
    }

    return result;
}

function buildJs(mode, buildPaths) {
    var isProd = mode === 'prod';
    var entryPoints = getJsEntryPoints(buildPaths);
    var outputFiles = getJsOutputFiles(entryPoints, buildPaths);

    if (entryPoints.length === 0) {
        console.log('Keine JS-Dateien im konfigurierten Quellverzeichnis gefunden.');
        return Promise.resolve([]);
    }

    return esbuild.build({
        entryPoints: entryPoints,
        bundle: true,
        sourcemap: isProd ? false : true,
        minify: isProd ? true : false,
        format: 'iife',
        target: ['es2018'],
        outdir: buildPaths.target.js,
        entryNames: '[name]'
    }).then(function onBuildDone() {
        return outputFiles;
    });
}

function compileSingleScssFile(filePath, mode, buildPaths) {
    var isProd = mode === 'prod';
    var fileName = path.basename(filePath, '.scss');
    var outFile = path.join(buildPaths.target.css, fileName + '.css');
    var result;
    var writtenFiles = [];

    result = sass.compile(filePath, {
        style: isProd ? 'compressed' : 'expanded',
        sourceMap: isProd ? false : true,
        sourceMapIncludeSources: true
    });

    fs.writeFileSync(outFile, result.css);
    writtenFiles.push(outFile);

    if (!isProd && result.sourceMap) {
        fs.writeFileSync(outFile + '.map', JSON.stringify(result.sourceMap));
        writtenFiles.push(outFile + '.map');
    } else {
        removeFileIfExists(outFile + '.map');
    }

    return writtenFiles;
}

function buildCss(mode, buildPaths) {
    var entryPoints = getScssEntryPoints(buildPaths);
    var i;
    var writtenFiles = [];

    if (entryPoints.length === 0) {
        console.log('Keine SCSS-Dateien im konfigurierten Quellverzeichnis gefunden.');
        return [];
    }

    for (i = 0; i < entryPoints.length; i++) {
        writtenFiles = writtenFiles.concat(
            compileSingleScssFile(entryPoints[i], mode, buildPaths)
        );
    }

    return writtenFiles;
}

function buildCssAsync(mode, buildPaths) {
    return new Promise(function buildCssAsyncExecutor(resolve, reject) {
        try {
            resolve(buildCss(mode, buildPaths));
        } catch (err) {
            reject(err);
        }
    });
}

function runOnce(mode, buildPaths) {
    var removedSourceMaps = {
        js: [],
        css: []
    };

    ensureDir(buildPaths.target.js);
    ensureDir(buildPaths.target.css);

    if (mode === 'prod') {
        removedSourceMaps = cleanupProdSourceMaps(buildPaths);
    }

    return buildJs(mode, buildPaths)
        .then(function onJsBuilt(jsFiles) {
            return buildCssAsync(mode, buildPaths).then(function onCssBuilt(cssFiles) {
                return {
                    js: jsFiles,
                    css: cssFiles,
                    removedSourceMaps: removedSourceMaps
                };
            });
        });
}

function createJsWatchContext(buildPaths) {
    var entryPoints = getJsEntryPoints(buildPaths);

    if (entryPoints.length === 0) {
        console.log('Keine JS-Dateien im konfigurierten Quellverzeichnis gefunden.');
        return Promise.resolve(null);
    }

    return esbuild.context({
        entryPoints: entryPoints,
        bundle: true,
        sourcemap: true,
        minify: false,
        format: 'iife',
        target: ['es2018'],
        outdir: buildPaths.target.js,
        entryNames: '[name]'
    }).then(function onContextCreated(ctx) {
        return ctx.watch().then(function onWatchStarted() {
            return ctx;
        });
    });
}

function debounce(fn, wait) {
    var timeoutId = null;

    return function debounced() {
        var args = arguments;
        var context = this;

        if (timeoutId) {
            clearTimeout(timeoutId);
        }

        timeoutId = setTimeout(function runDebounced() {
            timeoutId = null;
            fn.apply(context, args);
        }, wait);
    };
}

function watchDirectory(filePath, listener) {
    if (!fs.existsSync(filePath)) {
        return null;
    }

    return fs.watch(filePath, listener);
}

function runWatch(mode, buildPaths, projectRoot) {
    var currentJsContext = null;
    var sassWatchers = [];
    var jsDirWatcher = null;

    function closeWatchers(watchers) {
        var i;

        for (i = 0; i < watchers.length; i++) {
            watchers[i].close();
        }
    }

    function rebuildCssLogged() {
        return buildCssAsync(mode, buildPaths)
            .then(function onCssBuilt(cssFiles) {
                logBuiltFiles('SCSS neu kompiliert', cssFiles, projectRoot);
                return true;
            })
            .catch(function onCssError(err) {
                console.error(err);
            });
    }

    function rebuildJsContext() {
        var oldContext = currentJsContext;
        var entryPoints = getJsEntryPoints(buildPaths);
        var outputFiles = getJsOutputFiles(entryPoints, buildPaths);

        return Promise.resolve()
            .then(function disposeOldContext() {
                if (oldContext) {
                    return oldContext.dispose();
                }

                return true;
            })
            .then(function createNewContext() {
                return createJsWatchContext(buildPaths);
            })
            .then(function storeContext(ctx) {
                currentJsContext = ctx;
                logBuiltFiles('JS-Watch-Kontext aktualisiert', outputFiles, projectRoot);
                return true;
            })
            .catch(function onJsWatchError(err) {
                console.error(err);
            });
    }

    function setupSassWatchers() {
        var dirs = [buildPaths.source.css]
            .concat(listSubdirectoriesRecursive(buildPaths.source.css));
        var i;
        var watcher;

        closeWatchers(sassWatchers);
        sassWatchers = [];

        for (i = 0; i < dirs.length; i++) {
            watcher = watchDirectory(
                dirs[i],
                debounce(function onSassFsEvent() {
                    rebuildCssLogged().then(function afterCssBuild() {
                        setupSassWatchers();
                    });
                }, 100)
            );

            if (watcher) {
                sassWatchers.push(watcher);
            }
        }
    }

    function onJsDirEvent(eventType, filename) {
        if (!filename) {
            return;
        }

        if (path.extname(filename) !== '.js') {
            return;
        }

        if (eventType === 'rename') {
            rebuildJsContext();
        }
    }

    ensureDir(buildPaths.target.js);
    ensureDir(buildPaths.target.css);

    return runOnce(mode, buildPaths)
        .then(function onInitialBuildDone(result) {
            logBuiltFiles('JS gebaut', result.js, projectRoot);
            logBuiltFiles('CSS gebaut', result.css, projectRoot);
            return createJsWatchContext(buildPaths);
        })
        .then(function onJsWatchContextReady(ctx) {
            currentJsContext = ctx;

            jsDirWatcher = watchDirectory(
                buildPaths.source.js,
                onJsDirEvent
            );

            setupSassWatchers();

            console.log('Watch-Modus aktiv (JS + SCSS).');

            return {
                jsContext: currentJsContext,
                jsDirWatcher: jsDirWatcher,
                sassWatchers: sassWatchers
            };
        });
}

function main() {
    var args = parseArgs(process.argv);
    var projectRoot = process.cwd();
    var buildPaths = getBuildPaths(projectRoot);

    if (args.watch) {
        return runWatch('dev', buildPaths, projectRoot).then(function onWatchStarted() {
            console.log('Erledigt. Watch-Modus läuft.');
            return true;
        });
    }

    return runOnce(args.mode, buildPaths).then(function onRunOnceDone(result) {
        if (args.mode === 'prod') {
            logBuiltFiles('JS-Sourcemaps entfernt', result.removedSourceMaps.js, projectRoot);
            logBuiltFiles('CSS-Sourcemaps entfernt', result.removedSourceMaps.css, projectRoot);
        }

        logBuiltFiles('JS gebaut', result.js, projectRoot);
        logBuiltFiles('CSS gebaut', result.css, projectRoot);
        console.log('Erledigt. Assets wurden erfolgreich gebaut.');
        return true;
    });
}

main().catch(function onError(err) {
    console.error('Fehler beim Build der Assets.');
    console.error(err);
    process.exit(1);
});