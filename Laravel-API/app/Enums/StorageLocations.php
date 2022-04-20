<?php

namespace Rhf\Enums;

use BenSampo\Enum\Enum;

final class StorageLocations extends Enum
{
    public const S3_PUBLIC = 's3-public';
    public const S3_PRIVATE = 's3-private';
    public const SPACES = 'spaces';
}
