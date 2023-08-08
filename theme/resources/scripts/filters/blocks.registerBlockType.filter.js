export default {
    callback: (settings) => {
        if ('core/latest-posts' === settings.name) {
            settings.attributes = {
                ...settings.attributes,
                displayCategories: {type: 'boolean', default: false}
            };
        }

        return settings;
    },
    hook: 'blocks.registerBlockType',
    name: 'theme/blocks.registerBlockType'
};
