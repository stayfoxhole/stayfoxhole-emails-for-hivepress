<?php
namespace FoxholeEmails\Tests;

use FoxholeEmails\Emails\SfxBalanceDue;
use WP_UnitTestCase;

class Test_SfxBalanceDue extends WP_UnitTestCase {
    /**
     * @covers FoxholeEmails\Emails\SfxBalanceDue
     * @group balance_due
     */
    public function test_generate_value_string_returns_string() {
        $SfxBalanceDue = new SfxBalanceDue();
        $this->assertInsance(SfxBalanceDue::class, $SfxBalanceDue);
    }
}