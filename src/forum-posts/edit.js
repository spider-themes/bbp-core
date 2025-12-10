import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps();
    const { ppp, order } = attributes;

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
            </InspectorControls>

            <ServerSideRender
                block="bbp-core/forum-posts"
                attributes={attributes}
            />
        </div>
    );
}
