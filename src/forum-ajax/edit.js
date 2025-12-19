import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl, TextControl, Button } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps();
    const {
        ppp2,
        order,
        filter_btns,
        forum_title_color,
        forum_title_hover_color,
        forum_meta_color,
        parent_forum_color,
        parent_forum_color_hover
    } = attributes;

    const isProActive = window.bbpc_upsell_config?.is_pro_active === '1' || window.bbpc_upsell_config?.is_pro_active === true;
    const upsellImageUrl = window.bbpc_upsell_config?.upsell_image_url;
    const upgradeUrl = window.bbpc_upsell_config?.upgrade_url;

    if (!isProActive) {
        return (
            <div {...blockProps}>
                <div className="bbpc-upsell-wrapper" style={{ position: 'relative', display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '300px', background: '#f5f5f5', border: '1px dashed #ccc' }}>
                    {upsellImageUrl ? (
                        <img src={upsellImageUrl} alt="Pro Feature" style={{ maxWidth: '100%', height: 'auto', display: 'block' }} />
                    ) : (
                        <p>{__('Image not found', 'bbp-core')}</p>
                    )}

                    <div style={{ position: 'absolute', top: '50%', left: '50%', transform: 'translate(-50%, -50%)', zIndex: 10 }}>
                        <Button
                            variant="primary"
                            href={upgradeUrl}
                            target="_blank"
                            className="bbpc-upgrade-btn"
                        >
                            {__('Upgrade to Pro', 'bbp-core')}
                        </Button>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Filter Options', 'bbp-core')}>
                    <TextControl
                        label={__('Show Forums', 'bbp-core')}
                        type="number"
                        value={ppp2}
                        onChange={(value) => setAttributes({ ppp2: parseInt(value) })}
                        help={__('Show the forums count at the initial view. Default is 9 forums in a row.', 'bbp-core')}
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
                        label={__('Tab Filter', 'bbp-core')}
                        checked={filter_btns}
                        onChange={(value) => setAttributes({ filter_btns: value })}
                        help={filter_btns ? __('Show', 'bbp-core') : __('Hide', 'bbp-core')}
                    />
                </PanelBody>
            </InspectorControls>

            <InspectorControls group="styles">
                <PanelColorSettings
                    title={__('Colors', 'bbp-core')}
                    initialOpen={false}
                    colorSettings={[
                        {
                            value: forum_title_color,
                            onChange: (value) => setAttributes({ forum_title_color: value }),
                            label: __('Forum Title Color', 'bbp-core'),
                        },
                        {
                            value: forum_title_hover_color,
                            onChange: (value) => setAttributes({ forum_title_hover_color: value }),
                            label: __('Forum Title Hover Color', 'bbp-core'),
                        },
                        {
                            value: forum_meta_color,
                            onChange: (value) => setAttributes({ forum_meta_color: value }),
                            label: __('Forum Meta Color', 'bbp-core'),
                        },
                        {
                            value: parent_forum_color,
                            onChange: (value) => setAttributes({ parent_forum_color: value }),
                            label: __('Parent Forum Color', 'bbp-core'),
                        },
                        {
                            value: parent_forum_color_hover,
                            onChange: (value) => setAttributes({ parent_forum_color_hover: value }),
                            label: __('Parent Forum Hover Color', 'bbp-core'),
                        },
                    ]}
                />
            </InspectorControls>

            <ServerSideRender
                block="bbp-core/forum-ajax"
                attributes={attributes}
            />
        </div>
    );
}
