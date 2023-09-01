const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .addEntry('editor', './assets/editor.js')
    .addEntry('style', './assets/style.js')
    .cleanupOutputBeforeBuild()
    .enableSassLoader()
    .enableSingleRuntimeChunk()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .setOutputPath('web/app/themes/saleziani/assets/')
    .setPublicPath('/app/themes/saleziani/assets')
    .splitEntryChunks()
;

module.exports = Encore.getWebpackConfig();
