<?php
namespace FoxholeEmails\Tests;

use FoxholeEmails\Emails\SfxhBalanceDue;
use WP_UnitTestCase;

class Test_SfxhBalanceDue extends WP_UnitTestCase {
    /**
     * @covers FoxholeEmails\Emails\SfxBalanceDue
     * @group balance_due
     */
    public function test_generate_value_string_returns_string() {
        $SfxhBalanceDue = new SfxhBalanceDue();
        $this->assertInstanceOf(SfxhBalanceDue::class, $SfxhBalanceDue);
    }
}