import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps();
    const { ppp, order, show_meta, title_color, meta_color, bg_color } = attributes;

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Filter Options', 'bbp-core')}>
                    <TextControl
                        label={__('Show Forum Topics', 'bbp-core')}
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
                </PanelBody>
                <PanelBody title={__('Advanced', 'bbp-core')} initialOpen={false}>
                    <ToggleControl
                        label={__('Show Meta', 'bbp-core')}
                        checked={show_meta}
                        onChange={(value) => setAttributes({ show_meta: value })}
                    />
                </PanelBody>
            </InspectorControls>

            <InspectorControls group="styles">
                <PanelColorSettings
                    title={__('Colors', 'bbp-core')}
                    initialOpen={false}
                    colorSettings={[
                        {
                            value: title_color,
                            onChange: (value) => setAttributes({ title_color: value }),
                            label: __('Title Color', 'bbp-core'),
                        },
                        {
                            value: meta_color,
                            onChange: (value) => setAttributes({ meta_color: value }),
                            label: __('Meta Color', 'bbp-core'),
                        },
                        {
                            value: bg_color,
                            onChange: (value) => setAttributes({ bg_color: value }),
                            label: __('Background Color', 'bbp-core'),
                        },
                    ]}
                />
            </InspectorControls>

            <ServerSideRender
                block="bbp-core/forum-posts"
                attributes={attributes}
            />
        </div >
    );
}
