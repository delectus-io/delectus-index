<?php

class DelectusIndexFileTest extends FunctionalTest {
	protected static $fixture_file = 'delectus-tests.yml';

	public function setUp() {
		parent::setUp();
		$this->logInWithPermission( 'ADMIN' );
	}

	public function testAddFileAddsToIndex() {

		$file = $this->uploadTestFile();

		$requests = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => $file->ClassName,
			'ModelID'    => $file->ID,
			'Action'     => 'add',
		] );

		$this->assertEquals( 1, $requests->count(), "That a single request has been made to add the file" );

	}

	public function testDeleteRemovesFromIndex() {
		$file = $this->uploadTestFile();

		// preserve the ID
		$clone = clone $file;

		$file->delete();

		$requests = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => $clone->ClassName,
			'ModelID'    => $clone->ID,
			'Action'     => 'remove',
		] );

		$this->assertEquals( 1, $requests->count(), "That a single request has been made to remove the file" );

	}

	public function testQueuedAddFileTask() {
		$file = $this->uploadTestFile();

		$queueHandler = new QueueRunner();
		$queueHandler->runQueue( DelectusIndexJob::queue_name() );

		/** @var \DelectusApiRequestModel $request */
		$request = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => $file->ClassName,
			'ModelID'    => $file->ID,
		] )->first();

		$this->assertEquals( $request::StatusCompleted, $request->Status, "that request Status is Completed" );
		$this->assertEquals( $request::OutcomeSuccess, $request->Outcome, "that request Outcome is Success" );

	}

	protected function uploadTestFile() {
		$testFileName = 'delectustest.gif';

		$file = new File();

		$uploader = new Upload();

		$tmp = [
			'name'     => $testFileName,
			'tmp_name' => __DIR__ . "/$testFileName",
			'size'     => filesize( __DIR__ . "/$testFileName" ),
		];

		$uploader->loadIntoFile( $tmp, $file, '_delectus-tests' );
		$file->write();

		$uploadedPathName = Controller::join_links(
			BASE_PATH,
			$file->getFilename()
		);

		$this->assertEquals( true, file_exists( $uploadedPathName ), "That file uploaded into $uploadedPathName correctly" );

		return $file;
	}

}