<?php

namespace Rhf\Traits;

use function Sentry\captureMessage;

trait SentryTrait
{
    public static function createSentryError($message, $severity, $values = null)
    {
        switch ($severity) {
            case 'fatal':
                $status = \Sentry\Severity::fatal();
                break;
            case 'error':
                $status = \Sentry\Severity::error();
                break;
            case 'info':
                $status = \Sentry\Severity::info();
                break;
            case 'debug':
                $status = \Sentry\Severity::debug();
                break;
            case 'warning':
                $status = \Sentry\Severity::debug();
                break;
            default:
                $status = \Sentry\Severity::error();
                break;
        }
        captureMessage($message, $status);
    }
}
