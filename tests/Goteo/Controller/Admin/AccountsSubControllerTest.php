<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\AccountsSubController;

class AccountsSubControllerTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new AccountsSubController();

        $this->assertInstanceOf('\Goteo\Controller\Admin\AccountsSubController', $controller);

        return $controller;
    }
}
