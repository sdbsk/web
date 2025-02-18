import {symbol} from '@wordpress/icons'
import IconPicker, {AsyncIcon} from "../components/icon-picker";

const ServerSideRender = window.wp.serverSideRender;
const {registerBlockType} = window.wp.blocks;
const {InspectorControls, useBlockProps } = window.wp.blockEditor;
const {PanelBody} = window.wp.components;
const {useEffect} = window.React;
const {useSelect} = window.wp.data;

registerBlockType('saleziani/activity', {
    apiVersion: 3,
    category: 'theme',
    icon: symbol,
    title: `Aktivita`,
    // parent: ['saleziani/icon-columns'],
    edit: (({attributes, setAttributes}) => {
        const blockProps = useBlockProps();

        const postId = useSelect((select) => {
            return select('core/editor').getCurrentPostId();
        }, []);

        return (
            <>
                <ServerSideRender attributes={{...attributes, postId}} block={'saleziani/activity'}/>
            </>
        );
    })
});


