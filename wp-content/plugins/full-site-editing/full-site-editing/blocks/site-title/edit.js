/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { PlainText } from '@wordpress/editor';
import { withSelect, withDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';
import { ENTER } from '@wordpress/keycodes';

/**
 * Internal dependencies
 */
import useSiteOptions from '../useSiteOptions';

function SiteTitleEdit( {
	className,
	createErrorNotice,
	shouldUpdateSiteOption,
	isSelected,
	setAttributes,
	isLocked,
	insertDefaultBlock,
} ) {
	const inititalTitle = __( 'Site title loading…' );
	const { siteOptions, handleChange } = useSiteOptions(
		'title',
		inititalTitle,
		createErrorNotice,
		isSelected,
		shouldUpdateSiteOption,
		setAttributes
	);

	const { option } = siteOptions;

	const onKeyDown = event => {
		if ( event.keyCode !== ENTER ) {
			return;
		}
		event.preventDefault();
		if ( ! isLocked ) {
			insertDefaultBlock();
		}
	};

	return (
		<Fragment>
			<PlainText
				className={ classNames( 'site-title', className ) }
				value={ option }
				onChange={ value => handleChange( value ) }
				onKeyDown={ onKeyDown }
				placeholder={ __( 'Site Title' ) }
				aria-label={ __( 'Site Title' ) }
			/>
		</Fragment>
	);
}

export default compose( [
	withSelect( ( select, { clientId } ) => {
		const { isSavingPost, isPublishingPost, isAutosavingPost, isCurrentPostPublished } = select(
			'core/editor'
		);
		const { getBlockIndex, getBlockRootClientId, getTemplateLock } = select( 'core/block-editor' );
		const rootClientId = getBlockRootClientId( clientId );

		return {
			blockIndex: getBlockIndex( clientId, rootClientId ),
			isLocked: !! getTemplateLock( rootClientId ),
			rootClientId,
			shouldUpdateSiteOption:
				( ( isSavingPost() && isCurrentPostPublished() ) || isPublishingPost() ) &&
				! isAutosavingPost(),
		};
	} ),
	withDispatch( ( dispatch, { blockIndex, rootClientId } ) => ( {
		createErrorNotice: dispatch( 'core/notices' ).createErrorNotice,
		insertDefaultBlock: () =>
			dispatch( 'core/block-editor' ).insertDefaultBlock( {}, rootClientId, blockIndex + 1 ),
	} ) ),
] )( SiteTitleEdit );
