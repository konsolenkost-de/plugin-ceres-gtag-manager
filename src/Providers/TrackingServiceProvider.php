<?php

namespace GoogleTagManager\Providers;

use Plenty\Plugin\ServiceProvider;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\Templates\Twig;
use IO\Services\ItemSearch\Helper\ResultFieldTemplate;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Order\Events\OrderCreated;
use Plenty\Modules\System\Contracts\WebstoreConfigurationRepositoryContract;
use Plenty\Modules\Webshop\Consent\Contracts\ConsentRepositoryContract;
use Plenty\Plugin\ConfigRepository;
use IO\Extensions\Constants\ShopUrls;
use IO\Services\UrlBuilder\UrlQuery;

class TrackingServiceProvider extends ServiceProvider
{

    const PRIORITY = 0;

    /**
     * Register the service provider.
     */

    public function register()
    {
        /** @var ConsentRepositoryContract $consentRepository */
        $consentRepository = pluginApp(ConsentRepositoryContract::class);

        /** @var ConfigRepository $config */
        $config = pluginApp(ConfigRepository::class);

        $consentRepository->registerConsentGroup(
            'other',
            'GoogleTagManager::CookieConsent.consentGroupOtherLabel',
            [
                'position' => 300,
                'description' => 'GoogleTagManager::CookieConsent.consentGroupOtherDescription'
            ]
        );

        if ($config->get('GoogleTagManager.showProductList') === 'true') {
            $consentRepository->registerConsent(
                'gtmProductList',
                'GoogleTagManager::CookieConsent.consentProductListLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentProductListDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentProductListProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentProductListLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentProductListPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupProductList', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentProductListNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentProductListIsOptOut') === 'true',
                    'cookieNames' => array_map('trim', explode(',', $config->get('GoogleTagManager.consentProductListCookieNames')))

                ]
            );
        }

        if ($config->get('GoogleTagManager.showGoogleAnalytics') === 'true') {
            $consentRepository->registerConsent(
                'gtmGoogleAnalytics',
                'GoogleTagManager::CookieConsent.consentGoogleAnalyticsLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentGoogleAnalyticsDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentGoogleAnalyticsProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentGoogleAnalyticsLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentGoogleAnalyticsPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupGoogleAnalytics', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentGoogleAnalyticsNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentGoogleAnalyticsIsOptOut') === 'true',
                    'cookieNames' => array_map('trim', explode(',', $config->get('GoogleTagManager.consentGoogleAnalyticsCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showGoogleAds') === 'true') {
            $consentRepository->registerConsent(
                'gtmGoogleAds',
                'GoogleTagManager::CookieConsent.consentGoogleAdsLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentGoogleAdsDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentGoogleAdsProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentGoogleAdsLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentGoogleAdsPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupGoogleAds', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentGoogleAdsNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentGoogleAdsIsOptOut') === 'true',
                    'cookieNames' => array_map('trim', explode(',', $config->get('GoogleTagManager.consentGoogleAdsCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showFacebook') === 'true') {
            $consentRepository->registerConsent(
                'gtmFacebook',
                'GoogleTagManager::CookieConsent.consentFacebookLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentFacebookDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentFacebookProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentFacebookLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentFacebookPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupFacebook', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentFacebookNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentFacebookIsOptOut') === 'true',
                    'cookieNames' => array_map('trim', explode(',', $config->get('GoogleTagManager.consentFacebookCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showPinterest') === 'true') {
            $consentRepository->registerConsent(
                'gtmPinterest',
                'GoogleTagManager::CookieConsent.consentPinterestLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentPinterestDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentPinterestProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentPinterestLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentPinterestPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupPinterest', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentPinterestNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentPinterestIsOptOut') === 'true',
                    'cookieNames' => array_map('trim', explode(',', $config->get('GoogleTagManager.consentPinterestCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showBilligerDe') === 'true') {
            $consentRepository->registerConsent(
                'gtmBilligerDe',
                'GoogleTagManager::CookieConsent.consentBilligerDeLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentBilligerDeDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentBilligerDeProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentBilligerDeLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentBilligerDePolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupBilligerDe', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentBilligerDeNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentBilligerDeIsOptOut') === 'true',
                    'cookieNames' => array_map('trim', explode(',', $config->get('GoogleTagManager.consentBilligerDeCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showKelkoo') === 'true') {
            $consentRepository->registerConsent(
                'gtmKelkoo',
                'GoogleTagManager::CookieConsent.consentKelkooLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentKelkooDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentKelkooProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentKelkooLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentKelkooPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupKelkoo', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentKelkooNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentKelkooIsOptOut') === 'true',
                    'cookieNames' => array_map('trim', explode(',', $config->get('GoogleTagManager.consentKelkooCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showPaypal') === 'true') {
            $consentRepository->registerConsent(
                'gtmPaypal',
                'GoogleTagManager::CookieConsent.consentPaypalLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentPaypalDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentPaypalProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentPaypalLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentPaypalPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupPaypal', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentPaypalNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentPaypalIsOptOut') === 'true',
                    'cookieNames' => array_map('trim', explode(',', $config->get('GoogleTagManager.consentPaypalCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showAwin') === 'true') {
            $consentRepository->registerConsent(
                'gtmAwin',
                'GoogleTagManager::CookieConsent.consentAwinLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentAwinDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentAwinProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentAwinLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentAwinPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupAwin', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentAwinNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentAwinIsOptOut') === 'true',
                    'cookieNames' => array_map('trim', explode(',', $config->get('GoogleTagManager.consentAwinCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showWebgains') === 'true') {
            $consentRepository->registerConsent(
                'gtmWebgains',
                'GoogleTagManager::CookieConsent.consentWebgainsLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentWebgainsDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentWebgainsProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentWebgainsLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentWebgainsPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupWebgains', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentWebgainsNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentWebgainsIsOptOut') === 'true',
                    'cookieNames' => array_map('trim', explode(',', $config->get('GoogleTagManager.consentWebgainsCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showCustomCookieOne') === 'true') {
            $consentRepository->registerConsent(
                'gtmCustomCookieOne',
                'GoogleTagManager::CookieConsent.consentCustomCookieOneLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentCustomCookieOneDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentCustomCookieOneProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentCustomCookieOneLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentCustomCookieOnePolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupCustomCookieOne', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentCustomCookieOneNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentCustomCookieOneIsOptOut') === 'true',
                    'cookieNames' =>  array_map('trim', explode(',', $config->get('GoogleTagManager.consentCustomCookieOneCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showCustomCookieTwo') === 'true') {
            $consentRepository->registerConsent(
                'gtmCustomCookieTwo',
                'GoogleTagManager::CookieConsent.consentCustomCookieTwoLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentCustomCookieTwoDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentCustomCookieTwoProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentCustomCookieTwoLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentCustomCookieTwoPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupCustomCookieTwo', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentCustomCookieTwoNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentCustomCookieTwoIsOptOut') === 'true',
                    'cookieNames' =>  array_map('trim', explode(',', $config->get('GoogleTagManager.consentCustomCookieTwoCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showCustomCookieThree') === 'true') {
            $consentRepository->registerConsent(
                'gtmCustomCookieThree',
                'GoogleTagManager::CookieConsent.consentCustomCookieThreeLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentCustomCookieThreeDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentCustomCookieThreeProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentCustomCookieThreeLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentCustomCookieThreePolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupCustomCookieThree', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentCustomCookieThreeNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentCustomCookieThreeIsOptOut') === 'true',
                    'cookieNames' =>  array_map('trim', explode(',', $config->get('GoogleTagManager.consentCustomCookieThreeCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showCustomCookieFour') === 'true') {
            $consentRepository->registerConsent(
                'gtmCustomCookieFour',
                'GoogleTagManager::CookieConsent.consentCustomCookieFourLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentCustomCookieFourDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentCustomCookieFourProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentCustomCookieFourLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentCustomCookieFourPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupCustomCookieFour', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentCustomCookieFourNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentCustomCookieFourIsOptOut') === 'true',
                    'cookieNames' =>  array_map('trim', explode(',', $config->get('GoogleTagManager.consentCustomCookieFourCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showCustomCookieFive') === 'true') {
            $consentRepository->registerConsent(
                'gtmCustomCookieFive',
                'GoogleTagManager::CookieConsent.consentCustomCookieFiveLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentCustomCookieFiveDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentCustomCookieFiveProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentCustomCookieFiveLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentCustomCookieFivePolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupCustomCookieFive', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentCustomCookieFiveNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentCustomCookieFiveIsOptOut') === 'true',
                    'cookieNames' =>  array_map('trim', explode(',', $config->get('GoogleTagManager.consentCustomCookieFiveCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showCustomCookieSix') === 'true') {
            $consentRepository->registerConsent(
                'gtmCustomCookieSix',
                'GoogleTagManager::CookieConsent.consentCustomCookieSixLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentCustomCookieSixDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentCustomCookieSixProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentCustomCookieSixLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentCustomCookieSixPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupCustomCookieSix', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentCustomCookieSixNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentCustomCookieSixIsOptOut') === 'true',
                    'cookieNames' =>  array_map('trim', explode(',', $config->get('GoogleTagManager.consentCustomCookieSixCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showCustomCookieSeven') === 'true') {
            $consentRepository->registerConsent(
                'gtmCustomCookieSeven',
                'GoogleTagManager::CookieConsent.consentCustomCookieSevenLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentCustomCookieSevenDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentCustomCookieSevenProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentCustomCookieSevenLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentCustomCookieSevenPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupCustomCookieSeven', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentCustomCookieSevenNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentCustomCookieSevenIsOptOut') === 'true',
                    'cookieNames' =>  array_map('trim', explode(',', $config->get('GoogleTagManager.consentCustomCookieSevenCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showCustomCookieEight') === 'true') {
            $consentRepository->registerConsent(
                'gtmCustomCookieEight',
                'GoogleTagManager::CookieConsent.consentCustomCookieEightLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentCustomCookieEightDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentCustomCookieEightProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentCustomCookieEightLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentCustomCookieEightPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupCustomCookieEight', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentCustomCookieEightNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentCustomCookieEightIsOptOut') === 'true',
                    'cookieNames' =>  array_map('trim', explode(',', $config->get('GoogleTagManager.consentCustomCookieEightCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showCustomCookieNine') === 'true') {
            $consentRepository->registerConsent(
                'gtmCustomCookieNine',
                'GoogleTagManager::CookieConsent.consentCustomCookieNineLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentCustomCookieNineDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentCustomCookieNineProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentCustomCookieNineLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentCustomCookieNinePolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupCustomCookieNine', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentCustomCookieNineNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentCustomCookieNineIsOptOut') === 'true',
                    'cookieNames' =>  array_map('trim', explode(',', $config->get('GoogleTagManager.consentCustomCookieNineCookieNames')))
                ]
            );
        }

        if ($config->get('GoogleTagManager.showCustomCookieTen') === 'true') {
            $consentRepository->registerConsent(
                'gtmCustomCookieTen',
                'GoogleTagManager::CookieConsent.consentCustomCookieTenLabel',
                [
                    'description' => 'GoogleTagManager::CookieConsent.consentCustomCookieTenDescription',
                    'provider' => 'GoogleTagManager::CookieConsent.consentCustomCookieTenProvider',
                    'lifespan' => 'GoogleTagManager::CookieConsent.consentCustomCookieTenLifespan',
                    'policyUrl' => 'GoogleTagManager::CookieConsent.consentCustomCookieTenPolicyUrl',
                    'group' => $config->get('GoogleTagManager.consentGroupCustomCookieTen', 'tracking'),
                    'necessary' => $config->get('GoogleTagManager.consentCustomCookieTenNecessary') === 'true',
                    'isOptOut' => $config->get('GoogleTagManager.consentCustomCookieTenIsOptOut') === 'true',
                    'cookieNames' =>  array_map('trim', explode(',', $config->get('GoogleTagManager.consentCustomCookieTenCookieNames')))
                ]
            );
        }

    }

    /**
     * boot twig extensions and services
     * @param Twig $twig
     * @param Dispatcher $dispatcher
     */
    public function boot(Twig $twig, Dispatcher $dispatcher)
    {
        $dispatcher->listen(OrderCreated::class, function ($event) {
            /** @var FrontendSessionStorageFactoryContract $sessionStorage */
            $sessionStorage = pluginApp(FrontendSessionStorageFactoryContract::class);
            $sessionStorage->getPlugin()->setValue('GTM_TRACK_ORDER', 1);
        }, 0);
    }

}
