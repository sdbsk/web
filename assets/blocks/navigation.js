(wp => wp.blocks.registerBlockType('saleziani/navigation', {
    category: 'theme',
    description: 'Zobrazte navigáciu pre strom aktuálnej stránky.',
    example: {},
    icon: 'menu-alt',
    title: 'Navigácia',
    edit: props => wp.element.createElement('div', wp.blockEditor.useBlockProps(),
        wp.element.createElement(wp.serverSideRender, {block: props.name})
    )
}))(window.wp);
