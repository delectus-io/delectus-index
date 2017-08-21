<?php

class DelectusIndexPageTest extends FunctionalTest {
	protected static $fixture_file = 'delectus-index-page-test.yml';

	public function setUp() {
		parent::setUp();
		$this->logInWithPermission( 'ADMIN' );
	}

	public function testPublishAddsToIndex() {
		/** @var Page|Versioned $page */
		$page = $this->objFromFixture( 'Page', 'page1' );
		$page->doPublish();

		$requests = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => 'Page',
			'ModelToken' => $page->{DelectusIndexPageExtension::ModelTokenFieldName},
			'Action'     => 'add',
		] )->count();

		$this->assertEquals( 1, $requests, "That a single request has been made to add the page" );
	}

	public function testUnpublishRemovesFromIndex() {
		/** @var Page|Versioned $page */
		$page = $this->objFromFixture( 'Page', 'page2' );
		$page->doPublish();

		$page->doUnpublish();

		$requests = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => 'Page',
			'ModelToken' => $page->{DelectusIndexPageExtension::ModelTokenFieldName},
			'Action'     => 'remove',
		] )->count();

		$this->assertEquals( 1, $requests, "That a single request has been made to add the page" );
	}

	public function testDeleteFromLiveRemovesFromIndex() {
		/** @var Page|Versioned $page */
		$page = $this->objFromFixture( 'Page', 'page3' );
		$page->doPublish();

		$page->deleteFromStage( 'Live' );
		$requests = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => 'Page',
			'ModelToken' => $page->{DelectusIndexPageExtension::ModelTokenFieldName},
			'Action'     => 'remove',
		] )->count();

		$this->assertEquals( 1, $requests, "That a single request has been made to add the page" );

	}

	public function testDeleteFromStageLeavesInIndex() {
		/** @var Page|Versioned $page */
		$page = $this->objFromFixture( 'Page', 'page4' );
		$page->doPublish();

		$page->deleteFromStage( 'Stage' );

		$requests = DelectusApiRequestModel::get()->filter( [
			'ModelClass' => 'Page',
			'ModelToken' => $page->{DelectusIndexPageExtension::ModelTokenFieldName},
			'Action'     => 'remove',
		] )->count();

		$this->assertEquals( 0, $requests, "That a single request has been made to add the page" );

	}
}