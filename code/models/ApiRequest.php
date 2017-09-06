<?php
class DelectusIndexApiRequestModel extends DelectusApiRequestModel {
	private static $db = [
		'ModelClass' => 'Varchar(255)',
		'ModelID'    => 'Int',
		'ModelToken' => 'Varchar(255)',
	];

	public function onBeforeWrite() {
		$this->Environment = Director::get_environment_type();
		parent::onBeforeWrite();
	}

	/**
	 * Return the model from ModelClass and ModelID or null if can't or it doesn't exist in database (anymore)
	 *
	 * @return \DataObject
	 * @throws \DelectusException
	 */
	public function getModel() {
		$model = null;
		if ( $this->ModelID && $this->ModelClass && ClassInfo::exists( $this->ModelClass ) ) {
			$modelClass = $this->ModelClass;
			$model      = $modelClass::get()->byID( $this->ModelID );
		}

		return $model;
	}

	/**
	 * @param DataObject|\DelectusDataObjectExtension $model
	 */
	public function setModel( $model ) {
		$this->ModelClass = $model->ClassName;
		$this->ModelID    = $model->ID;
		$this->ModelToken = $model->{$model->modelTokenFieldName()};
	}

	public function ModelTitle() {
		if ( $model = $this->getModel() ) {
			return $model->Title;
		} else {
			return '';
		}
	}

	public function ModelLink() {
		if ( $model = $this->getModel() ) {
			return $model->Link();
		} else {
			return '';
		}
	}


}