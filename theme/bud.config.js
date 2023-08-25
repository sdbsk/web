export default async app => app
    .assets(['images'])
    .entry('app', ['@scripts/app', '@styles/app'])
    .entry('editor', ['@scripts/editor', '@styles/editor'])
    .setProxyUrl('https://saleziani.loc')
    .setPublicPath('/app/themes/sage/public/')
    .setUrl('http://localhost:3000')
    .watch(['resources/views', 'app'])
    .wpjson.enable().settings(theme => theme
        /**
         * @see https://bud.js.org/extensions/sage/theme.json
         * @see https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json
         */
        .set('layout.contentSize', '1200px')
        .set('layout.wideSize', '1600px')
    )
;
