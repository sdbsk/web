import {column, columns} from '@wordpress/icons'

const {registerBlockType} = window.wp.blocks;
const {InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps} = window.wp.blockEditor;
const {PanelBody, RadioControl} = window.wp.components;
const {useEffect} = window.React;

function columnClass(columnCount) {
    return `col-lg-${columnCount} col-md-6`;
}

function innerColumnClassName(propsClassName) {
    return ((propsClassName ?? '').match(/(has-[^ ]+)/) ?? []).join(' ');
}

export default (name, {parentTitle, childTitle, template, minColumnCount = 2, maxColumnCount = 4, defaultBackgroundColor}) => {
    const parentBlockName = `saleziani/${name}-columns`;
    const childBlockName = `saleziani/${name}-column`;
    const childBlockTemplate = [childBlockName];
    const styleAttributes = {};

    const columnCounts = [...Array(maxColumnCount - minColumnCount + 1).keys()].map(index => ({label: index + minColumnCount, value: index + minColumnCount}));

    console.log(columnCounts);

    if (defaultBackgroundColor) {
        styleAttributes['backgroundColor'] = {
            default: defaultBackgroundColor,
            type: 'string'
        };
    }

    registerBlockType(childBlockName, {
        apiVersion: 3,
        category: 'theme',
        icon: column,
        title: childTitle,
        parent: [parentBlockName],
        supports: {
            color: true
        },
        attributes: {
            ...styleAttributes,
            widthClass: {
                default: columnClass(3),
                type: 'string'
            }
        },
        usesContext: [`${parentBlockName}/count`],
        edit: ({context, attributes, setAttributes}) => {
            useEffect(() => setAttributes({
                widthClass: columnClass(12 / context[`${parentBlockName}/count`]),
            }), [context, setAttributes]);

            const blockProps = useBlockProps({
                className: `bootstrap-column-inner`
            });

            if (defaultBackgroundColor && blockProps.style) {
                useEffect(() => {
                    if (!blockProps.style?.backgroundColor) {
                        setAttributes({backgroundColor: defaultBackgroundColor})
                    }
                }, [blockProps.style]);
            }

            return (
                <div className={`${attributes.widthClass}`}>
                    <div {...useInnerBlocksProps(blockProps, {
                        template: template,
                        templateLock: 'insert',
                        className: innerColumnClassName(blockProps.className)
                    })}/>
                </div>
            );
        },
        save: (props) => {
            const blockProps = useBlockProps.save();

            return (
                <>
                    <div className={`${props.attributes.widthClass}`}>
                        <div className={`bootstrap-column-inner ${innerColumnClassName(blockProps.className)}`}>
                            <InnerBlocks.Content/>
                        </div>
                    </div>
                </>
            );
        },
    });

    registerBlockType(parentBlockName, {
        apiVersion: 3,
        category: 'theme',
        icon: columns,
        title: parentTitle,
        attributes: {
            count: {
                default: columnCounts[Math.ceil(columnCounts.length / 2) - 1].value,
                type: 'number'
            }
        },
        providesContext: {
            [`${parentBlockName}/count`]: 'count'
        },
        edit: (({attributes, setAttributes}) => {
            const blockProps = useBlockProps({
                className: `row`
            });

            const innerBlocksProps = useInnerBlocksProps(blockProps, {
                allowedBlocks: [childBlockTemplate[0]],
                template: [childBlockTemplate, childBlockTemplate, childBlockTemplate],
                orientation: 'horizontal',
                renderAppender: InnerBlocks.ButtonBlockAppender
            });

            return (
                <>
                    <InspectorControls key="setting">
                        <PanelBody
                            title={'Settings'}
                            initialOpen={true}
                        >
                            <RadioControl
                                label="Columns per row"
                                selected={attributes.count}
                                options={columnCounts}
                                onChange={(value) => setAttributes({count: parseInt(value)})}
                            />
                        </PanelBody>
                    </InspectorControls>

                    <div className={`bootstrap-columns-container bootstrap-${name}-columns`}>
                        <div {...innerBlocksProps} />
                    </div>
                </>
            );
        }),
        save: () => {
            return (
                <>
                    <div className={`bootstrap-columns-container bootstrap-${name}-columns`}>
                        <div className={`row`}>
                            <InnerBlocks.Content/>
                        </div>
                    </div>
                </>
            );
        },
    });
};


