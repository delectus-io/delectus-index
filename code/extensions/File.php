<?php

class DelectusIndexFileExtension extends DelectusDataObjectExtension {

	// set to false to disable Delectus functions at runtime, e.g. during testing other functionality
	private static $delectus_enabled = true;

	protected function addDelectusTokenField() {
		return $this->owner->ParentID != 0;
	}

	public function onBeforeWrite() {
		parent::onBeforeWrite();
		if ( $this->owner->isChanged() ) {
			if ( $this->enabled() ) {
				if ( ! $this->owner->hasExtension( Versioned::class ) ) {
					$responseMessage = '';
					$result = DelectusIndexModule::index_service()->removeFile( $this->owner, __METHOD__, $responseMessage );
				}
			}

		}
	}

	public function onAfterWrite() {
		parent::onAfterWrite();
		if ( $this->enabled() ) {
			if ( ! $this->owner->hasExtension( Versioned::class ) ) {
				$responseMessage = '';
				$result = DelectusIndexModule::index_service()->addFile( $this->owner, __METHOD__, $responseMessage );
			}
		}
	}

	public function onAfterDelete() {
		parent::onAfterDelete();
		if ( $this->enabled() ) {
			$responseMessage = '';
			$result = DelectusIndexModule::index_service()->removeFile( $this->owner, __METHOD__, $responseMessage );
		}
	}

	public function onBeforePublish() {
		if ( $this->enabled() ) {
			$responseMessage = '';
			$result = DelectusIndexModule::index_service()->removeFile( $this->owner, __METHOD__, $responseMessage );
		}
	}

	public function onAfterPublish() {
		if ( $this->enabled() ) {
			$responseMessage = '';
			$result = DelectusIndexModule::index_service()->addFile( $this->owner, __METHOD__, $responseMessage );
		}
	}

	public function onAfterUnpublish() {
		if ( $this->enabled() ) {
			$responseMessage = '';
			$result = DelectusIndexModule::index_service()->removeFile( $this->owner, __METHOD__, $responseMessage );
		}
	}

}