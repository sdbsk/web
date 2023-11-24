import {columns} from '@wordpress/icons'

const {registerBlockVariation, unregisterBlockVariation} = window.wp.blocks;

const VARIATION_NAME = 'saleziani/post-columns';

setTimeout(() => {
    unregisterBlockVariation('core/query', 'posts-list');
}, 1000);

registerBlockVariation('core/query', {
        name: VARIATION_NAME,
        title: 'Kartičky článkov/stránok/kampaní',
        description: 'Zobrazí zoznam príspevkov ako kartičky',
        isActive: ({namespace}) => namespace === VARIATION_NAME,
        icon: columns,
        innerBlocks: [
            [
                'core/post-template',
                {
                    className: 'row',
                },
                [
                    ['core/group', {className: 'bootstrap-column-inner'}, [
                        ['core/post-featured-image'],
                        ['core/group', {className: 'content', metadata: {name: 'Obsah'}, lock: {move: true, remove: true}}, [
                            ['core/group', {className: 'text', metadata: {name: 'Text'}, lock: {move: true, remove: true}}, [
                                ['core/post-title', {isLink: true, level: 3}],
                                ['core/post-excerpt', {
                                    moreText: false,
                                    lock: {move: true, remove: true}
                                }],
                            ]],
                            ['core/read-more', {content: "Čítať viac", className: 'stretched-link'}]
                        ]]
                    ]]
                ],
            ],
            ['core/query-pagination'],
        ],
        allowedControls: ['postType', 'taxQuery'],
        attributes: {
            namespace: VARIATION_NAME,
            className: 'wp-block-saleziani-latest-posts',
            query: {
                perPage: 6,
                pages: 0,
                offset: 0,
                postType: 'post',
                order: 'desc',
                orderBy: 'date',
                author: '',
                search: '',
                exclude: [],
                sticky: '',
                inherit: false,
            },
        },
        scope: ['inserter'],
    }
);
