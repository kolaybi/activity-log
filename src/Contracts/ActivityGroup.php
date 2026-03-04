<?php

namespace KolayBi\ActivityLog\Contracts;

/**
 * Marker interface for app-defined activity group enums.
 *
 * Apps implement as a backed string enum:
 * enum MyActivityGroup: string implements ActivityGroup { case COMPANY = 'company'; }
 */
interface ActivityGroup {}
