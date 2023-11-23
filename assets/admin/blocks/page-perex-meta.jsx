const {registerBlockType} = window.wp.blocks;
const {TextControl} = window.wp.components;
const {useSelect} = window.wp.data;
const {useEntityProp} = window.wp.coreData;
const {useBlockProps, RichText} = window.wp.blockEditor;

registerBlockType('saleziani/page-perex-meta', {
    title: 'Perex stránky',
    edit: () => {
        const blockProps = useBlockProps();
        const postType = useSelect(
            (select) => select('core/editor').getCurrentPostType(),
            []
        );

        const [meta, setMeta] = useEntityProp('postType', postType, 'meta');

        return (
            <div
                {...blockProps}>
                <RichText
                    tagName="p"
                    value={meta.page_perex}
                    allowedFormats={['core/bold', 'core/italic']}
                    onChange={(value) => {
                        setMeta({...meta, page_perex: value});
                    }}
                    placeholder={'Napíš stručný perex'}
                />
            </div>
        );
    }
});
