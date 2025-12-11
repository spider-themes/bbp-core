import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps();
    const {
        forum_tab_title,
        ppp,
        order,
        is_forum_tab_btn,
        more_txt,
        more_url,
        topics_tab_title,
        ppp2,
        order2,
        is_topic_tab_btn,
        more_txt2,
        more_url2,
        forum_tab_title_color,
        topics_tab_title_color,
        forum_tab_content_color,
        topics_tab_content_color
    } = attributes;

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Forum Filter Options', 'bbp-core')}>
                    <TextControl
                        label={__('Forum Tab Title', 'bbp-core')}
                        value={forum_tab_title}
                        onChange={(value) => setAttributes({ forum_tab_title: value })}
                    />
                    <TextControl
                        label={__('Show Forums', 'bbp-core')}
                        type="number"
                        value={ppp}
                        onChange={(value) => setAttributes({ ppp: parseInt(value) })}
                    />
                    <SelectControl
                        label={__('Order', 'bbp-core')}
                        value={order}
                        options={[
                            { label: 'ASC', value: 'ASC' },
                            { label: 'DESC', value: 'DESC' },
                        ]}
                        onChange={(value) => setAttributes({ order: value })}
                    />
                    <ToggleControl
                        label={__('Button (Show/Hide)', 'bbp-core')}
                        checked={is_forum_tab_btn}
                        onChange={(value) => setAttributes({ is_forum_tab_btn: value })}
                    />
                    {is_forum_tab_btn && (
                        <>
                            <TextControl
                                label={__('Button Label', 'bbp-core')}
                                value={more_txt}
                                onChange={(value) => setAttributes({ more_txt: value })}
                            />
                            <TextControl
                                label={__('Button URL', 'bbp-core')}
                                value={more_url}
                                onChange={(value) => setAttributes({ more_url: value })}
                            />
                        </>
                    )}
                </PanelBody>

                <PanelBody title={__('Topic Filter Options', 'bbp-core')}>
                    <TextControl
                        label={__('Topics Tab Title', 'bbp-core')}
                        value={topics_tab_title}
                        onChange={(value) => setAttributes({ topics_tab_title: value })}
                    />
                    <TextControl
                        label={__('Show Topics', 'bbp-core')}
                        type="number"
                        value={ppp2}
                        onChange={(value) => setAttributes({ ppp2: parseInt(value) })}
                    />
                    <SelectControl
                        label={__('Order', 'bbp-core')}
                        value={order2}
                        options={[
                            { label: 'ASC', value: 'ASC' },
                            { label: 'DESC', value: 'DESC' },
                        ]}
                        onChange={(value) => setAttributes({ order2: value })}
                    />
                    <ToggleControl
                        label={__('Button (Show/Hide)', 'bbp-core')}
                        checked={is_topic_tab_btn}
                        onChange={(value) => setAttributes({ is_topic_tab_btn: value })}
                    />
                    {is_topic_tab_btn && (
                        <>
                            <TextControl
                                label={__('Button Label', 'bbp-core')}
                                value={more_txt2}
                                onChange={(value) => setAttributes({ more_txt2: value })}
                            />
                            <TextControl
                                label={__('Button URL', 'bbp-core')}
                                value={more_url2}
                                onChange={(value) => setAttributes({ more_url2: value })}
                            />
                        </>
                    )}
                </PanelBody>
            </InspectorControls>

            <InspectorControls group="styles">
                <PanelColorSettings
                    title={__('Colors', 'bbp-core')}
                    initialOpen={false}
                    colorSettings={[
                        {
                            value: forum_tab_title_color,
                            onChange: (value) => setAttributes({ forum_tab_title_color: value }),
                            label: __('Forums Tab Label Color', 'bbp-core'),
                        },
                        {
                            value: topics_tab_title_color,
                            onChange: (value) => setAttributes({ topics_tab_title_color: value }),
                            label: __('Topics Tab Label Color', 'bbp-core'),
                        },
                        {
                            value: forum_tab_content_color,
                            onChange: (value) => setAttributes({ forum_tab_content_color: value }),
                            label: __('Forums Content Color', 'bbp-core'),
                        },
                        {
                            value: topics_tab_content_color,
                            onChange: (value) => setAttributes({ topics_tab_content_color: value }),
                            label: __('Topics Content Color', 'bbp-core'),
                        },
                    ]}
                />
            </InspectorControls>
            <ServerSideRender
                block="bbp-core/forum-tab"
                attributes={attributes}
            />
        </div>
    );
}
