<?php
use DelectusModule as Module;
/**
 * DelectusCallbackController accepts callbacks from Delectus services to indicate that a request has completed.
 */
class DelectusIndexCallbackController extends DelectusCallbackController {
	const ModelClass = DelectusApiRequest::class;

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
		/** @var \DelectusApiRequest $model */
		if ( $model = $this->currentModel( ) ) {
			$data = Module::decode_data( $request->getBody());
			if ($data['ResponseCode'] == 200) {
				$className = $data['ModelClass'];

				if ($fileOrPage = $className::get()->byID($data['ModelID'])) {
					$fileOrPage->DelectusStatus = self::ActionAdded;
					$fileOrPage->DelectusUpdated = data('Y-m-d H:i:s');
					$fileOrPage->write();
				}

			}

		}

	}

	public function removed( SS_HTTPRequest $request ) {

	}

	public function reindexed( SS_HTTPRequest $request ) {

	}

	public function blocked( SS_HTTPRequest $request ) {

	}

	public function unblocked( SS_HTTPRequest $request ) {

	}

	/**
	 * Return a client from the request (either by auth token or login)
	 *
	 * @return \Delectus\Models\Client
	 */
	public function currentClient( ) {
		return null;
	}

	/**
	 * Return the model from the request this will be an DelectusApiRequest derived class
	 *
	 * @return \DelectusApiRequest
	 * @throws \InvalidArgumentException
	 * @internal param null|\SS_HTTPRequest $request
	 *
	 */
	public function currentModel( ) {
		$request     = $this->getRequest();
		$data        = Module::decode_data( $request->getBody() );
		return DelectusApiRequest::get()->find(DelectusApiRequest::RequestTokenKey, $data['RequestToken']);
	}

	/**
	 * Return the client for the current model (resolve relationships from currentModel to its Client)
	 *
	 * @return \Delectus\Models\Client
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