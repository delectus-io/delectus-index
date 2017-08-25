<?php

class DelectusIndexPageTest extends FunctionalTest {
	protected static $fixture_file = 'delectus-tests.yml';

	public function setUp() {
		parent::setUp();
		$this->logInWithPermission( 'ADMIN' );
	}

	public function testPublishAddsToIndex() {
		/** @var Page|Versioned $page */
		$page = $this->objFromFixture( 'Page', 'page1' );
		$page->doPublish();

		$requests = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => $page->ClassName,
			'ModelID'    => $page->ID,
			'Action'     => 'add',
		] );

		$this->assertEquals( 1, $requests->count(), "That a single request has been made to add the page" );
	}

	public function testUnpublishRemovesFromIndex() {
		/** @var Page|Versioned $page */
		$page = $this->objFromFixture( 'Page', 'page2' );
		$page->doPublish();

		$requests = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => $page->ClassName,
			'ModelID'    => $page->ID,
			'Action'     => 'add',
		] );
		$this->assertEquals( 1, $requests->count(), "That a single request has been made to add the page" );

		$page->doUnpublish();

		$requests = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => $page->ClassName,
			'ModelID'    => $page->ID,
			'Action'     => 'remove',
		] );

		$this->assertEquals( 1, $requests->count(), "That a single request has been made to remove the page" );
	}

	public function testDeleteFromLiveRemovesFromIndex() {
		/** @var Page|Versioned $page */
		$page = $this->objFromFixture( 'Page', 'page3' );
		$page->doPublish();

		$requests = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => $page->ClassName,
			'ModelID'    => $page->ID,
			'Action'     => 'add',
		] );
		$this->assertEquals( 1, $requests->count(), "That a single request has been made to add the page" );

		$page->doDeleteFromLive();
		$requests = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => $page->ClassName,
			'ModelID'    => $page->ID,
			'Action'     => 'remove',
		] );

		$this->assertEquals( 1, $requests->count(), "That a single request has been made to remove the page" );

	}

	public function testQueuedAddPageTask() {
		$page = $this->objFromFixture( 'Page', 'page4' );
		$page->doPublish();

		$queueHandler = new QueueRunner();
		$queueHandler->runQueue( DelectusIndexJob::queue_name() );

		/** @var \DelectusApiRequestModel $request */
		$request = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => $page->ClassName,
			'ModelID'    => $page->ID,
		] )->first();

		$this->assertEquals( $request::StatusCompleted, $request->Status, "that request Status is Completed" );
		$this->assertEquals( $request::OutcomeSuccess, $request->Outcome, "that request Outcome is Success" );

	}
}