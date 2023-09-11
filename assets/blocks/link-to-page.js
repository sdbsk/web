(wp => {
    const pages = [];
    load();

    return wp.blocks.registerBlockType('saleziani/link-to-page', {
        attributes: {
            page: {
                default: 0,
                type: 'integer'
            }
        },
        category: 'theme',
        description: 'Zobrazte odkaz na stránku s jej obrázkom, nadpisom a útržkom obsahu.',
        example: {},
        icon: 'admin-links',
        title: 'Odkaz na stránku',
        edit: props => wp.element.createElement('div', wp.blockEditor.useBlockProps(), [
            wp.element.createElement(wp.blockEditor.InspectorControls, {key: 'inspector-controls'},
                wp.element.createElement(wp.components.PanelBody, {title: 'Cieľová stránka'},
                    wp.element.createElement(wp.components.SelectControl, {
                        label: 'Stránka',
                        onChange: (value) => props.setAttributes({page: parseInt(value)}),
                        options: pages,
                        value: props.attributes.page
                    })
                )
            ),
            wp.element.createElement(wp.serverSideRender, {attributes: props.attributes, block: props.name, key: 'rendered'})
        ])
    });

    function load(page = 1) {
        if (1 === page) {
            pages.push({label: '', value: 0});
        } else {
            pages.pop();
        }

        fetch(window.location.origin + '/wp-json/wp/v2/pages?_fields=id,title&order=asc&per_page=100&page=' + page).then(response => response.json().then(json => {
            json.forEach(page => pages.push({label: page.title.rendered, value: page.id}));

            if (response.headers.get('x-wp-totalpages') > page) {
                load(++page);
            }
        }));
    }
})(window.wp);
