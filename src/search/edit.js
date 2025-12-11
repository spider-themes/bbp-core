import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl, RadioControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps();
    const {
        placeholder,
        submit_btn_type,
        submit_btn_text,
        submit_btn_align,
        is_keywords,
        keywords_label,
        keywords_align,
        color_text,
        color_placeholder,
        input_bg_color,
        color_icon,
        search_bg,
        color_keywords_label,
        bbpc_color_keywords,
        bbpc_color_keywords_bg,
        bbpc_color_keywords_hover,
        bbpc_color_keywords_bg_hover
    } = attributes;

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Form', 'bbp-core')}>
                    <TextControl
                        label={__('Placeholder', 'bbp-core')}
                        value={placeholder}
                        onChange={(value) => setAttributes({ placeholder: value })}
                    />
                    <SelectControl
                        label={__('Search Type', 'bbp-core')}
                        value={submit_btn_type}
                        options={[
                            { label: 'Icon', value: 'icon' },
                            { label: 'Text', value: 'text' },
                        ]}
                        onChange={(value) => setAttributes({ submit_btn_type: value })}
                    />
                    {submit_btn_type === 'text' && (
                        <TextControl
                            label={__('Submit Button Text', 'bbp-core')}
                            value={submit_btn_text}
                            onChange={(value) => setAttributes({ submit_btn_text: value })}
                        />
                    )}
                    <SelectControl
                        label={__('Submit Alignment', 'bbp-core')}
                        value={submit_btn_align}
                        options={[
                            { label: 'Left', value: 'left' },
                            { label: 'Right', value: 'right' },
                        ]}
                        onChange={(value) => setAttributes({ submit_btn_align: value })}
                    />
                    <ToggleControl
                        label={__('Keywords', 'bbp-core')}
                        checked={is_keywords}
                        onChange={(value) => setAttributes({ is_keywords: value })}
                    />
                    {is_keywords && (
                        <>
                            <TextControl
                                label={__('Keywords Label', 'bbp-core')}
                                value={keywords_label}
                                onChange={(value) => setAttributes({ keywords_label: value })}
                            />
                            <SelectControl
                                label={__('Keywords Alignment', 'bbp-core')}
                                value={keywords_align}
                                options={[
                                    { label: 'Left', value: 'left' },
                                    { label: 'Center', value: 'center' },
                                    { label: 'Right', value: 'right' },
                                ]}
                                onChange={(value) => setAttributes({ keywords_align: value })}
                            />
                        </>
                    )}
                </PanelBody>

            </InspectorControls>

            <InspectorControls group="styles">
                <PanelColorSettings
                    title={__('Form Colors', 'bbp-core')}
                    initialOpen={false}
                    colorSettings={[
                        {
                            value: color_text,
                            onChange: (value) => setAttributes({ color_text: value }),
                            label: __('Text Color', 'bbp-core'),
                        },
                        {
                            value: color_placeholder,
                            onChange: (value) => setAttributes({ color_placeholder: value }),
                            label: __('Placeholder Color', 'bbp-core'),
                        },
                        {
                            value: input_bg_color,
                            onChange: (value) => setAttributes({ input_bg_color: value }),
                            label: __('Input Background Color', 'bbp-core'),
                        },
                        {
                            value: color_icon,
                            onChange: (value) => setAttributes({ color_icon: value }),
                            label: __('Button Color', 'bbp-core'),
                        },
                        {
                            value: search_bg,
                            onChange: (value) => setAttributes({ search_bg: value }),
                            label: __('Button Background', 'bbp-core'),
                        },
                    ]}
                />

                <PanelColorSettings
                    title={__('Keywords Colors', 'bbp-core')}
                    initialOpen={false}
                    colorSettings={[
                        {
                            value: color_keywords_label,
                            onChange: (value) => setAttributes({ color_keywords_label: value }),
                            label: __('Label Color', 'bbp-core'),
                        },
                        {
                            value: bbpc_color_keywords,
                            onChange: (value) => setAttributes({ bbpc_color_keywords: value }),
                            label: __('Keyword Color', 'bbp-core'),
                        },
                        {
                            value: bbpc_color_keywords_bg,
                            onChange: (value) => setAttributes({ bbpc_color_keywords_bg: value }),
                            label: __('Keyword Background', 'bbp-core'),
                        },
                        {
                            value: bbpc_color_keywords_hover,
                            onChange: (value) => setAttributes({ bbpc_color_keywords_hover: value }),
                            label: __('Keyword Hover Color', 'bbp-core'),
                        },
                        {
                            value: bbpc_color_keywords_bg_hover,
                            onChange: (value) => setAttributes({ bbpc_color_keywords_bg_hover: value }),
                            label: __('Keyword Background Hover', 'bbp-core'),
                        },
                    ]}
                />
            </InspectorControls>

            <ServerSideRender
                block="bbp-core/search"
                attributes={attributes}
            />
        </div>
    );
}
