import {symbol} from '@wordpress/icons'
import IconPicker, {AsyncIcon} from "../components/icon-picker";

const {registerBlockType} = window.wp.blocks;
const {InspectorControls, useBlockProps} = window.wp.blockEditor;
const {PanelBody} = window.wp.components;
const {useEffect} = window.React;

registerBlockType('saleziani/icon', {
    apiVersion: 3,
    category: 'theme',
    icon: symbol,
    title: `Icon`,
    attributes: {
        selectedIcon: {
            default: '',
            type: 'string'
        },
        icon: {
            default: 'no icon',
            type: 'string'
        },
        color: {
            default: '',
            type: 'string'
        }
    },
    supports: {
        color: {
            background: false,
            text: true
        }
    },
    edit: (({attributes, setAttributes}) => {
        const blockProps = useBlockProps();

        useEffect(() => {
            setAttributes({color: blockProps?.style.color});
        }, [blockProps?.style])

        return (
            <div{...blockProps}>
                <InspectorControls key="setting">
                    <PanelBody
                        title={'Settings'}
                        initialOpen={true}
                    >
                        <IconPicker
                            onSelectedIcon={(selectedIcon) => setAttributes({selectedIcon})}
                            onLoadedIcon={(icon) => setAttributes({icon})}
                            selectedIcon={attributes.selectedIcon} color={blockProps?.style?.color}/>
                    </PanelBody>
                </InspectorControls>

                <div>
                    <div dangerouslySetInnerHTML={{__html: attributes.icon}}/>
                </div>
            </div>
        );
    }),
    save: ({attributes}) => {
        return (
            <div className={'wp-block-saleziani-icon'}>
                <div dangerouslySetInnerHTML={{__html: attributes.icon}}/>
            </div>
        );
    },
});


