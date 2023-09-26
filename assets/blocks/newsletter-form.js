(wp => {
    return wp.blocks.registerBlockType('saleziani/newsletter-form', {
        category: 'theme',
        description: 'Získajte nových adresátov vašich newslettrov.',
        example: {},
        icon: 'email',
        title: 'Newsletter formulár',
        edit: props => wp.element.createElement('div', wp.blockEditor.useBlockProps(),
            wp.element.createElement(wp.serverSideRender, {block: props.name})
        )
    });

})(window.wp);
