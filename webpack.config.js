const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .addEntry('admin', './assets/admin/admin.js')
    .addEntry('blocks', './assets/blocks/blocks.js')
    .addEntry('public', './assets/public/public.js')
    .cleanupOutputBeforeBuild()
    .disableSingleRuntimeChunk()
    .enableSassLoader()
    .enablePostCssLoader((options) => {
        options.postcssOptions = {
            config: 'postcss.config.js',
        };
    })
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .setOutputPath('web/app/themes/saleziani/assets/')
    .setPublicPath('/app/themes/saleziani/assets')
    .copyFiles({
        from: './assets/public/images/',
        to: '[path]images/[name].[ext]'
    })
    .configureImageRule({
        // tell Webpack it should consider inlining
        type: 'asset',
        maxSize: 4 * 1024,
    })
    .enableReactPreset();

module.exports = Encore.getWebpackConfig();
