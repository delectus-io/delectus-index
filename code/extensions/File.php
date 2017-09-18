<?php

/**
 * DelectusIndexFileExtension adds event handlers for onBeforeWrite, onAfterWrite, onAfterPublish etc
 */
class DelectusIndexFileExtension extends \DataExtension {

	// set to false to disable Delectus functions at runtime, e.g. during testing other functionality
	private static $delectus_enabled = true;

	protected function shouldAddDelectusTokenField() {
		return $this->owner->ParentID != 0;
	}

	public function shouldRemoveFromIndex() {
		return $this->enabled() &&
		       $this->owner->isInDB()
		       && $this->owner->isChanged()
		       && $this->owner->{DelectusFileExtension::LastUpdatedFieldName};
	}

	public function shouldAddToIndex() {
		$resourcesFolder = \DelectusModule::resources_folder($this->owner)->Filename;

		return $this->enabled()
			&& $this->owner->isChanged()
			&& strtolower(substr($this->owner->Filename, 0, strlen($resourcesFolder))) == strtolower($resourcesFolder);
	}

	public function onBeforeWrite() {
		parent::onBeforeWrite();
		if ( $this->shouldRemoveFromIndex() ) {
			if ( $this->enabled() ) {
				if ( ! $this->owner->hasExtension( Versioned::class ) ) {
					$responseMessage = '';
					DelectusIndexModule::index_service()->removeFile( $this->owner, __METHOD__, $responseMessage );
				}
			}

		}
	}

	public function onBeforeDelete() {
		parent::onBeforeDelete();
		if ( $this->shouldRemoveFromIndex()) {
			$responseMessage = '';
			DelectusIndexModule::index_service()->removeFile( $this->owner, __METHOD__, $responseMessage );
		}
	}

	public function onAfterWrite() {
		parent::onAfterWrite();
		if ( $this->shouldAddToIndex() ) {
			if ( ! $this->owner->hasExtension( Versioned::class ) ) {
				$responseMessage = '';
				DelectusIndexModule::index_service()->addFile( $this->owner, __METHOD__, $responseMessage );
			}
		}
	}

	public function onAfterPublish() {
		if ( $this->shouldAddToIndex() ) {
			$responseMessage = '';
			DelectusIndexModule::index_service()->addFile( $this->owner, __METHOD__, $responseMessage );
		}
	}

	public function onBeforeUnpublish() {
		if ( $this->shouldRemoveFromIndex() ) {
			$responseMessage = '';
			DelectusIndexModule::index_service()->removeFile( $this->owner, __METHOD__, $responseMessage );
		}
	}

	/**
	 * Set and/or get the current enabled state of this extension.
	 *
	 * @param null|bool $enable if passed then use it to set the enabled state of this extension
	 *
	 * @return bool if enable parameter was passed this will be the previous value otherwise the current value
	 */
	public function enabled( $enable = null ) {
		if ( func_num_args() ) {
			$return = \Config::inst()->get( static::class, 'delectus_enabled' );
			\Config::inst()->update( static::class, 'delectus_enabled', $enable );
		} else {
			$return = \Config::inst()->get( static::class, 'delectus_enabled' );
		}

		return (bool) $return;
	}
}