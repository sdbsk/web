(wp => {
    const pages = [];
    const backgroundColors = [
        {color: '#e1ecf8', name: 'Modrá', slug: 'light-blue'},
        {color: '#e5f3f0', name: 'Zelená', slug: 'light-green'},
        {color: '#f9f6f4', name: 'Hnedá', slug: 'light-brown'},
        {color: '#fbece9', name: 'Oranžová', slug: 'light-orange'},
        {color: '#fcf4e0', name: 'Žltá', slug: 'light-yellow'}
    ];

    load();

    return wp.blocks.registerBlockType('saleziani/link-to-page', {
        attributes: {
            page: {
                default: 0,
                type: 'integer'
            },
            backgroundColor: {
                default: 'light-brown',
                type: 'string'
            }
        },
        category: 'theme',
        description: 'Zobrazte odkaz na stránku s jej obrázkom, nadpisom a útržkom obsahu.',
        example: {},
        icon: 'admin-links',
        supports: {
            inserter: false
        },
        title: 'Odkaz na stránku',
        edit: props => wp.element.createElement('div', wp.blockEditor.useBlockProps(), [
            wp.element.createElement(wp.blockEditor.InspectorControls, {key: 'inspector-controls'}, [
                    wp.element.createElement(wp.components.PanelBody, {key: 'target-page', title: 'Cieľová stránka'},
                        wp.element.createElement(wp.components.SelectControl, {
                            label: 'Stránka',
                            onChange: (value) => props.setAttributes({page: parseInt(value)}),
                            options: pages,
                            value: props.attributes.page
                        })
                    ),
                    wp.element.createElement(wp.components.PanelBody, {key: 'background-color', title: 'Farba Pozadia'},
                        wp.element.createElement(wp.components.ColorPalette, {
                            colors: backgroundColors,
                            clearable: true,
                            disableCustomColors: true,
                            label: 'Farba',
                            onChange: (value, index) => props.setAttributes({backgroundColor: undefined === index ? '' : backgroundColors[index].slug}),
                            value: backgroundColors.filter((bc) => props.attributes.backgroundColor === bc.slug)[0].color
                        })
                    )
                ]
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

        fetch(window.location.origin + '/wp-json/wp/v2/pages/?_fields=id,title&order=asc&per_page=100&page=' + page).then(response => response.json().then(json => {
            json.forEach(page => pages.push({label: page.title.rendered, value: page.id}));

            if (response.headers.get('x-wp-totalpages') > page) {
                load(++page);
            }
        }));
    }
})(window.wp);
