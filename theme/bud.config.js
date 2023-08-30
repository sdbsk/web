export default async app => app
    .assets(['images'])
    .entry('editor', ['@scripts/editor'])
    .setProxyUrl('https://saleziani.loc')
    .setPublicPath('/app/themes/sage/public/')
    .setUrl('http://localhost:3000')
    .watch(['resources/views', 'app'])
    /**
     * @see https://bud.js.org/extensions/sage/theme.json
     * @see https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json
     */
    .wpjson.enable()
    .set('settings', {
        appearanceTools: true,
        color: {
            palette: [
                {
                    name: 'Black 700',
                    slug: 'black-700',
                    color: '#272727'
                },
                {
                    name: 'Black 800',
                    slug: 'black-800',
                    color: '#272727cc'
                },
                {
                    name: 'Red 200',
                    slug: 'red-200',
                    color: '#f8dad3'
                }, {
                    name: 'Red 500',
                    slug: 'red-500',
                    color: '#cf3942'
                },
                {
                    name: 'Yellow 800',
                    slug: 'yellow-800',
                    color: '#ee8d0f'
                }
            ]
        },
        layout: {
            contentSize: '75rem',
            wideSize: '100rem'
        },
        typography: {
            dropCap: false,
            fontFamilies: [
                {
                    fontFace: [
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 100,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-100.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 100,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-100italic.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 200,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-200.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 200,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-200italic.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 300,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-300.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 300,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-300italic.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 400,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-400.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 400,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-400italic.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 500,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-500.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 500,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-500italic.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 600,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-600.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 600,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-600italic.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 700,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-700.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 700,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-700italic.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 800,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-800.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 800,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-800italic.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 900,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-900.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Fira Sans',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 900,
                            src: [
                                'file:./resources/fonts/fira-sans-v17-latin-900italic.woff2'
                            ]
                        }
                    ],
                    fontFamily: 'Fira Sans',
                    name: 'Fira Sans',
                    slug: 'fira-sans'
                },
                {
                    fontFace: [
                        {
                            fontFamily: 'Merriweather',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 300,
                            src: [
                                'file:./resources/fonts/merriweather-v30-latin-300.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Merriweather',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 300,
                            src: [
                                'file:./resources/fonts/merriweather-v30-latin-300italic.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Merriweather',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 400,
                            src: [
                                'file:./resources/fonts/merriweather-v30-latin-400.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Merriweather',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 400,
                            src: [
                                'file:./resources/fonts/merriweather-v30-latin-400italic.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Merriweather',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 700,
                            src: [
                                'file:./resources/fonts/merriweather-v30-latin-700.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Merriweather',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 700,
                            src: [
                                'file:./resources/fonts/merriweather-v30-latin-700italic.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Merriweather',
                            fontStretch: 'normal',
                            fontStyle: 'normal',
                            fontWeight: 900,
                            src: [
                                'file:./resources/fonts/merriweather-v30-latin-900.woff2'
                            ]
                        },
                        {
                            fontFamily: 'Merriweather',
                            fontStretch: 'normal',
                            fontStyle: 'italic',
                            fontWeight: 900,
                            src: [
                                'file:./resources/fonts/merriweather-v30-latin-900italic.woff2'
                            ]
                        }
                    ],
                    fontFamily: 'Merriweather',
                    name: 'Merriweather',
                    slug: 'merriweather'
                }
            ]
        },
        useRootPaddingAwareAlignments: true
    })
    .set('styles', {
        blocks: {
            'core/button': {
                spacing: {
                    padding: {
                        bottom: '0.75rem',
                        left: '2rem',
                        right: '2rem',
                        top: '0.75rem'
                    }
                }
            },
            'core/paragraph': {
                color: {
                    text: 'var(--wp--preset--color--black-800)'
                },
                typography: {
                    fontFamily: 'var(--wp--preset--font-family--fira-sans)'
                }
            },
            'theme/call-to-action': {
                css: '{align-items: center; display: flex; flex-direction: column; max-width: 48rem;}',
                spacing: {
                    margin: {
                        bottom: '0',
                        left: 'auto',
                        right: 'auto',
                        top: '0'
                    },
                    padding: {
                        bottom: '3rem',
                        left: '4rem',
                        right: '4rem',
                        top: '3rem'
                    }
                }
            }
        },
        elements: {
            h1: {
                color: {
                    text: 'var(--wp--preset--color--red-500)'
                },
                typography: {
                    fontSize: '4rem',
                    fontWeight: '900',
                    lineHeight: '4.875rem'
                }
            },
            heading: {
                color: {
                    text: 'var(--wp--preset--color--black-700)'
                },
                typography: {
                    fontFamily: 'var(--wp--preset--font-family--merriweather)'
                }
            },
            button: {
                border: {
                    radius: '1.5rem'
                },
                color: {
                    background: 'var(--wp--preset--color--yellow-800)'
                },
                spacing: {
                    padding: {
                        bottom: '0.25rem',
                        left: '0.75rem',
                        right: '0.75rem',
                        top: '0.25rem'
                    }
                },
                typography: {
                    fontFamily: 'var(--wp--preset--font-family--fira-sans)',
                    fontWeight: '500',
                    lineHeight: '1.375rem'
                }
            }
        }
    })
;
