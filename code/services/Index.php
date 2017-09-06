<?php

/**
 * DelectusIndexService talks to delectus services to add/remove/reindex and block files and pages
 */
class DelectusIndexService extends DelectusApiRequestService {
	// key of endpoint to lookup in config.endpoints
	const Endpoint = 'index';

	const ApiRequestClassName = DelectusIndexApiRequestModel::class;

	// actions which will be called on the Delectus api service to perform some work
	const ActionAdd     = 'add';
	const ActionRemove  = 'remove';
	const ActionReindex = 'reindex';
	const ActionBlock   = 'block';
	const ActionUnBlock = 'unblock';

	const TypeFile = 1;
	const TypePage = 2;

	// hint when the request should be sent (relative to now, strtotime compatible)
	private static $request_delay = '+1 min';

	// hint when the request should be processed by backend (relative to when it is received, strtotime compatible)
	private static $backend_delay = '+1 min';

	/**
	 * @param SiteTree|int|string $pageOrLinkOrID
	 *
	 * @return mixed
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 *
	 */
	public function addPage( $pageOrLinkOrID, $source, &$responseMessage ) {
		$response = null;

		try {
			if ( $page = static::resolve_page( $pageOrLinkOrID ) ) {
				if ( $page->ShowInSearch ) {
					$request = static::enqueue_request( $page, $source, self::ActionAdd );

					$resultCode = 0;
					$resultMessage = '';
					$result = $this->makeRequest( $request);

					if ($result) {
						$response = new DelectusIndexResponse(
							DelectusIndexResponse::ResponseCodeOK,
							$resultMessage,
							$result
						);
					} else {
						$response = new DelectusErrorResponse(
							$resultCode,
							$resultMessage,
							$result
						);
					}

					$request->write();

				} else {
					$responseMessage = _t(
						'Delectus.ShowInSearchMessage',
						"{type} {id} ShowInSearch precludes indexing",
						[
							'type' => 'Page',
							'id'   => $page->ID,
						]
					);
				}
			}
		} catch ( Exception $e ) {
			$responseMessage = _t(
				'Delectus.AddActionFailed',
				"Failed to add {type} to index: {exception}",
				[
					'type'      => 'Page',
					'exception' => $e->getMessage(),
				]
			);
		}

		return $response;
	}

	public function removePage( $pageOrLinkOrID, $source, &$responseMessage ) {
		$response = null;
		try {
			if ( $page = static::resolve_page( $pageOrLinkOrID ) ) {
				$request  = static::enqueue_request( $page, $source, self::ActionRemove );

				$response = $this->makeRequest( $request );

				$request->write();

			}
		} catch ( Exception $e ) {
			$responseMessage = _t(
				'Delectus.RemoveActionFailed',
				"Failed to remove {type} from index: {exception}",
				[
					'type'      => 'Page',
					'exception' => $e->getMessage(),
				]
			);

		}

		return $response;
	}

	public function addFile( $fileOrIDOrPath, $source, &$responseMessage ) {
		$response = null;

		try {
			if ( $file = static::resolve_file( $fileOrIDOrPath ) ) {
				if ( $file->ShowInSearch ) {
					$request  = static::enqueue_request( $file, $source, self::ActionAdd );

					$response = $this->makeRequest( $request );

					$request->write();

				}
			}
		} catch ( Exception $e ) {
			$responseMessage = _t(
				'Delectus.AddActionFailed',
				"Failed to add {type} to index: {exception}",
				[
					'type'      => 'File',
					'exception' => $e->getMessage(),
				]
			);
		}

		return $response;
	}

	public function removeFile( $fileOrLinkOrID, $source, &$responseMessage ) {
		$response = null;
		try {
			if ( $file = static::resolve_file( $fileOrLinkOrID ) ) {
				$request  = static::enqueue_request( $file, $source, self::ActionRemove );

				$response = $this->makeRequest( $request );

				$request->write();
			}
		} catch ( Exception $e ) {
			$responseMessage = _t(
				'Delectus.RemoveActionFailed',
				"Failed to remove {type} from index: {exception}",
				[
					'type'      => 'File',
					'exception' => $e->getMessage(),
				]
			);

		}

		return $response;
	}

	/**
	 * Given a Page model, a url or a Page ID return the Page for it or null
	 *
	 * @param $pageOrLinkOrID
	 *
	 * @return \DataObject|null|\SiteTree
	 * @throws \InvalidArgumentException
	 */
	protected static function resolve_page( $pageOrLinkOrID ) {
		if ( $pageOrLinkOrID instanceof SiteTree ) {
			$page = $pageOrLinkOrID;
		} elseif ( is_int( $pageOrLinkOrID ) ) {
			$page = SiteTree::get()->byID( $pageOrLinkOrID );
		} elseif ( is_string( $pageOrLinkOrID ) ) {
			$page = SiteTree::get_by_link( $pageOrLinkOrID );
		} else {
			$page = null;
		}

		return ( $page && $page->exists() ) ? $page : null;
	}

	/**
	 * Given a File model, a url or a File ID return the File for it or null
	 *
	 * @param $fileOrLinkOrID
	 *
	 * @return \DataObject|null|\SiteTree
	 * @throws \InvalidArgumentException
	 */
	protected static function resolve_file( $fileOrLinkOrID ) {
		if ( $fileOrLinkOrID instanceof File ) {
			$file = $fileOrLinkOrID;
		} elseif ( is_int( $fileOrLinkOrID ) ) {
			$file = File::get()->byID( $fileOrLinkOrID );
		} elseif ( is_string( $fileOrLinkOrID ) ) {
			$file = File::get()->filter( [
				'Filename' => trim( $fileOrLinkOrID, '/' ),
			] )->first();
		} else {
			$file = null;
		}

		return ( $file && $file->exists() ) ? $file : null;
	}

}