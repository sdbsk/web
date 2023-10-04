if ('post-php' === window.adminpage) {
    window.addEventListener('load', () => {
        const template = 'post' === window.pagenow ? 'single' : 'page';

        const title = document.querySelector('.editor-styles-wrapper .edit-post-visual-editor__post-title-wrapper');

        if (title instanceof Element) {
            title.classList.add('wp-template-' + template);
        }

        const postContent = document.querySelector('.editor-styles-wrapper .wp-block-post-content');

        if (postContent instanceof Element) {
            postContent.classList.add('wp-template-' + template);
        }
    });
}
