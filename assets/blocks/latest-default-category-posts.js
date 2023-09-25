(wp => wp.blocks.registerBlockType('saleziani/latest-default-category-posts', {
    category: 'theme',
    description: 'Zobrazte najnovšie články z predvolenej kategórie. Ak ju chcete zmeniť prejdite do Nastavenia -> Písanie.',
    example: {},
    icon: 'text-page',
    title: 'Najnovšie články',
    edit: props => wp.element.createElement('div', wp.blockEditor.useBlockProps(),
        wp.element.createElement(wp.serverSideRender, {block: props.name})
    )
}))(window.wp);
