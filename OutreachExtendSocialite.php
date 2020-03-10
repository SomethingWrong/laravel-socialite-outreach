<?php

namespace SocialiteProviders\Outreach;

use SocialiteProviders\Manager\SocialiteWasCalled;

class OutreachExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('outreach', __NAMESPACE__.'\Provider');
    }
}
