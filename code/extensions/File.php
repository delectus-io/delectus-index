<?php

class DelectusIndexFileExtension extends DelectusDataObjectExtension {

	// set to false to disable Delectus functions at runtime, e.g. during testing other functionality
	private static $delectus_enabled = true;

	public function onBeforeWrite() {
		parent::onBeforeWrite();
		if ( $this->owner->isChanged() ) {
			if ( $this->enabled() ) {
				if ( ! $this->owner->hasExtension( Versioned::class ) ) {
					DelectusIndexService::remove_file( $this->owner, __METHOD__, $responseMessage );
				}
			}

		}
	}

	public function onAfterWrite() {
		parent::onAfterWrite();
		if ( $this->enabled() ) {
			if ( ! $this->owner->hasExtension( Versioned::class ) ) {
				DelectusIndexService::add_file( $this->owner, __METHOD__, $responseMessage );
			}
		}
	}

	public function onAfterDelete() {
		parent::onAfterDelete();
		if ( $this->enabled() ) {
			DelectusIndexService::remove_file( $this->owner, __METHOD__, $responseMessage );
		}
	}

	public function onBeforePublish() {
		if ( $this->enabled() ) {
			DelectusIndexService::remove_file( $this->owner, __METHOD__, $responseMessage );
		}
	}

	public function onAfterPublish() {
		if ( $this->enabled() ) {
			DelectusIndexService::add_file( $this->owner, __METHOD__, $responseMessage );
		}
	}

	public function onAfterUnpublish() {
		if ( $this->enabled() ) {
			DelectusIndexService::remove_file( $this->owner, __METHOD__, $responseMessage );
		}
	}

}