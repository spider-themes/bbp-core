import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import './editor.scss';
export default function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps();
    const {
        ppp,
        ppp2,
        order,
        more_txt,
        more_text_color,
        title_color,
        content_color
    } = attributes;

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Filter Options', 'bbp-core')}>
                    <TextControl
                        label={__('Show Forums', 'bbp-core')}
                        type="number"
                        value={ppp}
                        onChange={(value) => setAttributes({ ppp: parseInt(value) })}
                        help={__('Show the forums count at the initial view. Default is 5 forums in a row.', 'bbp-core')}
                    />
                    <TextControl
                        label={__('Hidden Forums', 'bbp-core')}
                        type="number"
                        value={ppp2}
                        onChange={(value) => setAttributes({ ppp2: parseInt(value) })}
                        help={__('Hidden forums will show on clicking on the More button.', 'bbp-core')}
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
                    <TextControl
                        label={__('Read More Text', 'bbp-core')}
                        value={more_txt}
                        onChange={(value) => setAttributes({ more_txt: value })}
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
                            value: content_color,
                            onChange: (value) => setAttributes({ content_color: value }),
                            label: __('Content Color', 'bbp-core'),
                        },
                        {
                            value: more_text_color,
                            onChange: (value) => setAttributes({ more_text_color: value }),
                            label: __('More Text Color', 'bbp-core'),
                        },
                    ]}
                />
            </InspectorControls>

            <ServerSideRender
                block="bbp-core/forums"
                attributes={attributes}
            />
        </div>
    );
}
