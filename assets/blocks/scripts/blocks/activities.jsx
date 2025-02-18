import {postList} from '@wordpress/icons';

const {registerBlockVariation, unregisterBlockVariation} = window.wp.blocks;

const VARIATION_NAME = 'saleziani/activities';

setTimeout(() => {
    unregisterBlockVariation('core/query', 'posts-list');
}, 1000);

registerBlockVariation('core/query', {
    name: VARIATION_NAME,
    title: 'Zoznam aktivít',
    description: 'Zobrazí zoznam aktivít',
    isActive: ({namespace}) => namespace === VARIATION_NAME,
    icon: postList,
    innerBlocks: [
        ['core/post-template', {className: 'row'}, [
            ['saleziani/activity']
        ]]
    ],
    allowedControls: ['taxQuery'],
    attributes: {
        namespace: VARIATION_NAME, className: 'wp-block-saleziani-activities', query: {
            perPage: 6,
            pages: 0,
            offset: 0,
            postType: 'activity',
            order: 'desc',
            orderBy: 'date',
            author: '',
            search: '',
            exclude: [],
            sticky: '',
            inherit: false
        }
    },
    scope: ['inserter']
});
