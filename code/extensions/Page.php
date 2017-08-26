<?php

/**
 * DelectusPageExtension added to Page class provides fields needed by Delectus to track indexing and search and hooks to make sure
 * changes made to pages in the CMS are advertised to delectus
 */
class DelectusIndexPageExtension extends DelectusDataObjectExtension {

	public function onAfterPublish() {
		if ( $this->enabled() ) {
			$responseMessage = '';
			DelectusIndexModule::index_service()->addPage( $this->owner, __METHOD__, $responseMessage);
		}
	}

	public function onBeforeUnpublish() {
		if ( $this->enabled() ) {
			$responseMessage = '';
			DelectusIndexModule::index_service()->removePage( $this->owner, __METHOD__, $responseMessage);
		}
	}

	public function onBeforeDelete() {
		parent::onBeforeDelete();
		if ($this->enabled()) {
			$responseMessage = '';
			DelectusIndexModule::index_service()->removePage( $this->owner, __METHOD__, $responseMessage );
		}
	}

}