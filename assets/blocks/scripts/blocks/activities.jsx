import {postList} from '@wordpress/icons';

const {registerBlockVariation, unregisterBlockVariation} = window.wp.blocks;

const VARIATION_NAME = 'saleziani/activities';

setTimeout(() => {
    unregisterBlockVariation('core/query', 'posts-list');
}, 1000);

registerBlockVariation('core/query', {
    name: VARIATION_NAME, title: 'Zoznam aktivít', description: 'Zobrazí zoznam aktivít', isActive: ({namespace}) => namespace === VARIATION_NAME, icon: postList, innerBlocks: [
        [
            'core/post-template', {
            className: 'row'
        }, [
            [
                'core/group', {className: 'activity-list'}, [
                [
                    'core/group', {className: 'activity-item'}, [
                    [
                        'core/group', {className: 'activity-item-title'}, [
                        ['core/post-title', {level: 2}], [
                            'core/list', {className: 'tags'}, [
                                ['core/list-item', {content: 'pre všetkých'}], ['core/list-item', {content: 'duchovno'}]
                            ]
                        ]
                    ], [
                        'core/post-excerpt', {
                            className: 'activity-item-venue', moreText: false, lock: {move: true, remove: true}, content: 'v kostole, každý piatok o 19:00 (okrem školských prázdnin)'
                        }
                    ], [
                        'core/post-excerpt', {
                            className: 'activity-item-content', moreText: false, lock: {move: true, remove: true}, content: 'Sv. omša je prežívaná v mládežníckom štýle. Pomáha k tomu príhovor kňazov a obohatenie sv. omše našimi mládežníckymi speváckymi zbormi.'
                        }
                    ], [
                        'core/group', {className: 'activity-item-bottom-content'}, [
                            [
                                'core/buttons', [
                                ['core/button', {text: 'Prihlásiť sa', url: '#'}]
                            ]
                            ], ['core/navigation-link', {text: 'Zobraziť viac', url: '#'}], [
                                'core/post-excerpt', {
                                    moreText: false, lock: {move: true, remove: true}, content: 'Kontakt: Rusty Rumanovič, 0912 123 456'
                                }
                            ]
                        ]
                    ]
                    ]

                ]
                ]
            ]
            ]
        ]
        ]
    ], allowedControls: ['postType', 'taxQuery'], attributes: {
        namespace: VARIATION_NAME, className: 'wp-block-saleziani-activities', query: {
            perPage: 6, pages: 0, offset: 0, postType: 'post', order: 'desc', orderBy: 'date', author: '', search: '', exclude: [], sticky: '', inherit: false
        }
    }, scope: ['inserter']
});
