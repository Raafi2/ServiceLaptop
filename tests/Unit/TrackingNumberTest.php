<?php

namespace Tests\Unit;

use App\Helpers\TrackingNumber;
use Tests\TestCase;

class TrackingNumberTest extends TestCase
{
    public function test_tracking_number_generates_correct_length_and_format(): void
    {
        $trackingNo = TrackingNumber::generate();

        $this->assertStringStartsWith('SL-', $trackingNo);
        $code = substr($trackingNo, 3);
        $this->assertEquals(6, strlen($code));
        $this->assertMatchesRegularExpression('/^[ABCDEFGHJKMNPQRSTUVWXYZ23456789]{6}$/', $code);
    }

    public function test_tracking_number_validation_and_normalization(): void
    {
        $valid = 'SL-ABC234';
        $this->assertTrue(TrackingNumber::isValid($valid));
        $this->assertEquals('SL-ABC234', TrackingNumber::normalize($valid));

        // Input dengan spasi dan lowercase
        $this->assertEquals('SL-ABC234', TrackingNumber::normalize('sl-abc 234'));
        $this->assertEquals('SL-ABC234', TrackingNumber::normalize('  sl-abc234  '));

        // Format yang valid: harus SL- diikuti 6 karakter dari charset
        $this->assertTrue(TrackingNumber::isValid('SL-ABC234'));
        $this->assertTrue(TrackingNumber::isValid('SL-XYZABC'));

        // Karakter terlarang seperti I, O, L, 0, 1
        $this->assertFalse(TrackingNumber::isValid('SL-ABCI01'));
        $this->assertFalse(TrackingNumber::isValid('SL-ABC1234')); // 7 karakter
        $this->assertFalse(TrackingNumber::isValid('SL-ABC12'));   // 5 karakter
        $this->assertFalse(TrackingNumber::isValid('ABC234'));     // Tanpa prefix SL-
    }
}
