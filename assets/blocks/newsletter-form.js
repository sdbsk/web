(wp => {
    return wp.blocks.registerBlockType('saleziani/newsletter-form', {
        category: 'theme',
        description: 'Získajte nových adresátov vašich newslettrov.',
        example: {},
        icon: 'email',
        title: 'Newsletter formulár',
        edit: () => wp.element.createElement('div', wp.blockEditor.useBlockProps(),
            wp.element.createElement('div', {className: 'wp-block-saleziani-newsletter-form'}, 'Tu sa zobrazí newsletter formulár.')
        )
    });

})(window.wp);
