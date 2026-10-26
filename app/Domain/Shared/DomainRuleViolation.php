<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use RuntimeException;

abstract class DomainRuleViolation extends RuntimeException {}
