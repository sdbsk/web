const {createHigherOrderComponent} = wp.compose;
const {InspectorControls} = wp.blockEditor;
const {PanelBody, ToggleControl} = wp.components;

export default {
    callback: createHigherOrderComponent((BlockEdit) => (props) => {
        if ('core/latest-posts' === props.name) {
            return <>
                <BlockEdit {...props}/>
                <InspectorControls>
                    <PanelBody title={'Vlastné nastavenia post meta'}>
                        <ToggleControl
                            checked={props.attributes.displayCategories}
                            label={'Zobraziť kategórie'}
                            onChange={(value) => props.setAttributes({displayCategories: value})}
                        />
                    </PanelBody>
                </InspectorControls>
            </>;
        }

        return <BlockEdit {...props}/>;
    }, 'withInspectorControl'),
    hook: 'editor.BlockEdit',
    name: 'theme/editor.BlockEdit'
};
