import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck, PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, Button, Spinner } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useSelect } from '@wordpress/data';

export default function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps();
    const {
        forum_id,
        style,
        cover_image,
        ppp,
        order,
        word_length,
        read_more,
        title_color,
        excerpt_color
    } = attributes;

    // Fetch forums for the select control
    const forums = useSelect((select) => {
        return select('core').getEntityRecords('postType', 'forum', { per_page: -1 });
    }, []);

    const forumOptions = forums ? forums.map((forum) => ({
        label: forum.title.rendered,
        value: forum.id,
    })) : [];

    // Add a default option
    forumOptions.unshift({ label: __('Select Forum', 'bbp-core'), value: '' });

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Preset Skins', 'bbp-core')}>
                    <SelectControl
                        label={__('Select Forum', 'bbp-core')}
                        value={forum_id}
                        options={forumOptions}
                        onChange={(value) => setAttributes({ forum_id: value })}
                    />
                    {!forums && <Spinner />}
                    <SelectControl
                        label={__('Forums Style', 'bbp-core')}
                        value={style}
                        options={[
                            { label: __('01 : Single Forum With Topics', 'bbp-core'), value: '1' },
                            { label: __('02 : Single Forum', 'bbp-core'), value: '2' },
                        ]}
                        onChange={(value) => setAttributes({ style: value })}
                    />
                </PanelBody>

                <PanelBody title={__('Thumbnail', 'bbp-core')}>
                    <p>{__('Custom Cover Image', 'bbp-core')}</p>
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={(media) => setAttributes({ cover_image: media })}
                            allowedTypes={['image']}
                            value={cover_image ? cover_image.id : ''}
                            render={({ open }) => (
                                <Button variant="secondary" onClick={open}>
                                    {cover_image ? __('Replace Image', 'bbp-core') : __('Select Image', 'bbp-core')}
                                </Button>
                            )}
                        />
                    </MediaUploadCheck>
                    {cover_image && (
                        <div style={{ marginTop: '10px' }}>
                            <img src={cover_image.url} alt="Cover" style={{ maxWidth: '100%' }} />
                            <Button isLink isDestructive onClick={() => setAttributes({ cover_image: null })}>
                                {__('Remove Image', 'bbp-core')}
                            </Button>
                        </div>
                    )}
                </PanelBody>

                <PanelBody title={__('Filter Options', 'bbp-core')}>
                    <TextControl
                        label={__('Topics', 'bbp-core')}
                        type="number"
                        value={ppp}
                        onChange={(value) => setAttributes({ ppp: parseInt(value) })}
                        help={__('Maximum number of topics.', 'bbp-core')}
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
                        label={__('Number of Words', 'bbp-core')}
                        type="number"
                        value={word_length}
                        onChange={(value) => setAttributes({ word_length: parseInt(value) })}
                    />
                    <TextControl
                        label={__('Read More Text', 'bbp-core')}
                        value={read_more}
                        onChange={(value) => setAttributes({ read_more: value })}
                    />
                </PanelBody>

                <PanelColorSettings
                    title={__('Styles', 'bbp-core')}
                    initialOpen={false}
                    colorSettings={[
                        {
                            value: title_color,
                            onChange: (value) => setAttributes({ title_color: value }),
                            label: __('Title Color', 'bbp-core'),
                        },
                        {
                            value: excerpt_color,
                            onChange: (value) => setAttributes({ excerpt_color: value }),
                            label: __('Excerpt Color', 'bbp-core'),
                        },
                    ]}
                />
            </InspectorControls>

            <ServerSideRender
                block="bbp-core/single-forum"
                attributes={attributes}
            />
        </div>
    );
}
