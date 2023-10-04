import onMount from '../util/on-mount';

if ('post-php' === window.adminpage) {
    const template = 'post' === window.pagenow ? 'single' : 'page';

    onMount('.editor-styles-wrapper .edit-post-visual-editor__post-title-wrapper').then((title) => {
        title.classList.add('wp-template-' + template);
    });

    onMount('.editor-styles-wrapper .wp-block-post-content').then((content) => {
        content.classList.add('wp-template-' + template);
    });
}
