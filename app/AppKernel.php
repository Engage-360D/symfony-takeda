<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // basic bundles
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // doctrine bundles
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),

            // jsm bundles
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\TranslationBundle\JMSTranslationBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),

            // fos bundles
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),

            // other bundles
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle(),
            new Sensio\Bundle\BuzzBundle\SensioBuzzBundle(),

            // our project bundles

            new Melodia\UtilBundle\MelodiaUtilBundle(),
            new Melodia\PageBundle\MelodiaPageBundle(),
            new Engage360d\Bundle\RestBundle\Engage360dRestBundle(),
            new Engage360d\Bundle\SecurityBundle\Engage360dSecurityBundle(),
            new Engage360d\Bundle\SearchBundle\Engage360dSearchBundle(),
            new Engage360d\Bundle\CountriesBundle\Engage360dCountriesBundle(),
            new Engage360d\Bundle\TakedaBundle\Engage360dTakedaBundle(),
            new Engage360d\Bundle\JsonApiBundle\Engage360dJsonApiBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
