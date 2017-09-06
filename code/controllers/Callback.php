<?php
use DelectusModule as Module;
/**
 * DelectusCallbackController accepts callbacks from Delectus services to indicate that a request has completed.
 */
class DelectusIndexCallbackController extends DelectusCallbackController {
	const ModelClass = DelectusApiRequestModel::class;

	const ActionAdded     = 'added';
	const ActionRemoved   = 'removed';
	const ActionReindexed = 'reindexed';
	const ActionBlocked   = 'blocked';
	const ActionUnBlocked = 'unblocked';

	private static $allowed_actions = [
		self::ActionAdded     => '->checkRequestIsValid',
		self::ActionRemoved   => '->checkRequestIsValid',
		self::ActionReindexed => '->checkRequestIsValid',
		self::ActionBlocked   => '->checkRequestIsValid',
		self::ActionUnBlocked => '->checkRequestIsValid',
	];

	public function added( SS_HTTPRequest $request ) {
		$this->updateModel( $request, self::ActionAdded);
	}

	public function removed( SS_HTTPRequest $request ) {
		$this->updateModel( $request, self::ActionRemoved);

	}

	public function reindexed( SS_HTTPRequest $request ) {
		$this->updateModel( $request, self::ActionReindexed);

	}

	public function blocked( SS_HTTPRequest $request ) {
		$this->updateModel( $request, self::ActionBlocked );

	}

	public function unblocked( SS_HTTPRequest $request ) {
		$this->updateModel( $request, self::ActionUnBlocked );

	}

	protected function updateModel(SS_HTTPRequest $request, $action) {
		/** @var \DelectusApiRequestModel $model */
		if ( $model = $this->currentModel() ) {

			$transport = DelectusIndexModule::transport();

			$data = $transport->decode( $request->getBody(), $this->getRequest()->getHeader( $transport::ContentTypeHeader ) );

			if ( $data['ResponseCode'] == 200 ) {
				$className = $data['ModelClass'];

				if ( $fileOrPage = $className::get()->byID( $data['ModelID'] ) ) {
					$fileOrPage->{DelectusModelExtension::StatusFieldName}      = self::ActionAdded;
					$fileOrPage->{DelectusModelExtension::LastUpdatedFieldName} = date( 'Y-m-d H:i:s' );
					$fileOrPage->write();
				}
			}

		}
	}

	/**
	 * Return a client from the request (either by auth token or login)
	 *
	 * @return null
	 */
	public function currentClient( ) {
		return null;
	}

	/**
	 * Return the model from the request this will be an DelectusApiRequestModel derived class
	 *
	 * @return \DelectusApiRequestModel
	 * @throws \InvalidArgumentException
	 * @internal param null|\SS_HTTPRequest $request
	 *
	 */
	public function currentModel( ) {
		$request     = $this->getRequest();
		$transport = DelectusModule::transport();

		$data        = $transport->decode( $request->getBody(), $request->getHeader( $transport::ContentTypeHeader) );

		return DelectusApiRequestModel::get()->find(DelectusApiRequestModel::RequestTokenKey, $data['RequestToken']);
	}

	/**
	 * Return the client for the current model (resolve relationships from currentModel to its Client)
	 *
	 * @return
	 */
	public function currentModelClient() {
		return null;
	}

	/**
	 * Render json outcome of the callback for delectus to process. Also needs
	 * to set a valid HTTP response code.
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	protected function renderResponse( array $data = [] ) {
		// TODO: Implement renderResponse() method.
	}
}