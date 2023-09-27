const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .addEntry('editor', './assets/editor.js')
    .addEntry('style', './assets/style.js')
    .copyFiles({
        from: 'assets/blocks',
        to: 'blocks/[path][name]' + (Encore.isProduction() ? '.[hash:8]' : '') + '.[ext]'
    })
    .copyFiles({
        from: 'assets/metaboxes',
        to: 'metaboxes/[path][name]' + (Encore.isProduction() ? '.[hash:8]' : '') + '.[ext]'
    })
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
