const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .addEntry('editor', './assets/private/editor.js')
    .addEntry('style', './assets/private/style.js')
    .cleanupOutputBeforeBuild()
    .enableSassLoader()
    .enableSingleRuntimeChunk()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .setOutputPath('assets/public/')
    .setPublicPath('/app/themes/saleziani/assets/public')
    .splitEntryChunks()
;

module.exports = Encore.getWebpackConfig();
