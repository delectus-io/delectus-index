<?php

/**
 * DelectusPageExtension added to Page class provides fields needed by Delectus to track indexing and search and hooks to make sure
 * changes made to pages in the CMS are advertised to delectus
 */
class DelectusIndexPageExtension extends DelectusDataObjectExtension {

	public function onBeforePublish() {
		if ( $this->enabled() ) {
			DelectusIndexService::add_page( $this->owner, __METHOD__, $responseMessage);
		}

	}

	public function onAfterUnpublish() {
		if ( $this->enabled() ) {
			DelectusIndexService::remove_page( $this->owner, __METHOD__, $responseMessage);
		}
	}

	public function onAfterDelete() {
		parent::onAfterDelete();
		if ($this->enabled()) {
			DelectusIndexService::remove_page( $this->owner, __METHOD__, $responseMessage );
		}
	}

}