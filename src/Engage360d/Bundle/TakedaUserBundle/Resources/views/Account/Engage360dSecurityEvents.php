<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\SecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class Engage360dSecurityEvents
{
    const REGISTRATION_SUCCESS = 'engage360d_security.registration.success';
    const RESETTING_USER_PASSWORD = 'engage360d_security.resetting.user.password';
}
