if ('activity' === window.pagenow) {
    const {registerPlugin} = window.wp.plugins;
    const {PluginPostStatusInfo} = window.wp.editPost;
    const {TextControl} = window.wp.components;
    const {withSelect, withDispatch} = window.wp.data;
    const {compose} = window.wp.compose;

    let ButtonLabelField = (props) => <PluginPostStatusInfo>
        <TextControl label="ButtonLabel" value={props.buttonLabel} onChange={newButtonLabel => props.onButtonLabelChange(newButtonLabel)}/>
    </PluginPostStatusInfo>;

    ButtonLabelField = compose([
        withSelect(select => {
            const meta = select('core/editor').getEditedPostAttribute('meta');

            return ({buttonLabel: meta ? meta.buttonLabel : ''});
        }), withDispatch(dispatch => ({
            onButtonLabelChange: (value) => {
                dispatch('core/editor').editPost({meta: {buttonLabel: value}});
            }
        }))
    ])(ButtonLabelField);

    registerPlugin('buttonLabel', {render: ButtonLabelField});
}
