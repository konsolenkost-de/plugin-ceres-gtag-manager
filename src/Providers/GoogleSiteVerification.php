<?php

namespace GoogleTagManager\Providers;

use Plenty\Plugin\Templates\Twig;

class GoogleSiteVerification
{
    public function call(Twig $twig)
    {
        return $twig->render('GoogleTagManager::GoogleSiteVerification');
    }
}
