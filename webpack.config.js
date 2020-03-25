var Encore = require("@symfony/webpack-encore");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It"s useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath("public/build/")
    // public path used by the web server to access the output path
    .setPublicPath("/build")
    // only needed for CDN"s or sub-directory deploy
    //.setManifestKeyPrefix("build/")

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that"s included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry("app", "./assets/js/app.js") // j'ai désactivé ça parce que c'est pas facile a repérer
    .addEntry("css/portfolio", "./assets/js/portfolio.js")
    .addEntry("scss/style", "./assets/scss/style.scss")
    .addEntry("css/bootstrap", "./assets/css/bootstrap.css")
    // .addEntry("js/theme", "./assets/js/theme.js") // pour la navbar
    .addEntry("js/navbar", "./assets/js/navbar.js") // pour la navbar
    //.addEntry("page2", "./assets/js/page2.js")
    /*
     * Les entrées CSS/JS de la partie admin
     */
    .addEntry("css/admin/theme", "./assets/admin/scss/theme.scss")
    .addEntry("css/admin/fontawesome-free", "./assets/admin/css/fontawesome-free/css/all.min.css")
    .addEntry("css/admin/icon-kit", "./assets/admin/css/icon-kit/dist/css/iconkit.min.css")

    .addEntry("js/admin/theme", "./assets/admin/js/theme.js")
    .addEntry("js/admin/popper", "./assets/admin/plugins/popper.js/dist/umd/popper.min.js")
    .addEntry("js/admin/bootstrap", "./assets/admin/plugins/bootstrap/dist/js/bootstrap.min.js")
    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you"re building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = "usage";
        config.corejs = 3;
    })
    .configureFilenames({
        images: "[path][name].[hash:8].[ext]",
    })
    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you"re having problems with a jQuery plugin
    //.autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry("admin", "./assets/js/admin.js")
;

module.exports = Encore.getWebpackConfig();
