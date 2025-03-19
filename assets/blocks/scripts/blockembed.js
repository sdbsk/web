wp.domReady && wp.domReady(function () {
    const allowedEmbedBlocks = [
        'youtube'
    ];

    wp.blocks &&
    wp.blocks.getBlockVariations('core/embed') &&
    wp.blocks.getBlockVariations('core/embed').forEach(function (blockVariation) {
        if (false === allowedEmbedBlocks.includes(blockVariation.name)) {
            wp.blocks.unregisterBlockVariation('core/embed', blockVariation.name);
        }
    });
});
