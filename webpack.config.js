const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .addEntry('admin', './assets/admin/app.js')
    .addEntry('public', './assets/public/app.js')
    .cleanupOutputBeforeBuild()
    .disableSingleRuntimeChunk()
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .setOutputPath('web/app/themes/saleziani/assets/')
    .setPublicPath('/app/themes/saleziani/assets');

module.exports = Encore.getWebpackConfig();
