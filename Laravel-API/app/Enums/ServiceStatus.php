<?php

namespace Rhf\Enums;

use BenSampo\Enum\Enum;

final class ServiceStatus extends Enum
{
    public const LIVE = 'live';
    public const ISSUES = 'issues';
    public const DOWN = 'down';
}
