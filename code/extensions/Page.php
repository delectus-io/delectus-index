<?php

/**
 * DelectusIndexFileExtension adds event handlers for onBeforeWrite, onAfterWrite, onAfterPublish etc
 */
class DelectusIndexPageExtension extends DataExtension {

	private static $delectus_enabled = true;

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