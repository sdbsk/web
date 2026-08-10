wp.domReady && wp.domReady(function () {
    const allowedEmbedBlocks = [
        'youtube'
    ];
    // getBlockVariations returns undefined where core/embed is not registered.
    wp.blocks && (wp.blocks.getBlockVariations('core/embed') || []).forEach(function (blockVariation) {
        if (-1 === allowedEmbedBlocks.indexOf(blockVariation.name)) {
            wp.blocks.unregisterBlockVariation('core/embed', blockVariation.name);
        }
    });
});
